<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['auth/login'] = 'auth/login';
$route['auth/logout'] = 'auth/logout';

$route['user/session'] = 'user/session';

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
