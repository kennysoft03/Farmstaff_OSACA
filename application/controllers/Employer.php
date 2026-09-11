<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties]

class Employer extends CI_Controller
{
    protected $data      = [];
    protected $employer  = null;

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
    private function require_login()
    {
        $employer_id = $this->session->userdata('employer_id');
        if (!$employer_id) {
            redirect('login');
        }
        $this->employer = $this->fsmodel->get_employer_by_id($employer_id);
        if (!$this->employer || $this->employer['status'] === 'suspended') {
            $this->session->sess_destroy();
            $this->session->set_flashdata('error', 'Your account has been suspended. Contact support.');
            redirect('login');
        }
        $this->data['employer']       = $this->employer;
        $this->data['unread_notifs']  = $this->fsmodel->count_unread_notifications('employer', $employer_id);
    }

    // -------------------------------------------------------
    // Upload helper
    // -------------------------------------------------------
    private function do_upload($field, $dest_path, $allowed = 'jpg|jpeg|png|gif', $max_size = 2048)
    {
        if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Create destination directory if it doesn't exist
        $full_path = FCPATH . $dest_path;
        if (!is_dir($full_path)) {
            mkdir($full_path, 0755, true);
        }

        // Validate file type by MIME
        $finfo     = new finfo(FILEINFO_MIME_TYPE);
        $mime      = $finfo->file($_FILES[$field]['tmp_name']);
        $allowed_mimes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $ext_map   = array_flip($allowed_mimes);
        $ext       = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
        $ext       = strtolower($ext);
        $allowed_arr = explode('|', $allowed);

        if (!in_array($ext, $allowed_arr)) {
            log_message('error', 'Upload rejected — extension not allowed: ' . $ext);
            return false;
        }

        // Validate size
        if ($_FILES[$field]['size'] > $max_size * 1024) {
            log_message('error', 'Upload rejected — file too large: ' . $_FILES[$field]['size']);
            return false;
        }

        // Generate unique filename
        $new_name = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest_file = $full_path . $new_name;

        if (move_uploaded_file($_FILES[$field]['tmp_name'], $dest_file)) {
            return $dest_path . $new_name;
        }

        log_message('error', 'move_uploaded_file failed for field: ' . $field . ' to: ' . $dest_file);
        return false;
    }

    // -------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------
    public function login()
    {
        if ($this->session->userdata('employer_id')) redirect('dashboard');

        if ($this->input->post()) {
            $this->form_validation->set_rules('email',    'Email',    'required|valid_email|trim');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run() === FALSE) {
                $this->data['error'] = validation_errors(' ', ' | ');
            } else {
                $email    = $this->input->post('email', TRUE);
                $password = $this->input->post('password');
                $employer = $this->fsmodel->get_employer_by_email($email);

                if (!$employer) {
                    $this->data['error'] = 'No account found with that email address.';
                } elseif ($employer['status'] === 'pending') {
                    $this->data['error'] = 'Your account is pending approval. Please check your email.';
                } elseif ($employer['status'] === 'suspended') {
                    $this->data['error'] = 'Your account has been suspended. Contact support.';
                } elseif (!password_verify($password, $employer['password'])) {
                    $this->data['error'] = 'Incorrect password. Please try again.';
                } else {
                    $this->session->set_userdata([
                        'employer_id'   => $employer['id'],
                        'employer_name' => $employer['farm_name'],
                        'employer_email'=> $employer['email'],
                        'logged_in'     => true,
                    ]);
                    $this->fsmodel->log_action('employer', $employer['id'], $employer['farm_name'], 'login', 'auth', 'Employer logged in');
                    redirect('dashboard');
                }
            }
        }

        $this->data['page_title'] = 'Employer Login';
        $this->load->view('public/layout/header', $this->data);
        $this->load->view('public/auth/login', $this->data);
        $this->load->view('public/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // REGISTER
    // -------------------------------------------------------
    public function register()
    {
        if ($this->session->userdata('employer_id')) redirect('dashboard');

        if ($this->input->post()) {
            $this->form_validation->set_rules('farm_name',       'Farm / Business Name', 'required|trim|min_length[3]');
            $this->form_validation->set_rules('contact_person',  'Contact Person',       'required|trim');
            $this->form_validation->set_rules('email',           'Email',                'required|valid_email|trim|is_unique[fs_employers.email]');
            $this->form_validation->set_rules('phone',           'Phone Number',         'required|trim|min_length[10]|is_unique[fs_employers.phone]');
            $this->form_validation->set_rules('password',        'Password',             'required|min_length[8]');
            $this->form_validation->set_rules('confirm_password','Confirm Password',     'required|matches[password]');
            $this->form_validation->set_rules('lga',             'LGA',                  'required|trim');
            $this->form_validation->set_rules('farm_type',       'Farm Type',            'required|trim');

            $this->form_validation->set_message('is_unique', 'That {field} is already registered.');

            if ($this->form_validation->run() === FALSE) {
                $this->data['error'] = validation_errors(' ', ' | ');
            } else {
                $data = [
                    'farm_name'      => $this->input->post('farm_name', TRUE),
                    'contact_person' => $this->input->post('contact_person', TRUE),
                    'email'          => $this->input->post('email', TRUE),
                    'phone'          => $this->input->post('phone', TRUE),
                    'password'       => $this->input->post('password'),
                    'address'        => $this->input->post('address', TRUE),
                    'lga'            => $this->input->post('lga', TRUE),
                    'state'          => $this->input->post('state', TRUE) ?: 'Ondo',
                    'farm_type'      => $this->input->post('farm_type', TRUE),
                    'farm_size'      => $this->input->post('farm_size', TRUE),
                    'reg_number'     => $this->input->post('reg_number', TRUE),
                ];

                $id = $this->fsmodel->register_employer($data);
                if ($id) {
                    $this->fsmodel->log_action('employer', $id, $data['farm_name'], 'register', 'auth', 'New employer registered');
                    $employer = $this->fsmodel->get_employer_by_id($id);
                    $this->session->set_userdata([
                        'employer_id'   => $employer['id'],
                        'employer_name' => $employer['farm_name'],
                        'employer_email'=> $employer['email'],
                        'logged_in'     => true,
                    ]);
                    $this->session->set_flashdata('success', 'Welcome! Your account has been created successfully.');
                    redirect('dashboard');
                } else {
                    $this->data['error'] = 'Registration failed. Please try again.';
                }
            }
        }

        $this->data['page_title'] = 'Register as Employer';
        $this->load->view('public/layout/header', $this->data);
        $this->load->view('public/auth/register', $this->data);
        $this->load->view('public/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------
    public function logout()
    {
        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'You have been logged out successfully.');
        redirect('login');
    }

    // -------------------------------------------------------
    // VERIFY EMAIL
    // -------------------------------------------------------
    public function verify_email($token)
    {
        $ok = $this->fsmodel->verify_employer_email($token);
        $this->session->set_flashdata($ok ? 'success' : 'error',
            $ok ? 'Email verified! You can now log in.' : 'Invalid or expired verification link.');
        redirect('login');
    }

    // -------------------------------------------------------
    // DASHBOARD HOME
    // -------------------------------------------------------
    public function dashboard()
    {
        $this->require_login();
        $eid = $this->employer['id'];

        $this->data['page_title']     = 'Dashboard';
        $this->data['active_nav']     = 'dashboard';
        $this->data['worker_count']   = $this->fsmodel->count_workers_by_employer($eid);
        $this->data['active_workers'] = $this->fsmodel->count_workers_by_employer($eid, ['status' => 'active']);
        $this->data['pending_incidents'] = $this->fsmodel->count_incidents(['employer_id' => $eid, 'status' => 'pending']);
        $this->data['recent_workers'] = $this->fsmodel->get_workers_by_employer($eid, [], 5);
        $this->data['recent_incidents']= $this->fsmodel->get_incidents(['employer_id' => $eid], 5);
        $this->data['notifications']  = $this->fsmodel->get_notifications('employer', $eid);

        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/dashboard', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // WORKERS LIST
    // -------------------------------------------------------
    public function workers()
    {
        $this->require_login();
        $eid     = $this->employer['id'];
        $filters = [
            'status' => $this->input->get('status', TRUE),
            'search' => trim((string)$this->input->get('search', TRUE)),
        ];
        $page    = max(1, (int)$this->input->get('page', TRUE));
        $limit   = 20;
        $offset  = ($page - 1) * $limit;

        $this->data['page_title'] = 'My Workers';
        $this->data['active_nav'] = 'workers';
        $this->data['workers']    = $this->fsmodel->get_workers_by_employer($eid, $filters, $limit, $offset);
        $this->data['total']      = $this->fsmodel->count_workers_by_employer($eid, $filters);
        $this->data['filters']    = $filters;
        $this->data['page']       = $page;
        $this->data['limit']      = $limit;

        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/workers', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // REGISTER WORKER
    // -------------------------------------------------------
    public function register_worker()
    {
        $this->require_login();
        $eid = $this->employer['id'];

        if ($this->input->post()) {
            $this->form_validation->set_rules('firstname',  'First Name',    'required|trim');
            $this->form_validation->set_rules('lastname',   'Last Name',     'required|trim');
            $this->form_validation->set_rules('phone',      'Phone',         'required|trim|min_length[10]|is_unique[fs_workers.phone]');
            $this->form_validation->set_rules('gender',     'Gender',        'required');
            $this->form_validation->set_rules('role',       'Role/Position', 'required|trim');
            $this->form_validation->set_rules('start_date', 'Start Date',    'required');
            $this->form_validation->set_message('is_unique', 'A worker with that phone number is already registered in the system.');

            if ($this->form_validation->run() === FALSE) {
                $this->data['error'] = validation_errors(' ', ' | ');
            } else {
                // --- Photo upload ---
                $photo  = null;
                $upload_error = null;

                if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $uploaded = $this->do_upload('photo', UPLOAD_WORKERS);
                    if ($uploaded === false) {
                        $upload_error = 'Photo upload failed. Please use a JPEG or PNG image under 2MB.';
                    } else {
                        $photo = $uploaded;
                    }
                }

                if ($upload_error) {
                    $this->data['error'] = $upload_error;
                } else {
                    // --- ID document upload ---
                    $id_doc = null;
                    if (!empty($_FILES['id_document']['name']) && $_FILES['id_document']['error'] === UPLOAD_ERR_OK) {
                        $uploaded = $this->do_upload('id_document', UPLOAD_WORKERS . 'ids/', 'jpg|jpeg|png|pdf', 3072);
                        if ($uploaded) $id_doc = $uploaded;
                    }

                    // --- Build worker record ---
                    $worker_data = [
                        'firstname'     => $this->input->post('firstname',  TRUE),
                        'lastname'      => $this->input->post('lastname',   TRUE),
                        'othername'     => $this->input->post('othername',  TRUE),
                        'gender'        => $this->input->post('gender',     TRUE),
                        'dob'           => $this->input->post('dob',        TRUE) ?: null,
                        'phone'         => $this->input->post('phone',      TRUE),
                        'alt_phone'     => $this->input->post('alt_phone',  TRUE),
                        'email'         => $this->input->post('email',      TRUE),
                        'address'       => $this->input->post('address',    TRUE),
                        'lga'           => $this->input->post('lga',        TRUE),
                        'state'         => $this->input->post('state',      TRUE) ?: 'Ondo',
                        'id_type'       => $this->input->post('id_type',    TRUE),
                        'id_number'     => $this->input->post('id_number',  TRUE),
                        'photo'         => $photo,
                        'id_document'   => $id_doc,
                        'registered_by' => $eid,
                    ];

                    $worker_id = $this->fsmodel->register_worker($worker_data);

                    if ($worker_id) {
                        // Auto-create work history entry
                        $this->fsmodel->add_work_history([
                            'worker_id'   => $worker_id,
                            'employer_id' => $eid,
                            'role'        => $this->input->post('role',       TRUE),
                            'start_date'  => $this->input->post('start_date', TRUE),
                            'is_current'  => 1,
                            'status'      => 'active',
                        ]);

                        // Add skills if provided
                        $skills = $this->input->post('skills', TRUE);
                        if (!empty($skills)) {
                            foreach ((array)$skills as $skill) {
                                $skill = trim($skill);
                                if ($skill) {
                                    $this->fsmodel->add_skill([
                                        'worker_id'   => $worker_id,
                                        'employer_id' => $eid,
                                        'skill_name'  => $skill,
                                        'proficiency' => 'intermediate',
                                        'rating'      => 3,
                                    ]);
                                }
                            }
                        }

                        $worker = $this->fsmodel->get_worker_by_id($worker_id);
                        $this->fsmodel->log_action(
                            'employer', $eid, $this->employer['farm_name'],
                            'register_worker', 'workers',
                            "Registered worker: {$worker['firstname']} {$worker['lastname']}",
                            $worker_id, 'worker'
                        );
                        $this->session->set_flashdata('success',
                            "Worker {$worker['firstname']} {$worker['lastname']} registered successfully. Registry ID: {$worker['worker_id']}"
                        );
                        redirect('dashboard/worker/' . $worker_id);
                        return;
                    } else {
                        $this->data['error'] = 'Worker registration failed. Please try again.';
                    }
                }
            }
        }

        $this->data['page_title'] = 'Register Worker';
        $this->data['active_nav'] = 'workers';
        $this->data['lgas']       = $this->_ondo_lgas();
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/register_worker', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // VIEW WORKER
    // -------------------------------------------------------
    public function view_worker($worker_id)
    {
        $this->require_login();
        $eid    = $this->employer['id'];
        $worker = $this->fsmodel->get_worker_by_id((int)$worker_id);
        if (!$worker || $worker['registered_by'] != $eid) show_404();

        $this->data['page_title']   = 'Worker Profile';
        $this->data['active_nav']   = 'workers';
        $this->data['worker']       = $worker;
        $this->data['work_history'] = $this->fsmodel->get_work_history_by_worker($worker['id']);
        $this->data['skills']       = $this->fsmodel->get_skills_by_worker($worker['id']);
        $this->data['ratings']      = $this->fsmodel->get_worker_ratings($worker['id']);
        $this->data['incidents']    = $this->fsmodel->get_incidents(['worker_id' => $worker['id'], 'employer_id' => $eid]);
        $this->data['att_summary']  = $this->fsmodel->get_attendance_summary($worker['id'], $eid);
        $this->data['trust_log']    = $this->fsmodel->get_trust_score_log($worker['id']);

        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/view_worker', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // EDIT WORKER
    // -------------------------------------------------------
    public function edit_worker($worker_id)
    {
        $this->require_login();
        $eid    = $this->employer['id'];
        $worker = $this->fsmodel->get_worker_by_id((int)$worker_id);
        if (!$worker || $worker['registered_by'] != $eid) show_404();

        if ($this->input->post()) {
            $data = [
                'firstname'  => $this->input->post('firstname', TRUE),
                'lastname'   => $this->input->post('lastname', TRUE),
                'othername'  => $this->input->post('othername', TRUE),
                'gender'     => $this->input->post('gender', TRUE),
                'dob'        => $this->input->post('dob', TRUE) ?: null,
                'alt_phone'  => $this->input->post('alt_phone', TRUE),
                'email'      => $this->input->post('email', TRUE),
                'address'    => $this->input->post('address', TRUE),
                'lga'        => $this->input->post('lga', TRUE),
                'id_type'    => $this->input->post('id_type', TRUE),
                'id_number'  => $this->input->post('id_number', TRUE),
                'status'     => $this->input->post('status', TRUE),
            ];
            if (!empty($_FILES['photo']['name'])) {
                $uploaded = $this->do_upload('photo', UPLOAD_WORKERS);
                if ($uploaded) $data['photo'] = $uploaded;
            }
            $this->fsmodel->update_worker($worker['id'], $data);
            $this->fsmodel->log_action('employer', $eid, $this->employer['farm_name'], 'edit_worker', 'workers', "Updated worker ID {$worker['worker_id']}", $worker['id'], 'worker');
            $this->session->set_flashdata('success', 'Worker profile updated successfully.');
            redirect('dashboard/worker/' . $worker['id']);
        }

        $this->data['page_title'] = 'Edit Worker';
        $this->data['active_nav'] = 'workers';
        $this->data['worker']     = $worker;
        $this->data['lgas']       = $this->_ondo_lgas();
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/edit_worker', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // WORK HISTORY
    // -------------------------------------------------------
    public function work_history($worker_id)
    {
        $this->require_login();
        $eid    = $this->employer['id'];
        $worker = $this->fsmodel->get_worker_by_id((int)$worker_id);
        if (!$worker || $worker['registered_by'] != $eid) show_404();

        $this->data['page_title'] = 'Work History';
        $this->data['active_nav'] = 'workers';
        $this->data['worker']     = $worker;
        $this->data['history']    = $this->fsmodel->get_work_history_by_worker($worker['id']);
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/work_history', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    public function add_work_history($worker_id)
    {
        $this->require_login();
        $eid    = $this->employer['id'];
        $worker = $this->fsmodel->get_worker_by_id((int)$worker_id);
        if (!$worker || $worker['registered_by'] != $eid) show_404();

        if ($this->input->post()) {
            $this->form_validation->set_rules('role',       'Role',       'required|trim');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required');
            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors(' ', ' | '));
            } else {
                $data = [
                    'worker_id'        => $worker['id'],
                    'employer_id'      => $eid,
                    'role'             => $this->input->post('role', TRUE),
                    'start_date'       => $this->input->post('start_date', TRUE),
                    'end_date'         => $this->input->post('end_date', TRUE) ?: null,
                    'is_current'       => (int)$this->input->post('is_current', TRUE),
                    'responsibilities' => $this->input->post('responsibilities', TRUE),
                    'leaving_reason'   => $this->input->post('leaving_reason', TRUE),
                    'employer_remarks' => $this->input->post('employer_remarks', TRUE),
                    'status'           => $this->input->post('is_current') ? 'active' : 'completed',
                ];
                if (!empty($data['end_date'])) {
                    $data['duration_days'] = (int)((strtotime($data['end_date']) - strtotime($data['start_date'])) / 86400);
                }
                $this->fsmodel->add_work_history($data);
                $this->session->set_flashdata('success', 'Work history record added.');
            }
            redirect('dashboard/worker/' . $worker['id'] . '/history');
        }

        $this->data['page_title'] = 'Add Work History';
        $this->data['active_nav'] = 'workers';
        $this->data['worker']     = $worker;
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/add_work_history', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // SKILLS
    // -------------------------------------------------------
    public function skills($worker_id)
    {
        $this->require_login();
        $eid    = $this->employer['id'];
        $worker = $this->fsmodel->get_worker_by_id((int)$worker_id);
        if (!$worker || $worker['registered_by'] != $eid) show_404();

        if ($this->input->post()) {
            $skill_name = trim((string)$this->input->post('skill_name', TRUE));
            if ($skill_name) {
                $this->fsmodel->add_skill([
                    'worker_id'   => $worker['id'],
                    'employer_id' => $eid,
                    'skill_name'  => $skill_name,
                    'proficiency' => $this->input->post('proficiency', TRUE),
                    'rating'      => (int)$this->input->post('rating', TRUE) ?: 3,
                    'notes'       => $this->input->post('notes', TRUE),
                ]);
                $this->session->set_flashdata('success', "Skill '{$skill_name}' added.");
            }
            redirect('dashboard/worker/' . $worker['id'] . '/skills');
        }

        $this->data['page_title'] = 'Skills & Verification';
        $this->data['active_nav'] = 'workers';
        $this->data['worker']     = $worker;
        $this->data['skills']     = $this->fsmodel->get_skills_by_worker($worker['id']);
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/skills', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // ATTENDANCE
    // -------------------------------------------------------
    public function attendance($worker_id)
    {
        $this->require_login();
        $eid    = $this->employer['id'];
        $worker = $this->fsmodel->get_worker_by_id((int)$worker_id);
        if (!$worker || $worker['registered_by'] != $eid) show_404();

        if ($this->input->post()) {
            $dates   = $this->input->post('attendance_date', TRUE);
            $statuses= $this->input->post('att_status', TRUE);
            if (is_array($dates)) {
                foreach ($dates as $i => $date) {
                    if (empty($date)) continue;
                    $this->fsmodel->log_attendance([
                        'worker_id'       => $worker['id'],
                        'employer_id'     => $eid,
                        'attendance_date' => $date,
                        'status'          => $statuses[$i] ?? 'present',
                        'notes'           => '',
                    ]);
                }
                $this->session->set_flashdata('success', 'Attendance records saved.');
            } else {
                // Single record
                $this->fsmodel->log_attendance([
                    'worker_id'       => $worker['id'],
                    'employer_id'     => $eid,
                    'attendance_date' => $this->input->post('attendance_date', TRUE),
                    'status'          => $this->input->post('att_status', TRUE),
                    'check_in'        => $this->input->post('check_in', TRUE) ?: null,
                    'check_out'       => $this->input->post('check_out', TRUE) ?: null,
                    'notes'           => $this->input->post('notes', TRUE),
                ]);
                $this->session->set_flashdata('success', 'Attendance record saved.');
            }
            redirect('dashboard/worker/' . $worker['id'] . '/attendance');
        }

        $month  = (int)($this->input->get('month') ?: date('n'));
        $year   = (int)($this->input->get('year')  ?: date('Y'));

        $this->data['page_title']  = 'Attendance';
        $this->data['active_nav']  = 'workers';
        $this->data['worker']      = $worker;
        $this->data['attendance']  = $this->fsmodel->get_attendance_by_worker($worker['id'], $eid, $month, $year);
        $this->data['att_summary'] = $this->fsmodel->get_attendance_summary($worker['id'], $eid);
        $this->data['month']       = $month;
        $this->data['year']        = $year;
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/attendance', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // REPORT INCIDENT
    // -------------------------------------------------------
    public function report_incident($worker_id)
    {
        $this->require_login();
        $eid    = $this->employer['id'];
        $worker = $this->fsmodel->get_worker_by_id((int)$worker_id);
        if (!$worker || $worker['registered_by'] != $eid) show_404();

        if ($this->input->post()) {
            $this->form_validation->set_rules('incident_type', 'Incident Type',  'required');
            $this->form_validation->set_rules('incident_date', 'Incident Date',  'required');
            $this->form_validation->set_rules('description',   'Description',    'required|min_length[30]');
            $this->form_validation->set_rules('severity',      'Severity',       'required');

            if ($this->form_validation->run() === FALSE) {
                $this->data['error'] = validation_errors(' ', ' | ');
            } else {
                $evidence = null;
                if (!empty($_FILES['evidence_file']['name'])) {
                    $uploaded = $this->do_upload('evidence_file', UPLOAD_INCIDENTS, 'jpg|jpeg|png|pdf|doc|docx', 5120);
                    if ($uploaded) $evidence = $uploaded;
                }
                $this->fsmodel->report_incident([
                    'worker_id'     => $worker['id'],
                    'employer_id'   => $eid,
                    'incident_type' => $this->input->post('incident_type', TRUE),
                    'incident_date' => $this->input->post('incident_date', TRUE),
                    'description'   => $this->input->post('description', TRUE),
                    'severity'      => $this->input->post('severity', TRUE),
                    'evidence_file' => $evidence,
                    'status'        => 'pending',
                ]);
                $this->fsmodel->log_action('employer', $eid, $this->employer['farm_name'],
                    'report_incident', 'incidents', "Reported incident for worker ID {$worker['worker_id']}", $worker['id'], 'worker');
                $this->session->set_flashdata('success', 'Incident report submitted for admin review.');
                redirect('dashboard/worker/' . $worker['id']);
            }
        }

        $this->data['page_title'] = 'Report Incident';
        $this->data['active_nav'] = 'workers';
        $this->data['worker']     = $worker;
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/report_incident', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // RATE WORKER
    // -------------------------------------------------------
    public function rate_worker($worker_id)
    {
        $this->require_login();
        $eid    = $this->employer['id'];
        $worker = $this->fsmodel->get_worker_by_id((int)$worker_id);
        if (!$worker || $worker['registered_by'] != $eid) show_404();

        if ($this->input->post()) {
            $this->form_validation->set_rules('overall_rating', 'Overall Rating', 'required|integer|greater_than[0]|less_than[6]');
            if ($this->form_validation->run() === FALSE) {
                $this->data['error'] = validation_errors(' ', ' | ');
            } else {
                $this->fsmodel->rate_worker([
                    'worker_id'      => $worker['id'],
                    'employer_id'    => $eid,
                    'overall_rating' => (int)$this->input->post('overall_rating', TRUE),
                    'work_quality'   => (int)$this->input->post('work_quality', TRUE) ?: null,
                    'punctuality'    => (int)$this->input->post('punctuality', TRUE) ?: null,
                    'teamwork'       => (int)$this->input->post('teamwork', TRUE) ?: null,
                    'reliability'    => (int)$this->input->post('reliability', TRUE) ?: null,
                    'review_text'    => $this->input->post('review_text', TRUE),
                    'would_rehire'   => (int)$this->input->post('would_rehire', TRUE) ?: null,
                ]);
                $this->session->set_flashdata('success', 'Worker rated successfully.');
                redirect('dashboard/worker/' . $worker['id']);
            }
        }

        $this->data['page_title'] = 'Rate Worker';
        $this->data['active_nav'] = 'workers';
        $this->data['worker']     = $worker;
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/rate_worker', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // BACKGROUND CHECK
    // -------------------------------------------------------
    public function background_check()
    {
        $this->require_login();
        $eid     = $this->employer['id'];
        $query   = trim((string)$this->input->get_post('q', TRUE));
        $type    = in_array($this->input->get_post('type', TRUE), ['phone','worker_id']) ? $this->input->get_post('type', TRUE) : 'phone';
        $worker  = null;
        $profile = null;

        if ($query !== '') {
            $worker = $this->fsmodel->search_worker_for_check($query, $type);
            if ($worker) {
                $profile = $this->fsmodel->get_worker_full_profile($worker['id']);
                $this->fsmodel->log_background_check([
                    'employer_id'  => $eid,
                    'worker_id'    => $worker['id'],
                    'search_query' => $query,
                    'search_type'  => $type,
                    'result_found' => 1,
                    'ip_address'   => $this->input->ip_address(),
                ]);
            } else {
                $this->fsmodel->log_background_check([
                    'employer_id'  => $eid,
                    'search_query' => $query,
                    'search_type'  => $type,
                    'result_found' => 0,
                    'ip_address'   => $this->input->ip_address(),
                ]);
            }
        }

        $this->data['page_title'] = 'Background Check';
        $this->data['active_nav'] = 'check';
        $this->data['query']      = $query;
        $this->data['type']       = $type;
        $this->data['worker']     = $worker;
        $this->data['profile']    = $profile;
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/background_check', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // RATE FARM (worker rates this employer after leaving)
    // -------------------------------------------------------
    public function rate_farm($work_history_id)
    {
        $this->require_login();
        $eid = $this->employer['id'];
        // This is for workers rating farms; employer-side is read-only view of their ratings
        $history = $this->fsmodel->get_work_history_entry((int)$work_history_id);
        if (!$history) show_404();

        $this->data['page_title'] = 'Farm Ratings';
        $this->data['active_nav'] = 'dashboard';
        $this->data['ratings']    = $this->fsmodel->get_farm_ratings(['employer_id' => $eid]);
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/farm_ratings', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // PROFILE
    // -------------------------------------------------------
    public function profile()
    {
        $this->require_login();
        $eid = $this->employer['id'];

        if ($this->input->post()) {
            $data = [
                'farm_name'      => $this->input->post('farm_name', TRUE),
                'contact_person' => $this->input->post('contact_person', TRUE),
                'address'        => $this->input->post('address', TRUE),
                'lga'            => $this->input->post('lga', TRUE),
                'farm_type'      => $this->input->post('farm_type', TRUE),
                'farm_size'      => $this->input->post('farm_size', TRUE),
                'reg_number'     => $this->input->post('reg_number', TRUE),
            ];
            if (!empty($_FILES['logo']['name'])) {
                $uploaded = $this->do_upload('logo', UPLOAD_EMPLOYERS);
                if ($uploaded) $data['logo'] = $uploaded;
            }
            // Password change
            $new_pass = $this->input->post('new_password');
            if ($new_pass) {
                $confirm = $this->input->post('confirm_password');
                if ($new_pass !== $confirm) {
                    $this->session->set_flashdata('error', 'Passwords do not match.');
                    redirect('dashboard/profile');
                }
                $data['password'] = password_hash($new_pass, PASSWORD_DEFAULT);
            }
            $this->fsmodel->update_employer($eid, $data);
            // Update session name
            $this->session->set_userdata('employer_name', $data['farm_name']);
            $this->session->set_flashdata('success', 'Profile updated successfully.');
            redirect('dashboard/profile');
        }

        $this->data['page_title'] = 'My Profile';
        $this->data['active_nav'] = 'profile';
        $this->data['lgas']       = $this->_ondo_lgas();
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/profile', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // NOTIFICATIONS
    // -------------------------------------------------------
    public function notifications()
    {
        $this->require_login();
        $eid = $this->employer['id'];
        $this->fsmodel->mark_notifications_read('employer', $eid);
        $this->data['page_title']    = 'Notifications';
        $this->data['active_nav']    = 'notifications';
        $this->data['notifications'] = $this->fsmodel->get_notifications('employer', $eid);
        $this->load->view('employer/layout/header', $this->data);
        $this->load->view('employer/notifications', $this->data);
        $this->load->view('employer/layout/footer', $this->data);
    }

    // -------------------------------------------------------
    // Ondo LGAs
    // -------------------------------------------------------
    private function _ondo_lgas()
    {
        return ['Akoko North-East','Akoko North-West','Akoko South-East','Akoko South-West',
            'Akure North','Akure South','Ese Odo','Idanre','Ifedore','Ilaje','Ile Oluji/Okeigbo',
            'Irele','Odigbo','Okitipupa','Ondo East','Ondo West','Ose','Owo'];
    }
}
