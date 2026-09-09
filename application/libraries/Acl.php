<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Acl
{
    protected $CI;
    protected $cache = NULL;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Access_model', 'accessmodel', TRUE);
    }

    public function can($permissionCode, $userId = NULL)
    {
        if ($permissionCode === '' || $permissionCode === NULL) {
            return FALSE;
        }

        $sessionUsergroup = (int) $this->CI->session->userdata('usergroup');
        if ($sessionUsergroup === 1) {
            return TRUE;
        }

        if ($userId === NULL) {
            $userId = (int) $this->CI->session->userdata('auto_id');
        }

        if ((int) $userId < 1) {
            return FALSE;
        }

        $permissions = $this->user_permissions((int) $userId, $sessionUsergroup);

        if (in_array('*', $permissions, TRUE)) {
            return TRUE;
        }

        return in_array((string) $permissionCode, $permissions, TRUE);
    }

    public function require_permission($permissionCode, $redirect = 'Alkebulan/dashboard')
    {
        if ($this->can($permissionCode)) {
            return TRUE;
        }

        $this->CI->session->set_flashdata('errors', 'Access denied. Required privilege: ' . $permissionCode);
        redirect($redirect);
        exit();
    }

    public function user_permissions($userId = NULL, $legacyUsergroup = NULL)
    {
        if ($userId === NULL) {
            $userId = (int) $this->CI->session->userdata('auto_id');
        }

        if ($legacyUsergroup === NULL) {
            $legacyUsergroup = (int) $this->CI->session->userdata('usergroup');
        }

        $cacheKey = (int) $userId . ':' . (int) $legacyUsergroup;
        if ($this->cache !== NULL && isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $codes = $this->CI->accessmodel->get_user_permission_codes((int) $userId, (int) $legacyUsergroup);
        if ($this->cache === NULL) {
            $this->cache = array();
        }
        $this->cache[$cacheKey] = $codes;

        return $codes;
    }
}
