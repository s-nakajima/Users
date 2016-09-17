<?php
/**
 * Unitテスト用Fixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('PluginFixture', 'PluginManager.Test/Fixture');

/**
 * Unitテスト用Fixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Fixture
 */
class Plugin4userFixture extends PluginFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Plugin';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'plugins';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'language_id' => '2',
			'key' => 'user_manager',
			'name' => 'User Manager',
			'weight' => '1',
			'type' => '2',
			'default_action' => 'user_manager/index',
		),
		array(
			'language_id' => '2',
			'key' => 'user_attributes',
			'name' => 'User Attributes',
			'weight' => '1',
			'type' => '2',
			'default_action' => 'user_attributes/index',
		),
		array(
			'language_id' => '2',
			'key' => 'rooms',
			'name' => 'Rooms Manager',
			'weight' => '1',
			'type' => '2',
			'default_action' => 'rooms/index',
		),
	);

}
