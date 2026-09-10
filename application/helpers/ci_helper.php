<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Farmstaff CI Helper
 * Common utility functions available globally
 */

if (!function_exists('fs_truncate')) {
    function fs_truncate($text, $length = 100, $suffix = '...') {
        $text = strip_tags((string)$text);
        return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . $suffix : $text;
    }
}

if (!function_exists('fs_stars')) {
    function fs_stars($rating, $max = 5) {
        $r   = (int)round((float)$rating);
        $out = '';
        for ($i = 1; $i <= $max; $i++) {
            $out .= $i <= $r ? '★' : '☆';
        }
        return $out;
    }
}

if (!function_exists('fs_trust_label')) {
    function fs_trust_label($score) {
        $score = (float)$score;
        if ($score >= 70) return ['label' => 'High Trust',   'color' => '#1a5c2a', 'class' => 'high'];
        if ($score >= 40) return ['label' => 'Medium Trust', 'color' => '#d4a017', 'class' => 'medium'];
        return                   ['label' => 'Low Trust',    'color' => '#dc3545', 'class' => 'low'];
    }
}

if (!function_exists('fs_status_badge')) {
    function fs_status_badge($status) {
        $map = [
            'active'    => 'badge-success',
            'pending'   => 'badge-warning',
            'suspended' => 'badge-danger',
            'inactive'  => 'badge-secondary',
            'flagged'   => 'badge-warning',
            'approved'  => 'badge-success',
            'rejected'  => 'badge-danger',
            'accepted'  => 'badge-danger',
            'completed' => 'badge-secondary',
        ];
        $cls = $map[$status] ?? 'badge-secondary';
        return '<span class="badge ' . $cls . '">' . htmlspecialchars(ucfirst($status)) . '</span>';
    }
}

if (!function_exists('fs_time_ago')) {
    function fs_time_ago($datetime) {
        $time = time() - strtotime((string)$datetime);
        if ($time < 60)     return 'just now';
        if ($time < 3600)   return floor($time/60) . ' min ago';
        if ($time < 86400)  return floor($time/3600) . ' hr ago';
        if ($time < 604800) return floor($time/86400) . ' days ago';
        return date('d M Y', strtotime((string)$datetime));
    }
}

if (!function_exists('fs_duration')) {
    function fs_duration($days) {
        $days = (int)$days;
        if ($days < 30)   return $days . ' day' . ($days != 1 ? 's' : '');
        if ($days < 365)  return floor($days/30) . ' month' . (floor($days/30) != 1 ? 's' : '');
        $y = floor($days/365); $m = floor(($days%365)/30);
        return $y . ' yr' . ($y!=1?'s':'') . ($m > 0 ? ', ' . $m . ' mo' : '');
    }
}

if (!function_exists('fs_worker_photo')) {
    function fs_worker_photo($photo, $size = 40) {
        $ci  = &get_instance();
        $url = !empty($photo) ? base_url($photo) : '';
        if ($url) {
            return '<img src="' . $url . '" alt="Worker" style="width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;object-fit:cover;">';
        }
        return '<div style="width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;background:var(--green-pale);display:inline-flex;align-items:center;justify-content:center;font-size:' . round($size*0.45) . 'px;">👤</div>';
    }
}

if (!function_exists('fs_paginate')) {
    function fs_paginate($total, $limit, $current_page, $base_url, $extra_qs = '') {
        if ($total <= $limit) return '';
        $pages  = ceil($total / $limit);
        $html   = '<div class="pagination">';
        for ($p = 1; $p <= $pages; $p++) {
            $url   = $base_url . $p . $extra_qs;
            $active= $p == $current_page ? ' active' : '';
            $html .= '<a href="' . $url . '" class="page-btn' . $active . '">' . $p . '</a>';
        }
        $html .= '</div>';
        return $html;
    }
}
