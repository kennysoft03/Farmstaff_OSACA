<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(E_ALL);
class Backoffice_model extends CI_Model {
    protected $owner_role_slug = 'owner_admin';

    protected function resolve_users_table_name()
    {
        if ($this->db->table_exists('alkebulan_users')) {
            return 'alkebulan_users';
        }

        return 'alkebulan_user';
    }

    protected function is_legacy_md5_hash($hash)
    {
        return is_string($hash) && preg_match('/^[a-f0-9]{32}$/i', $hash);
    }

    protected function build_password_hash($plainPassword)
    {
        if (function_exists('password_hash')) {
            return password_hash($plainPassword, PASSWORD_DEFAULT);
        }

        return md5($plainPassword);
    }

    protected function verify_password($plainPassword, $storedHash)
    {
        if ($this->is_legacy_md5_hash($storedHash)) {
            return hash_equals(strtolower($storedHash), strtolower(md5($plainPassword)));
        }

        if (function_exists('password_verify')) {
            return password_verify($plainPassword, $storedHash);
        }

        return FALSE;
    }

    public function __construct()   {
        parent::__construct();
        $this->load->helper('ci_helper');
        $this->load->library(['migration','cencryption']);
        $this->load->model('User_Model','umodel',TRUE);
        #tables
       
        //define('CPATH', base_url());

        //BNPL STARTS HERE  
       
        $this->alkebulan_user          = $this->resolve_users_table_name();
        $this->department     = "department";
        $this->alkebulan_chalets     = "chalets";
        $this->chalets_images        = "chaletpictures";
        $this->chalets_facilities    = "chaletfacilities";
        $this->alkebulan_chalet_rates= "alk_chalet_rates";
        $this->os_setting            = "os_setting";
        $this->season_pricing_table  = "alk_season_pricing_settings";
        $this->pos_points_table      = "alk_pos_points";
        $this->pos_items_table       = "alk_pos_items";
        $this->pos_point_items_table = "alk_pos_point_items";

        $this->ensure_chalet_rates_table();
        $this->ensure_pos_tables();
    }

    /*Start: Global Json Response Method */
    public function response_status($content=array()){
        return json_encode($content);
    }

    protected function resolve_staff_name_columns()
    {
        $surnameCol = $this->db->field_exists('surname', $this->alkebulan_user) ? 'surname' : ($this->db->field_exists('sname', $this->alkebulan_user) ? 'sname' : '');
        $otherCol = $this->db->field_exists('othernames', $this->alkebulan_user) ? 'othernames' : ($this->db->field_exists('onames', $this->alkebulan_user) ? 'onames' : '');

        return array($surnameCol, $otherCol);
    }

    protected function optional_user_column_expr($column, $alias)
    {
        return $this->db->field_exists($column, $this->alkebulan_user) ? "s.{$column} AS {$alias}" : "'' AS {$alias}";
    }

    protected function is_user_owner_admin($userId)
    {
        $userId = (int) $userId;
        if ($userId < 1) {
            return FALSE;
        }

        if (!$this->db->table_exists('alk_user_roles') || !$this->db->table_exists('alk_roles')) {
            return FALSE;
        }

        $sql = "SELECT 1
                FROM alk_user_roles ur
                INNER JOIN alk_roles r ON r.id = ur.role_id
                WHERE ur.user_id = ? AND ur.status = 1 AND r.role_slug = ?
                LIMIT 1";
        $row = $this->db->query($sql, array($userId, $this->owner_role_slug))->row_array();

        return !empty($row);
    }

    protected function owner_log_visibility_filter($logAlias = 'oa')
    {
        $viewerUserId = (int) $this->session->userdata('auto_id');
        if ($viewerUserId > 0 && $this->is_user_owner_admin($viewerUserId)) {
            return '';
        }

        if (!$this->db->table_exists('alk_user_roles') || !$this->db->table_exists('alk_roles')) {
            return '';
        }

        $ownerSlug = $this->db->escape($this->owner_role_slug);

        return " AND {$logAlias}.log_user_id NOT IN (
            SELECT ur.user_id
            FROM alk_user_roles ur
            INNER JOIN alk_roles r ON r.id = ur.role_id
            WHERE ur.status = 1 AND r.role_slug = {$ownerSlug}
        )";
    }

    public function log_operation($userid,$operation,$systemIP,$BrowserOS,$devicetype,$itemid, $type, $year){
        $logdata = array(
            'log_user_id'       => $userid,
            'log_operation'     => $operation,
            'log_ip'            => $systemIP,
            'log_browser_data'  => $BrowserOS,
            'log_from'          => $devicetype,
            'log_entity_id'     => $itemid,
            'log_year'          => $year,
            'log_oper_type'     => $type
        ); 

        $this->db->set($logdata); 
        if ($this->db->insert('oc_activity'))
        {
            return  1;
        }   
        else
            return 0;   
    }

    public function retreive_members(){
        list($surnameCol, $otherCol) = $this->resolve_staff_name_columns();
        $surnameExpr = !empty($surnameCol) ? "COALESCE(s.{$surnameCol}, '')" : "''";
        $otherExpr = !empty($otherCol) ? "COALESCE(s.{$otherCol}, '')" : "''";
        $saluteExpr = $this->optional_user_column_expr('salute', 'salute');
        $deptExpr = $this->optional_user_column_expr('dept', 'dept');
        $ownerclassExpr = $this->optional_user_column_expr('ownerclass', 'ownerclass');
        $phoneExpr = $this->optional_user_column_expr('phonenumber', 'phonenumber');
        $pixExpr = $this->optional_user_column_expr('profilepix', 'profilepix');

        $sqlstm ="SELECT s.auto_id, s.employmentno, s.usergroup, s.emailaddress,
        {$saluteExpr}, {$surnameExpr} AS sname, {$otherExpr} AS onames, {$deptExpr}, {$ownerclassExpr}, {$phoneExpr}, {$pixExpr}
        FROM $this->alkebulan_user s
        WHERE s.auto_id!=1 AND s.usergroup=3 ORDER BY s.emailaddress";
        
        $query =  $this->db->query($sqlstm);
        return $query->result_array();
    }

    public function retreive_members_dept($userclass){
        list($surnameCol, $otherCol) = $this->resolve_staff_name_columns();
        $surnameExpr = !empty($surnameCol) ? "COALESCE(s.{$surnameCol}, '')" : "''";
        $otherExpr = !empty($otherCol) ? "COALESCE(s.{$otherCol}, '')" : "''";
        $saluteExpr = $this->optional_user_column_expr('salute', 'salute');
        $deptExpr = $this->optional_user_column_expr('dept', 'dept');
        $ownerclassExpr = $this->optional_user_column_expr('ownerclass', 'ownerclass');
        $phoneExpr = $this->optional_user_column_expr('phonenumber', 'phonenumber');
        $pixExpr = $this->optional_user_column_expr('profilepix', 'profilepix');

        $sqlstm ="SELECT s.auto_id, s.employmentno, s.usergroup, s.emailaddress,
        {$saluteExpr}, {$surnameExpr} AS sname, {$otherExpr} AS onames, {$deptExpr}, {$ownerclassExpr}, {$phoneExpr}, {$pixExpr}
        FROM $this->alkebulan_user s
        WHERE s.auto_id!=1 AND s.usergroup=3 AND s.ownerclass='$userclass' ORDER BY s.emailaddress";
        
        $query =  $this->db->query($sqlstm);
        return $query->result_array();
    }

    public function retreive_member_by_id($staff_id) {
        list($surnameCol, $otherCol) = $this->resolve_staff_name_columns();
        $surnameExpr = !empty($surnameCol) ? "COALESCE(s.{$surnameCol}, '')" : "''";
        $otherExpr = !empty($otherCol) ? "COALESCE(s.{$otherCol}, '')" : "''";

        $query =  $this->db->query("SELECT s.*, s.auto_id, {$surnameExpr} AS sname, {$otherExpr} AS onames
        FROM $this->alkebulan_user s WHERE s.auto_id=$staff_id");
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }else{
            return 0;
        }
    }

    public function retreive_a_user_by_id($user_id)
    {
        $user_id = (int) $user_id;
        if ($user_id < 1) {
            return array();
        }

        list($surnameCol, $otherCol) = $this->resolve_staff_name_columns();
        $surnameExpr = !empty($surnameCol) ? "COALESCE(s.{$surnameCol}, '')" : "''";
        $otherExpr = !empty($otherCol) ? "COALESCE(s.{$otherCol}, '')" : "''";

        $query = $this->db->query("SELECT s.*, s.auto_id, {$surnameExpr} AS sname, {$otherExpr} AS onames
        FROM {$this->alkebulan_user} s WHERE s.auto_id={$user_id} LIMIT 1");

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return array();
    }

    function delete_member($member_id)
    {
        $this->db->where('auto_id', $member_id);
        if($this->db->delete($this->alkebulan_user)){
            return TRUE;
        } 
        else 
            return FALSE;
    }

    //Seler Insertion
    
    public function custom_send_mail($from_email,$to_email,$subject,$mailbody){
         @require_once('pjmail/pjmail.class.php');
        $mail = new PJmail();
        $mail->setAllFrom($from_email, SITE_TITLE);
        $mail->addrecipient($to_email);
        $mail->addsubject($subject);
        $mail->text = $mailbody;
        //$mail->addbinattachement("orders.pdf", $orders.pdf);
        for($t=0;$t<10;$t++){
            $res = $mail->sendmail();
        }
    }

    public function update_seller_status($seller_id,$post){
        $this->db->where('seller_id', $seller_id);
        if($this->db->update($this->seller_table, $post)){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    /* Validate User Before login*/
    public function validate_user_login($password,$email) {
        
        $this->db->where('emailaddress', $email);           //Check against user table
        $query = $this->db->get_where($this->alkebulan_user);
      
        if ($query->num_rows() > 0) {
            $user_data = $query->row_array(); 

            if (!$this->verify_password((string) $password, (string) ($user_data['password'] ?? ''))) {
                return 2;
            }

            if ($this->is_legacy_md5_hash((string) ($user_data['password'] ?? ''))) {
                $this->db->where('auto_id', (int) $user_data['auto_id'])->update($this->alkebulan_user, array(
                    'password' => $this->build_password_hash((string) $password)
                ));
            } elseif (function_exists('password_needs_rehash') && password_needs_rehash((string) $user_data['password'], PASSWORD_DEFAULT)) {
                $this->db->where('auto_id', (int) $user_data['auto_id'])->update($this->alkebulan_user, array(
                    'password' => $this->build_password_hash((string) $password)
                ));
            }

            $userstatus = $user_data['accstatus'];  // Change Activated flag to 1 if it is 0
            if ($userstatus == 0 ) { return 4; }
            elseif ($userstatus == 1) {
                unset($user_data['password']);

                $session_data = array(
                    'user_data'    => $user_data,
                    'auto_id'      => isset($user_data['auto_id']) ? $user_data['auto_id'] : 0,
                    'usergroup'    => isset($user_data['usergroup']) ? $user_data['usergroup'] : '',
                    'userclass'    => isset($user_data['userclass']) ? $user_data['userclass'] : '',
                    'emailaddress' => isset($user_data['emailaddress']) ? $user_data['emailaddress'] : '',
                    'logged_in'    => TRUE
                );

                $this->session->sess_regenerate(TRUE);
                $this->session->set_userdata($session_data);
                return 1; 
            }
        
        } 
        else 
        {
           return 3;
        }    
    }

    public function update_user_password($user_id,$password){
        $user_id = (int) $user_id;
        $password = (string) $password;
        if ($user_id < 1 || $password === '') {
            return FALSE;
        }

        if ($this->db->where('auto_id', $user_id)->update($this->alkebulan_user, array('password' => $this->build_password_hash($password)))){
            return TRUE;
        }
        else{
            return FALSE;
        }
        
    }

    public function check_old_password($user_id, $oldpassword)
    {
        $user_id = (int) $user_id;
        if ($user_id < 1) {
            return FALSE;
        }

        $user = $this->db->select('password')->where('auto_id', $user_id)->get($this->alkebulan_user)->row_array();
        if (empty($user) || !isset($user['password'])) {
            return FALSE;
        }

        return $this->verify_password((string) $oldpassword, (string) $user['password']);
    }

    #>>>>> Start: Insert New Reference code for seller subscription Record <<<<<#
    public function insert_record($table,$posted){
        if($this->db->insert($table, $posted)) {
            return true;
        }else{
            return FALSE;
        }
    }

    /* Website Settings*/
    public function retreive_site_settings($id)
    {
        $query =  $this->db->query("SELECT * FROM os_setting WHERE id=$id");
        return $query->row_array();
    }

    public function retrievecountry()
    {
        if (!$this->db->table_exists('country')) {
            return array();
        }

        $this->db->from('country');
        if ($this->db->field_exists('country_name', 'country')) {
            $this->db->order_by('country_name', 'ASC');
        } elseif ($this->db->field_exists('name', 'country')) {
            $this->db->order_by('name', 'ASC');
        }

        return $this->db->get()->result_array();
    }

    public function retrievestate()
    {
        if (!$this->db->table_exists('state')) {
            return array();
        }

        $this->db->from('state');
        if ($this->db->field_exists('state_name', 'state')) {
            $this->db->order_by('state_name', 'ASC');
        } elseif ($this->db->field_exists('name', 'state')) {
            $this->db->order_by('name', 'ASC');
        }

        return $this->db->get()->result_array();
    }

    public function site_update_settings($site_id,$posted)
    {
        return $this->db->where("id = $site_id")->update($this->os_setting, $posted);
    }

    // Sign Out User
    function signout() {
        $this->session->sess_destroy();
        return 1;
    }

    public function get_total_staff()
    {
        $query =  $this->db->query("SELECT COUNT(*) as total_staff FROM {$this->alkebulan_user} WHERE usergroup=3");
        return $query->row();
    }

    public function get_total_unit()
    {
        $query =  $this->db->query("SELECT COUNT(*) as total_unit FROM department");
        return $query->row();
    }

    public function get_total_news()
    {
        $query =  $this->db->query("SELECT COUNT(*) as total_news FROM news");
        return $query->row();
    }

    public function get_total_menu()
    {
        $query =  $this->db->query("SELECT COUNT(*) as total_menu FROM menu");
        return $query->row();
    }

    public function get_total_pages()
    {
        $query =  $this->db->query("SELECT COUNT(*) as total_pages FROM pages");
        return $query->row();
    }

    public function get_total_album()
    {
        $query =  $this->db->query("SELECT COUNT(*) as total_albums FROM galleryalbums");
        return $query->row();
    }

    public function get_total_publication()
    {
        $query =  $this->db->query("SELECT COUNT(*) as total_publications FROM profile_publication");
        return $query->row();
    }

    public function get_total_lectures()
    {
        $query =  $this->db->query("SELECT COUNT(*) as total_lectures FROM lectures");
        return $query->row();
    }

    public function log_data_details(){        
        $ownerFilter = $this->owner_log_visibility_filter('oa');
        $query =  $this->db->query("SELECT log_operation, log_oper_type, log_browser, log_entity_id, log_user_id, userclass, emailaddress, log_time, log_from, log_ip FROM oc_activity oa INNER JOIN {$this->alkebulan_user} ou ON ou.auto_id=oa.log_user_id WHERE 1=1 {$ownerFilter} ORDER BY log_time DESC LIMIT 12");
        return $query->result_array();
    }

    public function log_data_complete(){        
        $ownerFilter = $this->owner_log_visibility_filter('oa');
        $query =  $this->db->query("SELECT log_operation, log_oper_type, log_browser, log_entity_id, log_user_id, userclass, emailaddress, log_time, log_from, log_ip FROM oc_activity oa INNER JOIN {$this->alkebulan_user} ou ON ou.auto_id=oa.log_user_id WHERE 1=1 {$ownerFilter} ORDER BY log_time DESC ");
        return $query->result_array();
    }

    #>>>>> General Class to Update  Collecting Parameter SeachID, Array Post Content, Table Name and Table Update ID <<<<<#
    public function insert_update($seachID, $post, $table, $id = '') {
        if ($seachID==0){
            if($this->db->insert($table, $post)) {
                $dept_id  =$this->db->insert_id();
                return $dept_id;
            }else{
                return FALSE;
            }
        }
        else{
            //return $this->db->where("member_id=$member_id")->update($this->alkebulan_user, $post);
            $this->db->where($id, $seachID);
            if ($this->db->update($table, $post)){
                return TRUE;
            }
            else{
                return FALSE;
            }
         }   
    }

    public function delete_record($tab_id, $id, $tablename)
    {
        $this->db->where($tab_id, $id);
        if($this->db->delete($tablename)){
            return TRUE;  
        } 
        else 
            return FALSE;
    }

    

    /*Department Classess*/

    public function retreive_departments()
    {
        $query =  $this->db->query("SELECT * FROM $this->department ORDER BY deptname ASC");
        return $query->result_array();
    }

    function retrievedept()
    {
        $query =  $this->db->query("SELECT deptid, shortcode FROM $this->department ORDER BY shortcode ASC");
        return $query->result();
    }

    public function retreive_a_departments_by_id($deptid) {
        $query =  $this->db->query("SELECT * FROM $this->department WHERE deptid='$deptid'");
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }else{
            return 0;
        }
    }

    public function retrievefaculty()
    {
        $query =  $this->db->query("SELECT * FROM school");
        return $query->result();
    }

    protected function ensure_season_pricing_settings_table()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->season_pricing_table}` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `season_1_name` VARCHAR(80) NOT NULL DEFAULT 'Season 1',
            `season_1_start_md` VARCHAR(5) DEFAULT NULL,
            `season_1_end_md` VARCHAR(5) DEFAULT NULL,
            `season_2_name` VARCHAR(80) NOT NULL DEFAULT 'Season 2',
            `season_2_start_md` VARCHAR(5) DEFAULT NULL,
            `season_2_end_md` VARCHAR(5) DEFAULT NULL,
            `season_3_name` VARCHAR(80) NOT NULL DEFAULT 'Season 3',
            `season_3_start_md` VARCHAR(5) DEFAULT NULL,
            `season_3_end_md` VARCHAR(5) DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    protected function ensure_chalet_rates_table()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->alkebulan_chalet_rates}` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `chalet_id` INT NOT NULL,
            `chalet_name` VARCHAR(150) NOT NULL,
            `nightly_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `standard_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `season_1_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `season_2_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `season_3_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `deposit_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `max_guests` INT NOT NULL DEFAULT 4,
            `inventory_count` INT NOT NULL DEFAULT 1,
            `is_manual_rate` TINYINT NOT NULL DEFAULT 0,
            `status` TINYINT NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_chalet_rate` (`chalet_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        if (!$this->db->field_exists('standard_rate', $this->alkebulan_chalet_rates)) {
            $this->db->query("ALTER TABLE `{$this->alkebulan_chalet_rates}` ADD `standard_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `nightly_rate`");
        }

        if (!$this->db->field_exists('season_1_rate', $this->alkebulan_chalet_rates)) {
            $this->db->query("ALTER TABLE `{$this->alkebulan_chalet_rates}` ADD `season_1_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `standard_rate`");
        }

        if (!$this->db->field_exists('season_2_rate', $this->alkebulan_chalet_rates)) {
            $this->db->query("ALTER TABLE `{$this->alkebulan_chalet_rates}` ADD `season_2_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `season_1_rate`");
        }

        if (!$this->db->field_exists('season_3_rate', $this->alkebulan_chalet_rates)) {
            $this->db->query("ALTER TABLE `{$this->alkebulan_chalet_rates}` ADD `season_3_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `season_2_rate`");
        }

        if (!$this->db->field_exists('inventory_count', $this->alkebulan_chalet_rates)) {
            $this->db->query("ALTER TABLE `{$this->alkebulan_chalet_rates}` ADD `inventory_count` INT NOT NULL DEFAULT 1 AFTER `max_guests`");
        }

        if (!$this->db->field_exists('is_manual_rate', $this->alkebulan_chalet_rates)) {
            $this->db->query("ALTER TABLE `{$this->alkebulan_chalet_rates}` ADD `is_manual_rate` TINYINT NOT NULL DEFAULT 0 AFTER `inventory_count`");
        }

        $this->db->query("ALTER TABLE `{$this->alkebulan_chalet_rates}` MODIFY `nightly_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00");
        $this->db->query("UPDATE `{$this->alkebulan_chalet_rates}` SET `standard_rate` = CASE WHEN COALESCE(`standard_rate`, 0) > 0 THEN `standard_rate` ELSE `nightly_rate` END");
        $this->db->query("UPDATE `{$this->alkebulan_chalet_rates}` SET `nightly_rate` = `standard_rate`");
        $this->db->query("UPDATE `{$this->alkebulan_chalet_rates}` SET `is_manual_rate` = 1 WHERE `updated_at` > `created_at` OR `standard_rate` > 0 OR `season_1_rate` > 0 OR `season_2_rate` > 0 OR `season_3_rate` > 0");
        $this->db->query("UPDATE `{$this->alkebulan_chalet_rates}` SET `nightly_rate` = 0.00, `standard_rate` = 0.00, `season_1_rate` = 0.00, `season_2_rate` = 0.00, `season_3_rate` = 0.00 WHERE `is_manual_rate` = 0");

        $this->ensure_season_pricing_settings_table();
    }

    protected function suggest_chalet_rate($title = '')
    {
        $title = strtolower(trim((string) $title));

        if (strpos($title, '3 bedroom') !== FALSE || strpos($title, '3-bedroom') !== FALSE) {
            return 250000.00;
        }

        if (strpos($title, 'executive') !== FALSE) {
            return 175000.00;
        }

        if (strpos($title, 'jacuzzi') !== FALSE) {
            return 150000.00;
        }

        if (strpos($title, 'simple') !== FALSE || strpos($title, 'single') !== FALSE) {
            return 75000.00;
        }

        if (strpos($title, 'oso') !== FALSE || strpos($title, 'kodi') !== FALSE || strpos($title, 'laba') !== FALSE) {
            return 125000.00;
        }

        return 100000.00;
    }

    protected function normalize_month_day($value = '')
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $value = str_replace('/', '-', $value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $timestamp = strtotime($value);
            return ($timestamp !== FALSE) ? date('m-d', $timestamp) : '';
        }

        if (!preg_match('/^\d{2}-\d{2}$/', $value)) {
            return '';
        }

        $parts = explode('-', $value);
        $first = (int) ($parts[0] ?? 0);
        $second = (int) ($parts[1] ?? 0);

        if ($first < 1 || $first > 31 || $second < 1 || $second > 31) {
            return '';
        }

        // Accept both DD-MM and MM-DD input; DD-MM is treated as the default user format.
        if ($first > 12 && $second <= 12) {
            $day = $first;
            $month = $second;
        } elseif ($second > 12 && $first <= 12) {
            $month = $first;
            $day = $second;
        } else {
            $day = $first;
            $month = $second;
        }

        if (!checkdate($month, $day, 2024)) {
            return '';
        }

        // Persist season settings in DD-MM format to match what users enter in Season Manager.
        return sprintf('%02d-%02d', $day, $month);
    }

    protected function get_default_chalet_pricing_settings()
    {
        return array(
            'season_1_name' => 'Season 1',
            'season_1_start_md' => '',
            'season_1_end_md' => '',
            'season_2_name' => 'Season 2',
            'season_2_start_md' => '',
            'season_2_end_md' => '',
            'season_3_name' => 'Season 3',
            'season_3_start_md' => '',
            'season_3_end_md' => '',
        );
    }

    protected function get_primary_season_settings_row()
    {
        if (!$this->db->table_exists($this->season_pricing_table)) {
            return array();
        }

        return (array) $this->db
            ->order_by('id', 'ASC')
            ->limit(1)
            ->get($this->season_pricing_table)
            ->row_array();
    }

    public function get_chalet_pricing_settings()
    {
        $this->ensure_chalet_rates_table();

        $defaults = $this->get_default_chalet_pricing_settings();

        if (!$this->db->table_exists($this->season_pricing_table)) {
            return $defaults;
        }

        $settings = $this->get_primary_season_settings_row();
        if (empty($settings)) {
            $this->db->insert($this->season_pricing_table, $defaults);
            return $defaults;
        }

        foreach ($defaults as $key => $value) {
            if (strpos($key, '_name') !== FALSE) {
                $defaults[$key] = trim((string) ($settings[$key] ?? $value));
                if ($defaults[$key] === '') {
                    $defaults[$key] = $value;
                }
            } else {
                $defaults[$key] = $this->normalize_month_day($settings[$key] ?? $value);
            }
        }

        return $defaults;
    }

    public function save_chalet_pricing_settings($payload = array())
    {
        $this->ensure_chalet_rates_table();

        if (!$this->db->table_exists($this->season_pricing_table)) {
            return FALSE;
        }

        $payload = is_array($payload) ? $payload : array();
        $updates = $this->get_default_chalet_pricing_settings();

        for ($index = 1; $index <= 3; $index++) {
            $nameKey = 'season_' . $index . '_name';
            $startKey = 'season_' . $index . '_start_md';
            $endKey = 'season_' . $index . '_end_md';
            $updates[$nameKey] = trim((string) ($payload[$nameKey] ?? ('Season ' . $index)));
            if ($updates[$nameKey] === '') {
                $updates[$nameKey] = 'Season ' . $index;
            }
            $updates[$startKey] = $this->normalize_month_day($payload[$startKey] ?? '');
            $updates[$endKey] = $this->normalize_month_day($payload[$endKey] ?? '');
        }

        $existing = $this->get_primary_season_settings_row();
        if (!empty($existing['id'])) {
            return $this->db->where('id', (int) $existing['id'])->update($this->season_pricing_table, $updates);
        }

        return $this->db->insert($this->season_pricing_table, $updates);
    }

    public function save_chalet_rate($chaletId, $chaletName = '', $nightlyRate = 0, $inventoryCount = 1, $seasonRates = array())
    {
        $chaletId = (int) $chaletId;
        if ($chaletId < 1) {
            return FALSE;
        }

        $this->ensure_chalet_rates_table();

        $cleanRate = preg_replace('/[^0-9.]/', '', (string) $nightlyRate);
        $standardRate = max(0, (float) $cleanRate);
        $inventoryCount = max(1, (int) $inventoryCount);
        $seasonRates = is_array($seasonRates) ? $seasonRates : array();

        $payload = array(
            'chalet_id' => $chaletId,
            'chalet_name' => !empty($chaletName) ? $chaletName : ('Chalet ' . $chaletId),
            'nightly_rate' => $standardRate,
            'standard_rate' => $standardRate,
            'season_1_rate' => max(0, (float) preg_replace('/[^0-9.]/', '', (string) ($seasonRates['season_1_rate'] ?? 0))),
            'season_2_rate' => max(0, (float) preg_replace('/[^0-9.]/', '', (string) ($seasonRates['season_2_rate'] ?? 0))),
            'season_3_rate' => max(0, (float) preg_replace('/[^0-9.]/', '', (string) ($seasonRates['season_3_rate'] ?? 0))),
            'inventory_count' => $inventoryCount,
            'is_manual_rate' => 1,
            'status' => 1,
        );

        $existing = $this->db->get_where($this->alkebulan_chalet_rates, array('chalet_id' => $chaletId), 1)->row_array();
        if (!empty($existing)) {
            return $this->db->where('chalet_id', $chaletId)->update($this->alkebulan_chalet_rates, $payload);
        }

        return $this->db->insert($this->alkebulan_chalet_rates, $payload);
    }

    /*Slider Management Classes*/
    public function retreive_chalets(){
        $sqlstm ="SELECT c.*, COALESCE(r.standard_rate, COALESCE(r.nightly_rate, 0)) AS standard_rate, COALESCE(r.standard_rate, COALESCE(r.nightly_rate, 0)) AS nightly_rate, COALESCE(r.season_1_rate, 0) AS season_1_rate, COALESCE(r.season_2_rate, 0) AS season_2_rate, COALESCE(r.season_3_rate, 0) AS season_3_rate, COALESCE(r.inventory_count, 1) AS inventory_count FROM $this->alkebulan_chalets c LEFT JOIN $this->alkebulan_chalet_rates r ON r.chalet_id = c.chalet_id ORDER BY c.slid DESC";
        $query =  $this->db->query($sqlstm);
        return $query->result_array();
    }

    public function retreive_chalet_by_id($slid) {
        $query =  $this->db->query("SELECT c.*, COALESCE(r.standard_rate, COALESCE(r.nightly_rate, 0)) AS standard_rate, COALESCE(r.standard_rate, COALESCE(r.nightly_rate, 0)) AS nightly_rate, COALESCE(r.season_1_rate, 0) AS season_1_rate, COALESCE(r.season_2_rate, 0) AS season_2_rate, COALESCE(r.season_3_rate, 0) AS season_3_rate, COALESCE(r.inventory_count, 1) AS inventory_count FROM $this->alkebulan_chalets c LEFT JOIN $this->alkebulan_chalet_rates r ON r.chalet_id = c.chalet_id WHERE c.chalet_id=$slid");
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }else{
            return 0;
        }
    }

    public function retreive_chalet_images_by_id($slid) {
        $query =  $this->db->query("SELECT * FROM $this->chalets_images WHERE chalet_id=$slid");
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else{
            return 0;
        }
    }

     public function retreive_chalet_facilities_by_id($slid) {
        $query =  $this->db->query("SELECT * FROM $this->chalets_facilities WHERE chalet_id=$slid");
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else{
            return 0;
        }
    }

    public function delete_chalet_img($picid)
    {
        $this->db->where('picid', $picid);
        if($this->db->delete($this->chalets_images)){
            return TRUE;  
        } 
        else 
            return FALSE;
    }

    public function delete_chalet_fac($facid)
    {
        $this->db->where('facid', $facid);
        if($this->db->delete($this->chalets_facilities)){
            return TRUE;  
        } 
        else 
            return FALSE;
    }

    public function delete_chalets($slid)
    {
        $this->db->where('slid', $slid);
        if($this->db->delete($this->alkebulan_chalets)){
            return TRUE;  
        } 
        else 
            return FALSE;
    }



    /* Department Level Admin Setup*/
    function retrieveanadmin($auto_id)
    {
        $query =  $this->db->query("SELECT * FROM {$this->alkebulan_user} WHERE auto_id='$auto_id'");
        return $query->row();
    }

    function retrievedeptdetails($userclass)
    {
        $query =  $this->db->query("SELECT * FROM $this->department WHERE shortcode='$userclass'");
        return $query->row_array();
    }

    public function dept_update_settings($deptid,$posted)
    {
        return $this->db->where("deptid = '$deptid'")->update($this->department, $posted);
    }


    public function retreive_administrators()
    {
        $query =  $this->db->query("SELECT * FROM {$this->alkebulan_user} WHERE usergroup=2");
        return $query->result_array();
    }


    public function retreive_anadmin_by_id($auto_id) {
        $query =  $this->db->query("SELECT * FROM {$this->alkebulan_user} WHERE auto_id='$auto_id'");
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }else{
            return 0;
        }
    }

    protected function ensure_pos_tables()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->pos_points_table}` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `point_name` VARCHAR(120) NOT NULL,
            `point_code` VARCHAR(30) NOT NULL,
            `description` VARCHAR(255) DEFAULT NULL,
            `status` TINYINT NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_pos_point_code` (`point_code`),
            UNIQUE KEY `uq_pos_point_name` (`point_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->pos_items_table}` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `item_name` VARCHAR(160) NOT NULL,
            `item_code` VARCHAR(40) NOT NULL,
            `unit_name` VARCHAR(50) NOT NULL DEFAULT 'Unit',
            `status` TINYINT NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_pos_item_code` (`item_code`),
            UNIQUE KEY `uq_pos_item_name` (`item_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->pos_point_items_table}` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `point_id` INT NOT NULL,
            `item_id` INT NOT NULL,
            `selling_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            `status` TINYINT NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_pos_point_item` (`point_id`, `item_id`),
            KEY `idx_pos_point` (`point_id`),
            KEY `idx_pos_item` (`item_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function get_pos_points()
    {
        $this->ensure_pos_tables();
        return $this->db->order_by('point_name', 'ASC')->get($this->pos_points_table)->result_array();
    }

    public function get_pos_items()
    {
        $this->ensure_pos_tables();
        return $this->db->order_by('item_name', 'ASC')->get($this->pos_items_table)->result_array();
    }

    public function get_pos_point_item_prices()
    {
        $this->ensure_pos_tables();
        $sql = "SELECT ppi.id, ppi.point_id, ppi.item_id, ppi.selling_price, ppi.status,
                       pp.point_name, pp.point_code, pi.item_name, pi.item_code, pi.unit_name
                FROM {$this->pos_point_items_table} ppi
                INNER JOIN {$this->pos_points_table} pp ON pp.id = ppi.point_id
                INNER JOIN {$this->pos_items_table} pi ON pi.id = ppi.item_id
                ORDER BY pp.point_name ASC, pi.item_name ASC";
        return $this->db->query($sql)->result_array();
    }

    public function save_pos_point($payload = array())
    {
        $this->ensure_pos_tables();
        $payload = is_array($payload) ? $payload : array();

        $pointId = (int) ($payload['id'] ?? 0);
        $pointName = trim((string) ($payload['point_name'] ?? ''));
        $pointCode = strtoupper(trim((string) ($payload['point_code'] ?? '')));
        $description = trim((string) ($payload['description'] ?? ''));
        $status = (int) ($payload['status'] ?? 1) === 1 ? 1 : 0;

        if ($pointName === '' || $pointCode === '') {
            return FALSE;
        }

        $data = array(
            'point_name' => $pointName,
            'point_code' => $pointCode,
            'description' => $description,
            'status' => $status,
        );

        if ($pointId > 0) {
            return $this->db->where('id', $pointId)->update($this->pos_points_table, $data);
        }

        return $this->db->insert($this->pos_points_table, $data);
    }

    public function save_pos_item($payload = array())
    {
        $this->ensure_pos_tables();
        $payload = is_array($payload) ? $payload : array();

        $itemId = (int) ($payload['id'] ?? 0);
        $itemName = trim((string) ($payload['item_name'] ?? ''));
        $itemCode = strtoupper(trim((string) ($payload['item_code'] ?? '')));
        $unitName = trim((string) ($payload['unit_name'] ?? 'Unit'));
        $status = (int) ($payload['status'] ?? 1) === 1 ? 1 : 0;

        if ($itemName === '' || $itemCode === '') {
            return FALSE;
        }

        $data = array(
            'item_name' => $itemName,
            'item_code' => $itemCode,
            'unit_name' => ($unitName !== '') ? $unitName : 'Unit',
            'status' => $status,
        );

        if ($itemId > 0) {
            return $this->db->where('id', $itemId)->update($this->pos_items_table, $data);
        }

        return $this->db->insert($this->pos_items_table, $data);
    }

    public function save_pos_point_item_price($payload = array())
    {
        $this->ensure_pos_tables();
        $payload = is_array($payload) ? $payload : array();

        $pointId = (int) ($payload['point_id'] ?? 0);
        $itemId = (int) ($payload['item_id'] ?? 0);
        $sellingPrice = max(0, (float) preg_replace('/[^0-9.]/', '', (string) ($payload['selling_price'] ?? 0)));
        $status = (int) ($payload['status'] ?? 1) === 1 ? 1 : 0;

        if ($pointId < 1 || $itemId < 1) {
            return FALSE;
        }

        $existing = $this->db->get_where($this->pos_point_items_table, array(
            'point_id' => $pointId,
            'item_id' => $itemId,
        ), 1)->row_array();

        $data = array(
            'point_id' => $pointId,
            'item_id' => $itemId,
            'selling_price' => $sellingPrice,
            'status' => $status,
        );

        if (!empty($existing['id'])) {
            return $this->db->where('id', (int) $existing['id'])->update($this->pos_point_items_table, $data);
        }

        return $this->db->insert($this->pos_point_items_table, $data);
    }

    public function delete_pos_point($pointId = 0)
    {
        $this->ensure_pos_tables();
        $pointId = (int) $pointId;
        if ($pointId < 1) {
            return FALSE;
        }

        $this->db->where('point_id', $pointId)->delete($this->pos_point_items_table);
        return $this->db->where('id', $pointId)->delete($this->pos_points_table);
    }

    public function delete_pos_item($itemId = 0)
    {
        $this->ensure_pos_tables();
        $itemId = (int) $itemId;
        if ($itemId < 1) {
            return FALSE;
        }

        $this->db->where('item_id', $itemId)->delete($this->pos_point_items_table);
        return $this->db->where('id', $itemId)->delete($this->pos_items_table);
    }

    public function delete_pos_point_item_price($id = 0)
    {
        $this->ensure_pos_tables();
        $id = (int) $id;
        if ($id < 1) {
            return FALSE;
        }

        return $this->db->where('id', $id)->delete($this->pos_point_items_table);
    }


}
