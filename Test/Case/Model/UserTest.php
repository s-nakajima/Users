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
		'plugin.roles.role',
		'app.language',
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
		'app.boxes_page',
		'app.containers_page',
		'app.languages_page',
		'app.roles_plugin',
		'plugin.users.user_attribute',
		'app.roles_user_attribute',
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
 * Test saveAdmin()
 *
 * @return void
 */
	public function testSaveAdmin() {
		// Test saveAdmin() creates admin user
		$this->User->saveAdmin(array(
			'User' => array(
				'username' => 'admin',
				'handlename' => 'admin',
				'password_again' => 'password',
				'password' => 'password',
			)
		));
		$created = $this->User->find('all', array(
			'conditions' => array(
				'User.username' => 'admin'
			),
		));

		// Expect only one admin record exist
		$this->assertEqual(count($created), 1);
		// Expect User#saveAdmin() succeed
		$this->assertTrue(is_numeric($this->User->id));

		// Test saveAdmin() updates previous admin user for the second attempt
		$this->User->saveAdmin(array(
			'User' => array(
				'username' => 'admin',
				'handlename' => 'admin2',
				'password_again' => 'password2',
				'password' => 'password2',
			)
		));
		$updated = $this->User->find('all', array(
			'conditions' => array(
				'User.username' => 'admin'
			),
		));

		// Expect only one admin record exist
		$this->assertEqual(count($updated), 1);
		// Expect created user and updated user to have same id
		$this->assertEqual($created[0]['User']['id'], $updated[0]['User']['id']);
		// Expect password changed
		$this->assertNotEqual($created[0]['User']['password'], $updated[0]['User']['password']);
	}

/**
 * Test saveAdmin() w/ invalid request
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
