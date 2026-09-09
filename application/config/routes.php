<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

// -------------------------------------------------------
// FARMSTAFF REGISTRY - Routes
// -------------------------------------------------------

// Default: public homepage
$route['default_controller'] = 'Home';
$route['404_override']       = 'Home/not_found';

// Public pages
$route['home']               = 'Home/index';
$route['about']              = 'Home/about';
$route['resources']          = 'Home/resources';
$route['for-transparency']   = 'Home/transparency';
$route['search-worker']      = 'Home/search_worker';
$route['worker/profile/(:any)'] = 'Home/worker_profile/$1';

// Employer auth
$route['login']              = 'Employer/login';
$route['register']           = 'Employer/register';
$route['logout']             = 'Employer/logout';
$route['verify-email/(:any)']= 'Employer/verify_email/$1';

// Employer dashboard
$route['dashboard']                          = 'Employer/dashboard';
$route['dashboard/workers']                  = 'Employer/workers';
$route['dashboard/register-worker']          = 'Employer/register_worker';
$route['dashboard/worker/(:num)']            = 'Employer/view_worker/$1';
$route['dashboard/worker/(:num)/edit']       = 'Employer/edit_worker/$1';
$route['dashboard/worker/(:num)/history']    = 'Employer/work_history/$1';
$route['dashboard/worker/(:num)/add-history']= 'Employer/add_work_history/$1';
$route['dashboard/worker/(:num)/skills']     = 'Employer/skills/$1';
$route['dashboard/worker/(:num)/attendance'] = 'Employer/attendance/$1';
$route['dashboard/worker/(:num)/incident']   = 'Employer/report_incident/$1';
$route['dashboard/worker/(:num)/rate']       = 'Employer/rate_worker/$1';
$route['dashboard/background-check']         = 'Employer/background_check';
$route['dashboard/rate-farm/(:num)']         = 'Employer/rate_farm/$1';
$route['dashboard/profile']                  = 'Employer/profile';
$route['dashboard/notifications']            = 'Employer/notifications';

// Admin
$route['admin']              = 'Farmadmin/login';
$route['admin/login']        = 'Farmadmin/login';
$route['admin/logout']       = 'Farmadmin/logout';
$route['admin/dashboard']    = 'Farmadmin/dashboard';
$route['admin/workers']      = 'Farmadmin/workers';
$route['admin/worker/(:num)']= 'Farmadmin/view_worker/$1';
$route['admin/employers']    = 'Farmadmin/employers';
$route['admin/employer/(:num)'] = 'Farmadmin/view_employer/$1';
$route['admin/incidents']    = 'Farmadmin/incidents';
$route['admin/incident/(:num)'] = 'Farmadmin/view_incident/$1';
$route['admin/incident/(:num)/review'] = 'Farmadmin/review_incident/$1';
$route['admin/farm-ratings'] = 'Farmadmin/farm_ratings';
$route['admin/farm-rating/(:num)/review'] = 'Farmadmin/review_farm_rating/$1';
$route['admin/reports']      = 'Farmadmin/reports';
$route['admin/audit']        = 'Farmadmin/audit';
$route['admin/trust-scores'] = 'Farmadmin/trust_scores';
$route['admin/settings']     = 'Farmadmin/settings';
$route['admin/create-admin'] = 'Farmadmin/create_admin';
$route['admin/(:any)']       = 'Farmadmin/$1';


/* End of file routes.php */
/* Location: ./application/config/routes.php */