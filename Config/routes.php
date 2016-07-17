<?php
/**
 * Users routes configuration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

$params = array();

Router::connect(
	'/users/users/download/:user_id/:field_name/:size',
	['plugin' => 'users', 'controller' => 'users', 'action' => 'download'],
	['user_id' => '[0-9]+', 'size' => 'big|medium|small|thumb']
);
Router::connect(
	'/users/users/download/:user_id/:field_name',
	['plugin' => 'users', 'controller' => 'users', 'action' => 'download', 'size' => 'medium'],
	['user_id' => '[0-9]+', 'size' => 'medium']
);
Router::connect(
	'/users/users/:action/:user_id',
	['plugin' => 'users', 'controller' => 'users'],
	['user_id' => '[0-9]+']
);
