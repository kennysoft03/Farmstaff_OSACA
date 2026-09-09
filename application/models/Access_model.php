<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access_model extends CI_Model
{
    protected $owner_role_slug = 'owner_admin';
    protected $roles_table = 'alk_roles';
    protected $permissions_table = 'alk_permissions';
    protected $role_permissions_table = 'alk_role_permissions';
    protected $user_roles_table = 'alk_user_roles';
    protected $users_table = 'alkebulan_user';

    protected function resolve_users_table_name()
    {
        if ($this->db->table_exists('alkebulan_users')) {
            return 'alkebulan_users';
        }

        return 'alkebulan_user';
    }

    protected function set_optional_user_field(&$payload, $column, $value)
    {
        if ($this->db->field_exists($column, $this->users_table)) {
            $payload[$column] = $value;
        }
    }

    protected function resolve_user_name_columns()
    {
        $surnameCandidates = array('surname', 'sname', 'last_name', 'lastname');
        $otherNameCandidates = array('othernames', 'onames', 'first_name', 'firstname');

        $surnameCol = '';
        foreach ($surnameCandidates as $col) {
            if ($this->db->field_exists($col, $this->users_table)) {
                $surnameCol = $col;
                break;
            }
        }

        $otherCol = '';
        foreach ($otherNameCandidates as $col) {
            if ($this->db->field_exists($col, $this->users_table)) {
                $otherCol = $col;
                break;
            }
        }

        return array($surnameCol, $otherCol);
    }

    public function __construct()
    {
        parent::__construct();
        $this->users_table = $this->resolve_users_table_name();
        $this->ensure_user_profile_columns();
        $this->ensure_access_tables();
        $this->seed_access_defaults();
        $this->ensure_owner_admin_bootstrap();
    }

    protected function ensure_user_profile_columns()
    {
        $columnsToEnsure = array(
            "ADD COLUMN surname VARCHAR(120) NULL AFTER password",
            "ADD COLUMN othernames VARCHAR(160) NULL AFTER surname",
            "ADD COLUMN phonenumber VARCHAR(50) NULL AFTER othernames",
            "ADD COLUMN salute VARCHAR(40) NULL AFTER phonenumber",
            "ADD COLUMN presentposition VARCHAR(150) NULL AFTER salute",
            "ADD COLUMN `rank` VARCHAR(80) NULL AFTER presentposition"
        );

        $hasSurname = $this->db->field_exists('surname', $this->users_table) || $this->db->field_exists('sname', $this->users_table);
        $hasOthernames = $this->db->field_exists('othernames', $this->users_table) || $this->db->field_exists('onames', $this->users_table);

        if (!$hasSurname || !$hasOthernames) {
            foreach ($columnsToEnsure as $ddl) {
                $this->db->query("ALTER TABLE {$this->users_table} {$ddl}");
            }
        } else {
            if (!$this->db->field_exists('phonenumber', $this->users_table)) {
                $this->db->query("ALTER TABLE {$this->users_table} ADD COLUMN phonenumber VARCHAR(50) NULL");
            }
            if (!$this->db->field_exists('salute', $this->users_table)) {
                $this->db->query("ALTER TABLE {$this->users_table} ADD COLUMN salute VARCHAR(40) NULL");
            }
            if (!$this->db->field_exists('presentposition', $this->users_table)) {
                $this->db->query("ALTER TABLE {$this->users_table} ADD COLUMN presentposition VARCHAR(150) NULL");
            }
            if (!$this->db->field_exists('rank', $this->users_table)) {
                $this->db->query("ALTER TABLE {$this->users_table} ADD COLUMN `rank` VARCHAR(80) NULL");
            }
        }
    }

    public function ensure_access_tables()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS {$this->roles_table} (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            role_slug VARCHAR(80) NOT NULL,
            role_name VARCHAR(120) NOT NULL,
            description VARCHAR(255) DEFAULT NULL,
            status TINYINT(1) NOT NULL DEFAULT 1,
            is_system TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_role_slug (role_slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS {$this->permissions_table} (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            permission_code VARCHAR(120) NOT NULL,
            module_name VARCHAR(80) NOT NULL,
            action_name VARCHAR(80) NOT NULL,
            label VARCHAR(140) NOT NULL,
            description VARCHAR(255) DEFAULT NULL,
            status TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_permission_code (permission_code)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS {$this->role_permissions_table} (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            role_id INT UNSIGNED NOT NULL,
            permission_id INT UNSIGNED NOT NULL,
            allow_access TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_role_permission (role_id, permission_id),
            KEY idx_permission (permission_id),
            CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES {$this->roles_table}(id) ON DELETE CASCADE,
            CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES {$this->permissions_table}(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS {$this->user_roles_table} (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id INT UNSIGNED NOT NULL,
            role_id INT UNSIGNED NOT NULL,
            is_primary TINYINT(1) NOT NULL DEFAULT 1,
            status TINYINT(1) NOT NULL DEFAULT 1,
            assigned_by INT UNSIGNED DEFAULT NULL,
            assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_user_role_primary (user_id, role_id),
            KEY idx_user_primary (user_id, is_primary),
            CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES {$this->roles_table}(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function seed_access_defaults()
    {
        $roles = array(
            array('role_slug' => 'owner_admin', 'role_name' => 'Owner Admin', 'description' => 'Protected owner account with full system governance'),
            array('role_slug' => 'administrator', 'role_name' => 'Administrator', 'description' => 'Full system access'),
            array('role_slug' => 'operations_manager', 'role_name' => 'Operations Manager', 'description' => 'Reservations, POS oversight, reports'),
            array('role_slug' => 'reception_officer', 'role_name' => 'Reception Officer', 'description' => 'Front desk and reservation operations'),
            array('role_slug' => 'pos_manager', 'role_name' => 'POS Manager', 'description' => 'POS controls, refunds, audit, reports'),
            array('role_slug' => 'pos_cashier', 'role_name' => 'POS Cashier', 'description' => 'Record sales and run shifts'),
            array('role_slug' => 'stock_manager', 'role_name' => 'Stock Manager', 'description' => 'Inventory and stock movement control'),
            array('role_slug' => 'account_officer', 'role_name' => 'Account Officer', 'description' => 'Financial reports and reconciliation')
        );

        foreach ($roles as $row) {
            $exists = $this->db->where('role_slug', $row['role_slug'])->get($this->roles_table)->row_array();
            if (empty($exists)) {
                $this->db->insert($this->roles_table, $row);
            }
        }

        $permissions = array(
            array('permission_code' => 'access.manage', 'module_name' => 'access', 'action_name' => 'manage', 'label' => 'Manage Access Control'),
            array('permission_code' => 'users.manage', 'module_name' => 'users', 'action_name' => 'manage', 'label' => 'Manage Users'),
            array('permission_code' => 'reservation.dashboard.view', 'module_name' => 'reservation', 'action_name' => 'dashboard', 'label' => 'View Reservation Dashboard'),
            array('permission_code' => 'reservation.frontdesk.view', 'module_name' => 'reservation', 'action_name' => 'frontdesk', 'label' => 'Use Front Desk Board'),
            array('permission_code' => 'reservation.manage', 'module_name' => 'reservation', 'action_name' => 'manage', 'label' => 'Create/Edit Reservations'),
            array('permission_code' => 'reservation.checkin', 'module_name' => 'reservation', 'action_name' => 'checkin', 'label' => 'Check In Guest'),
            array('permission_code' => 'reservation.checkout', 'module_name' => 'reservation', 'action_name' => 'checkout', 'label' => 'Check Out Guest'),
            array('permission_code' => 'reservation.reports.view', 'module_name' => 'reservation', 'action_name' => 'reports', 'label' => 'View Reservation Reports'),
            array('permission_code' => 'pos.setup.manage', 'module_name' => 'pos', 'action_name' => 'setup', 'label' => 'Manage POS Setup'),
            array('permission_code' => 'pos.sale.view', 'module_name' => 'pos', 'action_name' => 'view_sale', 'label' => 'View POS Sales/Receipts'),
            array('permission_code' => 'pos.sale.record', 'module_name' => 'pos', 'action_name' => 'record_sale', 'label' => 'Record POS Sale'),
            array('permission_code' => 'pos.sale.void', 'module_name' => 'pos', 'action_name' => 'void_sale', 'label' => 'Void POS Sale'),
            array('permission_code' => 'pos.sale.refund', 'module_name' => 'pos', 'action_name' => 'refund_sale', 'label' => 'Refund POS Sale'),
            array('permission_code' => 'pos.stock.view', 'module_name' => 'pos', 'action_name' => 'view_stock', 'label' => 'View Stock Ledger'),
            array('permission_code' => 'pos.stock.manage', 'module_name' => 'pos', 'action_name' => 'manage_stock', 'label' => 'Record Stock Movement'),
            array('permission_code' => 'pos.shift.open', 'module_name' => 'pos', 'action_name' => 'open_shift', 'label' => 'Open Shift'),
            array('permission_code' => 'pos.shift.close', 'module_name' => 'pos', 'action_name' => 'close_shift', 'label' => 'Close Shift'),
            array('permission_code' => 'pos.shift.report', 'module_name' => 'pos', 'action_name' => 'print_shift', 'label' => 'Print Shift Report'),
            array('permission_code' => 'pos.reports.view', 'module_name' => 'pos', 'action_name' => 'reports', 'label' => 'View POS Reports'),
            array('permission_code' => 'pos.audit.view', 'module_name' => 'pos', 'action_name' => 'audit', 'label' => 'View POS Audit Logs'),
            array('permission_code' => 'finance.reports.view', 'module_name' => 'finance', 'action_name' => 'reports', 'label' => 'View Finance Reports')
        );

        foreach ($permissions as $p) {
            $exists = $this->db->where('permission_code', $p['permission_code'])->get($this->permissions_table)->row_array();
            if (empty($exists)) {
                $this->db->insert($this->permissions_table, $p);
            }
        }

        $this->seed_default_role_permissions();
    }

    protected function seed_default_role_permissions()
    {
        $roles = $this->get_roles(FALSE);
        $permissions = $this->get_permissions(FALSE);
        if (empty($roles) || empty($permissions)) {
            return;
        }

        $roleBySlug = array();
        foreach ($roles as $r) {
            $roleBySlug[$r['role_slug']] = (int) $r['id'];
        }

        $permissionByCode = array();
        foreach ($permissions as $p) {
            $permissionByCode[$p['permission_code']] = (int) $p['id'];
        }

        $allCodes = array_keys($permissionByCode);
        $sets = array(
            'owner_admin' => $allCodes,
            'administrator' => $allCodes,
            'operations_manager' => array(
                'reservation.dashboard.view', 'reservation.frontdesk.view', 'reservation.manage',
                'reservation.checkin', 'reservation.checkout', 'reservation.reports.view',
                'pos.sale.view', 'pos.sale.record', 'pos.sale.void', 'pos.sale.refund',
                'pos.stock.view', 'pos.stock.manage', 'pos.shift.open', 'pos.shift.close',
                'pos.shift.report', 'pos.reports.view', 'pos.audit.view', 'finance.reports.view'
            ),
            'reception_officer' => array(
                'reservation.dashboard.view', 'reservation.frontdesk.view', 'reservation.manage',
                'reservation.checkin', 'reservation.checkout', 'pos.sale.view'
            ),
            'pos_manager' => array(
                'pos.setup.manage', 'pos.sale.view', 'pos.sale.record', 'pos.sale.void',
                'pos.sale.refund', 'pos.stock.view', 'pos.stock.manage', 'pos.shift.open',
                'pos.shift.close', 'pos.shift.report', 'pos.reports.view', 'pos.audit.view'
            ),
            'pos_cashier' => array(
                'pos.sale.view', 'pos.sale.record', 'pos.shift.open', 'pos.shift.report', 'pos.stock.view'
            ),
            'stock_manager' => array(
                'pos.stock.view', 'pos.stock.manage', 'pos.reports.view'
            ),
            'account_officer' => array(
                'reservation.reports.view', 'pos.reports.view', 'finance.reports.view', 'pos.sale.view'
            )
        );

        foreach ($sets as $roleSlug => $codes) {
            if (empty($roleBySlug[$roleSlug])) {
                continue;
            }
            $roleId = (int) $roleBySlug[$roleSlug];
            $assigned = $this->db->where('role_id', $roleId)->count_all_results($this->role_permissions_table);
            if ((int) $assigned > 0) {
                continue;
            }

            foreach ($codes as $code) {
                if (empty($permissionByCode[$code])) {
                    continue;
                }
                $this->db->insert($this->role_permissions_table, array(
                    'role_id' => $roleId,
                    'permission_id' => (int) $permissionByCode[$code],
                    'allow_access' => 1
                ));
            }
        }
    }

    protected function get_owner_role_id()
    {
        $row = $this->db->select('id')->where('role_slug', $this->owner_role_slug)->get($this->roles_table)->row_array();
        return !empty($row['id']) ? (int) $row['id'] : 0;
    }

    protected function ensure_owner_admin_bootstrap()
    {
        $ownerRoleId = $this->get_owner_role_id();
        if ($ownerRoleId < 1) {
            return;
        }

        $ownerCountSql = "SELECT COUNT(*) AS total_owner
                          FROM {$this->user_roles_table} ur
                          INNER JOIN {$this->roles_table} r ON r.id = ur.role_id
                          WHERE ur.status = 1 AND r.role_slug = ?";
        $ownerCount = $this->db->query($ownerCountSql, array($this->owner_role_slug))->row_array();
        if (!empty($ownerCount['total_owner']) && (int) $ownerCount['total_owner'] > 0) {
            return;
        }

        $ownerUserId = 1;
        $ownerUser = $this->db->select('auto_id')->where('auto_id', $ownerUserId)->get($this->users_table)->row_array();
        if (empty($ownerUser)) {
            return;
        }

        $this->db->trans_start();
        $this->db->where('user_id', $ownerUserId)->update($this->user_roles_table, array('is_primary' => 0, 'status' => 1));

        $existing = $this->db->where('user_id', $ownerUserId)->where('role_id', $ownerRoleId)->get($this->user_roles_table)->row_array();
        if (!empty($existing)) {
            $this->db->where('id', (int) $existing['id'])->update($this->user_roles_table, array(
                'is_primary' => 1,
                'status' => 1,
                'assigned_by' => $ownerUserId,
                'assigned_at' => date('Y-m-d H:i:s')
            ));
        } else {
            $this->db->insert($this->user_roles_table, array(
                'user_id' => $ownerUserId,
                'role_id' => $ownerRoleId,
                'is_primary' => 1,
                'status' => 1,
                'assigned_by' => $ownerUserId,
                'assigned_at' => date('Y-m-d H:i:s')
            ));
        }

        $this->db->trans_complete();
    }

    public function is_owner_admin($userId = 0)
    {
        $userId = (int) $userId;
        if ($userId < 1) {
            return FALSE;
        }

        $sql = "SELECT 1
                FROM {$this->user_roles_table} ur
                INNER JOIN {$this->roles_table} r ON r.id = ur.role_id
                WHERE ur.user_id = ? AND ur.status = 1 AND r.role_slug = ?
                LIMIT 1";
        $row = $this->db->query($sql, array($userId, $this->owner_role_slug))->row_array();

        return !empty($row);
    }

    protected function can_manage_target_user($actorUserId, $targetUserId)
    {
        $actorUserId = (int) $actorUserId;
        $targetUserId = (int) $targetUserId;

        if ($targetUserId < 1) {
            return FALSE;
        }

        if ($this->is_owner_admin($actorUserId)) {
            return TRUE;
        }

        return !$this->is_owner_admin($targetUserId);
    }

    public function get_roles($activeOnly = TRUE, $includeOwnerRole = TRUE)
    {
        if ($activeOnly) {
            $this->db->where('status', 1);
        }

        if (!$includeOwnerRole) {
            $this->db->where('role_slug <>', $this->owner_role_slug);
        }

        return $this->db->order_by('role_name', 'ASC')->get($this->roles_table)->result_array();
    }

    public function get_permissions($activeOnly = TRUE)
    {
        if ($activeOnly) {
            $this->db->where('status', 1);
        }
        return $this->db->order_by('module_name', 'ASC')->order_by('label', 'ASC')->get($this->permissions_table)->result_array();
    }

    public function get_users_for_access($viewerUserId = 0)
    {
        $viewerUserId = (int) $viewerUserId;
        $isOwnerViewer = $this->is_owner_admin($viewerUserId);
        list($surnameCol, $otherCol) = $this->resolve_user_name_columns();

        $surnameExpr = !empty($surnameCol) ? "COALESCE(u.{$surnameCol}, '')" : "''";
        $otherExpr = !empty($otherCol) ? "COALESCE(u.{$otherCol}, '')" : "''";

        $ownerExclusionSql = '';
        if (!$isOwnerViewer) {
            $ownerExclusionSql = " AND u.auto_id NOT IN (
                SELECT ur.user_id
                FROM {$this->user_roles_table} ur
                INNER JOIN {$this->roles_table} r ON r.id = ur.role_id
                WHERE ur.status = 1 AND r.role_slug = '" . $this->db->escape_str($this->owner_role_slug) . "'
            )";
        }

        $sql = "SELECT u.auto_id,
                       COALESCE(NULLIF(TRIM(CONCAT({$surnameExpr}, ' ', {$otherExpr})), ''), u.emailaddress) AS full_name,
                       u.emailaddress,
                       u.usergroup,
                       u.userclass,
                       u.accstatus
                FROM {$this->users_table} u
                WHERE 1=1 {$ownerExclusionSql}
                ORDER BY u.auto_id ASC";
        return $this->db->query($sql)->result_array();
    }

    public function get_user_role_map($viewerUserId = 0)
    {
        $viewerUserId = (int) $viewerUserId;
        $isOwnerViewer = $this->is_owner_admin($viewerUserId);

        $ownerExclusionSql = '';
        if (!$isOwnerViewer) {
            $ownerExclusionSql = " AND ur.user_id NOT IN (
                SELECT ur2.user_id
                FROM {$this->user_roles_table} ur2
                INNER JOIN {$this->roles_table} r2 ON r2.id = ur2.role_id
                WHERE ur2.status = 1 AND r2.role_slug = '" . $this->db->escape_str($this->owner_role_slug) . "'
            )";
        }

        $sql = "SELECT ur.user_id, ur.role_id, ur.is_primary, ur.status, r.role_name, r.role_slug
                FROM {$this->user_roles_table} ur
                INNER JOIN {$this->roles_table} r ON r.id = ur.role_id
                WHERE ur.status = 1 {$ownerExclusionSql}
                ORDER BY ur.user_id ASC, ur.is_primary DESC, ur.id DESC";
        $rows = $this->db->query($sql)->result_array();

        $map = array();
        foreach ($rows as $row) {
            $uid = (int) $row['user_id'];
            if (!isset($map[$uid])) {
                $map[$uid] = array();
            }
            $map[$uid][] = $row;
        }

        return $map;
    }

    public function get_user($userId = 0, $viewerUserId = 0)
    {
        $userId = (int) $userId;
        if ($userId < 1) {
            return NULL;
        }

        if ((int) $viewerUserId > 0 && !$this->can_manage_target_user((int) $viewerUserId, $userId)) {
            return NULL;
        }

        return $this->db->where('auto_id', $userId)->get($this->users_table)->row_array();
    }

    public function assign_primary_role($userId, $roleId, $assignedBy = NULL)
    {
        $userId = (int) $userId;
        $roleId = (int) $roleId;
        if ($userId < 1 || $roleId < 1) {
            return FALSE;
        }

        if (!empty($assignedBy) && !$this->can_manage_target_user((int) $assignedBy, $userId)) {
            return FALSE;
        }

        $ownerRoleId = $this->get_owner_role_id();
        if ($this->is_owner_admin($userId) && $ownerRoleId > 0 && $roleId !== $ownerRoleId) {
            return FALSE;
        }

        if (!empty($assignedBy) && !$this->is_owner_admin((int) $assignedBy) && $ownerRoleId > 0 && $roleId === $ownerRoleId) {
            return FALSE;
        }

        $this->db->trans_start();

        $this->db->where('user_id', $userId)->update($this->user_roles_table, array(
            'is_primary' => 0,
            'status' => 1
        ));

        $existing = $this->db->where('user_id', $userId)->where('role_id', $roleId)->get($this->user_roles_table)->row_array();

        if (!empty($existing)) {
            $this->db->where('id', (int) $existing['id'])->update($this->user_roles_table, array(
                'is_primary' => 1,
                'status' => 1,
                'assigned_by' => !empty($assignedBy) ? (int) $assignedBy : NULL,
                'assigned_at' => date('Y-m-d H:i:s')
            ));
        } else {
            $this->db->insert($this->user_roles_table, array(
                'user_id' => $userId,
                'role_id' => $roleId,
                'is_primary' => 1,
                'status' => 1,
                'assigned_by' => !empty($assignedBy) ? (int) $assignedBy : NULL,
                'assigned_at' => date('Y-m-d H:i:s')
            ));
        }

        $this->db->trans_complete();
        return $this->db->trans_status() === TRUE;
    }

    public function create_user_with_role($input = array(), $assignedBy = NULL)
    {
        $email = trim((string) ($input['emailaddress'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $usergroup = (int) ($input['usergroup'] ?? 3);
        $userclass = strtoupper(trim((string) ($input['userclass'] ?? '')));
        $employmentNo = trim((string) ($input['employmentno'] ?? ''));
        $roleId = (int) ($input['role_id'] ?? 0);

        if ($email === '' || $password === '' || $roleId < 1) {
            return array('status' => FALSE, 'message' => 'Email, password, and role are required.');
        }

        $ownerRoleId = $this->get_owner_role_id();
        if (!empty($assignedBy) && !$this->is_owner_admin((int) $assignedBy) && $ownerRoleId > 0 && $roleId === $ownerRoleId) {
            return array('status' => FALSE, 'message' => 'Owner Admin role cannot be assigned by this account.');
        }

        $existsEmail = $this->db->where('emailaddress', $email)->count_all_results($this->users_table);
        if ((int) $existsEmail > 0) {
            return array('status' => FALSE, 'message' => 'Email address already exists.');
        }

        if ($employmentNo !== '' && $this->db->field_exists('employmentno', $this->users_table)) {
            $existsEmp = $this->db->where('employmentno', $employmentNo)->count_all_results($this->users_table);
            if ((int) $existsEmp > 0) {
                return array('status' => FALSE, 'message' => 'Employment number already exists.');
            }
        }

        $payload = array(
            'emailaddress' => $email,
            'password' => function_exists('password_hash') ? password_hash($password, PASSWORD_DEFAULT) : md5($password),
            'usergroup' => $usergroup,
            'userclass' => $userclass,
        );

        $this->set_optional_user_field($payload, 'employmentno', $employmentNo);
        $this->set_optional_user_field($payload, 'accstatus', (int) ($input['accstatus'] ?? 1));
        $this->set_optional_user_field($payload, 'acccreator', !empty($assignedBy) ? (int) $assignedBy : NULL);
        $this->set_optional_user_field($payload, 'creator_id', !empty($assignedBy) ? (int) $assignedBy : NULL);
        $this->set_optional_user_field($payload, 'salute', trim((string) ($input['salute'] ?? '')));
        $this->set_optional_user_field($payload, 'presentposition', trim((string) ($input['presentposition'] ?? '')));
        $this->set_optional_user_field($payload, 'rank', trim((string) ($input['rank'] ?? '')));
        $this->set_optional_user_field($payload, 'phonenumber', trim((string) ($input['phonenumber'] ?? '')));
        $this->set_optional_user_field($payload, 'ownerclass', $userclass);
        $this->set_optional_user_field($payload, 'dept', $userclass);

        if ($this->db->field_exists('surname', $this->users_table)) {
            $payload['surname'] = trim((string) ($input['surname'] ?? ''));
        } elseif ($this->db->field_exists('sname', $this->users_table)) {
            $payload['sname'] = trim((string) ($input['surname'] ?? ''));
        }

        if ($this->db->field_exists('othernames', $this->users_table)) {
            $payload['othernames'] = trim((string) ($input['othernames'] ?? ''));
        } elseif ($this->db->field_exists('onames', $this->users_table)) {
            $payload['onames'] = trim((string) ($input['othernames'] ?? ''));
        }

        $this->db->trans_start();
        $this->db->insert($this->users_table, $payload);
        $userId = (int) $this->db->insert_id();

        if ($userId > 0) {
            $this->assign_primary_role($userId, $roleId, $assignedBy);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() !== TRUE || $userId < 1) {
            return array('status' => FALSE, 'message' => 'Unable to create user account.');
        }

        return array('status' => TRUE, 'message' => 'User created successfully.', 'user_id' => $userId);
    }

    public function reset_user_password($userId = 0, $newPassword = '')
    {
        $userId = (int) $userId;
        $newPassword = (string) $newPassword;
        if ($userId < 1 || $newPassword === '') {
            return FALSE;
        }

        $actorUserId = (int) $this->session->userdata('auto_id');
        if (!$this->can_manage_target_user($actorUserId, $userId)) {
            return FALSE;
        }

        return $this->db->where('auto_id', $userId)->update($this->users_table, array(
            'password' => function_exists('password_hash') ? password_hash($newPassword, PASSWORD_DEFAULT) : md5($newPassword)
        ));
    }

    public function update_user_with_role($userId = 0, $input = array(), $assignedBy = NULL)
    {
        $userId = (int) $userId;
        if ($userId < 1) {
            return array('status' => FALSE, 'message' => 'Invalid user selected.');
        }

        if (!$this->can_manage_target_user((int) $assignedBy, $userId)) {
            return array('status' => FALSE, 'message' => 'Owner Admin account cannot be modified by this user.');
        }

        $email = trim((string) ($input['emailaddress'] ?? ''));
        $employmentNo = trim((string) ($input['employmentno'] ?? ''));
        $userclass = strtoupper(trim((string) ($input['userclass'] ?? '')));
        $roleId = (int) ($input['role_id'] ?? 0);

        $ownerRoleId = $this->get_owner_role_id();
        if ($this->is_owner_admin($userId) && $ownerRoleId > 0 && $roleId > 0 && $roleId !== $ownerRoleId) {
            return array('status' => FALSE, 'message' => 'Owner Admin role cannot be changed.');
        }

        if ($email === '') {
            return array('status' => FALSE, 'message' => 'Email is required.');
        }

        $duplicateEmail = $this->db->where('emailaddress', $email)
            ->where('auto_id <>', $userId)
            ->count_all_results($this->users_table);
        if ((int) $duplicateEmail > 0) {
            return array('status' => FALSE, 'message' => 'Email address already exists.');
        }

        if ($employmentNo !== '' && $this->db->field_exists('employmentno', $this->users_table)) {
            $duplicateEmp = $this->db->where('employmentno', $employmentNo)
                ->where('auto_id <>', $userId)
                ->count_all_results($this->users_table);
            if ((int) $duplicateEmp > 0) {
                return array('status' => FALSE, 'message' => 'Employment number already exists.');
            }
        }

        $payload = array(
            'emailaddress' => $email,
            'usergroup' => (int) ($input['usergroup'] ?? 3),
            'userclass' => $userclass,
        );

        $this->set_optional_user_field($payload, 'employmentno', $employmentNo);
        $this->set_optional_user_field($payload, 'accstatus', (int) ($input['accstatus'] ?? 1));
        $this->set_optional_user_field($payload, 'salute', trim((string) ($input['salute'] ?? '')));
        $this->set_optional_user_field($payload, 'presentposition', trim((string) ($input['presentposition'] ?? '')));
        $this->set_optional_user_field($payload, 'rank', trim((string) ($input['rank'] ?? '')));
        $this->set_optional_user_field($payload, 'phonenumber', trim((string) ($input['phonenumber'] ?? '')));
        $this->set_optional_user_field($payload, 'ownerclass', $userclass);
        $this->set_optional_user_field($payload, 'dept', $userclass);

        if ($this->db->field_exists('surname', $this->users_table)) {
            $payload['surname'] = trim((string) ($input['surname'] ?? ''));
        } elseif ($this->db->field_exists('sname', $this->users_table)) {
            $payload['sname'] = trim((string) ($input['surname'] ?? ''));
        }

        if ($this->db->field_exists('othernames', $this->users_table)) {
            $payload['othernames'] = trim((string) ($input['othernames'] ?? ''));
        } elseif ($this->db->field_exists('onames', $this->users_table)) {
            $payload['onames'] = trim((string) ($input['othernames'] ?? ''));
        }

        $this->db->trans_start();
        $this->db->where('auto_id', $userId)->update($this->users_table, $payload);
        if ($roleId > 0) {
            $this->assign_primary_role($userId, $roleId, $assignedBy);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() !== TRUE) {
            return array('status' => FALSE, 'message' => 'Unable to update user account.');
        }

        return array('status' => TRUE, 'message' => 'User updated successfully.');
    }

    public function set_user_status($userId = 0, $status = 1)
    {
        $userId = (int) $userId;
        $status = ((int) $status === 1) ? 1 : 0;
        if ($userId < 1) {
            return FALSE;
        }

        $actorUserId = (int) $this->session->userdata('auto_id');
        if (!$this->can_manage_target_user($actorUserId, $userId)) {
            return FALSE;
        }

        if ($this->is_owner_admin($userId)) {
            return FALSE;
        }

        $payload = array();
        if ($this->db->field_exists('accstatus', $this->users_table)) {
            $payload['accstatus'] = $status;
        } elseif ($this->db->field_exists('status', $this->users_table)) {
            $payload['status'] = $status;
        } else {
            return FALSE;
        }

        return $this->db->where('auto_id', $userId)->update($this->users_table, $payload);
    }

    public function delete_user_with_roles($userId = 0)
    {
        $userId = (int) $userId;
        if ($userId < 1) {
            return FALSE;
        }

        $actorUserId = (int) $this->session->userdata('auto_id');
        if (!$this->can_manage_target_user($actorUserId, $userId)) {
            return FALSE;
        }

        if ($this->is_owner_admin($userId)) {
            return FALSE;
        }

        $this->db->trans_start();
        $this->db->where('user_id', $userId)->delete($this->user_roles_table);
        $this->db->where('auto_id', $userId)->delete($this->users_table);
        $this->db->trans_complete();

        return $this->db->trans_status() === TRUE;
    }

    public function get_role_permission_ids($roleId)
    {
        $rows = $this->db->select('permission_id')
            ->from($this->role_permissions_table)
            ->where('role_id', (int) $roleId)
            ->where('allow_access', 1)
            ->get()->result_array();

        return array_map('intval', array_column($rows, 'permission_id'));
    }

    public function save_role_permissions($roleId, $permissionIds = array())
    {
        $roleId = (int) $roleId;
        if ($roleId < 1) {
            return FALSE;
        }

        $actorUserId = (int) $this->session->userdata('auto_id');
        $ownerRoleId = $this->get_owner_role_id();
        if ($ownerRoleId > 0 && $roleId === $ownerRoleId && !$this->is_owner_admin($actorUserId)) {
            return FALSE;
        }

        $permissionIds = array_values(array_unique(array_filter(array_map('intval', (array) $permissionIds), function ($v) {
            return $v > 0;
        })));

        $this->db->trans_start();
        $this->db->where('role_id', $roleId)->delete($this->role_permissions_table);

        foreach ($permissionIds as $pid) {
            $this->db->insert($this->role_permissions_table, array(
                'role_id' => $roleId,
                'permission_id' => $pid,
                'allow_access' => 1
            ));
        }

        $this->db->trans_complete();
        return $this->db->trans_status() === TRUE;
    }

    public function get_user_permission_codes($userId, $legacyUsergroup = NULL)
    {
        $userId = (int) $userId;
        if ($userId < 1) {
            return array();
        }

        $sql = "SELECT DISTINCT p.permission_code
                FROM {$this->user_roles_table} ur
                INNER JOIN {$this->role_permissions_table} rp ON rp.role_id = ur.role_id AND rp.allow_access = 1
                INNER JOIN {$this->permissions_table} p ON p.id = rp.permission_id AND p.status = 1
                WHERE ur.user_id = ? AND ur.status = 1";
        $rows = $this->db->query($sql, array($userId))->result_array();
        $codes = array_column($rows, 'permission_code');

        if (!empty($codes)) {
            return array_values(array_unique($codes));
        }

        if ((int) $legacyUsergroup === 1) {
            return array('*');
        }

        if ((int) $legacyUsergroup === 2) {
            $managerCodes = array(
                'reservation.dashboard.view', 'reservation.frontdesk.view', 'reservation.manage',
                'reservation.checkin', 'reservation.checkout', 'reservation.reports.view',
                'pos.sale.view', 'pos.sale.record', 'pos.sale.void', 'pos.sale.refund',
                'pos.stock.view', 'pos.stock.manage', 'pos.shift.open', 'pos.shift.close',
                'pos.shift.report', 'pos.reports.view', 'pos.audit.view', 'finance.reports.view'
            );
            return $managerCodes;
        }

        return array();
    }
}
