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
		//'plugin.blocks.block',
		//'plugin.boxes.box',
		//'plugin.boxes.boxes_page',
		//'plugin.frames.frame',
		//'plugin.containers.container',
		//'plugin.containers.containers_page',
		//'plugin.groups.group',
		////'plugin.groups.groups_language',
		////'plugin.groups.groups_user',
		//'plugin.m17n.language',
		//'plugin.pages.languages_page',
		//'plugin.pages.page',
		//'plugin.plugin_manager.plugin',
		//'plugin.public_space.space',
		'plugin.roles.role',
		////'plugin.roles.roles_plugin',
		////'plugin.roles.roles_user_attribute',
		//'plugin.rooms.room',
		'plugin.users.user',
		//'plugin.users.user_select_attribute',
		//'plugin.users.user_select_attributes_user',
		'plugin.users.user_attributes_user',
		'plugin.user_attributes.user_attribute',
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
 * Test saveUser()
 *
 * @return void
 */
	public function testSaveUser() {
		// Test saveUser() creates admin user
		$this->User->saveUser(array(
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
		// Expect User#saveUser() succeed
		$this->assertTrue(is_numeric($this->User->id));

		// Test saveUser() updates previous admin user for the second attempt
		$this->User->saveUser(array(
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
 * Test saveUser() w/ invalid request
 *
 * @return void
 */
	public function testSaveUserInvalid() {
		$this->User->saveUser(array(
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
