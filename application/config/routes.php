<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['auth/login'] = 'auth/login';
$route['auth/logout'] = 'auth/logout';


$route['user/information/(:any)'] = 'user/user_information/$1';
$route['auth/edit-user/(:any)'] = 'auth/edit_user/$1';

$route['auth/user/create'] = 'auth/create_user';


$route['user/session'] = 'user/session';


$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
