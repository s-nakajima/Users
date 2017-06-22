<?php
/**
 * UsersControllerDownloadAvatarTest
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('NetCommonsControllerTestCase', 'NetCommons.TestSuite');
App::uses('UserRole', 'UserRoles.Model');
App::uses('User', 'Users.Model');
App::uses('UsersControllerTestCase', 'Users.TestSuite');

/**
 * UsersAvatarController::download()のテスト
 *
 */
class UsersControllerDownloadAvatarTest extends UsersControllerTestCase {

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'users_avatar';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->_testApp = App::pluginPath('Users') . DS . 'Test' . DS . 'test_app' . DS;
		$this->_testWebroot = $this->_testApp . 'webroot' . DS;
		$this->_testTmp = $this->_testApp . 'tmp' . DS;
		$this->_testUploadPath = $this->_testWebroot . 'files' . DS . 'upload_file' . DS . 'real_file_name' . DS;

		$this->generateNc(Inflector::camelize($this->_controller), array(
			'components' => array(
				'Files.Download' => array('doDownloadByUploadFileId')
			)
		));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		UserAttribute::$userAttributes = array();
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * UserAttributeデータ取得
 *
 * @return void
 */
	private function __getUserAttribute() {
		return array(
			'1' => array('1' => array('1' => array(
				'UserAttributeSetting' => array(
					'id' => '1',
					'user_attribute_key' => 'avatar',
					'data_type_key' => 'img',
					'row' => '1',
					'col' => '1',
					'weight' => '1',
					'required' => '0',
					'display' => '1',
					'only_administrator_readable' => '0',
					'only_administrator_editable' => '0',
					'self_public_setting' => '0',
				),
				'UserAttributesRole' => array(
					'other_readable' => '1'
				)
			)))
		);
	}

/**
 * UserAttributeデータ取得
 *
 * @param string $userId ユーザID
 * @param bool $public 公開フラグ
 * @return void
 */
	private function __getAvatarPath($userId, $public) {
		if ($userId === '2') {
			if ($public) {
				$avatarPath = $this->_testUploadPath .
						'1' . DS . 'thumb_38bfb11bf48fc2f56d2ca2d796d0b0af.gif';

				$this->controller->Download->expects($this->at(0))->method('doDownloadByUploadFileId')
					->with('1', array('size' => 'thumb'))
					->will($this->returnCallback(function () {
						$this->controller->response->file(
							$this->_testUploadPath . '1' . DS . 'thumb_38bfb11bf48fc2f56d2ca2d796d0b0af.gif'
						);
						return $this->controller->response;
					}));

			} else {
				$avatarPath = $this->_testTmp . 'avatar' . DS . '89a13a117ac9f759587ad54c400a7e16.png';
				$this->_mockForReturn(
					'Users.User', 'temporaryAvatar', $avatarPath
				);
			}

		} elseif ($userId === '3') {
			if ($public) {
				$avatarPath = $this->_testUploadPath .
								'2' . DS . 'thumb_7bb5a56eb63531bcb40bda56aafceef3.png';

				$this->controller->Download->expects($this->at(0))->method('doDownloadByUploadFileId')
					->with('2', array('size' => 'thumb'))
					->will($this->returnCallback(function () {
						$this->controller->response->file(
							$this->_testUploadPath . '2' . DS . 'thumb_7bb5a56eb63531bcb40bda56aafceef3.png'
						);
						return $this->controller->response;
					}));
			} else {
				$avatarPath = $this->_testTmp . 'avatar' . DS . 'b419157fce2b95196a715ac2df0b4a83.png';
				$this->_mockForReturn(
					'Users.User', 'temporaryAvatar', $avatarPath
				);
			}

		} elseif ($userId === '7') {
			$avatarPath = $this->_testTmp . 'avatar' . DS . '5fe6005bf6e415c950c011fb65f12b8f.png';
			$this->_mockForReturn(
				'Users.User', 'temporaryAvatar', $avatarPath
			);

		} elseif ($userId === '1') {
			$avatarPath = $this->_testTmp . 'avatar' . DS . 'a7e1833849089f83c4caabc93a168e99.png';
			$this->_mockForReturn(
				'Users.User', 'temporaryAvatar', $avatarPath
			);
		} else {
			$avatarPath = App::pluginPath('Users') . 'webroot' . DS . 'img' . DS . User::AVATAR_THUMB;
		}

		return $avatarPath;
	}

/**
 * download()アクションのGetリクエストテスト
 *
 * @return void
 */
	public function testDownloadGet() {
		//ログイン
		TestAuthGeneral::login($this, UserRole::USER_ROLE_KEY_ADMINISTRATOR);

		//事前準備
		$userId = '2';
		$avatarPath = $this->__getAvatarPath($userId, true);

		$userAttribute = $this->__getUserAttribute();
		$this->_mockForReturn(
			'UserAttributes.UserAttributeSetting', 'find', $userAttribute[1], 'any'
		);

		//テスト実行
		$this->_testGetAction(
			array('controller' => 'users', 'action' => 'download', 'key' => $userId, 'avatar', 'thumb'),
			null, null, 'view'
		);

		//チェック
		$this->assertEquals(filesize($avatarPath), $this->controller->response->header()['Content-Length']);
	}

/**
 * avatarを登録していない場合のテスト(UploadFile.avatar.field_nameがない)
 *
 * @return void
 */
	public function testNoSavedAvatar() {
		//事前準備
		$userId = '1';
		$avatarPath = $this->__getAvatarPath($userId, true);

		$userAttribute = $this->__getUserAttribute();
		$this->_mockForReturn(
			'UserAttributes.UserAttributeSetting', 'find', $userAttribute[1], 'any'
		);

		//テスト実行
		$this->_testGetAction(
			array('controller' => 'users', 'action' => 'download', 'key' => $userId, 'avatar', 'thumb'),
			null, null, 'view'
		);

		//チェック
		$this->assertEquals(filesize($avatarPath), $this->controller->response->header()['Content-Length']);
	}

/**
 * UserAttributeSetting.attribute_key=avatarがない場合のテスト(イレギュラー)
 *
 * @return void
 */
	public function testNoUserAttribute() {
		//事前準備
		$userId = '1';
		$avatarPath = $this->__getAvatarPath($userId, true);

		//テスト実行
		$this->_testGetAction(
			array('controller' => 'users', 'action' => 'download', 'key' => $userId, 'avatar', 'thumb'),
			null, null, 'view'
		);

		//チェック
		$this->assertEquals(filesize($avatarPath), $this->controller->response->header()['Content-Length']);
	}

/**
 * 他人が閲覧している場合のテスト
 *
 * ### 戻り値
 *  - userId ユーザID
 *  -
 *  - expectedDisplay 期待値(表示有無)
 *
 * @return array テストデータ
 */
	public function dataProviderOthers() {
		return array(
			//user_id=2は、User.is_avatar_public=0のもの
			// * User.is_avatar_public=0のテスト
			array(
				'data' => ['user_id' => '2', 'self_public_setting' => '1', 'other_readable' => '1', 'display' => '1'],
				'expectedDisplay' => false
			),
			// * self_public_setting=0のテスト⇒User.is_avatar_public=1と同じ扱い
			array(
				'data' => ['user_id' => '2', 'self_public_setting' => '0', 'other_readable' => '1', 'display' => '1'],
				'expectedDisplay' => true
			),
			// * other_readable=0(管理者のみ読み取り可)
			array(
				'data' => ['user_id' => '2', 'self_public_setting' => '1', 'other_readable' => '0', 'display' => '1'],
				'expectedDisplay' => false
			),
			// * display=0(項目の非表示)
			array(
				'data' => ['user_id' => '2', 'self_public_setting' => '1', 'other_readable' => '1', 'display' => '0'],
				'expectedDisplay' => false
			),

			//user_id=3は、User.is_avatar_public=1のもの
			// * User.is_avatar_public=1のテスト
			array(
				'data' => ['user_id' => '3', 'self_public_setting' => '1', 'other_readable' => '1', 'display' => '1'],
				'expectedDisplay' => true
			),
			// * self_public_setting=0のテスト⇒User.is_avatar_public=1と同じ扱い
			array(
				'data' => ['user_id' => '3', 'self_public_setting' => '0', 'other_readable' => '1', 'display' => '1'],
				'expectedDisplay' => true
			),
			// * other_readable=0(管理者のみ読み取り可)
			array(
				'data' => ['user_id' => '3', 'self_public_setting' => '1', 'other_readable' => '0', 'display' => '1'],
				'expectedDisplay' => false
			),
			// * display=0(項目の非表示)
			array(
				'data' => ['user_id' => '2', 'self_public_setting' => '1', 'other_readable' => '1', 'display' => '0'],
				'expectedDisplay' => false
			),
		);
	}

/**
 * 他人が閲覧している場合のテスト
 *
 * @param array $data 他人のアバター閲覧条件
 * @param bool $expectedDisplay 期待値(表示有無)
 * @dataProvider dataProviderOthers
 * @return void
 */
	public function testOthers($data, $expectedDisplay) {
		//事前準備
		$avatarPath = $this->__getAvatarPath($data['user_id'], $expectedDisplay);

		$userAttribute = $this->__getUserAttribute();
		$userAttribute[1]['UserAttributeSetting']['self_public_setting'] = $data['self_public_setting'];
		$userAttribute[1]['UserAttributesRole']['other_readable'] = $data['other_readable'];
		$userAttribute[1]['UserAttributeSetting']['display'] = $data['display'];
		$this->_mockForReturn(
			'UserAttributes.UserAttributeSetting', 'find', $userAttribute[1], 'any'
		);

		//テスト実行
		$this->_testGetAction(
			array('controller' => 'users', 'action' => 'download', 'key' => $data['user_id'], 'avatar', 'thumb'),
			null, null, 'view'
		);

		//チェック
		$this->assertEquals(filesize($avatarPath), $this->controller->response->header()['Content-Length']);
	}

/**
 * 削除されたユーザテスト
 *
 * @return void
 */
	public function testDeleted() {
		//事前準備
		$userId = '7';
		$avatarPath = $this->__getAvatarPath($userId, true);

		$userAttribute = $this->__getUserAttribute();
		$this->_mockForReturn(
			'UserAttributes.UserAttributeSetting', 'find', $userAttribute[1], 'any'
		);

		//テスト実行
		$this->_testGetAction(
			array('controller' => 'users', 'action' => 'download', 'key' => $userId, 'avatar', 'thumb'),
			null, null, 'view'
		);

		//チェック
		$this->assertEquals(filesize($avatarPath), $this->controller->response->header()['Content-Length']);
	}

/**
 * 存在しないユーザテスト
 *
 * @return void
 */
	public function testNotExists() {
		//事前準備
		$userId = '99';
		$avatarPath = $this->__getAvatarPath($userId, true);

		$userAttribute = $this->__getUserAttribute();
		$this->_mockForReturn(
			'UserAttributes.UserAttribute', 'find', $userAttribute[1], 'any'
		);

		//テスト実行
		$this->_testGetAction(
			array('controller' => 'users', 'action' => 'download', 'key' => $userId, 'avatar', 'thumb'),
			null, null, 'view'
		);

		//チェック
		$this->assertEquals(filesize($avatarPath), $this->controller->response->header()['Content-Length']);
	}

}
