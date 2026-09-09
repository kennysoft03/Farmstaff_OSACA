<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties]

class Farmstaff_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // =========================================================
    // STATS (Homepage / Admin Dashboard)
    // =========================================================

    public function get_platform_stats()
    {
        return [
            'total_employers' => $this->db->count_all('fs_employers'),
            'active_employers'=> $this->db->where('status','active')->count_all_results('fs_employers'),
            'total_workers'   => $this->db->count_all('fs_workers'),
            'active_workers'  => $this->db->where('status','active')->count_all_results('fs_workers'),
            'verified_profiles'=> $this->db->where('status','active')->count_all_results('fs_workers'),
            'total_incidents' => $this->db->count_all('fs_incidents'),
            'pending_incidents'=> $this->db->where('status','pending')->count_all_results('fs_incidents'),
            'pending_ratings' => $this->db->where('status','pending')->count_all_results('fs_farm_ratings'),
            'avg_farm_rating' => $this->_avg_farm_rating(),
        ];
    }

    private function _avg_farm_rating()
    {
        $row = $this->db->select_avg('overall_rating','avg_rating')
                        ->where('status','approved')
                        ->get('fs_farm_ratings')->row();
        return $row ? round((float)$row->avg_rating, 1) : 0;
    }

    // =========================================================
    // ADMIN AUTH
    // =========================================================

    public function get_admin_by_username($username)
    {
        return $this->db->where('username', $username)
                        ->where('status', 1)
                        ->get('fs_admin_users')->row_array();
    }

    public function get_admin_by_id($id)
    {
        return $this->db->where('id', (int)$id)
                        ->get('fs_admin_users')->row_array();
    }

    public function update_admin_last_login($id)
    {
        $this->db->where('id', (int)$id)
                 ->update('fs_admin_users', ['last_login' => date('Y-m-d H:i:s')]);
    }

    public function create_admin($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->db->insert('fs_admin_users', $data);
        return $this->db->insert_id();
    }

    public function get_all_admins()
    {
        return $this->db->order_by('created_at','DESC')->get('fs_admin_users')->result_array();
    }

    // =========================================================
    // EMPLOYER AUTH & MANAGEMENT
    // =========================================================

    public function get_employer_by_email($email)
    {
        return $this->db->where('email', $email)->get('fs_employers')->row_array();
    }

    public function get_employer_by_phone($phone)
    {
        return $this->db->where('phone', $phone)->get('fs_employers')->row_array();
    }

    public function get_employer_by_id($id)
    {
        return $this->db->where('id', (int)$id)->get('fs_employers')->row_array();
    }

    public function get_employer_by_token($token)
    {
        return $this->db->where('verification_token', $token)->get('fs_employers')->row_array();
    }

    public function register_employer($data)
    {
        $data['password']           = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['verification_token'] = bin2hex(random_bytes(32));
        $data['status']             = 'active'; // auto-activate for now
        $data['email_verified']     = 1;
        $this->db->insert('fs_employers', $data);
        return $this->db->insert_id();
    }

    public function update_employer($id, $data)
    {
        $this->db->where('id', (int)$id)->update('fs_employers', $data);
        return $this->db->affected_rows() > 0;
    }

    public function verify_employer_email($token)
    {
        $employer = $this->get_employer_by_token($token);
        if (!$employer) return false;
        $this->db->where('id', $employer['id'])
                 ->update('fs_employers', ['email_verified' => 1, 'status' => 'active', 'verification_token' => null]);
        return true;
    }

    public function get_all_employers($filters = [], $limit = 50, $offset = 0)
    {
        if (!empty($filters['status']))  $this->db->where('status', $filters['status']);
        if (!empty($filters['search'])) {
            $s = $this->db->escape_like_str($filters['search']);
            $this->db->group_start()
                     ->like('farm_name', $s)
                     ->or_like('contact_person', $s)
                     ->or_like('email', $s)
                     ->or_like('phone', $s)
                     ->group_end();
        }
        $this->db->order_by('created_at','DESC');
        if ($limit > 0) $this->db->limit($limit, $offset);
        return $this->db->get('fs_employers')->result_array();
    }

    public function count_employers($filters = [])
    {
        if (!empty($filters['status']))  $this->db->where('status', $filters['status']);
        if (!empty($filters['search'])) {
            $s = $this->db->escape_like_str($filters['search']);
            $this->db->group_start()
                     ->like('farm_name', $s)
                     ->or_like('contact_person', $s)
                     ->or_like('email', $s)
                     ->group_end();
        }
        return $this->db->count_all_results('fs_employers');
    }

    public function update_employer_reputation($employer_id)
    {
        $row = $this->db->select_avg('overall_rating','avg_rating')
                        ->select('COUNT(*) as total', false)
                        ->where('employer_id', (int)$employer_id)
                        ->where('status', 'approved')
                        ->get('fs_farm_ratings')->row_array();
        if ($row) {
            $this->db->where('id', (int)$employer_id)
                     ->update('fs_employers', [
                         'reputation_score' => round((float)($row['avg_rating'] ?? 0), 2),
                         'total_ratings'    => (int)($row['total'] ?? 0),
                     ]);
        }
    }

    // =========================================================
    // WORKERS
    // =========================================================

    private function _generate_worker_id()
    {
        do {
            $id = WORKER_ID_PREFIX . '-' . strtoupper(substr(uniqid(), -6)) . rand(10, 99);
        } while ($this->db->where('worker_id', $id)->count_all_results('fs_workers') > 0);
        return $id;
    }

    public function register_worker($data)
    {
        $data['worker_id']  = $this->_generate_worker_id();
        $data['trust_score'] = TRUST_SCORE_START;
        $this->db->insert('fs_workers', $data);
        return $this->db->insert_id();
    }

    public function update_worker($id, $data)
    {
        $this->db->where('id', (int)$id)->update('fs_workers', $data);
        return $this->db->affected_rows() > 0;
    }

    public function get_worker_by_id($id)
    {
        return $this->db->where('id', (int)$id)->get('fs_workers')->row_array();
    }

    public function get_worker_by_worker_id($worker_id)
    {
        return $this->db->where('worker_id', $worker_id)->get('fs_workers')->row_array();
    }

    public function get_worker_by_phone($phone)
    {
        return $this->db->where('phone', $phone)->get('fs_workers')->row_array();
    }

    public function get_workers_by_employer($employer_id, $filters = [], $limit = 50, $offset = 0)
    {
        $this->db->where('w.registered_by', (int)$employer_id);
        if (!empty($filters['status'])) $this->db->where('w.status', $filters['status']);
        if (!empty($filters['search'])) {
            $s = $this->db->escape_like_str($filters['search']);
            $this->db->group_start()
                     ->like('w.firstname', $s)
                     ->or_like('w.lastname', $s)
                     ->or_like('w.phone', $s)
                     ->or_like('w.worker_id', $s)
                     ->group_end();
        }
        $this->db->select('w.*, wh.role as current_role, wh.start_date as hire_date')
                 ->from('fs_workers w')
                 ->join('fs_work_history wh', 'wh.worker_id = w.id AND wh.is_current = 1', 'left')
                 ->order_by('w.created_at', 'DESC');
        if ($limit > 0) $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

    public function count_workers_by_employer($employer_id, $filters = [])
    {
        $this->db->where('registered_by', (int)$employer_id);
        if (!empty($filters['status'])) $this->db->where('status', $filters['status']);
        if (!empty($filters['search'])) {
            $s = $this->db->escape_like_str($filters['search']);
            $this->db->group_start()
                     ->like('firstname', $s)
                     ->or_like('lastname', $s)
                     ->or_like('phone', $s)
                     ->group_end();
        }
        return $this->db->count_all_results('fs_workers');
    }

    public function get_all_workers($filters = [], $limit = 50, $offset = 0)
    {
        $this->db->select('w.*, e.farm_name as employer_name')
                 ->from('fs_workers w')
                 ->join('fs_employers e', 'e.id = w.registered_by', 'left');
        if (!empty($filters['status'])) $this->db->where('w.status', $filters['status']);
        if (!empty($filters['search'])) {
            $s = $this->db->escape_like_str($filters['search']);
            $this->db->group_start()
                     ->like('w.firstname', $s)
                     ->or_like('w.lastname', $s)
                     ->or_like('w.phone', $s)
                     ->or_like('w.worker_id', $s)
                     ->group_end();
        }
        $this->db->order_by('w.created_at','DESC');
        if ($limit > 0) $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

    public function count_all_workers($filters = [])
    {
        $this->db->from('fs_workers w');
        if (!empty($filters['status'])) $this->db->where('w.status', $filters['status']);
        if (!empty($filters['search'])) {
            $s = $this->db->escape_like_str($filters['search']);
            $this->db->group_start()
                     ->like('w.firstname', $s)
                     ->or_like('w.lastname', $s)
                     ->or_like('w.phone', $s)
                     ->group_end();
        }
        return $this->db->count_all_results();
    }

    // Background check search (employer searching before hiring)
    public function search_worker_for_check($query, $type = 'phone')
    {
        if ($type === 'phone') {
            return $this->db->where('phone', $query)->get('fs_workers')->row_array();
        } elseif ($type === 'worker_id') {
            return $this->db->where('worker_id', $query)->get('fs_workers')->row_array();
        }
        $s = $this->db->escape_like_str($query);
        return $this->db->group_start()
                        ->like('firstname', $s)
                        ->or_like('lastname', $s)
                        ->group_end()
                        ->get('fs_workers')->row_array();
    }

    public function log_background_check($data)
    {
        $this->db->insert('fs_background_checks', $data);
        return $this->db->insert_id();
    }

    // =========================================================
    // WORK HISTORY
    // =========================================================

    public function add_work_history($data)
    {
        // Close any existing current employment at same employer
        if (!empty($data['is_current']) && $data['is_current'] == 1) {
            $this->db->where('worker_id', (int)$data['worker_id'])
                     ->where('employer_id', (int)$data['employer_id'])
                     ->where('is_current', 1)
                     ->update('fs_work_history', ['is_current' => 0]);
        }
        $this->db->insert('fs_work_history', $data);
        return $this->db->insert_id();
    }

    public function update_work_history($id, $data)
    {
        // Calculate duration if end date provided
        if (!empty($data['end_date']) && !empty($data['start_date'])) {
            $start = strtotime($data['start_date']);
            $end   = strtotime($data['end_date']);
            $data['duration_days'] = (int)(($end - $start) / 86400);
            $data['is_current']    = 0;
            $data['status']        = 'completed';
        }
        $this->db->where('id', (int)$id)->update('fs_work_history', $data);
        return $this->db->affected_rows() > 0;
    }

    public function get_work_history_by_worker($worker_id)
    {
        return $this->db->select('wh.*, e.farm_name, e.lga, e.state')
                        ->from('fs_work_history wh')
                        ->join('fs_employers e', 'e.id = wh.employer_id', 'left')
                        ->where('wh.worker_id', (int)$worker_id)
                        ->order_by('wh.start_date', 'DESC')
                        ->get()->result_array();
    }

    public function get_work_history_by_employer($employer_id, $worker_id = null)
    {
        $this->db->select('wh.*, CONCAT(w.firstname," ",w.lastname) as worker_name, w.worker_id as reg_id, w.photo')
                 ->from('fs_work_history wh')
                 ->join('fs_workers w', 'w.id = wh.worker_id', 'left')
                 ->where('wh.employer_id', (int)$employer_id);
        if ($worker_id) $this->db->where('wh.worker_id', (int)$worker_id);
        return $this->db->order_by('wh.start_date','DESC')->get()->result_array();
    }

    public function get_work_history_entry($id)
    {
        return $this->db->where('id', (int)$id)->get('fs_work_history')->row_array();
    }

    // =========================================================
    // SKILLS
    // =========================================================

    public function add_skill($data)
    {
        $this->db->insert('fs_skills', $data);
        return $this->db->insert_id();
    }

    public function update_skill($id, $data)
    {
        $this->db->where('id', (int)$id)->update('fs_skills', $data);
        return $this->db->affected_rows() > 0;
    }

    public function delete_skill($id, $employer_id)
    {
        $this->db->where('id', (int)$id)->where('employer_id', (int)$employer_id)->delete('fs_skills');
        return $this->db->affected_rows() > 0;
    }

    public function get_skills_by_worker($worker_id, $employer_id = null)
    {
        $this->db->select('s.*, e.farm_name as verified_by_farm, a.surname as admin_name')
                 ->from('fs_skills s')
                 ->join('fs_employers e', 'e.id = s.employer_id', 'left')
                 ->join('fs_admin_users a', 'a.id = s.verified_by', 'left')
                 ->where('s.worker_id', (int)$worker_id);
        if ($employer_id) $this->db->where('s.employer_id', (int)$employer_id);
        return $this->db->order_by('s.verified','DESC')->order_by('s.rating','DESC')->get()->result_array();
    }

    // =========================================================
    // ATTENDANCE
    // =========================================================

    public function log_attendance($data)
    {
        // Upsert – update if same worker+employer+date exists
        $existing = $this->db->where('worker_id', (int)$data['worker_id'])
                             ->where('employer_id', (int)$data['employer_id'])
                             ->where('attendance_date', $data['attendance_date'])
                             ->get('fs_attendance')->row_array();
        if ($existing) {
            $this->db->where('id', $existing['id'])->update('fs_attendance', $data);
            return $existing['id'];
        }
        $this->db->insert('fs_attendance', $data);
        return $this->db->insert_id();
    }

    public function get_attendance_by_worker($worker_id, $employer_id = null, $month = null, $year = null)
    {
        $this->db->where('worker_id', (int)$worker_id);
        if ($employer_id) $this->db->where('employer_id', (int)$employer_id);
        if ($month && $year) {
            $this->db->where('MONTH(attendance_date)', (int)$month, false)
                     ->where('YEAR(attendance_date)', (int)$year, false);
        }
        return $this->db->order_by('attendance_date','DESC')->get('fs_attendance')->result_array();
    }

    public function get_attendance_summary($worker_id, $employer_id)
    {
        $rows = $this->db->where('worker_id', (int)$worker_id)
                         ->where('employer_id', (int)$employer_id)
                         ->get('fs_attendance')->result_array();
        $total   = count($rows);
        $present = count(array_filter($rows, fn($r) => $r['status'] === 'present'));
        $late    = count(array_filter($rows, fn($r) => $r['status'] === 'late'));
        $absent  = count(array_filter($rows, fn($r) => $r['status'] === 'absent'));
        $score   = $total > 0 ? round((($present + ($late * 0.5)) / $total) * 100) : 0;
        return compact('total', 'present', 'late', 'absent', 'score');
    }

    public function bulk_attendance($records)
    {
        foreach ($records as $record) {
            $this->log_attendance($record);
        }
        return true;
    }

    // =========================================================
    // INCIDENTS
    // =========================================================

    public function report_incident($data)
    {
        $this->db->insert('fs_incidents', $data);
        $id = $this->db->insert_id();
        $this->_notify_admin('New incident reported', 'A new incident has been submitted for review.', 'admin/incident/' . $id . '/review', 'warning');
        return $id;
    }

    public function get_incident($id)
    {
        return $this->db->select('i.*, CONCAT(w.firstname," ",w.lastname) as worker_name, w.worker_id as reg_id, e.farm_name, a.surname as reviewed_by_name')
                        ->from('fs_incidents i')
                        ->join('fs_workers w', 'w.id = i.worker_id', 'left')
                        ->join('fs_employers e', 'e.id = i.employer_id', 'left')
                        ->join('fs_admin_users a', 'a.id = i.reviewed_by', 'left')
                        ->where('i.id', (int)$id)
                        ->get()->row_array();
    }

    public function get_incidents($filters = [], $limit = 50, $offset = 0)
    {
        $this->db->select('i.*, CONCAT(w.firstname," ",w.lastname) as worker_name, w.worker_id as reg_id, e.farm_name')
                 ->from('fs_incidents i')
                 ->join('fs_workers w', 'w.id = i.worker_id', 'left')
                 ->join('fs_employers e', 'e.id = i.employer_id', 'left');
        if (!empty($filters['status']))      $this->db->where('i.status', $filters['status']);
        if (!empty($filters['employer_id'])) $this->db->where('i.employer_id', (int)$filters['employer_id']);
        if (!empty($filters['worker_id']))   $this->db->where('i.worker_id', (int)$filters['worker_id']);
        $this->db->order_by('i.created_at','DESC');
        if ($limit > 0) $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

    public function count_incidents($filters = [])
    {
        $this->db->from('fs_incidents i');
        if (!empty($filters['status']))      $this->db->where('i.status', $filters['status']);
        if (!empty($filters['employer_id'])) $this->db->where('i.employer_id', (int)$filters['employer_id']);
        return $this->db->count_all_results();
    }

    public function review_incident($id, $data, $admin_id)
    {
        $incident = $this->get_incident($id);
        if (!$incident) return false;
        $data['reviewed_by'] = (int)$admin_id;
        $data['reviewed_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', (int)$id)->update('fs_incidents', $data);
        // If accepted, deduct trust score
        if ($data['status'] === 'accepted' && !empty($data['disciplinary_points'])) {
            $pts = (int)$data['disciplinary_points'];
            $this->adjust_trust_score($incident['worker_id'], -$pts, 'Incident accepted: ' . $incident['incident_type'], $id, 'incident', $admin_id);
        }
        return true;
    }

    // =========================================================
    // FARM RATINGS
    // =========================================================

    public function submit_farm_rating($data)
    {
        $this->db->insert('fs_farm_ratings', $data);
        $id = $this->db->insert_id();
        $this->_notify_admin('New farm rating submitted', 'A farm rating is pending your review.', 'admin/farm-rating/' . $id . '/review', 'info');
        return $id;
    }

    public function get_farm_ratings($filters = [], $limit = 50, $offset = 0)
    {
        $this->db->select('r.*, e.farm_name, CONCAT(w.firstname," ",w.lastname) as worker_name')
                 ->from('fs_farm_ratings r')
                 ->join('fs_employers e', 'e.id = r.employer_id', 'left')
                 ->join('fs_workers w', 'w.id = r.worker_id', 'left');
        if (!empty($filters['status']))      $this->db->where('r.status', $filters['status']);
        if (!empty($filters['employer_id'])) $this->db->where('r.employer_id', (int)$filters['employer_id']);
        $this->db->order_by('r.created_at','DESC');
        if ($limit > 0) $this->db->limit($limit, $offset);
        return $this->db->get()->result_array();
    }

    public function count_farm_ratings($filters = [])
    {
        $this->db->from('fs_farm_ratings r');
        if (!empty($filters['status']))      $this->db->where('r.status', $filters['status']);
        if (!empty($filters['employer_id'])) $this->db->where('r.employer_id', (int)$filters['employer_id']);
        return $this->db->count_all_results();
    }

    public function review_farm_rating($id, $status, $admin_notes, $admin_id)
    {
        $this->db->where('id', (int)$id)->update('fs_farm_ratings', [
            'status'      => $status,
            'admin_notes' => $admin_notes,
            'reviewed_by' => (int)$admin_id,
        ]);
        if ($status === 'approved') {
            $r = $this->db->where('id', (int)$id)->get('fs_farm_ratings')->row_array();
            if ($r) $this->update_employer_reputation($r['employer_id']);
        }
        return $this->db->affected_rows() > 0;
    }

    // =========================================================
    // WORKER RATINGS (by employer)
    // =========================================================

    public function rate_worker($data)
    {
        $this->db->insert('fs_worker_ratings', $data);
        $id = $this->db->insert_id();
        // Immediately adjust trust score
        $adj = ((int)$data['overall_rating'] - 3) * 5; // +10, +5, 0, -5, -10
        if ($adj != 0) {
            $this->adjust_trust_score($data['worker_id'], $adj, 'Worker performance rating', $id, 'rating', $data['employer_id']);
        }
        return $id;
    }

    public function get_worker_ratings($worker_id)
    {
        return $this->db->select('r.*, e.farm_name')
                        ->from('fs_worker_ratings r')
                        ->join('fs_employers e', 'e.id = r.employer_id', 'left')
                        ->where('r.worker_id', (int)$worker_id)
                        ->where('r.status', 'approved')
                        ->order_by('r.created_at','DESC')
                        ->get()->result_array();
    }

    // =========================================================
    // TRUST SCORE
    // =========================================================

    public function adjust_trust_score($worker_id, $change, $reason, $ref_id = null, $ref_type = 'manual', $created_by = null)
    {
        $worker = $this->get_worker_by_id($worker_id);
        if (!$worker) return false;
        $old   = (float)$worker['trust_score'];
        $new   = max(0, min(TRUST_SCORE_MAX, $old + $change));
        $this->db->where('id', (int)$worker_id)->update('fs_workers', ['trust_score' => $new]);
        $this->db->insert('fs_trust_score_log', [
            'worker_id'     => (int)$worker_id,
            'old_score'     => $old,
            'new_score'     => $new,
            'change_amount' => $change,
            'change_reason' => $reason,
            'reference_id'  => $ref_id,
            'reference_type'=> $ref_type,
            'created_by'    => $created_by,
        ]);
        return $new;
    }

    public function get_trust_score_log($worker_id)
    {
        return $this->db->where('worker_id', (int)$worker_id)
                        ->order_by('created_at','DESC')
                        ->get('fs_trust_score_log')->result_array();
    }

    // =========================================================
    // REPORTS & ANALYTICS
    // =========================================================

    public function get_workers_by_month($months = 12)
    {
        $start = date('Y-m-d', strtotime("-{$months} months"));
        return $this->db->select("DATE_FORMAT(created_at,'%Y-%m') as month, COUNT(*) as total", false)
                        ->where('created_at >=', $start)
                        ->group_by("DATE_FORMAT(created_at,'%Y-%m')")
                        ->order_by('month','ASC')
                        ->get('fs_workers')->result_array();
    }

    public function get_employers_by_month($months = 12)
    {
        $start = date('Y-m-d', strtotime("-{$months} months"));
        return $this->db->select("DATE_FORMAT(created_at,'%Y-%m') as month, COUNT(*) as total", false)
                        ->where('created_at >=', $start)
                        ->group_by("DATE_FORMAT(created_at,'%Y-%m')")
                        ->order_by('month','ASC')
                        ->get('fs_employers')->result_array();
    }

    public function get_top_rated_workers($limit = 10)
    {
        return $this->db->select('w.id, w.worker_id, w.firstname, w.lastname, w.photo, w.trust_score, COUNT(r.id) as rating_count, AVG(r.overall_rating) as avg_rating')
                        ->from('fs_workers w')
                        ->join('fs_worker_ratings r', 'r.worker_id = w.id AND r.status = "approved"', 'left')
                        ->where('w.status', 'active')
                        ->group_by('w.id')
                        ->order_by('w.trust_score','DESC')
                        ->limit($limit)
                        ->get()->result_array();
    }

    public function get_incident_stats()
    {
        return $this->db->select('incident_type, COUNT(*) as total, severity')
                        ->group_by('incident_type, severity')
                        ->order_by('total','DESC')
                        ->get('fs_incidents')->result_array();
    }

    public function get_skill_distribution()
    {
        return $this->db->select('skill_name, COUNT(*) as total')
                        ->group_by('skill_name')
                        ->order_by('total','DESC')
                        ->limit(20)
                        ->get('fs_skills')->result_array();
    }

    // =========================================================
    // AUDIT LOGS
    // =========================================================

    public function log_action($actor_type, $actor_id, $actor_name, $action, $module, $description = '', $target_id = null, $target_type = null)
    {
        $this->db->insert('fs_audit_logs', [
            'actor_type'  => $actor_type,
            'actor_id'    => (int)$actor_id,
            'actor_name'  => $actor_name,
            'action'      => $action,
            'module'      => $module,
            'target_id'   => $target_id,
            'target_type' => $target_type,
            'description' => $description,
            'ip_address'  => $this->input->ip_address(),
            'user_agent'  => substr($this->input->user_agent(), 0, 500),
        ]);
        return $this->db->insert_id();
    }

    public function get_audit_logs($filters = [], $limit = 100, $offset = 0)
    {
        if (!empty($filters['module']))     $this->db->where('module', $filters['module']);
        if (!empty($filters['actor_type'])) $this->db->where('actor_type', $filters['actor_type']);
        if (!empty($filters['actor_id']))   $this->db->where('actor_id', (int)$filters['actor_id']);
        $this->db->order_by('created_at','DESC')->limit($limit, $offset);
        return $this->db->get('fs_audit_logs')->result_array();
    }

    // =========================================================
    // NOTIFICATIONS
    // =========================================================

    private function _notify_admin($title, $message, $link = null, $type = 'info')
    {
        // Notify all active admins
        $admins = $this->db->where('status', 1)->get('fs_admin_users')->result_array();
        foreach ($admins as $admin) {
            $this->db->insert('fs_notifications', [
                'recipient_type' => 'admin',
                'recipient_id'   => $admin['id'],
                'title'          => $title,
                'message'        => $message,
                'type'           => $type,
                'link'           => $link,
            ]);
        }
    }

    public function get_notifications($recipient_type, $recipient_id, $unread_only = false)
    {
        $this->db->where('recipient_type', $recipient_type)
                 ->where('recipient_id', (int)$recipient_id);
        if ($unread_only) $this->db->where('is_read', 0);
        return $this->db->order_by('created_at','DESC')->limit(30)->get('fs_notifications')->result_array();
    }

    public function mark_notifications_read($recipient_type, $recipient_id)
    {
        $this->db->where('recipient_type', $recipient_type)
                 ->where('recipient_id', (int)$recipient_id)
                 ->update('fs_notifications', ['is_read' => 1]);
    }

    public function count_unread_notifications($recipient_type, $recipient_id)
    {
        return $this->db->where('recipient_type', $recipient_type)
                        ->where('recipient_id', (int)$recipient_id)
                        ->where('is_read', 0)
                        ->count_all_results('fs_notifications');
    }

    // =========================================================
    // WORKER FULL PROFILE (for background check)
    // =========================================================

    public function get_worker_full_profile($worker_id)
    {
        $worker = $this->get_worker_by_id($worker_id);
        if (!$worker) return null;
        $worker['work_history'] = $this->get_work_history_by_worker($worker_id);
        $worker['skills']       = $this->get_skills_by_worker($worker_id);
        $worker['ratings']      = $this->get_worker_ratings($worker_id);
        $worker['incidents']    = $this->get_incidents(['worker_id' => $worker_id, 'status' => 'accepted']);
        $worker['att_summary']  = [];
        return $worker;
    }
}
