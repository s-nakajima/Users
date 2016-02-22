<?php
/**
 * User::prepare()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsModelTestCase', 'NetCommons.TestSuite');

/**
 * User::prepare()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Model\User
 */
class UserPrepareTest extends NetCommonsModelTestCase {

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
	protected $_methodName = 'prepare';

/**
 * prepare()用DataProvider
 *
 * ### 戻り値
 *  - force 強制的に取得するフラグ
 *  - count getUserAttributesForLayout()の実行回数
 *
 * @return array テストデータ
 */
	public function dataProvider() {
		return array(
			array('force' => false, 'count' => 1),
			array('force' => false, 'count' => 0),
			array('force' => true, 'count' => 1),
		);
	}

/**
 * prepare()のテスト
 *
 * @param bool $force 強制的に取得するフラグ
 * @param int $count getUserAttributesForLayout()の実行回数
 * @dataProvider dataProvider
 * @return void
 */
	public function testPrepare($force, $count) {
		$model = $this->_modelName;
		$methodName = $this->_methodName;

		//データ生成
		$returnData = array();
		$returnData = Hash::insert($returnData, '1.1.1.UserAttribute.id', '1');
		$returnData = Hash::insert($returnData, '1.1.1.UserAttributeSetting', array(
			'data_type_key' => 'img',
			'user_attribute_key' => 'avatar',
		));

		$this->_mockForReturn($model, 'UserAttributes.UserAttribute', 'getUserAttributesForLayout', $returnData, $count);
		$this->_mockForReturnTrue($model, 'Users.User', 'uploadSettings');

		//テスト実施
		if ($force || $count === 0) {
			$this->$model->userAttributeData = $returnData[1][1];
		}
		$this->$model->$methodName($force);

		//チェック
		$this->assertEquals($returnData[1][1], $this->$model->userAttributeData);
	}

}
