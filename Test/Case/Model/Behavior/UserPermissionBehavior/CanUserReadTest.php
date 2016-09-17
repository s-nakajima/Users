<?php
/**
 * UserPermissionBehavior::canUserRead()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * UserPermissionBehavior::canUserRead()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Model\Behavior\UserPermissionBehavior
 */
class UserPermissionBehaviorCanUserReadTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'users';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		//テストプラグインのロード
		NetCommonsCakeTestCase::loadTestPlugin($this, 'Users', 'TestUsers');
		$this->TestModel = ClassRegistry::init('TestUsers.TestUserPermissionBehaviorModel');
	}

/**
 * canUserRead()テストのDataProvider
 *
 * ### 戻り値
 *  - roleKey 会員権限キー
 *  - user ユーザデータ
 *  - expected 期待値
 *
 * @return array データ
 */
	public function dataProvider() {
		//0: データが取れなかった場合
		$index = 0;
		$result[$index] = array();
		$result[$index]['user'] = array();
		$result[$index]['expected'] = false;

		//1: 削除された場合
		$index = 1;
		$result[$index] = array();
		$result[$index]['user'] = array('User' => array('is_deleted' => true));
		$result[$index]['expected'] = false;

		//2: 編集OK
		$index = 2;
		$result[$index] = array();
		$result[$index]['user'] = array('User' => array('is_deleted' => false));
		$result[$index]['expected'] = true;

		return $result;
	}

/**
 * canUserRead()のテスト
 *
 * @param array $user ユーザデータ
 * @param bool $expected 期待値
 * @dataProvider dataProvider
 * @return void
 */
	public function testCanUserRead($user, $expected) {
		//テスト実施
		$result = $this->TestModel->canUserRead($user);

		//チェック
		$this->assertEquals($result, $expected);
	}

}
