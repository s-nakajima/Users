<?php
/**
 * User Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('UsersModelTestBase', 'Users.Test/Case/Model');

/**
 * User Test Case
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Model
 */
class UserTest extends UsersModelTestBase {

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
	//public function testSaveUser() {
	//	// Test saveUser() creates admin user
	//	$this->User->saveUser(array(
	//		'User' => array(
	//			'username' => 'admin',
	//			'handlename' => 'admin',
	//			'password_again' => 'password',
	//			'password' => 'password',
	//		),
	//		'UsersLanguage' => array(),
	//	));
	//	$created = $this->User->find('all', array(
	//		'conditions' => array(
	//			'User.username' => 'admin'
	//		),
	//	));
	//
	//	// Expect only one admin record exist
	//	$this->assertEqual(count($created), 1);
	//	// Expect User#saveUser() succeed
	//	$this->assertTrue(is_numeric($this->User->id));
	//
	//	// Test saveUser() updates previous admin user for the second attempt
	//	$this->User->saveUser(array(
	//		'User' => array(
	//			'id' => $this->User->id,
	//			'username' => 'admin',
	//			'handlename' => 'admin2',
	//			'password_again' => 'password2',
	//			'password' => 'password2',
	//		),
	//		'UsersLanguage' => array(),
	//	));
	//	$updated = $this->User->find('all', array(
	//		'conditions' => array(
	//			'User.username' => 'admin'
	//		),
	//	));
	//
	//	// Expect only one admin record exist
	//	$this->assertEqual(count($updated), 1);
	//	// Expect created user and updated user to have same id
	//	$this->assertEqual($created[0]['User']['id'], $updated[0]['User']['id']);
	//	// Expect password changed
	//	$this->assertNotEqual($created[0]['User']['password'], $updated[0]['User']['password']);
	//}

/**
 * Test saveUser() w/ invalid request
 *
 * @return void
 */
	public function testSaveUserInvalid() {
		$result = $this->User->saveUser(array(
			'User' => array(
				'username' => 'admin',
				'handlename' => 'admin',
				'password_again' => 'password',
				'password' => 'wrong_password',
			),
			'UsersLanguage' => array(),
		));
		$this->assertFalse($result);
	}
}
