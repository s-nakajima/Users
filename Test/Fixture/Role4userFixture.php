<?php
/**
 * UserRoleFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('RoleFixture', 'Roles.Test/Fixture');

/**
 * UserRoleFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Fixture
 */
class Role4userFixture extends RoleFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Role';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'roles';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		//会員の権限
		array(
			'language_id' => '2', 'key' => 'system_administrator', 'type' => '1',
			'name' => 'System administrator', 'description' => 'System administrator description', 'is_system' => 1,
		),
		array(
			'language_id' => '2',	'key' => 'administrator', 'type' => '1',
			'name' => 'Site administrator', 'description' => 'Site administrator description', 'is_system' => 1,
		),
		array(
			'language_id' => '2', 'key' => 'common_user', 'type' => '1',
			'name' => 'Common user', 'description' => 'Common user description', 'is_system' => 1,
		),
		array(
			'language_id' => '2', 'key' => 'test_user', 'type' => '1',
			'name' => 'Test user', 'description' => 'Test user description', 'is_system' => 0,
		),
		//ルーム内の役割
		array(
			'language_id' => '2', 'key' => 'room_administrator', 'type' => '2', 'name' => 'Room Manager', 'is_system' => 1,
		),
		array(
			'language_id' => '2', 'key' => 'chief_editor', 'type' => '2', 'name' => 'Chief editor', 'is_system' => 1,
		),
		array(
			'language_id' => '2', 'key' => 'editor', 'type' => '2', 'name' => 'Editor', 'is_system' => 1,
		),
		array(
			'language_id' => '2', 'key' => 'general_user', 'type' => '2', 'name' => 'General user', 'is_system' => 1,
		),
		array(
			'language_id' => '2', 'key' => 'visitor', 'type' => '2', 'name' => 'Visitor', 'is_system' => 1,
		)
	);

}
