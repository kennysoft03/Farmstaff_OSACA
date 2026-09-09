<?php
$page_title = isset($page_title) ? $page_title : 'Reservation Dashboard';
$page_note = isset($page_note) ? $page_note : 'Unified chalet operations';
$page_mode = isset($page_mode) ? $page_mode : 'dashboard';
$summary = isset($summary) && is_array($summary) ? $summary : array();
$status_board = isset($status_board) && is_array($status_board) ? $status_board : array();
$recent_reservations = isset($recent_reservations) && is_array($recent_reservations) ? $recent_reservations : array();
$frontdesk = isset($frontdesk) && is_array($frontdesk) ? $frontdesk : array();
$walkin_options = isset($walkin_options) && is_array($walkin_options) ? $walkin_options : array();
$payment_methods = isset($payment_methods) && is_array($payment_methods) ? $payment_methods : array();
$reports = isset($reports) && is_array($reports) ? $reports : array();
$reports = array_merge(array(
    'filters' => array(
        'from' => date('Y-m-d', strtotime('-29 days')),
        'to' => date('Y-m-d'),
        'days' => 30,
    ),
    'overview' => array(),
    'payment_methods' => array(),
    'status_mix' => array(),
    'daily_revenue' => array(),
    'top_units' => array(),
    'payment_audit' => array(),
), $reports);
$reports['overview'] = array_merge(array(
    'reservations' => 0,
    'paid_reservations' => 0,
    'unpaid_reservations' => 0,
    'checked_in' => 0,
    'gross_revenue' => 0,
    'paid_revenue' => 0,
    'outstanding_revenue' => 0,
    'average_booking_value' => 0,
    'average_stay_length' => 0,
    'guest_nights' => 0,
    'occupancy_rate' => 0,
    'collection_rate' => 0,
    'online_bookings' => 0,
    'walkin_bookings' => 0,
), is_array($reports['overview']) ? $reports['overview'] : array());
$report_settings = isset($report_settings) && is_array($report_settings) ? $report_settings : array();
$report_settings = array_merge(array(
    'report_token' => '',
    'admin_emails' => '',
    'daily_enabled' => 1,
    'weekly_enabled' => 1,
    'monthly_enabled' => 1,
    'yearly_enabled' => 0,
    'last_daily_sent_at' => '',
    'last_weekly_sent_at' => '',
    'last_monthly_sent_at' => '',
    'last_yearly_sent_at' => '',
    'last_run_at' => '',
    'last_run_note' => '',
), $report_settings);
$success_message = $this->session->flashdata('success');
$error_message = $this->session->flashdata('errors');
$success_token = (string) $this->session->flashdata('success_token');
$error_token = (string) $this->session->flashdata('errors_token');

$frontdesk = array_merge(array(
    'arrivals' => array(),
    'in_house' => array(),
    'departures' => array(),
    'pending_payment' => array(),
    'upcoming' => array(),
), $frontdesk);

$dashboard_total_chalets = (int) ($summary['total_chalets'] ?? 0);
$dashboard_occupied_units = max(0, (int) ($summary['occupied_units'] ?? 0));
$dashboard_dirty_units = max(0, (int) ($summary['dirty_units'] ?? 0));
$dashboard_available_units = max(0, (int) ($summary['available_units'] ?? $dashboard_total_chalets));
$dashboard_unavailable_units = max($dashboard_occupied_units, $dashboard_occupied_units + $dashboard_dirty_units);
$today_reference = date('Y-m-d');
$today_reference_ts = strtotime($today_reference);
$checkout_watchlist = array();
$frontdesk_live_queue = array();
$frontdesk_live_queue_map = array();

foreach (array('arrivals', 'in_house', 'departures') as $frontdesk_group) {
    foreach ((array) ($frontdesk[$frontdesk_group] ?? array()) as $queue_row) {
        $queue_code = trim((string) ($queue_row['reservation_code'] ?? ''));
        if ($queue_code !== '') {
            $frontdesk_live_queue_map[$queue_code] = $queue_row;
        } else {
            $frontdesk_live_queue[] = $queue_row;
        }
    }
}

if (!empty($frontdesk_live_queue_map)) {
    $frontdesk_live_queue = array_merge($frontdesk_live_queue, array_values($frontdesk_live_queue_map));
}

if ($dashboard_unavailable_units > 0) {
    $dashboard_available_units = min($dashboard_available_units, max(0, $dashboard_total_chalets - $dashboard_unavailable_units));
}

foreach ((array) ($frontdesk['in_house'] ?? array()) as $watch_row) {
    $watch_departure = trim((string) ($watch_row['departure_date'] ?? ''));
    $watch_departure_ts = !empty($watch_departure) ? strtotime($watch_departure) : FALSE;

    if ($watch_departure_ts !== FALSE && $watch_departure_ts <= $today_reference_ts) {
        $checkout_watchlist[] = $watch_row;
    }
}

$status_badge = function ($status) {
    $status = strtolower(trim((string) $status));
    switch ($status) {
        case 'available':
        case 'clean':
        case 'paid':
            return 'success';
        case 'confirmed':
        case 'pending_arrival':
            return 'warning';
        case 'pending':
        case 'pending_payment':
        case 'pay_at_resort':
            return 'brand';
        case 'unpaid':
            return 'danger';
        case 'occupied':
        case 'checked_in':
            return 'danger';
        case 'dirty':
            return 'dark';
        case 'checked_out':
            return 'info';
        default:
            return 'secondary';
    }
};

$status_label = function ($status) {
    $status = strtolower(trim((string) $status));

    switch ($status) {
        case 'pending':
        case 'pending_payment':
            return 'Pending Payment';
        case 'pay_at_resort':
            return 'Pay at Resort';
        case 'checked_in':
            return 'Checked In';
        case 'checked_out':
            return 'Checked Out';
        default:
            return ucwords(str_replace('_', ' ', (string) $status));
    }
};

$money = function ($amount) {
    return '₦' . number_format((float) $amount, 2);
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Alkebulan | Lacampagne Tropicana Resorts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php echo $css; ?>
    <style>
        .hero-panel {
            background: linear-gradient(135deg, #0b5ed7 0%, #073b8f 100%);
            color: #fff;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 14px 30px rgba(11, 94, 215, .18);
        }
        .hero-panel h2 { color: #fff; margin-bottom: 6px; }
        .hero-actions .btn { margin: 0 8px 8px 0; }
        .metric-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 10px 22px rgba(28,39,60,.07);
        }
        .metric-card .metric-label { color: #6c7a91; font-size: .92rem; text-transform: uppercase; letter-spacing: .04em; }
        .metric-card .metric-value { font-size: 1.9rem; font-weight: 700; margin-top: 6px; }
        .mini-kpi { background: rgba(255,255,255,.12); border-radius: 12px; padding: 12px 14px; margin-bottom: 10px; }
        .mini-kpi strong { display: block; font-size: 1.35rem; }
        .status-tile {
            border: 1px solid #edf1f7;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 14px;
            background: #fff;
            min-height: 150px;
        }
        .status-tile .tile-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        .status-meta { color: #6c7a91; font-size: .92rem; }
        .section-note {
            color: #6c7a91;
            margin-top: -6px;
            margin-bottom: 14px;
        }
        .action-stack form { display: inline-block; margin: 0 6px 6px 0; }
        .table td, .table th { vertical-align: middle; }
        .walkin-form .form-control, .walkin-form .custom-select { margin-bottom: 10px; }
        .workflow-list li { margin-bottom: 8px; }
        .soft-box {
            background: #f8fafc;
            border: 1px solid #edf1f7;
            border-radius: 12px;
            padding: 14px;
        }
        @media (max-width: 991px) {
            .hero-panel .row > div:last-child { margin-top: 15px; }
        }
    </style>
</head>
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
<?php echo $header_mobile; ?>
<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
        <?php echo $side_menu; ?>
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
            <div id="kt_header" class="kt-header kt-grid__item kt-header--fixed ">
                <?php echo $header_menu; ?>
                <div class="kt-header__topbar"><?php echo $userbar_menu; ?></div>
            </div>

            <div class="kt-content kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
                <div class="kt-subheader kt-grid__item" id="kt_subheader">
                    <div class="kt-container kt-container--fluid ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title"><?php echo html_escape($page_title); ?></h3>
                            <span class="kt-subheader__separator kt-hidden"></span>
                            <div class="kt-subheader__breadcrumbs">
                                <a href="#" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                                <span class="kt-subheader__breadcrumbs-separator"></span>
                                <span class="kt-subheader__breadcrumbs-link"><?php echo html_escape($page_note); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
                    <div class="hero-panel">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h2><?php echo html_escape($page_title); ?></h2>
                                <p class="mb-3"><?php echo html_escape($page_note); ?></p>
                                <div class="hero-actions">
                                    <a href="<?php echo site_url('reservations/dashboard'); ?>" class="btn btn-light btn-sm">Overview</a>
                                    <a href="<?php echo site_url('reservations/frontdesk'); ?>" class="btn btn-warning btn-sm">Front Desk</a>
                                    <a href="<?php echo site_url('reservations/online-bookings'); ?>" class="btn btn-outline-light btn-sm">Online Bookings</a>
                                    <a href="<?php echo site_url('reservations/reports'); ?>" class="btn btn-info btn-sm">Reports</a>
                                    <a href="<?php echo site_url('book'); ?>" class="btn btn-success btn-sm" target="_blank">Open Booking Portal</a>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mini-kpi">
                                    <span>Check-ins Today</span>
                                    <strong><?php echo (int) ($summary['due_checkins'] ?? 0); ?></strong>
                                </div>
                                <div class="mini-kpi">
                                    <span>In-House Guests</span>
                                    <strong><?php echo (int) ($summary['in_house_guests'] ?? 0); ?></strong>
                                </div>
                                <div class="mini-kpi mb-0">
                                    <span>Pending Online Payments</span>
                                    <strong><?php echo (int) ($summary['pending_payments'] ?? 0); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="kt-portlet metric-card">
                                <div class="kt-portlet__body">
                                    <div class="metric-label">Total Chalets</div>
                                    <div class="metric-value kt-font-brand"><?php echo $dashboard_total_chalets; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="kt-portlet metric-card">
                                <div class="kt-portlet__body">
                                    <div class="metric-label">Available Units</div>
                                    <div class="metric-value kt-font-success"><?php echo $dashboard_available_units; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-2">
                            <div class="kt-portlet metric-card">
                                <div class="kt-portlet__body">
                                    <div class="metric-label">Current Check-Ins</div>
                                    <div class="metric-value kt-font-warning"><?php echo (int) ($summary['in_house_guests'] ?? 0); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-2">
                            <div class="kt-portlet metric-card">
                                <div class="kt-portlet__body">
                                    <div class="metric-label">Dirty Units</div>
                                    <div class="metric-value kt-font-dark"><?php echo $dashboard_dirty_units; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($checkout_watchlist) && $page_mode !== 'reports') { ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="alert alert-warning" role="alert">
                                    <strong>Checkout attention needed:</strong>
                                    <?php echo count($checkout_watchlist); ?> in-house guest(s) are due or overdue for checkout.
                                    <ul class="mb-0 mt-2 pl-4">
                                        <?php foreach ($checkout_watchlist as $watch_row) {
                                            $watch_departure = trim((string) ($watch_row['departure_date'] ?? ''));
                                            $watch_departure_ts = !empty($watch_departure) ? strtotime($watch_departure) : FALSE;
                                            $watch_is_overdue = ($watch_departure_ts !== FALSE && $watch_departure_ts < $today_reference_ts);
                                        ?>
                                            <li>
                                                <strong><?php echo html_escape($watch_row['guest_name'] ?? '-'); ?></strong>
                                                in <?php echo html_escape($watch_row['room_label'] ?? 'an assigned chalet'); ?> —
                                                <?php echo $watch_is_overdue ? 'overdue since ' : 'due on '; ?><?php echo html_escape($watch_departure ?: $today_reference); ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($page_mode === 'online_bookings') { ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head">
                                        <div class="kt-portlet__head-label">
                                            <h3 class="kt-portlet__head-title">Online Booking Queue</h3>
                                        </div>
                                    </div>
                                    <div class="kt-portlet__body">
                                        <p class="section-note">These are the website bookings already captured in Alkebulan. Future arrivals will appear here even before their check-in date.</p>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Reservation</th>
                                                        <th>Guest</th>
                                                        <th>Arrival</th>
                                                        <th>Departure</th>
                                                        <th>Amount</th>
                                                        <th>Payment</th>
                                                        <th>Reservation Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if (!empty($frontdesk['upcoming'])) { ?>
                                                    <?php foreach ($frontdesk['upcoming'] as $row) { ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?php echo html_escape($row['reservation_code'] ?? '-'); ?></strong><br>
                                                                <small>Order #<?php echo html_escape((string) ($row['order_id'] ?? '-')); ?></small>
                                                            </td>
                                                            <td>
                                                                <?php echo html_escape($row['guest_name'] ?? '-'); ?><br>
                                                                <small><?php echo html_escape($row['guest_email'] ?? $row['guest_phone'] ?? '-'); ?></small>
                                                            </td>
                                                            <td><?php echo html_escape($row['arrival_date'] ?? '-'); ?></td>
                                                            <td><?php echo html_escape($row['departure_date'] ?? '-'); ?></td>
                                                            <td><?php echo $money($row['total_amount'] ?? 0); ?></td>
                                                            <td>
                                                                <span class="kt-badge kt-badge--<?php echo $status_badge($row['payment_status'] ?? 'pending'); ?> kt-badge--inline">
                                                                    <?php echo html_escape($status_label($row['payment_option'] ?? 'online')); ?> / <?php echo html_escape($status_label($row['payment_status'] ?? 'pending')); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="kt-badge kt-badge--<?php echo $status_badge($row['reservation_status'] ?? 'pending'); ?> kt-badge--inline">
                                                                    <?php echo html_escape($status_label($row['reservation_status'] ?? 'pending')); ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td colspan="7" class="text-center">No online bookings are available yet.</td></tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($page_mode === 'reports') { ?>
                        <?php $reportOverview = $reports['overview']; ?>
                        <?php $reportTrendRows = !empty($reports['daily_revenue']) ? array_slice($reports['daily_revenue'], -14) : array(); ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head">
                                        <div class="kt-portlet__head-label">
                                            <h3 class="kt-portlet__head-title">Payment Reports & Executive Analysis</h3>
                                        </div>
                                    </div>
                                    <div class="kt-portlet__body">
                                        <form method="get" action="<?php echo site_url('reservations/reports'); ?>">
                                            <div class="row align-items-end">
                                                <div class="col-md-3 form-group">
                                                    <label>From</label>
                                                    <input type="date" name="from" class="form-control" value="<?php echo html_escape($reports['filters']['from'] ?? ''); ?>">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label>To</label>
                                                    <input type="date" name="to" class="form-control" value="<?php echo html_escape($reports['filters']['to'] ?? ''); ?>">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <button type="submit" class="btn btn-primary">Run Report</button>
                                                    <a href="<?php echo site_url('reservations/reports'); ?>" class="btn btn-outline-secondary">Last 30 Days</a>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="soft-box">
                                            Showing system analysis from <strong><?php echo html_escape($reports['filters']['from'] ?? '-'); ?></strong> to <strong><?php echo html_escape($reports['filters']['to'] ?? '-'); ?></strong> (<?php echo (int) ($reports['filters']['days'] ?? 0); ?> day(s)).
                                        </div>

                                        <?php $reportQuery = '?from=' . rawurlencode((string) ($reports['filters']['from'] ?? '')) . '&to=' . rawurlencode((string) ($reports['filters']['to'] ?? '')) . '&period=custom'; ?>
                                        <?php $dispatchUrl = site_url('reservations/report-dispatch') . '?token=' . rawurlencode((string) ($report_settings['report_token'] ?? '')); ?>
                                        <div class="row mt-4">
                                            <div class="col-lg-6 mb-3">
                                                <div class="soft-box h-100">
                                                    <h5 class="mb-3">Exports & Printable Cashier Reports</h5>
                                                    <p class="mb-3">Use these tools to download finance-friendly files or open a printer-ready report that can be saved as PDF.</p>
                                                    <a href="<?php echo site_url('reservations/reports/export/csv') . $reportQuery; ?>" class="btn btn-success btn-sm mr-2 mb-2">Export CSV</a>
                                                    <a href="<?php echo site_url('reservations/reports/printable') . $reportQuery . '&autoprint=1'; ?>" target="_blank" class="btn btn-danger btn-sm mr-2 mb-2">PDF / Print Summary</a>
                                                    <a href="<?php echo site_url('reservations/reports/printable?period=daily&type=cashier&autoprint=1'); ?>" target="_blank" class="btn btn-warning btn-sm mb-2">Print Daily Cashier Report</a>

                                                    <form method="post" action="<?php echo site_url('reservations/reports/dispatch'); ?>" class="js-send-reports-form mt-3">
                                                        <button type="submit" class="btn btn-primary btn-sm">Send Due Reports Now</button>
                                                        <small class="text-muted d-block mt-2">This will send only the enabled report cycles that are due at the moment.</small>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <div class="soft-box h-100">
                                                    <h5 class="mb-3">Automated Email Trigger & Flags</h5>
                                                    <form method="post" action="<?php echo site_url('reservations/reports/settings'); ?>">
                                                        <div class="form-group">
                                                            <label>Administrator Emails</label>
                                                            <textarea name="admin_emails" class="form-control" rows="3" placeholder="admin1@example.com, admin2@example.com"><?php echo html_escape($report_settings['admin_emails'] ?? ''); ?></textarea>
                                                            <small class="text-muted">Separate multiple email addresses with commas or new lines.</small>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-6 mb-2"><label><input type="checkbox" name="daily_enabled" value="1" <?php echo !empty($report_settings['daily_enabled']) ? 'checked' : ''; ?>> Daily</label></div>
                                                            <div class="col-sm-6 mb-2"><label><input type="checkbox" name="weekly_enabled" value="1" <?php echo !empty($report_settings['weekly_enabled']) ? 'checked' : ''; ?>> Weekly</label></div>
                                                            <div class="col-sm-6 mb-2"><label><input type="checkbox" name="monthly_enabled" value="1" <?php echo !empty($report_settings['monthly_enabled']) ? 'checked' : ''; ?>> Monthly</label></div>
                                                            <div class="col-sm-6 mb-2"><label><input type="checkbox" name="yearly_enabled" value="1" <?php echo !empty($report_settings['yearly_enabled']) ? 'checked' : ''; ?>> Yearly</label></div>
                                                        </div>
                                                        <div class="form-group mt-2">
                                                            <label>Apache / Linux Cron Command <span class="text-success">(recommended)</span></label>
                                                            <input type="text" class="form-control" readonly value="/usr/bin/php /var/www/html/lacampagnetropicana/alkebulan/index.php Reservations report_dispatch">
                                                            <small class="text-muted">Use this inside your server crontab, for example: <code>0 18 * * * /usr/bin/php /var/www/html/lacampagnetropicana/alkebulan/index.php Reservations report_dispatch >/dev/null 2>&1</code></small>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Optional Tokenized HTTP Trigger</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo html_escape($dispatchUrl); ?>">
                                                            <small class="text-muted">You can ignore this if you are using cron. It is only a fallback URL that can trigger the same report sender over HTTP.</small>
                                                        </div>
                                                        <button type="submit" class="btn btn-info btn-sm">Save Report Flags</button>
                                                    </form>
                                                    <hr>
                                                    <small class="d-block text-muted">Last daily: <?php echo html_escape(!empty($report_settings['last_daily_sent_at']) ? $report_settings['last_daily_sent_at'] : 'Never'); ?></small>
                                                    <small class="d-block text-muted">Last weekly: <?php echo html_escape(!empty($report_settings['last_weekly_sent_at']) ? $report_settings['last_weekly_sent_at'] : 'Never'); ?></small>
                                                    <small class="d-block text-muted">Last monthly: <?php echo html_escape(!empty($report_settings['last_monthly_sent_at']) ? $report_settings['last_monthly_sent_at'] : 'Never'); ?></small>
                                                    <small class="d-block text-muted">Last yearly: <?php echo html_escape(!empty($report_settings['last_yearly_sent_at']) ? $report_settings['last_yearly_sent_at'] : 'Never'); ?></small>
                                                    <small class="d-block text-muted mt-2">Dispatcher note: <?php echo html_escape(!empty($report_settings['last_run_note']) ? $report_settings['last_run_note'] : 'No automated dispatch has been logged yet.'); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="kt-portlet metric-card"><div class="kt-portlet__body"><div class="metric-label">Gross Booking Value</div><div class="metric-value kt-font-brand"><?php echo $money($reportOverview['gross_revenue'] ?? 0); ?></div></div></div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="kt-portlet metric-card"><div class="kt-portlet__body"><div class="metric-label">Collected Revenue</div><div class="metric-value kt-font-success"><?php echo $money($reportOverview['paid_revenue'] ?? 0); ?></div></div></div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="kt-portlet metric-card"><div class="kt-portlet__body"><div class="metric-label">Outstanding Balance</div><div class="metric-value kt-font-danger"><?php echo $money($reportOverview['outstanding_revenue'] ?? 0); ?></div></div></div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="kt-portlet metric-card"><div class="kt-portlet__body"><div class="metric-label">Occupancy Rate</div><div class="metric-value kt-font-warning"><?php echo number_format((float) ($reportOverview['occupancy_rate'] ?? 0), 1); ?>%</div></div></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Executive Summary</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <div class="soft-box">
                                            <p class="mb-2"><strong>Reservations captured:</strong> <?php echo (int) ($reportOverview['reservations'] ?? 0); ?></p>
                                            <p class="mb-2"><strong>Paid reservations:</strong> <?php echo (int) ($reportOverview['paid_reservations'] ?? 0); ?></p>
                                            <p class="mb-2"><strong>Unpaid / pending:</strong> <?php echo (int) ($reportOverview['unpaid_reservations'] ?? 0); ?></p>
                                            <p class="mb-2"><strong>Guest nights sold:</strong> <?php echo (int) ($reportOverview['guest_nights'] ?? 0); ?></p>
                                            <p class="mb-2"><strong>Average booking value:</strong> <?php echo $money($reportOverview['average_booking_value'] ?? 0); ?></p>
                                            <p class="mb-0"><strong>Average stay length:</strong> <?php echo number_format((float) ($reportOverview['average_stay_length'] ?? 0), 1); ?> night(s)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Business Mix</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <div class="row">
                                            <div class="col-sm-6 mb-3">
                                                <div class="soft-box h-100">
                                                    <h5 class="mb-1">Collection Rate</h5>
                                                    <div class="metric-value kt-font-success"><?php echo number_format((float) ($reportOverview['collection_rate'] ?? 0), 1); ?>%</div>
                                                    <small class="text-muted">Paid revenue versus total booking value.</small>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <div class="soft-box h-100">
                                                    <h5 class="mb-1">Online Bookings</h5>
                                                    <div class="metric-value kt-font-info"><?php echo (int) ($reportOverview['online_bookings'] ?? 0); ?></div>
                                                    <small class="text-muted">Bookings started from the public portal.</small>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="soft-box h-100">
                                                    <h5 class="mb-1">Walk-In / Desk Orders</h5>
                                                    <div class="metric-value kt-font-warning"><?php echo (int) ($reportOverview['walkin_bookings'] ?? 0); ?></div>
                                                    <small class="text-muted">Reservations created directly at the front desk.</small>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="soft-box h-100">
                                                    <h5 class="mb-1">Currently In House</h5>
                                                    <div class="metric-value kt-font-danger"><?php echo (int) ($reportOverview['checked_in'] ?? 0); ?></div>
                                                    <small class="text-muted">Guests actively checked in during this reporting period.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Payment Method Breakdown</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead><tr><th>Method</th><th>Transactions</th><th>Collected</th><th>Share</th></tr></thead>
                                                <tbody>
                                                <?php if (!empty($reports['payment_methods'])) { ?>
                                                    <?php foreach ($reports['payment_methods'] as $row) { ?>
                                                        <?php $share = (float) ($reportOverview['paid_revenue'] ?? 0) > 0 ? (((float) ($row['amount_collected'] ?? 0) / (float) $reportOverview['paid_revenue']) * 100) : 0; ?>
                                                        <tr>
                                                            <td><?php echo html_escape($status_label($row['method_key'] ?? 'unknown')); ?></td>
                                                            <td><?php echo (int) ($row['total_count'] ?? 0); ?></td>
                                                            <td><?php echo $money($row['amount_collected'] ?? 0); ?></td>
                                                            <td><?php echo number_format($share, 1); ?>%</td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td colspan="4" class="text-center">No confirmed payments were recorded in the selected range.</td></tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Reservation Status Mix</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead><tr><th>Status</th><th>Count</th><th>Booking Value</th></tr></thead>
                                                <tbody>
                                                <?php if (!empty($reports['status_mix'])) { ?>
                                                    <?php foreach ($reports['status_mix'] as $row) { ?>
                                                        <tr>
                                                            <td><span class="kt-badge kt-badge--<?php echo $status_badge($row['reservation_status'] ?? 'pending'); ?> kt-badge--inline"><?php echo html_escape($status_label($row['reservation_status'] ?? 'pending')); ?></span></td>
                                                            <td><?php echo (int) ($row['total_count'] ?? 0); ?></td>
                                                            <td><?php echo $money($row['total_amount'] ?? 0); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td colspan="3" class="text-center">No reservation activity was found for the selected period.</td></tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Daily Revenue Trend</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead><tr><th>Date</th><th>Payments</th><th>Collected</th></tr></thead>
                                                <tbody>
                                                <?php if (!empty($reportTrendRows)) { ?>
                                                    <?php foreach ($reportTrendRows as $row) { ?>
                                                        <tr>
                                                            <td><?php echo html_escape($row['report_day'] ?? '-'); ?></td>
                                                            <td><?php echo (int) ($row['total_count'] ?? 0); ?></td>
                                                            <td><?php echo $money($row['amount_collected'] ?? 0); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td colspan="3" class="text-center">No revenue activity is available yet.</td></tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Top Performing Chalets</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead><tr><th>Chalet</th><th>Bookings</th><th>Gross</th><th>Paid</th></tr></thead>
                                                <tbody>
                                                <?php if (!empty($reports['top_units'])) { ?>
                                                    <?php foreach ($reports['top_units'] as $row) { ?>
                                                        <tr>
                                                            <td><?php echo html_escape($row['room_label'] ?? '-'); ?></td>
                                                            <td><?php echo (int) ($row['total_count'] ?? 0); ?></td>
                                                            <td><?php echo $money($row['gross_amount'] ?? 0); ?></td>
                                                            <td><?php echo $money($row['paid_amount'] ?? 0); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td colspan="4" class="text-center">No chalet performance data is available for this range.</td></tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Recent Payment Audit Trail</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Reservation</th>
                                                        <th>Guest</th>
                                                        <th>Chalet</th>
                                                        <th>Method</th>
                                                        <th>Reference</th>
                                                        <th>Amount</th>
                                                        <th>Confirmed By</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if (!empty($reports['payment_audit'])) { ?>
                                                    <?php foreach ($reports['payment_audit'] as $row) { ?>
                                                        <tr>
                                                            <td><?php echo html_escape($row['reservation_code'] ?? '-'); ?><br><small>Order #<?php echo html_escape((string) ($row['order_id'] ?? '-')); ?></small></td>
                                                            <td><?php echo html_escape($row['guest_name'] ?? '-'); ?></td>
                                                            <td><?php echo html_escape($row['room_label'] ?? '-'); ?></td>
                                                            <td><?php echo html_escape($status_label($row['payment_method_label'] ?? 'online_pay')); ?></td>
                                                            <td><?php echo html_escape($row['payment_reference_manual'] ?? '-'); ?></td>
                                                            <td><?php echo $money($row['amount_paid'] ?? $row['total_amount'] ?? 0); ?></td>
                                                            <td><?php echo html_escape($row['payment_confirmed_by_user'] ?? 'System'); ?></td>
                                                            <td><?php echo html_escape($row['payment_confirmed_at'] ?? '-'); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td colspan="8" class="text-center">No payment audit entries were found in the selected range.</td></tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($page_mode === 'frontdesk') { ?>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head">
                                        <div class="kt-portlet__head-label">
                                            <h3 class="kt-portlet__head-title">Walk-In Check-In</h3>
                                        </div>
                                    </div>
                                    <div class="kt-portlet__body walkin-form">
                                        <p class="section-note">Use this to register guests who arrive directly at the resort and check them in immediately.</p>
                                        <form method="post" action="<?php echo site_url('reservations/walkin'); ?>" id="walkin-frontdesk-form">
                                            <div class="form-group">
                                                <label>Guest Title</label>
                                                <select name="title" class="form-control custom-select">
                                                    <option value="Mr">Mr</option>
                                                    <option value="Mrs">Mrs</option>
                                                    <option value="Miss">Miss</option>
                                                    <option value="Dr">Dr</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Available Chalet</label>
                                                <select name="unit_id" id="walkin_unit_id" class="form-control custom-select" required>
                                                    <option value="">Select a chalet</option>
                                                    <?php foreach ($walkin_options as $option) {
                                                        $walkin_standard_price = (float) ($option['standard_rate'] ?? $option['room_type_price'] ?? 0);
                                                    ?>
                                                        <option value="<?php echo (int) ($option['unit_id'] ?? 0); ?>" data-price="<?php echo html_escape((string) $walkin_standard_price); ?>" data-capacity="<?php echo html_escape((string) ($option['room_capacity'] ?? 0)); ?>" data-title="<?php echo html_escape($option['display_title'] ?? 'Chalet'); ?>">
                                                            <?php echo html_escape(($option['display_title'] ?? 'Chalet') . ' — ' . ($money($walkin_standard_price))); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>First / Other Names</label>
                                                <input type="text" name="onames" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="lname" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Phone Number</label>
                                                <input type="text" name="phonenumber" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Email Address</label>
                                                <input type="email" name="email" class="form-control" placeholder="Optional for walk-in">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>Arrival</label>
                                                    <input type="date" name="arrivaldate" id="walkin_arrivaldate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Departure</label>
                                                    <input type="date" name="departuredate" id="walkin_departuredate" class="form-control" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>Adults</label>
                                                    <input type="number" name="adultno" id="walkin_adultno" class="form-control" value="1" min="1">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Children</label>
                                                    <input type="number" name="kidsno" id="walkin_kidsno" class="form-control" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="soft-box mb-3" id="walkin-charge-box">
                                                <h5 class="mb-2">Auto Charge Preview</h5>
                                                <p class="mb-1"><strong>Chalet:</strong> <span id="walkin_preview_chalet">Select a chalet</span></p>
                                                <p class="mb-1"><strong>Nights:</strong> <span id="walkin_preview_nights">1</span></p>
                                                <p class="mb-1"><strong>Occupants:</strong> <span id="walkin_preview_guests">1</span></p>
                                                <p class="mb-1"><strong>Nightly Rate:</strong> <span id="walkin_preview_rate">₦0.00</span></p>
                                                <p class="mb-0"><strong>Estimated Total:</strong> <span id="walkin_preview_total">₦0.00</span></p>
                                                <small class="text-muted d-block mt-2" id="walkin_preview_note">The order will be created first as unpaid. Confirm payment afterwards to unlock check-in.</small>
                                            </div>
                                            <div class="form-group">
                                                <label>Front Desk Note</label>
                                                <textarea name="special_request" class="form-control" rows="3" placeholder="Walk-in note, VIP note, room preference, etc."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-success btn-block" id="walkin_submit_btn">Create Unpaid Walk-In Order</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head">
                                        <div class="kt-portlet__head-label">
                                            <h3 class="kt-portlet__head-title">Professional Front Desk Workflow</h3>
                                        </div>
                                    </div>
                                    <div class="kt-portlet__body">
                                        <p class="section-note">This front-desk monitor is now arranged into one <strong>Front Desk Guest Queue</strong> for arrivals and checked-in guests, plus a separate <strong>Departures &amp; Housekeeping</strong> board for release and cleanup.</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="soft-box">
                                                    <h5>How to Use It</h5>
                                                    <ol class="workflow-list pl-3 mb-0">
                                                        <li>Register a <strong>walk-in</strong> or open an existing reservation.</li>
                                                        <li>Use <strong>Front Desk Guest Queue</strong> for payment confirmation, check-in, and live guest visibility.</li>
                                                        <li>Guests who are already checked in stay visible in the same queue.</li>
                                                        <li>Use <strong>Departures &amp; Housekeeping</strong> for checkout and room reset.</li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="soft-box">
                                                    <h5>Operational Snapshot</h5>
                                                    <p class="mb-2"><strong>Arrivals awaiting front desk:</strong> <?php echo count($frontdesk['arrivals']); ?></p>
                                                    <p class="mb-2"><strong>Guests in house:</strong> <?php echo count($frontdesk['in_house']); ?></p>
                                                    <p class="mb-2"><strong>Departures / rooms to release:</strong> <?php echo count($frontdesk['departures']); ?></p>
                                                    <p class="mb-0"><strong>Pending online payments:</strong> <?php echo count($frontdesk['pending_payment']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Front Desk Guest Queue</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <p class="section-note">This merged queue keeps both arrivals and checked-in guests visible, so the panel stays active throughout the day.</p>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Reservation</th>
                                                        <th>Guest</th>
                                                        <th>Room</th>
                                                        <th>Stay / Checkout</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if (!empty($frontdesk_live_queue)) { ?>
                                                    <?php foreach ($frontdesk_live_queue as $row) { ?>
                                                        <?php
                                                            $rowStatus = strtolower((string) ($row['reservation_status'] ?? ''));
                                                            $paymentState = strtolower((string) ($row['payment_status'] ?? ''));
                                                            $housekeepingState = strtolower((string) ($row['housekeeping_status'] ?? 'clean'));
                                                            $departureDate = trim((string) ($row['departure_date'] ?? ''));
                                                            $departureTs = !empty($departureDate) ? strtotime($departureDate) : FALSE;
                                                            $daysLeft = ($departureTs !== FALSE) ? (int) floor(($departureTs - $today_reference_ts) / 86400) : NULL;
                                                            $isInHouse = ($rowStatus === 'checked_in');
                                                            $canConfirmPayment = (!$isInHouse && $paymentState !== 'paid' && in_array($rowStatus, array('confirmed', 'pending_arrival', 'pending_payment', 'pending'), TRUE));
                                                            $canCheckIn = (!$isInHouse && $paymentState === 'paid' && in_array($rowStatus, array('confirmed', 'pending_arrival', 'pending_payment', 'pending'), TRUE));
                                                            $canCheckOut = $isInHouse;
                                                            $canMarkClean = ($housekeepingState === 'dirty' || $rowStatus === 'checked_out');
                                                        ?>
                                                        <tr>
                                                            <td><?php echo html_escape($row['reservation_code'] ?? '-'); ?></td>
                                                            <td>
                                                                <strong><?php echo html_escape($row['guest_name'] ?? '-'); ?></strong><br>
                                                                <small><?php echo html_escape($row['guest_phone'] ?? '-'); ?></small>
                                                            </td>
                                                            <td><?php echo html_escape($row['room_label'] ?? '-'); ?></td>
                                                            <td>
                                                                <strong><?php echo html_escape(($row['arrival_date'] ?? '-') . ' → ' . ($row['departure_date'] ?? '-')); ?></strong>
                                                                <?php if ($isInHouse && !empty($row['checkin_time'])) { ?>
                                                                    <div><small>Checked in: <?php echo html_escape($row['checkin_time']); ?></small></div>
                                                                <?php } ?>
                                                                <?php if ($departureTs !== FALSE && $departureTs < $today_reference_ts) { ?>
                                                                    <div><span class="kt-badge kt-badge--danger kt-badge--inline">Overdue Checkout</span></div>
                                                                <?php } elseif ($departureTs !== FALSE && $departureTs === $today_reference_ts) { ?>
                                                                    <div><span class="kt-badge kt-badge--warning kt-badge--inline">Due Today</span></div>
                                                                <?php } elseif ($isInHouse && $departureTs !== FALSE) { ?>
                                                                    <div><small><?php echo max(1, $daysLeft); ?> day(s) left</small></div>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <div class="mb-1">
                                                                    <span class="kt-badge kt-badge--<?php echo $isInHouse ? 'danger' : 'warning'; ?> kt-badge--inline">
                                                                        <?php echo $isInHouse ? 'In House' : 'Arrival'; ?>
                                                                    </span>
                                                                </div>
                                                                <span class="kt-badge kt-badge--<?php echo $status_badge($row['reservation_status'] ?? 'pending'); ?> kt-badge--inline">
                                                                    <?php echo html_escape($status_label($row['reservation_status'] ?? 'pending')); ?>
                                                                </span>
                                                                <div class="mt-1">
                                                                    <span class="kt-badge kt-badge--<?php echo $status_badge($row['payment_status'] ?? 'pending'); ?> kt-badge--inline">
                                                                        <?php echo html_escape($status_label($row['payment_status'] ?? 'pending')); ?>
                                                                    </span>
                                                                </div>
                                                                <div><small><?php echo html_escape($status_label($row['payment_method_label'] ?? ($row['payment_option'] ?? 'pending'))); ?></small></div>
                                                                <?php if (!empty($row['payment_reference_manual'])) { ?><div><small>Ref: <?php echo html_escape($row['payment_reference_manual']); ?></small></div><?php } ?>
                                                            </td>
                                                            <td class="action-stack">
                                                                <?php if ($canConfirmPayment) { ?>
                                                                    <form method="post" class="js-confirm-payment-form" action="<?php echo site_url('reservations/action/confirm_payment/' . rawurlencode($row['reservation_code'] ?? '')); ?>">
                                                                        <select name="payment_method" class="form-control form-control-sm mb-1" required>
                                                                            <option value="">Select method</option>
                                                                            <?php foreach ($payment_methods as $methodKey => $methodLabel) { ?>
                                                                                <option value="<?php echo html_escape($methodKey); ?>"><?php echo html_escape($methodLabel); ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                        <input type="text" name="payment_reference" class="form-control form-control-sm mb-1" placeholder="Receipt / reference" required>
                                                                        <button type="submit" class="btn btn-primary btn-sm">Confirm Payment</button>
                                                                    </form>
                                                                    <div class="text-muted"><small>Check-in stays locked until paid.</small></div>
                                                                <?php } ?>

                                                                <?php if ($canCheckIn) { ?>
                                                                    <div class="text-success mb-1"><small>Payment confirmed</small></div>
                                                                    <form method="post" action="<?php echo site_url('reservations/action/checkin/' . rawurlencode($row['reservation_code'] ?? '')); ?>">
                                                                        <button type="submit" class="btn btn-success btn-sm">Check In</button>
                                                                    </form>
                                                                <?php } ?>

                                                                <?php if ($canCheckOut) { ?>
                                                                    <form method="post" action="<?php echo site_url('reservations/action/checkout/' . rawurlencode($row['reservation_code'] ?? '')); ?>">
                                                                        <button type="submit" class="btn btn-warning btn-sm">Check Out</button>
                                                                    </form>
                                                                <?php } ?>

                                                                <?php if ($canMarkClean) { ?>
                                                                    <form method="post" action="<?php echo site_url('reservations/action/mark_clean/' . rawurlencode($row['reservation_code'] ?? '')); ?>">
                                                                        <button type="submit" class="btn btn-success btn-sm">Mark Clean</button>
                                                                    </form>
                                                                <?php } ?>

                                                                <?php if (!$canConfirmPayment && !$canCheckIn && !$canCheckOut && !$canMarkClean) { ?>
                                                                    <span class="text-muted"><small>No action needed right now.</small></span>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td colspan="6" class="text-center">No arrival or in-house guest records are waiting right now.</td></tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-lg-12">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Upcoming Bookings Beyond Today</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Reservation</th>
                                                        <th>Guest</th>
                                                        <th>Arrival</th>
                                                        <th>Payment</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php if (!empty($frontdesk['upcoming'])) { ?>
                                                    <?php foreach ($frontdesk['upcoming'] as $row) { ?>
                                                        <tr>
                                                            <td><?php echo html_escape($row['reservation_code'] ?? '-'); ?></td>
                                                            <td><?php echo html_escape($row['guest_name'] ?? '-'); ?></td>
                                                            <td><?php echo html_escape(($row['arrival_date'] ?? '-') . ' → ' . ($row['departure_date'] ?? '-')); ?></td>
                                                            <td>
                                                                <?php echo html_escape($status_label($row['payment_option'] ?? 'online')); ?> / <?php echo html_escape($status_label($row['payment_status'] ?? 'pending')); ?>
                                                                <?php if (!empty($row['payment_reference_manual'])) { ?><br><small>Ref: <?php echo html_escape($row['payment_reference_manual']); ?></small><?php } ?>
                                                                <?php if (!empty($row['payment_confirmed_by_user'])) { ?><br><small>By: <?php echo html_escape($row['payment_confirmed_by_user']); ?></small><?php } ?>
                                                            </td>
                                                            <td><?php echo html_escape($status_label($row['reservation_status'] ?? 'pending')); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td colspan="5" class="text-center">No future bookings are queued yet.</td></tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($page_mode !== 'frontdesk' && $page_mode !== 'reports') { ?>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head">
                                        <div class="kt-portlet__head-label">
                                            <h3 class="kt-portlet__head-title">Live Chalet Status Board</h3>
                                        </div>
                                    </div>
                                    <div class="kt-portlet__body">
                                        <p class="section-note">A cleaner operational snapshot of every chalet with front-desk occupancy and housekeeping status reflected from the same local Alkebulan reservation records.</p>
                                        <div class="row">
                                            <?php if (!empty($status_board)) { ?>
                                                <?php foreach ($status_board as $row) { ?>
                                                    <div class="col-md-6 col-xl-4">
                                                        <div class="status-tile">
                                                            <div class="tile-top">
                                                                <div>
                                                                    <h5 class="mb-1"><?php echo html_escape($row['display_title'] ?? $row['room_type'] ?? 'Chalet'); ?></h5>
                                                                    <div class="status-meta">Unit <?php echo html_escape($row['room_number'] ?? $row['unit_id'] ?? '-'); ?></div>
                                                                </div>
                                                                <span class="kt-badge kt-badge--<?php echo $status_badge($row['room_status'] ?? 'available'); ?> kt-badge--inline">
                                                                    <?php echo html_escape($status_label($row['room_status'] ?? 'available')); ?>
                                                                </span>
                                                            </div>
                                                            <div class="status-meta mb-2">Capacity: <?php echo (int) ($row['room_capacity'] ?? 0); ?> guests</div>
                                                            <div class="status-meta mb-2">Bed Type: <?php echo html_escape($row['bed_type'] ?? 'N/A'); ?></div>
                                                            <div class="status-meta mb-2">Available Now: <?php echo (int) ($row['available_units'] ?? 0); ?> of <?php echo (int) ($row['inventory_count'] ?? 1); ?> unit(s)</div>
                                                            <div><strong><?php echo $money($row['room_type_price'] ?? 0); ?></strong> / night</div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <div class="col-12"><div class="alert alert-light">No chalet / room records found yet.</div></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Operational Quick Actions</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <div class="list-group mb-3">
                                            <a class="list-group-item list-group-item-action" href="<?php echo site_url('Alkebulan/manage_chalet'); ?>">Manage Chalet Catalog</a>
                                            <a class="list-group-item list-group-item-action" href="<?php echo site_url('reservations/frontdesk'); ?>">Open Front Desk Monitor</a>
                                            <a class="list-group-item list-group-item-action" href="<?php echo site_url('reservations/online-bookings'); ?>">Review Online Booking Queue</a>
                                            <a class="list-group-item list-group-item-action" href="<?php echo site_url('book'); ?>" target="_blank">Open Public Booking Portal</a>
                                        </div>
                                        <div class="soft-box">
                                            <h5 class="mb-3">Recommended Professional Flow</h5>
                                            <p class="mb-2">1. Online guest books and pays or reserves.</p>
                                            <p class="mb-2">2. Front desk checks guest in on arrival.</p>
                                            <p class="mb-2">3. Checkout marks the room dirty automatically.</p>
                                            <p class="mb-0">4. Housekeeping marks it clean before it returns to sale.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (!empty($frontdesk['pending_payment']) && $page_mode !== 'frontdesk' && $page_mode !== 'reports') { ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="kt-portlet">
                                    <div class="kt-portlet__head"><div class="kt-portlet__head-label"><h3 class="kt-portlet__head-title">Pending Online Payment Watchlist</h3></div></div>
                                    <div class="kt-portlet__body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Reservation</th>
                                                        <th>Guest</th>
                                                        <th>Room</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($frontdesk['pending_payment'] as $row) { ?>
                                                    <tr>
                                                        <td><?php echo html_escape($row['reservation_code'] ?? '-'); ?></td>
                                                        <td><?php echo html_escape($row['guest_name'] ?? '-'); ?></td>
                                                        <td><?php echo html_escape($row['room_label'] ?? '-'); ?></td>
                                                        <td><?php echo $money($row['total_amount'] ?? 0); ?></td>
                                                        <td><?php echo html_escape($status_label($row['payment_status'] ?? 'pending')); ?></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($page_mode === 'dashboard') { ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="kt-portlet">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">Recent Reservation Activities</h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    <p class="section-note">Overview-only activity feed. Status is shown here in color, while operational actions stay in the front-desk panels.</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Reference</th>
                                                    <th>Guest</th>
                                                    <th>Room</th>
                                                    <th>Stay</th>
                                                    <th>Amount</th>
                                                    <th>Current Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (!empty($recent_reservations)) { ?>
                                                <?php foreach ($recent_reservations as $row) { ?>
                                                    <tr>
                                                        <td><?php echo html_escape($row['reservation_code'] ?? '-'); ?></td>
                                                        <td><?php echo html_escape($row['guest_name'] ?? '-'); ?></td>
                                                        <td><?php echo html_escape($row['room_label'] ?? '-'); ?></td>
                                                        <td><?php echo html_escape(($row['reservation_checkin'] ?? '-') . ' → ' . ($row['reservation_checkout'] ?? '-')); ?></td>
                                                        <td><?php echo $money($row['reservation_total_payable_amount'] ?? 0); ?></td>
                                                        <td>
                                                            <span class="kt-badge kt-badge--<?php echo $status_badge($row['reservation_status_raw'] ?? 'pending'); ?> kt-badge--inline">
                                                                <?php echo html_escape($status_label($row['reservation_status_raw'] ?? ($row['reservation_status'] ?? 'pending'))); ?>
                                                            </span>
                                                            <?php if (!empty($row['payment_status_raw'])) { ?>
                                                                <div class="mt-1">
                                                                    <span class="kt-badge kt-badge--<?php echo $status_badge($row['payment_status_raw']); ?> kt-badge--inline">
                                                                        <?php echo html_escape($status_label($row['payment_status_raw'])); ?>
                                                                    </span>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (strtolower((string) ($row['housekeeping_status'] ?? '')) === 'dirty') { ?>
                                                                <div class="mt-1">
                                                                    <span class="kt-badge kt-badge--dark kt-badge--inline">Dirty Unit</span>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No reservation records available yet.</td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php echo $footer; ?>
        </div>
    </div>
</div>
<?php echo $scroll_top; ?>
<?php echo $js; ?>
<script>
jQuery(function ($) {
    var dashboardSuccessMessage = <?php echo json_encode(!empty($success_message) ? trim((string) $success_message) : ''); ?>;
    var dashboardErrorMessage = <?php echo json_encode(!empty($error_message) ? trim((string) $error_message) : ''); ?>;
    var dashboardSuccessToken = <?php echo json_encode(!empty($success_token) ? trim((string) $success_token) : ''); ?>;
    var dashboardErrorToken = <?php echo json_encode(!empty($error_token) ? trim((string) $error_token) : ''); ?>;

    if (dashboardSuccessMessage) {
        var successKey = 'alkebulan-alert-success-' + (dashboardSuccessToken || dashboardSuccessMessage);
        if (!window.sessionStorage || !sessionStorage.getItem(successKey)) {
            swal.fire('Successful!', dashboardSuccessMessage, 'success');
            if (window.sessionStorage) {
                sessionStorage.setItem(successKey, '1');
            }
        }
    }

    if (dashboardErrorMessage) {
        var errorKey = 'alkebulan-alert-error-' + (dashboardErrorToken || dashboardErrorMessage);
        if (!window.sessionStorage || !sessionStorage.getItem(errorKey)) {
            swal.fire('Error!', dashboardErrorMessage, 'error');
            if (window.sessionStorage) {
                sessionStorage.setItem(errorKey, '1');
            }
        }
    }

    $(document).on('submit', '.js-confirm-payment-form', function (event) {
        var form = $(this);
        if (form.data('confirmed') === true) {
            form.removeData('confirmed');
            return true;
        }

        var method = $.trim(form.find('[name="payment_method"]').val());
        var methodText = $.trim(form.find('[name="payment_method"] option:selected').text());
        var reference = $.trim(form.find('[name="payment_reference"]').val());

        if (!method) {
            event.preventDefault();
            swal.fire('Error!', 'Please select a payment method before confirming payment.', 'error');
            return false;
        }

        if (!reference) {
            event.preventDefault();
            swal.fire('Error!', 'Receipt / Reference Number is compulsory before confirming payment.', 'error');
            return false;
        }

        event.preventDefault();
        swal.fire({
            title: 'Confirm payment?',
            text: 'Mark this reservation as paid via ' + methodText + ' with receipt/reference ' + reference + '?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, confirm payment'
        }).then(function (result) {
            if (result.value || result.isConfirmed) {
                form.data('confirmed', true);
                form.trigger('submit');
            }
        });

        return false;
    });

    $(document).on('submit', '.js-send-reports-form', function (event) {
        var form = $(this);
        if (form.data('confirmed') === true) {
            form.removeData('confirmed');
            return true;
        }

        event.preventDefault();
        swal.fire({
            title: 'Send due reports now?',
            text: 'The system will email the enabled daily / weekly / monthly / yearly reports that are currently due.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, send reports'
        }).then(function (result) {
            if (result.value || result.isConfirmed) {
                form.data('confirmed', true);
                form.trigger('submit');
            }
        });

        return false;
    });

    function moneyFormat(value) {
        var amount = parseFloat(value || 0);
        if (isNaN(amount)) {
            amount = 0;
        }
        return '₦' + amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updateWalkinPreview() {
        var unitOption = $('#walkin_unit_id option:selected');
        var rate = parseFloat(unitOption.data('price') || 0);
        var capacity = parseInt(unitOption.data('capacity') || 0, 10);
        var title = $.trim(unitOption.data('title') || unitOption.text() || 'Select a chalet');
        var arrival = $('#walkin_arrivaldate').val();
        var departure = $('#walkin_departuredate').val();
        var adults = parseInt($('#walkin_adultno').val() || 0, 10);
        var kids = parseInt($('#walkin_kidsno').val() || 0, 10);
        var guests = adults + kids;
        var nights = 1;
        var submitButton = $('#walkin_submit_btn');
        var noteText = 'The order will be created first as unpaid. Confirm payment afterwards to unlock check-in.';

        if (arrival && departure) {
            var start = new Date(arrival);
            var end = new Date(departure);
            var diff = Math.round((end - start) / 86400000);
            nights = diff > 0 ? diff : 1;
        }

        var total = rate * nights;

        $('#walkin_preview_chalet').text(title || 'Select a chalet');
        $('#walkin_preview_nights').text(nights);
        $('#walkin_preview_guests').text(guests > 0 ? guests : 0);
        $('#walkin_preview_rate').text(moneyFormat(rate));
        $('#walkin_preview_total').text(moneyFormat(total));

        if (capacity > 0 && guests > capacity) {
            noteText = 'Selected chalet capacity is ' + capacity + ' guest(s). Reduce the occupants or pick a larger chalet.';
            $('#walkin_preview_note').removeClass('text-muted').addClass('text-danger').text(noteText);
            submitButton.prop('disabled', true);
            return;
        }

        $('#walkin_preview_note').removeClass('text-danger').addClass('text-muted').text(noteText);
        submitButton.prop('disabled', false);
    }

    $(document).on('change keyup', '#walkin_unit_id, #walkin_arrivaldate, #walkin_departuredate, #walkin_adultno, #walkin_kidsno', updateWalkinPreview);
    updateWalkinPreview();

    $(document).on('submit', '#walkin-frontdesk-form', function (event) {
        var form = $(this);
        var noteText = $('#walkin_preview_note').text();
        if (form.data('confirmed') === true) {
            form.removeData('confirmed');
            return true;
        }

        if ($('#walkin_submit_btn').prop('disabled')) {
            event.preventDefault();
            swal.fire('Error!', noteText || 'Please correct the walk-in details before creating the order.', 'error');
            return false;
        }

        event.preventDefault();
        swal.fire({
            title: 'Create unpaid walk-in order?',
            text: 'This will create the reservation/order now and leave payment pending until you confirm it from the front desk.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, create order'
        }).then(function (result) {
            if (result.value || result.isConfirmed) {
                form.data('confirmed', true);
                form.trigger('submit');
            }
        });

        return false;
    });
});
</script>
</body>
</html>
