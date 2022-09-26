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
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
#$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['default_controller'] = "parent_controller";

$route['confirmation'] = "parent_controller/show_confirmation";
$route['test'] = "parent_controller/test";
$route['file_download/:any/:any'] = "parent_controller/file_download";
$route['user_login_time_update'] = "parent_controller/user_login_time_update";

# user authentication
$route['login'] = 'parent_controller/login';
$route['logout'] = 'parent_controller/logout';

$route['change_password'] = 'parent_controller/change_password';
$route['change_password/:any'] = 'parent_controller/change_password';
$route['forgot_password'] = 'parent_controller/forgot_password';

# user management
$route['create_user']='configuration_controller/create_user';
$route['manage_user']='configuration_controller/manage_user';
$route['manage_user/:any']='configuration_controller/manage_user';
$route['manage_user/:any/:any']='configuration_controller/manage_user';
$route['create_role']='configuration_controller/create_role';
$route['manage_role']='configuration_controller/manage_role';
$route['manage_role/:any']='configuration_controller/manage_role';
$route['manage_role/:any/:any']='configuration_controller/manage_role';

# Vendor

$route['create_vendor']='master_data_controller/create_vendor';
$route['manage_vendor']='master_data_controller/manage_vendor';
$route['manage_vendor/:any']='master_data_controller/manage_vendor';
$route['manage_vendor/:any/:any']='master_data_controller/manage_vendor';

# configuration
$route['create_option']='configuration_controller/create_option';
$route['manage_option']='configuration_controller/manage_option';
$route['manage_option/:any']='configuration_controller/manage_option';
$route['manage_option/:any/:any']='configuration_controller/manage_option';

$route['create_configuration']='configuration_controller/create_configuration';
$route['manage_configuration']='configuration_controller/manage_configuration';
$route['manage_configuration/:any']='configuration_controller/manage_configuration';
$route['manage_configuration/:any/:any']='configuration_controller/manage_configuration';

$route['lease_onboarding']='configuration_controller/lease_onboarding';
$route['manage_lease_onboarding']='configuration_controller/manage_lease_onboarding';
$route['manage_lease_onboarding/:any']='configuration_controller/manage_lease_onboarding';
$route['manage_lease_onboarding/:any/:any']='configuration_controller/manage_lease_onboarding';

# Receivable
$route['create_received']='ReceivableController/createReceived';
$route['manage_received']='ReceivableController/manageReceived';
$route['manage_received/:any']='ReceivableController/manageReceived';
$route['manage_received/:any/:any']='ReceivableController/manageReceived';

# Payable
$route['create_payment']='PayableController/createPayment';
$route['manage_payment']='PayableController/managePayment';
$route['manage_payment/:any']='PayableController/managePayment';
$route['manage_payment/:any/:any']='PayableController/managePayment';

# report folder
$route['receivable_summary_report']='report_controller/receivable_summary_report';
$route['payable_summary_report']='report_controller/payable_summary_report';
$route['lease_history']='report_controller/lease_history';
$route['cost_center_report']='report_controller/cost_center_report';

$route['accounts_journal']='report_controller/accounts_journal';
$route['accounts_journal/:any']='report_controller/accounts_journal';
$route['consolidate_summary']='report_controller/consolidate_summary';
$route['consolidate_summary/:any']='report_controller/consolidate_summary';
$route['consolidate_summary_by_year']='report_controller/consolidate_summary_by_year';
$route['consolidate_summary_by_year/:any']='report_controller/consolidate_summary_by_year';
$route['region_wise_lease_information']='report_controller/region_wise_lease_information';
$route['region_wise_lease_information/:any']='report_controller/region_wise_lease_information';

# data migration
$route['data_migration']='configuration_controller/data_migration';
$route['data_migration_additional_info']='configuration_controller/data_migration_additional_info';