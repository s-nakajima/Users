<?php
/**
 * User::existsUser()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * User::existsUser()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Model\User
 */
class UserExistsUserTest extends NetCommonsModelTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.users.user',
		'plugin.users.user_select_count',
		'plugin.users.users_language',
	);

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'users';

/**
 * Model name
 *
 * @var string
 */
	protected $_modelName = 'User';

/**
 * Method name
 *
 * @var string
 */
	protected $_methodName = 'existsUser';

/**
 * Delete用DataProvider
 *
 * ### 戻り値
 *  - userId ユーザID
 *  - expected 期待値
 *
 * @return array テストデータ
 */
	public function dataProvider() {
		return array(
			array('userId' => null, 'expected' => false),
			array('userId' => '1', 'expected' => true),
			array('userId' => array('1', '3'), 'expected' => true),
			array('userId' => array('1', '99'), 'expected' => false),
		);
	}

/**
 * existsUser()のテスト
 *
 * @param int|array $userId ユーザID
 * @param bool $expected 期待値
 * @dataProvider dataProvider
 * @return void
 */
	public function testExistsUser($userId, $expected) {
		//テスト実施
		$model = $this->_modelName;
		$methodName = $this->_methodName;
		$result = $this->$model->$methodName($userId);

		//チェック
		$this->assertEquals($expected, $result);
	}

}
