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

/* Member list */
$route['admin/member/list/delete/(:num)'] 	= 'admin/member/member_list/delete/$1';
$route['admin/member/list/delete'] 			= 'admin/member/member_list/delete';

$route['admin/member/list/edit/(:num)'] 	= 'admin/member/member_list/edit/$1';
$route['admin/member/list/edit'] 			= 'admin/member/member_list/edit';

$route['admin/member/list/add'] 			= 'admin/member/member_list/add';

$route['admin/member/list/(:num)']			= 'admin/member/member_list';
$route['admin/member/list'] 				= 'admin/member/member_list';

/* Member group */
$route['admin/member/group/delete/(:num)'] 	= 'admin/member/member_group/delete/$1';
$route['admin/member/group/delete'] 		= 'admin/member/member_group/delete';

$route['admin/member/group/edit/(:num)'] 	= 'admin/member/member_group/edit/$1';
$route['admin/member/group/edit'] 			= 'admin/member/member_group/edit';

$route['admin/member/group/add']	 		= 'admin/member/member_group/add';

$route['admin/member/group/(:num)'] 		= 'admin/member/member_group';
$route['admin/member/group'] 				= 'admin/member/member_group';

/* Other Routes */
$route['admin/links/(:num)'] 				= 'admin/links/index';
$route['admin/categories/(:num)'] 			= 'admin/categories/index';
$route['admin/pages/(:num)'] 				= 'admin/pages/index';
$route['admin/info/(:num)'] 				= 'admin/info/index';
$route['admin/module/(:num)'] 				= 'admin/module/index';
$route['admin/languages/(:num)'] 			= 'admin/languages/index';

/* Alias Routes */
require_once( BASEPATH .'database/DB'. EXT );
$db =& DB();

$query2 = $db->query("SELECT slug FROM start_info WHERE active = 1");

if($query2->num_rows() > 0)
{
	$result2 = $query2->result();
	
	foreach($result2 as $info)
	{
		$route[$info->slug] = 'info/index/'.$info->slug;
	}
}

$query = $db->query("SELECT a.query, a.keyword FROM start_url_alias a LEFT JOIN start_settings b ON a.language_id = b.value WHERE b.name = 'site_language'");

if($query->num_rows() > 0)
{
	$result = $query->result();
	
	foreach($result as $alias)
	{
		$route[$alias->keyword] = $alias->query;
	}
}

$route['default_controller'] = 'main';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
