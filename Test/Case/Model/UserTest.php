<?php
/**
 * User Test Case
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('User', 'Model');

/**
 * Summary for User Test Case
 */
class UserTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.users.user',
		'app.role',
		'app.language',
		'app.languages_role',
		'app.plugin',
		'app.frame',
		'app.box',
		'app.container',
		'app.page',
		'app.room',
		'app.group',
		'app.groups_language',
		'app.groups_user',
		'app.space',
		'app.block',
		'app.blocks_language',
		'app.boxes_page',
		'app.containers_page',
		'app.languages_page',
		'app.frames_language',
		'app.roles_plugin',
		'plugin.users.user_attribute',
		'app.roles_user_attribute',
		'app.languages_user_attribute',
		'app.languages_user_attributes_user',
		'plugin.users.user_attributes_user',
		'plugin.users.user_select_attribute',
		'plugin.users.user_select_attributes_user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('Users.User');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->User);

		parent::tearDown();
	}

/**
 * test saveAdmin()
 *
 * @return void
 */
	public function testSaveAdmin() {
		$this->User->saveAdmin(array(
			'User' => array(
				'username' => 'admin',
				'handlename' => 'admin',
				'password_again' => 'password',
				'password' => 'password',
			)
		));
		$this->assertTrue(is_numeric($this->User->id));
	}
/**
 * test saveAdmin()
 *
 * @return void
 */
	public function testSaveAdminInvalid() {
		$this->User->saveAdmin(array(
			'User' => array(
				'username' => 'admin',
				'handlename' => 'admin',
				'password_again' => 'password',
				'password' => 'wrong_password',
			)
		));
		$this->assertFalse($this->User->id);
	}
}
