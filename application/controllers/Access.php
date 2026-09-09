<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties]

class Access extends CI_Controller
{
    protected $data = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('url', 'form', 'ci_helper'));
        $this->load->library(array('session', 'form_validation', 'acl'));
        $this->load->model('Access_model', 'accessmodel', TRUE);
    }

    protected function require_login()
    {
        $userData = $this->session->userdata('user_data');
        if (!(isset($userData) && $this->session->userdata('logged_in') == TRUE)) {
            redirect('backoffice');
            exit();
        }
    }

    protected function require_access_admin()
    {
        $this->require_login();
        if (!$this->acl->can('access.manage')) {
            $this->session->set_flashdata('errors', 'Access denied. Only access administrators can manage privileges.');
            redirect('Alkebulan/dashboard');
            exit();
        }
    }

    protected function can_manage_target_user($targetUserId = 0)
    {
        $targetUserId = (int) $targetUserId;
        if ($targetUserId < 1) {
            return FALSE;
        }

        $actorId = (int) $this->session->userdata('auto_id');
        if ($this->accessmodel->is_owner_admin($actorId)) {
            return TRUE;
        }

        return !$this->accessmodel->is_owner_admin($targetUserId);
    }

    protected function init_views_components()
    {
        $this->data['header_mobile'] = $this->load->view('backoffice/global/view_header_mobile', '', TRUE);
        $this->data['side_menu'] = $this->load->view('backoffice/global/view_side_menu', '', TRUE);
        $this->data['header_menu'] = $this->load->view('backoffice/global/view_header_menu', '', TRUE);
        $this->data['userbar_menu'] = $this->load->view('backoffice/global/view_userbar_menu', '', TRUE);
        $this->data['footer'] = $this->load->view('backoffice/global/view_footer', '', TRUE);
        $this->data['scroll_top'] = $this->load->view('backoffice/global/view_scroll_top', '', TRUE);
    }

    public function index()
    {
        $this->require_access_admin();

        $selectedRoleId = (int) $this->input->get('role_id', TRUE);
        $actorId = (int) $this->session->userdata('auto_id');
        $isOwnerActor = $this->accessmodel->is_owner_admin($actorId);

        $roles = $this->accessmodel->get_roles(TRUE, $isOwnerActor);
        if ($selectedRoleId < 1 && !empty($roles)) {
            $selectedRoleId = (int) $roles[0]['id'];
        }

        $this->init_views_components();
        $this->data['css'] = $this->load->view('backoffice/global/public_css', '', TRUE);
        $this->data['js'] = $this->load->view('backoffice/global/public_js', '', TRUE);

        $this->data['roles'] = $roles;
        $this->data['permissions'] = $this->accessmodel->get_permissions(TRUE);
        $this->data['selected_role_id'] = $selectedRoleId;
        $this->data['selected_role_permissions'] = $selectedRoleId > 0 ? $this->accessmodel->get_role_permission_ids($selectedRoleId) : array();

        $this->load->view('backoffice/view_access_control', $this->data);
    }

    public function users()
    {
        $this->require_access_admin();

        $mode = strtolower((string) $this->input->post('mode', TRUE));
        if ($mode === '') {
            $mode = strtolower((string) $this->session->flashdata('access_users_mode'));
        }
        if ($mode === '') {
            $mode = strtolower((string) $this->input->get('mode', TRUE));
        }
        if (!in_array($mode, array('edit', 'reset'), TRUE)) {
            $mode = 'edit';
        }

        $selectedUserId = (int) $this->input->post('params', TRUE);
        if ($selectedUserId < 1) {
            $selectedUserId = (int) $this->input->post('user_id', TRUE);
        }
        if ($selectedUserId < 1) {
            $selectedUserId = (int) $this->session->flashdata('access_users_selected_id');
        }
        if ($selectedUserId < 1) {
            $selectedUserId = (int) $this->input->get('user_id', TRUE);
        }

        $this->init_views_components();
        $this->data['css'] = $this->load->view('backoffice/global/public_css', '', TRUE);
        $this->data['js'] = $this->load->view('backoffice/global/public_js', '', TRUE);

        $actorId = (int) $this->session->userdata('auto_id');
        $isOwnerActor = $this->accessmodel->is_owner_admin($actorId);

        $this->data['roles'] = $this->accessmodel->get_roles(TRUE, $isOwnerActor);
        $this->data['legacy_groups'] = array(
            1 => 'Administrator',
            2 => 'Manager/Supervisor',
            3 => 'Staff/User',
        );
        $this->data['users'] = $this->accessmodel->get_users_for_access($actorId);
        $this->data['user_roles_map'] = $this->accessmodel->get_user_role_map($actorId);

        $selectedUser = NULL;
        if ($selectedUserId > 0) {
            $selectedUser = $this->accessmodel->get_user($selectedUserId, $actorId);
            if (empty($selectedUser)) {
                $selectedUserId = 0;
            }
        }

        if ($selectedUserId < 1 && !empty($this->data['users'])) {
            $selectedUserId = (int) ($this->data['users'][0]['auto_id'] ?? 0);
            if ($selectedUserId > 0) {
                $selectedUser = $this->accessmodel->get_user($selectedUserId, $actorId);
            }
        }

        $currentRoleId = 0;
        if ($selectedUserId > 0 && !empty($this->data['user_roles_map'][$selectedUserId])) {
            foreach ($this->data['user_roles_map'][$selectedUserId] as $assignedRole) {
                if ((int) ($assignedRole['is_primary'] ?? 0) === 1) {
                    $currentRoleId = (int) ($assignedRole['role_id'] ?? 0);
                    break;
                }
            }
        }

        $this->data['view_mode'] = $mode;
        $this->data['selected_user_id'] = $selectedUserId;
        $this->data['selected_user'] = $selectedUser;
        $this->data['selected_user_role_id'] = $currentRoleId;

        $this->load->view('backoffice/view_access_users', $this->data);
    }

    public function update_user()
    {
        $this->require_access_admin();

        if (!$this->input->post()) {
            redirect('Access/users');
            return;
        }

        $this->form_validation->set_rules('user_id', 'User', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('surname', 'Surname', 'required|trim');
        $this->form_validation->set_rules('othernames', 'Other Names', 'required|trim');
        $this->form_validation->set_rules('emailaddress', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('usergroup', 'Legacy User Group', 'required|integer|greater_than[0]');

        $userId = (int) $this->input->post('user_id', TRUE);
        if (!$this->can_manage_target_user($userId)) {
            $this->session->set_flashdata('errors', 'Owner Admin account cannot be modified by this user.');
            redirect('Access/users');
            return;
        }

        $this->session->set_flashdata('access_users_selected_id', $userId);
        $this->session->set_flashdata('access_users_mode', 'edit');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('errors', str_ireplace("\n", ' ', strip_tags(validation_errors())));
            redirect('Access/users');
            return;
        }

        if (!$this->can_manage_target_user((int) $this->input->post('user_id', TRUE))) {
            $this->session->set_flashdata('errors', 'Owner Admin account cannot be modified by this user.');
            redirect('Access/users');
            return;
        }

        $myId = (int) $this->session->userdata('auto_id');
        if ($userId === $myId && (int) $this->input->post('accstatus', TRUE) !== 1) {
            $this->session->set_flashdata('errors', 'You cannot disable your own account.');
            redirect('Access/users');
            return;
        }

        $result = $this->accessmodel->update_user_with_role($userId, array(
            'surname' => $this->input->post('surname', TRUE),
            'othernames' => $this->input->post('othernames', TRUE),
            'emailaddress' => $this->input->post('emailaddress', TRUE),
            'employmentno' => $this->input->post('employmentno', TRUE),
            'phonenumber' => $this->input->post('phonenumber', TRUE),
            'userclass' => $this->input->post('userclass', TRUE),
            'accstatus' => $this->input->post('accstatus', TRUE),
            'usergroup' => $this->input->post('usergroup', TRUE),
            'role_id' => $this->input->post('role_id', TRUE),
        ), (int) $this->session->userdata('auto_id'));

        if (!empty($result['status'])) {
            $this->session->set_flashdata('success', $result['message'] ?? 'User updated successfully.');
        } else {
            $this->session->set_flashdata('errors', $result['message'] ?? 'Unable to update user.');
        }

        redirect('Access/users');
    }

    public function assign_primary_role()
    {
        $this->require_access_admin();

        if (!$this->input->post()) {
            redirect('Access/users');
            return;
        }

        $this->form_validation->set_rules('user_id', 'User', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('role_id', 'Role', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('errors', str_ireplace("\n", ' ', strip_tags(validation_errors())));
            redirect('Access/users');
            return;
        }

        $ok = $this->accessmodel->assign_primary_role(
            (int) $this->input->post('user_id', TRUE),
            (int) $this->input->post('role_id', TRUE),
            (int) $this->session->userdata('auto_id')
        );

        if ($ok) {
            $this->session->set_flashdata('success', 'Primary role assigned successfully.');
        } else {
            $this->session->set_flashdata('errors', 'Unable to assign role.');
        }

        redirect('Access/users');
    }

    public function create_user()
    {
        $this->require_access_admin();

        if (!$this->input->post()) {
            redirect('Access/users');
            return;
        }

        $this->form_validation->set_rules('surname', 'Surname', 'required|trim');
        $this->form_validation->set_rules('othernames', 'Other Names', 'required|trim');
        $this->form_validation->set_rules('emailaddress', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('usergroup', 'Legacy User Group', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('role_id', 'Primary Role', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('errors', str_ireplace("\n", ' ', strip_tags(validation_errors())));
            redirect('Access/users');
            return;
        }

        $result = $this->accessmodel->create_user_with_role(array(
            'surname' => $this->input->post('surname', TRUE),
            'othernames' => $this->input->post('othernames', TRUE),
            'emailaddress' => $this->input->post('emailaddress', TRUE),
            'password' => $this->input->post('password', TRUE),
            'employmentno' => $this->input->post('employmentno', TRUE),
            'phonenumber' => $this->input->post('phonenumber', TRUE),
            'userclass' => $this->input->post('userclass', TRUE),
            'salute' => $this->input->post('salute', TRUE),
            'presentposition' => $this->input->post('presentposition', TRUE),
            'rank' => $this->input->post('rank', TRUE),
            'accstatus' => $this->input->post('accstatus', TRUE),
            'usergroup' => $this->input->post('usergroup', TRUE),
            'role_id' => $this->input->post('role_id', TRUE),
        ), (int) $this->session->userdata('auto_id'));

        if (!empty($result['status'])) {
            $this->session->set_flashdata('success', $result['message'] ?? 'User created successfully.');
        } else {
            $this->session->set_flashdata('errors', $result['message'] ?? 'Unable to create user.');
        }

        redirect('Access/users');
    }

    public function reset_password()
    {
        $this->require_access_admin();

        if (!$this->input->post()) {
            redirect('Access/users');
            return;
        }

        $this->form_validation->set_rules('user_id', 'User', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');

        $userId = (int) $this->input->post('user_id', TRUE);
        if (!$this->can_manage_target_user($userId)) {
            $this->session->set_flashdata('errors', 'Owner Admin account cannot be modified by this user.');
            redirect('Access/users');
            return;
        }

        $this->session->set_flashdata('access_users_selected_id', $userId);
        $this->session->set_flashdata('access_users_mode', 'reset');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('errors', str_ireplace("\n", ' ', strip_tags(validation_errors())));
            redirect('Access/users');
            return;
        }

        $ok = $this->accessmodel->reset_user_password(
            $userId,
            (string) $this->input->post('new_password', TRUE)
        );

        $this->session->set_flashdata($ok ? 'success' : 'errors', $ok ? 'Password reset successfully.' : 'Unable to reset password.');
        redirect('Access/users');
    }

    public function toggle_user_status()
    {
        $this->require_access_admin();

        if (!$this->input->post()) {
            redirect('Access/users');
            return;
        }

        $this->form_validation->set_rules('user_id', 'User', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('status', 'Status', 'required|integer');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('errors', str_ireplace("\n", ' ', strip_tags(validation_errors())));
            redirect('Access/users');
            return;
        }

        $targetUserId = (int) $this->input->post('user_id', TRUE);
        if (!$this->can_manage_target_user($targetUserId)) {
            $this->session->set_flashdata('errors', 'Owner Admin account cannot be modified by this user.');
            redirect('Access/users');
            return;
        }

        $myId = (int) $this->session->userdata('auto_id');
        if ($targetUserId === $myId) {
            $this->session->set_flashdata('errors', 'You cannot disable your own account.');
            redirect('Access/users');
            return;
        }

        $ok = $this->accessmodel->set_user_status($targetUserId, (int) $this->input->post('status', TRUE));
        $this->session->set_flashdata($ok ? 'success' : 'errors', $ok ? 'User status updated successfully.' : 'Unable to update user status. Owner Admin cannot be disabled.');
        redirect('Access/users');
    }

    public function delete_user()
    {
        $this->require_access_admin();

        if (!$this->input->post()) {
            redirect('Access/users');
            return;
        }

        $this->form_validation->set_rules('user_id', 'User', 'required|integer|greater_than[0]');
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('errors', str_ireplace("\n", ' ', strip_tags(validation_errors())));
            redirect('Access/users');
            return;
        }

        $targetUserId = (int) $this->input->post('user_id', TRUE);
        if (!$this->can_manage_target_user($targetUserId)) {
            $this->session->set_flashdata('errors', 'Owner Admin account cannot be modified by this user.');
            redirect('Access/users');
            return;
        }

        $myId = (int) $this->session->userdata('auto_id');
        if ($targetUserId === $myId) {
            $this->session->set_flashdata('errors', 'You cannot delete your own account.');
            redirect('Access/users');
            return;
        }

        if ($targetUserId === 1) {
            $this->session->set_flashdata('errors', 'Super admin account cannot be deleted.');
            redirect('Access/users');
            return;
        }

        $ok = $this->accessmodel->delete_user_with_roles($targetUserId);
        $this->session->set_flashdata($ok ? 'success' : 'errors', $ok ? 'User deleted successfully.' : 'Unable to delete user. Owner Admin cannot be removed.');
        redirect('Access/users');
    }

    public function save_role_permissions()
    {
        $this->require_access_admin();

        if (!$this->input->post()) {
            redirect('Access');
            return;
        }

        $roleId = (int) $this->input->post('role_id', TRUE);
        $permissionIds = $this->input->post('permission_ids', TRUE);

        if ($roleId < 1) {
            $this->session->set_flashdata('errors', 'Select a valid role.');
            redirect('Access');
            return;
        }

        $ok = $this->accessmodel->save_role_permissions($roleId, (array) $permissionIds);

        if ($ok) {
            $this->session->set_flashdata('success', 'Role privileges updated successfully.');
        } else {
            $this->session->set_flashdata('errors', 'Unable to update role privileges.');
        }

        redirect('Access?role_id=' . $roleId);
    }
}
