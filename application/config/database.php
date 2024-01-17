<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
$dotenv->load();

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => $_ENV['ADMIN_DATABASE_USERNAME'],
	'password' => $_ENV['ADMIN_DATABASE_PASSWORD'],
	'database' => $_ENV['ADMIN_DATABASE_NAME'],
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
