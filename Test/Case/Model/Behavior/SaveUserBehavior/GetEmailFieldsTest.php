<?php
/**
 * SaveUserBehavior::getEmailFields()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('UsersModelTestCase', 'Users.TestSuite');

/**
 * SaveUserBehavior::getEmailFields()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Model\Behavior\SaveUserBehavior
 */
class SaveUserBehaviorGetEmailFieldsTest extends UsersModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Users', 'TestUsers');
		$this->TestModel = ClassRegistry::init('TestUsers.TestSaveUserBehaviorModel');
	}

/**
 * getEmailFields()のテスト
 *
 * @return void
 */
	public function testGetEmailFields() {
		//テスト実施
		$result = $this->TestModel->getEmailFields();

		//チェック
		$this->assertEquals($result, array(0 => 'email', 1 => 'moblie_mail'));
	}

}
