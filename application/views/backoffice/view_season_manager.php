<?php
    $pricing_settings = (isset($pricing_settings) && is_array($pricing_settings)) ? $pricing_settings : array();

    $season_1_name = !empty($pricing_settings['season_1_name']) ? $pricing_settings['season_1_name'] : 'Season 1';
    $season_1_start_md = !empty($pricing_settings['season_1_start_md']) ? $pricing_settings['season_1_start_md'] : '';
    $season_1_end_md = !empty($pricing_settings['season_1_end_md']) ? $pricing_settings['season_1_end_md'] : '';

    $season_2_name = !empty($pricing_settings['season_2_name']) ? $pricing_settings['season_2_name'] : 'Season 2';
    $season_2_start_md = !empty($pricing_settings['season_2_start_md']) ? $pricing_settings['season_2_start_md'] : '';
    $season_2_end_md = !empty($pricing_settings['season_2_end_md']) ? $pricing_settings['season_2_end_md'] : '';

    $season_3_name = !empty($pricing_settings['season_3_name']) ? $pricing_settings['season_3_name'] : 'Season 3';
    $season_3_start_md = !empty($pricing_settings['season_3_start_md']) ? $pricing_settings['season_3_start_md'] : '';
    $season_3_end_md = !empty($pricing_settings['season_3_end_md']) ? $pricing_settings['season_3_end_md'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Alkebulan | Lacampagne Tropicana Resorts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php echo $css; ?>
</head>
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--fixed kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

<?php echo $header_mobile; ?>
<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
        <?php echo $side_menu; ?>

        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
            <div id="kt_header" class="kt-header kt-grid__item kt-header--fixed ">
                <?php echo $header_menu; ?>
                <div class="kt-header__topbar">
                    <?php echo $userbar_menu; ?>
                </div>
            </div>

            <div class="kt-content kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
                <div class="kt-subheader kt-grid__item" id="kt_subheader">
                    <div class="kt-container kt-container--fluid ">
                        <div class="kt-subheader__main">
                            <h3 class="kt-subheader__title">Season Manager</h3>
                            <span class="kt-subheader__separator kt-hidden"></span>
                            <div class="kt-subheader__breadcrumbs">
                                <a href="#" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                                <span class="kt-subheader__breadcrumbs-separator"></span>
                                <a href="#" class="kt-subheader__breadcrumbs-link"><strong>ALKEBULAN</strong></a>
                                <span class="kt-subheader__breadcrumbs-separator"></span>
                                <span class="kt-subheader__breadcrumbs-link">Global Season Dates</span>
                            </div>
                        </div>
                        <div class="kt-subheader__toolbar">
                            <div class="kt-subheader__wrapper">
                                <a href="<?php echo base_url() . 'Alkebulan/manage_chalet'; ?>" class="btn btn-brand btn-icon-sm">
                                    <i class="la la-home"></i> Chalet Manager
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="kt-portlet">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">Manage Shared Seasons</h3>
                                    </div>
                                </div>
                                <form class="kt-form kt-form--label-right" id="season_manager_form" method="post" action="<?php echo base_url() . 'Alkebulan/save_season_manager'; ?>">
                                    <div class="kt-portlet__body">
                                        <?php $season_manager_status = $this->session->flashdata('season_manager_status'); ?>
                                        <?php if (is_array($season_manager_status) && isset($season_manager_status['status'])) { ?>
                                            <div class="alert <?php echo !empty($season_manager_status['status']) ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                                                <?php echo html_escape((string) ($season_manager_status['message'] ?? 'Season settings updated.')); ?>
                                            </div>
                                        <?php } ?>
                                        <div class="alert alert-info" role="alert">
                                            Set the <strong>season names</strong> and <strong>date ranges</strong> once here for the whole resort. Each chalet will only keep its own prices.
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 30%;">Season Name</th>
                                                        <th style="width: 20%;">Start (DD-MM)</th>
                                                        <th style="width: 20%;">End (DD-MM)</th>
                                                        <th>Note</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="text" id="season_1_name" name="season_1_name" class="form-control" value="<?php echo html_escape($season_1_name); ?>" placeholder="e.g. Christmas / Peak"></td>
                                                        <td><input type="text" id="season_1_start_md" name="season_1_start_md" class="form-control season-month-day" maxlength="5" value="<?php echo html_escape($season_1_start_md); ?>" placeholder="20-12"></td>
                                                        <td><input type="text" id="season_1_end_md" name="season_1_end_md" class="form-control season-month-day" maxlength="5" value="<?php echo html_escape($season_1_end_md); ?>" placeholder="05-01"></td>
                                                        <td>Supports year-crossing ranges.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" id="season_2_name" name="season_2_name" class="form-control" value="<?php echo html_escape($season_2_name); ?>" placeholder="e.g. Easter"></td>
                                                        <td><input type="text" id="season_2_start_md" name="season_2_start_md" class="form-control season-month-day" maxlength="5" value="<?php echo html_escape($season_2_start_md); ?>" placeholder="01-04"></td>
                                                        <td><input type="text" id="season_2_end_md" name="season_2_end_md" class="form-control season-month-day" maxlength="5" value="<?php echo html_escape($season_2_end_md); ?>" placeholder="30-04"></td>
                                                        <td>Applies to all chalets automatically.</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="text" id="season_3_name" name="season_3_name" class="form-control" value="<?php echo html_escape($season_3_name); ?>" placeholder="e.g. Summer"></td>
                                                        <td><input type="text" id="season_3_start_md" name="season_3_start_md" class="form-control season-month-day" maxlength="5" value="<?php echo html_escape($season_3_start_md); ?>" placeholder="01-08"></td>
                                                        <td><input type="text" id="season_3_end_md" name="season_3_end_md" class="form-control season-month-day" maxlength="5" value="<?php echo html_escape($season_3_end_md); ?>" placeholder="31-08"></td>
                                                        <td>Booking/search uses the guest check-in date.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="kt-portlet__foot">
                                        <div class="row">
                                            <div class="col-lg-9 ml-lg-auto">
                                                <button type="submit" class="btn btn-primary">Save Seasons</button>
                                                <a href="<?php echo base_url() . 'Alkebulan/manage_chalet'; ?>" class="btn btn-secondary">Back to Chalet Manager</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="kt-portlet">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">How it works</h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    <ul class="mb-0" style="padding-left: 18px;">
                                        <li><strong>Season Manager</strong> controls the shared dates.</li>
                                        <li><strong>Chalet Manager</strong> only stores `Standard`, `Season 1`, `Season 2`, and `Season 3` prices per chalet.</li>
                                        <li>The booking engine charges the rate that matches the <strong>check-in date</strong>.</li>
                                        <li>If no season matches, the <strong>Standard rate</strong> is used.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php echo $footer; ?>
        </div>
    </div>
</div>

<?php echo $scroll_top; ?>
<?php echo $js; ?>
<script type="text/javascript">
    function normalizeSeasonDates() {
        $('.season-month-day').each(function() {
            var value = $.trim($(this).val() || '').replace(/\//g, '-');
            $(this).val(value);
        });
    }

    function hasPartialRange(startSelector, endSelector) {
        var startValue = $.trim($(startSelector).val() || '');
        var endValue = $.trim($(endSelector).val() || '');
        return ((startValue !== '' && endValue === '') || (startValue === '' && endValue !== ''));
    }

    function isValidDayMonth(value) {
        var cleaned = $.trim(value || '');
        if (cleaned === '') {
            return true;
        }

        var match = cleaned.match(/^(\d{2})-(\d{2})$/);
        if (!match) {
            return false;
        }

        var day = parseInt(match[1], 10);
        var month = parseInt(match[2], 10);

        if (month < 1 || month > 12 || day < 1 || day > 31) {
            return false;
        }

        var testDate = new Date(2024, month - 1, day);
        return (testDate.getFullYear() === 2024 && testDate.getMonth() === (month - 1) && testDate.getDate() === day);
    }

    $(document).on('change keyup', '.season-month-day', function() {
        normalizeSeasonDates();
    });

    function validateSeasonFormBeforeSubmit() {
        normalizeSeasonDates();

        var requiredNames = ['#season_1_name', '#season_2_name', '#season_3_name'];
        for (var n = 0; n < requiredNames.length; n++) {
            if ($.trim($(requiredNames[n]).val() || '') === '') {
                swal.fire('Error!', 'Please enter a name for each season.', 'error');
                return false;
            }
        }

        if (hasPartialRange('#season_1_start_md', '#season_1_end_md') || hasPartialRange('#season_2_start_md', '#season_2_end_md') || hasPartialRange('#season_3_start_md', '#season_3_end_md')) {
            swal.fire('Error!', 'Each season must have both a start and end date, or both can be left blank.', 'error');
            return false;
        }

        var dayMonthInputs = ['#season_1_start_md', '#season_1_end_md', '#season_2_start_md', '#season_2_end_md', '#season_3_start_md', '#season_3_end_md'];
        for (var i = 0; i < dayMonthInputs.length; i++) {
            var inputValue = $(dayMonthInputs[i]).val();
            if (!isValidDayMonth(inputValue)) {
                swal.fire('Error!', 'Please use Day-Month format (DD-MM), for example 25-12.', 'error');
                return false;
            }
        }

        return true;
    }

    function submitSeasonManagerAjax() {
        var formElement = document.getElementById('season_manager_form');
        var formData = new FormData(formElement);

        function toggleLoading(action) {
            if (typeof activity_indicator === 'function') {
                activity_indicator(action);
            }
        }

        $.ajax({
            url: "<?php echo base_url() . 'Alkebulan/save_season_manager'; ?>",
            type: 'POST',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                toggleLoading('start');
            },
            success: function(data) {
                toggleLoading('stop');
                try {
                    var items = $.parseJSON(data);
                    if (items.status === true) {
                        swal.fire('Successful!', items.message, 'success');
                    } else {
                        swal.fire('Error!', items.message, 'error');
                    }
                } catch (e) {
                    swal.fire('Error!', 'Unexpected server response while saving season settings. Please refresh and try again.', 'error');
                }
            },
            error: function() {
                toggleLoading('stop');
                swal.fire('Error!', 'Could not save season settings right now. Please check your connection and try again.', 'error');
            }
        });
    }

    $('#season_manager_form').on('submit', function(e) {
        e.preventDefault();
        if (!validateSeasonFormBeforeSubmit()) {
            return false;
        }
        submitSeasonManagerAjax();
        return false;
    });
</script>
</body>
</html>
