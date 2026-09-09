<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    protected $data = array();

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('url', 'form'));
        $this->load->library(array('session', 'form_validation'));
        $this->load->model('Property_model', 'property_model');
    }

    protected function check_login() {
        $admin_id = $this->session->userdata('admin_id');
        if (!$admin_id) {
            redirect('admin/login');
        }
    }

    protected function json_response($status, $message, $extra = array(), $httpStatus = 200) {
        $payload = array_merge(array(
            'status' => (bool) $status,
            'message' => (string) $message,
        ), $extra);

        $this->output
            ->set_status_header((int) $httpStatus)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    public function login() {
        if ($this->input->post()) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            // Debug logging
            $debug_log = "LOGIN ATTEMPT - Username: " . $username . " | Password length: " . strlen($password) . "\n";
            error_log($debug_log, 3, APPPATH . 'logs/login_debug.log');

            $admin = $this->property_model->get_admin_user($username);
            
            if (!$admin) {
                $this->data['error'] = 'Admin user not found in database. Check if admin exists.';
                error_log("Admin user '$username' not found in database\n", 3, APPPATH . 'logs/login_debug.log');
            } elseif (!password_verify($password, $admin['password'])) {
                $this->data['error'] = 'Password verification failed. Try: admin / password (or <a href="' . site_url('admin/reset_admin_password') . '" style="color: #007bff;">reset password</a>)';
                error_log("Password verification failed for user '$username'\n", 3, APPPATH . 'logs/login_debug.log');
                error_log("Entered password length: " . strlen($password) . "\n", 3, APPPATH . 'logs/login_debug.log');
                error_log("Stored hash: " . substr($admin['password'], 0, 20) . "...\n", 3, APPPATH . 'logs/login_debug.log');
            } else {
                // Set session data
                $this->session->set_userdata('admin_id', $admin['id']);
                $this->session->set_userdata('admin_username', $admin['username']);
                
                error_log("Login successful for user '$username'\n", 3, APPPATH . 'logs/login_debug.log');
                redirect('admin/dashboard');
            }
        }

        $this->load->view('admin/login', $this->data);
    }

    public function create_admin() {
        // One-time method to create admin user
        // Default credentials: admin / password
        
        // First check if admin already exists
        $existing = $this->property_model->get_admin_user('admin');
        if ($existing) {
            echo 'Admin user already exists. Username: admin | Email: ' . $existing['email'];
            return;
        }
        
        $data = array(
            'username' => 'admin',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'email' => 'admin@steavek.com'
        );
        $this->property_model->insert_admin_user($data);
        echo 'Admin user created successfully. Username: admin | Password: password';
    }

    public function reset_admin_password() {
        // Reset admin password to 'password'
        $new_password_hash = password_hash('password', PASSWORD_DEFAULT);
        
        $this->db->where('username', 'admin');
        $this->db->update('admin_users', array('password' => $new_password_hash));
        
        if ($this->db->affected_rows() > 0) {
            echo '<h2>Admin Password Reset Successful!</h2>';
            echo '<p>New login credentials:</p>';
            echo '<ul>';
            echo '<li><strong>Username:</strong> admin</li>';
            echo '<li><strong>Password:</strong> password</li>';
            echo '</ul>';
            echo '<p><a href="' . site_url('admin/login') . '">Click here to login</a></p>';
        } else {
            echo '<h2>Password Reset Failed</h2>';
            echo '<p>Could not update admin password. Check database connection.</p>';
        }
    }

    public function check_admin_users() {
        // Debug method - check all admin users in database
        $query = $this->db->get('admin_users');
        $admins = $query->result_array();
        
        if (empty($admins)) {
            echo '<h2>No admin users found!</h2>';
            echo '<p><a href="' . site_url('admin/create_admin') . '">Click here to create admin user</a></p>';
        } else {
            echo '<h2>Admin Users Found:</h2>';
            echo '<pre>';
            foreach ($admins as $admin) {
                echo "Username: " . $admin['username'] . "\n";
                echo "Email: " . $admin['email'] . "\n";
                echo "Password Hash: " . substr($admin['password'], 0, 20) . "...\n";
                echo "---\n";
            }
            echo '</pre>';
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('admin/login');
    }

    public function dashboard() {
        $this->check_login();
        $properties = $this->property_model->get_properties();
        
        // Load images for each property
        foreach ($properties as &$property) {
            $property['images'] = $this->property_model->get_property_images($property['id']);
        }
        
        $this->data['properties'] = $properties;
        $this->load->view('admin/dashboard', $this->data);
    }

    public function add_property() {
        $this->check_login();

        if ($this->input->post()) {
            $data = array(
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'price' => $this->input->post('price'),
                'type' => $this->input->post('type'),
                'category' => $this->input->post('category'),
                'location' => $this->input->post('location'),
                'bedrooms' => $this->input->post('bedrooms'),
                'bathrooms' => $this->input->post('bathrooms'),
                'size' => $this->input->post('size'),
                'features' => $this->input->post('features'),
                'video_url' => $this->input->post('video_url'),
                'status' => $this->input->post('status')
            );

            $property_id = $this->property_model->insert_property($data);

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $this->upload_images($property_id);
            }

            redirect('admin/dashboard');
        }

        $this->load->view('admin/add_property', $this->data);
    }

    public function edit_property($id) {
        $this->check_login();

        if ($this->input->post()) {
            $data = array(
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'price' => $this->input->post('price'),
                'type' => $this->input->post('type'),
                'category' => $this->input->post('category'),
                'location' => $this->input->post('location'),
                'bedrooms' => $this->input->post('bedrooms'),
                'bathrooms' => $this->input->post('bathrooms'),
                'size' => $this->input->post('size'),
                'features' => $this->input->post('features'),
                'video_url' => $this->input->post('video_url'),
                'status' => $this->input->post('status')
            );

            $this->property_model->update_property($id, $data);

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $this->property_model->delete_property_images($id);
                $this->upload_images($id);
            }

            redirect('admin/dashboard');
        }

        $this->data['property'] = $this->property_model->get_property($id);
        $this->data['images'] = $this->property_model->get_property_images($id);
        $this->load->view('admin/edit_property', $this->data);
    }

    public function delete_property($id) {
        $this->check_login();
        $this->property_model->delete_property($id);
        redirect('admin/dashboard');
    }

    public function debug() {
        // Check upload directory
        $upload_path = FCPATH . 'uploads/properties/';
        $output = array();
        
        $output['upload_path'] = $upload_path;
        $output['upload_path_exists'] = is_dir($upload_path) ? 'YES' : 'NO';
        $output['upload_path_writable'] = is_writable($upload_path) ? 'YES' : 'NO';
        
        if (is_dir($upload_path)) {
            $files = scandir($upload_path);
            $output['files_in_directory'] = $files;
            $output['file_count'] = count($files) - 2; // Exclude . and ..
        }
        
        // Check database connection
        $output['database_connected'] = $this->db->conn_id ? 'YES' : 'NO';
        
        // Check property_images table
        if ($this->db->conn_id) {
            $table_exists = $this->db->table_exists('property_images');
            $output['property_images_table_exists'] = $table_exists ? 'YES' : 'NO';
            
            if ($table_exists) {
                $columns = $this->db->query("DESCRIBE property_images")->result_array();
                $output['property_images_columns'] = $columns;
                
                // Count records
                $count = $this->db->count_all('property_images');
                $output['property_images_record_count'] = $count;
                
                // Show recent records
                $recent = $this->db->order_by('id', 'DESC')->limit(5)->get('property_images')->result_array();
                $output['recent_images'] = $recent;
            } else {
                $output['error'] = 'property_images table does not exist!';
                $output['suggestion'] = 'Create the table using the migration or manually with the provided SQL';
            }
        }
        
        // Check logs
        $log_path = FCPATH . 'application/logs/';
        if (is_dir($log_path)) {
            $log_files = array_diff(scandir($log_path), array('.', '..'));
            rsort($log_files);
            $output['recent_log_files'] = array_slice($log_files, 0, 5);
            
            // Get last log content
            if (!empty($log_files)) {
                $latest_log = reset($log_files);
                $log_file_path = $log_path . $latest_log;
                $content = file_get_contents($log_file_path);
                $lines = explode("\n", $content);
                $last_lines = array_slice($lines, -20); // Last 20 lines
                $output['last_log_lines'] = $last_lines;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function create_tables() {
        // Create property_images table if it doesn't exist
        if (!$this->db->table_exists('property_images')) {
            $sql = "CREATE TABLE property_images (
                id INT PRIMARY KEY AUTO_INCREMENT,
                property_id INT NOT NULL,
                image_url VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
            )";
            
            if ($this->db->query($sql)) {
                echo json_encode(array('status' => 'success', 'message' => 'property_images table created successfully'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Failed to create table', 'error' => $this->db->error));
            }
        } else {
            echo json_encode(array('status' => 'info', 'message' => 'property_images table already exists'));
        }
        exit;
    }

    private function upload_images($property_id) {
        // Verify property_id is valid
        if (empty($property_id) || !is_numeric($property_id)) {
            log_message('error', 'Invalid property_id: ' . $property_id);
            return false;
        }

        $config['upload_path'] = FCPATH . 'uploads/properties/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = false;

        // Create upload directory if it doesn't exist
        if (!is_dir($config['upload_path'])) {
            @mkdir($config['upload_path'], 0777, true);
        }

        // Check if directory is writable
        if (!is_writable($config['upload_path'])) {
            log_message('error', 'Upload directory not writable: ' . $config['upload_path']);
            return false;
        }

        // Check if files array exists and has data
        if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
            log_message('info', 'No images provided for property: ' . $property_id);
            return true; // Not an error if no images provided
        }

        $files = $_FILES['images'];
        $count = count($files['name']);

        $upload_count = 0;

        for ($i = 0; $i < $count; $i++) {
            // Skip if filename is empty
            if (empty($files['name'][$i]) || $files['error'][$i] != UPLOAD_ERR_OK) {
                continue;
            }

            // Create unique filename
            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $unique_filename = 'prop_' . $property_id . '_' . time() . '_' . $i . '.' . $ext;

            // Prepare file for upload
            $_FILES['upload_file']['name'] = $unique_filename;
            $_FILES['upload_file']['type'] = $files['type'][$i];
            $_FILES['upload_file']['tmp_name'] = $files['tmp_name'][$i];
            $_FILES['upload_file']['error'] = $files['error'][$i];
            $_FILES['upload_file']['size'] = $files['size'][$i];

            // Load and configure upload library fresh for each file
            $this->load->library('upload', $config);
            
            // Custom validation
            if (!is_uploaded_file($files['tmp_name'][$i])) {
                log_message('error', 'File not uploaded via HTTP POST for property ' . $property_id);
                continue;
            }

            // Move file manually to avoid upload library issues
            $destination = $config['upload_path'] . $unique_filename;
            
            if (move_uploaded_file($files['tmp_name'][$i], $destination)) {
                // File moved successfully, now save to database
                $image_data = array(
                    'property_id' => $property_id,
                    'image_url' => 'uploads/properties/' . $unique_filename
                );
                
                if ($this->property_model->insert_property_image($image_data)) {
                    $upload_count++;
                    log_message('info', 'Image uploaded successfully: ' . $unique_filename . ' for property ' . $property_id);
                } else {
                    log_message('error', 'Failed to insert image record in database for property ' . $property_id);
                    // Delete the file if database insert fails
                    @unlink($destination);
                }
            } else {
                log_message('error', 'Failed to move uploaded file for property ' . $property_id . ': ' . $files['name'][$i]);
            }
        }

        log_message('info', 'Image upload completed for property ' . $property_id . ': ' . $upload_count . ' files processed');
        return true;
    }

    public function error() {
        $this->data['heading'] = '404 Page Not Found';
        $this->data['message'] = 'The page you requested was not found.';
        $this->load->view('admin/error', $this->data);
    }
}
