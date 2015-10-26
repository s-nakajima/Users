<?php
/**
 * Users App Model Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('User', 'Users.Model');
App::uses('YACakeTestCase', 'NetCommons.TestSuite');

/**
 * Users App Model Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Model
 */
class UsersModelTestBase extends YACakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.data_types.data_type',
		'plugin.data_types.data_type_choice',
		'plugin.users.users_language',
		'plugin.user_roles.user_attributes_role',
		'plugin.user_attributes.user_attribute',
		'plugin.user_attributes.user_attribute_choice',
		'plugin.user_attributes.user_attribute_setting',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
	}
}
