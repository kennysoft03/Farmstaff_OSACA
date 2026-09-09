<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties]

class Home extends CI_Controller
{
    protected $data = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Farmstaff_model', 'fsmodel');
        $this->load->helper(['url', 'form', 'ci_helper']);
        $this->load->library(['session', 'form_validation']);
    }

    // --------------------------------------------------
    // Homepage
    // --------------------------------------------------
    public function index()
    {
        $this->data['page_title'] = 'Home';
        $this->data['stats']      = $this->fsmodel->get_platform_stats();
        $this->data['active_nav'] = 'home';
        $this->load->view('public/layout/header', $this->data);
        $this->load->view('public/home', $this->data);
        $this->load->view('public/layout/footer', $this->data);
    }

    // --------------------------------------------------
    // About Us
    // --------------------------------------------------
    public function about()
    {
        $this->data['page_title'] = 'About Us';
        $this->data['active_nav'] = 'about';
        $this->load->view('public/layout/header', $this->data);
        $this->load->view('public/about', $this->data);
        $this->load->view('public/layout/footer', $this->data);
    }

    // --------------------------------------------------
    // Resources
    // --------------------------------------------------
    public function resources()
    {
        $this->data['page_title'] = 'Resources';
        $this->data['active_nav'] = 'resources';
        $this->load->view('public/layout/header', $this->data);
        $this->load->view('public/resources', $this->data);
        $this->load->view('public/layout/footer', $this->data);
    }

    // --------------------------------------------------
    // For Transparency
    // --------------------------------------------------
    public function transparency()
    {
        $this->data['page_title'] = 'For Transparency';
        $this->data['active_nav'] = 'transparency';
        $this->load->view('public/layout/header', $this->data);
        $this->load->view('public/transparency', $this->data);
        $this->load->view('public/layout/footer', $this->data);
    }

    // --------------------------------------------------
    // Public worker search (AJAX + fallback)
    // --------------------------------------------------
    public function search_worker()
    {
        // Must be logged-in employer to do background checks
        $employer_id = $this->session->userdata('employer_id');
        if (!$employer_id) {
            redirect('login');
        }
        $this->data['page_title'] = 'Background Check';
        $this->data['active_nav'] = 'search';
        $query   = trim((string)$this->input->get('q', TRUE));
        $type    = in_array($this->input->get('type', TRUE), ['phone','worker_id','name']) ? $this->input->get('type', TRUE) : 'phone';
        $worker  = null;
        $profile = null;

        if ($query !== '') {
            $worker = $this->fsmodel->search_worker_for_check($query, $type);
            if ($worker) {
                $profile = $this->fsmodel->get_worker_full_profile($worker['id']);
                $this->fsmodel->log_background_check([
                    'employer_id'  => $employer_id,
                    'worker_id'    => $worker['id'],
                    'search_query' => $query,
                    'search_type'  => $type,
                    'result_found' => 1,
                    'ip_address'   => $this->input->ip_address(),
                ]);
                $this->fsmodel->log_action('employer', $employer_id,
                    $this->session->userdata('employer_name'),
                    'background_check', 'workers',
                    "Searched for worker: {$query}", $worker['id'], 'worker');
            } else {
                $this->fsmodel->log_background_check([
                    'employer_id'  => $employer_id,
                    'search_query' => $query,
                    'search_type'  => $type,
                    'result_found' => 0,
                    'ip_address'   => $this->input->ip_address(),
                ]);
            }
        }

        $this->data['query']   = $query;
        $this->data['type']    = $type;
        $this->data['worker']  = $worker;
        $this->data['profile'] = $profile;

        $this->load->view('public/layout/header', $this->data);
        $this->load->view('public/search_worker', $this->data);
        $this->load->view('public/layout/footer', $this->data);
    }

    // --------------------------------------------------
    // Public worker profile (limited public view)
    // --------------------------------------------------
    public function worker_profile($worker_id)
    {
        $employer_id = $this->session->userdata('employer_id');
        if (!$employer_id) {
            redirect('login');
        }
        $worker = $this->fsmodel->get_worker_by_worker_id($worker_id);
        if (!$worker) show_404();

        $this->data['page_title'] = 'Worker Profile';
        $this->data['profile']    = $this->fsmodel->get_worker_full_profile($worker['id']);
        $this->data['worker']     = $worker;

        $this->load->view('public/layout/header', $this->data);
        $this->load->view('public/worker_profile', $this->data);
        $this->load->view('public/layout/footer', $this->data);
    }

    // --------------------------------------------------
    // 404
    // --------------------------------------------------
    public function not_found()
    {
        $this->output->set_status_header(404);
        $this->data['page_title'] = 'Page Not Found';
        $this->load->view('public/layout/header', $this->data);
        $this->load->view('public/404', $this->data);
        $this->load->view('public/layout/footer', $this->data);
    }
}
