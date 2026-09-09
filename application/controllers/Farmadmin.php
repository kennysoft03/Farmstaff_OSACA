<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties]

class Farmadmin extends CI_Controller
{
    protected $data      = [];
    protected $admin     = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Farmstaff_model', 'fsmodel');
        $this->load->helper(['url', 'form', 'ci_helper']);
        $this->load->library(['session', 'form_validation', 'upload']);
    }

    // -------------------------------------------------------
    // Auth guard
    // -------------------------------------------------------
    private function require_admin()
    {
        $admin_id = $this->session->userdata('admin_id');
        if (!$admin_id) {
            redirect('admin/login');
        }
        $this->admin = $this->fsmodel->get_admin_by_id($admin_id);
        if (!$this->admin) {
            $this->session->sess_destroy();
            redirect('admin/login');
        }
        $this->data['admin']         = $this->admin;
        $this->data['unread_notifs'] = $this->fsmodel->count_unread_notifications('admin', $admin_id);
        // Always load summary stats so sidebar badges work on every page
        $this->data['stats']         = $this->fsmodel->get_platform_stats();
    }

    private function render($view)
    {
        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/' . $view, $this->data);
        $this->load->view('admin/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------
    public function login()
    {
        if ($this->session->userdata('admin_id')) redirect('admin/dashboard');

        if ($this->input->post()) {
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password');
            $admin    = $this->fsmodel->get_admin_by_username($username);

            if (!$admin || !password_verify($password, $admin['password'])) {
                $this->data['error'] = 'Invalid username or password.';
            } else {
                $this->session->set_userdata([
                    'admin_id'   => $admin['id'],
                    'admin_name' => $admin['surname'] . ' ' . $admin['othernames'],
                    'admin_role' => $admin['role'],
                    'admin_logged_in' => true,
                ]);
                $this->fsmodel->update_admin_last_login($admin['id']);
                $this->fsmodel->log_action('admin', $admin['id'], $admin['surname'] . ' ' . $admin['othernames'], 'login', 'auth', 'Admin logged in');
                redirect('admin/dashboard');
            }
        }

        $this->data['page_title'] = 'Admin Login';
        $this->load->view('admin/login', $this->data);
    }

    // -------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('admin/login');
    }

    // -------------------------------------------------------
    // DASHBOARD
    // -------------------------------------------------------
    public function dashboard()
    {
        $this->require_admin();
        $this->data['page_title']         = 'Dashboard';
        $this->data['active_nav']         = 'dashboard';
        $this->data['stats']              = $this->fsmodel->get_platform_stats();
        $this->data['recent_workers']     = $this->fsmodel->get_all_workers([], 8);
        $this->data['recent_employers']   = $this->fsmodel->get_all_employers([], 5);
        $this->data['pending_incidents']  = $this->fsmodel->get_incidents(['status' => 'pending'], 8);
        $this->data['pending_ratings']    = $this->fsmodel->get_farm_ratings(['status' => 'pending'], 5);
        $this->data['workers_by_month']   = $this->fsmodel->get_workers_by_month(6);
        $this->data['employers_by_month'] = $this->fsmodel->get_employers_by_month(6);
        $this->data['notifications']      = $this->fsmodel->get_notifications('admin', $this->admin['id']);
        $this->render('dashboard');
    }

    // -------------------------------------------------------
    // WORKERS
    // -------------------------------------------------------
    public function workers()
    {
        $this->require_admin();
        $filters = [
            'status' => $this->input->get('status', TRUE),
            'search' => trim((string)$this->input->get('search', TRUE)),
        ];
        $page   = max(1, (int)$this->input->get('page', TRUE));
        $limit  = 25;
        $offset = ($page - 1) * $limit;

        $this->data['page_title'] = 'Workers';
        $this->data['active_nav'] = 'workers';
        $this->data['workers']    = $this->fsmodel->get_all_workers($filters, $limit, $offset);
        $this->data['total']      = $this->fsmodel->count_all_workers($filters);
        $this->data['filters']    = $filters;
        $this->data['page']       = $page;
        $this->data['limit']      = $limit;
        $this->render('workers');
    }

    public function view_worker($id)
    {
        $this->require_admin();
        $worker = $this->fsmodel->get_worker_by_id((int)$id);
        if (!$worker) show_404();

        $this->data['page_title']   = 'Worker: ' . $worker['firstname'] . ' ' . $worker['lastname'];
        $this->data['active_nav']   = 'workers';
        $this->data['worker']       = $worker;
        $this->data['work_history'] = $this->fsmodel->get_work_history_by_worker($worker['id']);
        $this->data['skills']       = $this->fsmodel->get_skills_by_worker($worker['id']);
        $this->data['ratings']      = $this->fsmodel->get_worker_ratings($worker['id']);
        $this->data['incidents']    = $this->fsmodel->get_incidents(['worker_id' => $worker['id']]);
        $this->data['trust_log']    = $this->fsmodel->get_trust_score_log($worker['id']);
        $this->data['employer']     = $worker['registered_by'] ? $this->fsmodel->get_employer_by_id($worker['registered_by']) : null;

        if ($this->input->post('action') === 'update_status') {
            $this->fsmodel->update_worker($worker['id'], ['status' => $this->input->post('status', TRUE)]);
            $this->fsmodel->log_action('admin', $this->admin['id'], $this->data['admin']['surname'],
                'update_worker_status', 'workers', "Changed worker {$worker['worker_id']} status", $worker['id'], 'worker');
            $this->session->set_flashdata('success', 'Worker status updated.');
            redirect('admin/worker/' . $worker['id']);
        }

        if ($this->input->post('action') === 'adjust_score') {
            $change = (float)$this->input->post('score_change', TRUE);
            $reason = $this->input->post('score_reason', TRUE);
            $this->fsmodel->adjust_trust_score($worker['id'], $change, $reason, null, 'manual', $this->admin['id']);
            $this->session->set_flashdata('success', 'Trust score adjusted.');
            redirect('admin/worker/' . $worker['id']);
        }

        if ($this->input->post('action') === 'verify_skill') {
            $skill_id = (int)$this->input->post('skill_id', TRUE);
            $this->fsmodel->update_skill($skill_id, ['verified' => 1, 'verified_by' => $this->admin['id']]);
            $this->session->set_flashdata('success', 'Skill verified.');
            redirect('admin/worker/' . $worker['id']);
        }

        $this->render('view_worker');
    }

    // -------------------------------------------------------
    // EMPLOYERS
    // -------------------------------------------------------
    public function employers()
    {
        $this->require_admin();
        $filters = [
            'status' => $this->input->get('status', TRUE),
            'search' => trim((string)$this->input->get('search', TRUE)),
        ];
        $page   = max(1, (int)$this->input->get('page', TRUE));
        $limit  = 25;
        $offset = ($page - 1) * $limit;

        $this->data['page_title'] = 'Employers';
        $this->data['active_nav'] = 'employers';
        $this->data['employers']  = $this->fsmodel->get_all_employers($filters, $limit, $offset);
        $this->data['total']      = $this->fsmodel->count_employers($filters);
        $this->data['filters']    = $filters;
        $this->data['page']       = $page;
        $this->data['limit']      = $limit;
        $this->render('employers');
    }

    public function view_employer($id)
    {
        $this->require_admin();
        $employer = $this->fsmodel->get_employer_by_id((int)$id);
        if (!$employer) show_404();

        $this->data['page_title'] = 'Employer: ' . $employer['farm_name'];
        $this->data['active_nav'] = 'employers';
        $this->data['employer']   = $employer;
        $this->data['workers']    = $this->fsmodel->get_workers_by_employer($employer['id'], [], 50);
        $this->data['ratings']    = $this->fsmodel->get_farm_ratings(['employer_id' => $employer['id']]);
        $this->data['incidents']  = $this->fsmodel->get_incidents(['employer_id' => $employer['id']], 20);

        if ($this->input->post('action') === 'update_status') {
            $status = $this->input->post('status', TRUE);
            $this->fsmodel->update_employer($employer['id'], ['status' => $status]);
            $this->fsmodel->log_action('admin', $this->admin['id'], $this->data['admin']['surname'],
                'update_employer_status', 'employers', "Changed employer {$employer['farm_name']} status to {$status}", $employer['id'], 'employer');
            $this->session->set_flashdata('success', 'Employer status updated.');
            redirect('admin/employer/' . $employer['id']);
        }

        $this->render('view_employer');
    }

    // -------------------------------------------------------
    // INCIDENTS
    // -------------------------------------------------------
    public function incidents()
    {
        $this->require_admin();
        $filters = [
            'status' => $this->input->get('status', TRUE) ?: '',
        ];
        $page   = max(1, (int)$this->input->get('page', TRUE));
        $limit  = 25;
        $offset = ($page - 1) * $limit;

        $this->data['page_title'] = 'Incident Reports';
        $this->data['active_nav'] = 'incidents';
        $this->data['incidents']  = $this->fsmodel->get_incidents($filters, $limit, $offset);
        $this->data['total']      = $this->fsmodel->count_incidents($filters);
        $this->data['filters']    = $filters;
        $this->data['page']       = $page;
        $this->data['limit']      = $limit;
        $this->render('incidents');
    }

    public function view_incident($id)
    {
        $this->require_admin();
        $incident = $this->fsmodel->get_incident((int)$id);
        if (!$incident) show_404();

        $this->data['page_title'] = 'View Incident';
        $this->data['active_nav'] = 'incidents';
        $this->data['incident']   = $incident;
        $this->render('view_incident');
    }

    public function review_incident($id)
    {
        $this->require_admin();
        $incident = $this->fsmodel->get_incident((int)$id);
        if (!$incident) show_404();

        if ($this->input->post()) {
            $this->form_validation->set_rules('status',      'Decision',    'required');
            $this->form_validation->set_rules('admin_notes', 'Admin Notes', 'required|min_length[10]');
            if ($this->form_validation->run() === FALSE) {
                $this->data['error'] = validation_errors(' ', ' | ');
            } else {
                $data = [
                    'status'               => $this->input->post('status', TRUE),
                    'admin_notes'          => $this->input->post('admin_notes', TRUE),
                    'disciplinary_points'  => (int)$this->input->post('disciplinary_points', TRUE),
                ];
                $this->fsmodel->review_incident($id, $data, $this->admin['id']);
                $this->fsmodel->log_action('admin', $this->admin['id'], $this->data['admin']['surname'],
                    'review_incident', 'incidents', "Reviewed incident #{$id} — status: {$data['status']}", $id, 'incident');
                $this->session->set_flashdata('success', 'Incident reviewed successfully.');
                redirect('admin/incidents');
            }
        }

        $this->data['page_title'] = 'Review Incident';
        $this->data['active_nav'] = 'incidents';
        $this->data['incident']   = $incident;
        $this->render('review_incident');
    }

    // -------------------------------------------------------
    // FARM RATINGS
    // -------------------------------------------------------
    public function farm_ratings()
    {
        $this->require_admin();
        $filters = ['status' => $this->input->get('status', TRUE) ?: ''];
        $page    = max(1, (int)$this->input->get('page', TRUE));
        $limit   = 25;
        $offset  = ($page - 1) * $limit;

        $this->data['page_title'] = 'Farm Ratings';
        $this->data['active_nav'] = 'ratings';
        $this->data['ratings']    = $this->fsmodel->get_farm_ratings($filters, $limit, $offset);
        $this->data['total']      = $this->fsmodel->count_farm_ratings($filters);
        $this->data['filters']    = $filters;
        $this->data['page']       = $page;
        $this->data['limit']      = $limit;
        $this->render('farm_ratings');
    }

    public function review_farm_rating($id)
    {
        $this->require_admin();
        if (!$this->input->post()) { redirect('admin/farm-ratings'); }

        $status      = $this->input->post('status', TRUE);
        $admin_notes = $this->input->post('admin_notes', TRUE);
        $ok = $this->fsmodel->review_farm_rating($id, $status, $admin_notes, $this->admin['id']);
        $this->fsmodel->log_action('admin', $this->admin['id'], $this->data['admin']['surname'] ?? '',
            'review_farm_rating', 'ratings', "Reviewed farm rating #{$id} — {$status}", $id, 'farm_rating');
        $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Rating reviewed.' : 'Action failed.');
        redirect('admin/farm-ratings');
    }

    // -------------------------------------------------------
    // REPORTS
    // -------------------------------------------------------
    public function reports()
    {
        $this->require_admin();
        $this->data['page_title']        = 'Reports & Analytics';
        $this->data['active_nav']        = 'reports';
        $this->data['stats']             = $this->fsmodel->get_platform_stats();
        $this->data['top_workers']       = $this->fsmodel->get_top_rated_workers(10);
        $this->data['incident_stats']    = $this->fsmodel->get_incident_stats();
        $this->data['skill_dist']        = $this->fsmodel->get_skill_distribution();
        $this->data['workers_by_month']  = $this->fsmodel->get_workers_by_month(12);
        $this->data['employers_by_month']= $this->fsmodel->get_employers_by_month(12);
        $this->render('reports');
    }

    // -------------------------------------------------------
    // AUDIT LOGS
    // -------------------------------------------------------
    public function audit()
    {
        $this->require_admin();
        $filters = [
            'module'     => $this->input->get('module', TRUE),
            'actor_type' => $this->input->get('actor_type', TRUE),
        ];
        $page   = max(1, (int)$this->input->get('page', TRUE));
        $limit  = 50;
        $offset = ($page - 1) * $limit;

        $this->data['page_title'] = 'Audit Trail';
        $this->data['active_nav'] = 'audit';
        $this->data['logs']       = $this->fsmodel->get_audit_logs($filters, $limit, $offset);
        $this->data['filters']    = $filters;
        $this->data['page']       = $page;
        $this->render('audit');
    }

    // -------------------------------------------------------
    // TRUST SCORES
    // -------------------------------------------------------
    public function trust_scores()
    {
        $this->require_admin();
        $this->data['page_title'] = 'Trust Score Management';
        $this->data['active_nav'] = 'workers';
        $this->data['workers']    = $this->fsmodel->get_all_workers(['status' => 'active'], 50);
        $this->render('trust_scores');
    }

    // -------------------------------------------------------
    // SETTINGS
    // -------------------------------------------------------
    public function settings()
    {
        $this->require_admin();
        $this->data['page_title'] = 'Settings';
        $this->data['active_nav'] = 'settings';
        $this->data['admins']     = $this->fsmodel->get_all_admins();

        if ($this->input->post('action') === 'create_admin') {
            $this->form_validation->set_rules('new_surname',    'Surname',  'required|trim');
            $this->form_validation->set_rules('new_othernames', 'Names',    'required|trim');
            $this->form_validation->set_rules('new_email',      'Email',    'required|valid_email|trim');
            $this->form_validation->set_rules('new_username',   'Username', 'required|trim|min_length[4]');
            $this->form_validation->set_rules('new_password',   'Password', 'required|min_length[8]');
            if ($this->form_validation->run() === FALSE) {
                $this->data['error'] = validation_errors(' ', ' | ');
            } else {
                $this->fsmodel->create_admin([
                    'surname'    => $this->input->post('new_surname', TRUE),
                    'othernames' => $this->input->post('new_othernames', TRUE),
                    'email'      => $this->input->post('new_email', TRUE),
                    'username'   => $this->input->post('new_username', TRUE),
                    'password'   => $this->input->post('new_password'),
                    'role'       => $this->input->post('new_role', TRUE) ?: 'admin',
                ]);
                $this->session->set_flashdata('success', 'Admin user created successfully.');
                redirect('admin/settings');
            }
        }

        $this->render('settings');
    }

    // -------------------------------------------------------
    // One-time admin setup
    // -------------------------------------------------------
    public function create_admin()
    {
        $existing = $this->fsmodel->get_admin_by_username('admin');
        if ($existing) {
            echo '<p>Admin already exists. <a href="' . site_url('admin/login') . '">Login here</a>.</p>';
            return;
        }
        $this->fsmodel->create_admin([
            'surname'    => 'System',
            'othernames' => 'Administrator',
            'email'      => 'admin@farmstaff.ng',
            'username'   => 'admin',
            'password'   => 'Admin@1234',
            'role'       => 'superadmin',
            'status'     => 1,
        ]);
        echo '<p>Admin created. Username: <strong>admin</strong> | Password: <strong>Admin@1234</strong>. <a href="' . site_url('admin/login') . '">Login here</a>.</p>';
    }
}
