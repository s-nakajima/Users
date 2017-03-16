<?php
/**
 * UsersController::view()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('UsersControllerTestCase', 'Users.TestSuite');
App::uses('UserAttribute', 'UserAttributes.Model');

/**
 * UsersController::view()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Controller\UsersController
 */
class UsersControllerViewTest extends UsersControllerTestCase {

/**
 * Controller name
 *
 * @var string
 */
	protected $_controller = 'users';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		UserAttribute::$userAttributes = null;
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//ログアウト
		TestAuthGeneral::logout($this);

		parent::tearDown();
	}

/**
 * UserManagerController::view()テストのDataProvider
 *
 * ### 戻り値
 *  - userId ユーザID
 *  - loginUserId ログイン中のUserId
 *
 * @return array データ
 */
	public function dataProvider() {
		$result = array();

		$result[0]['userId'] = '1';
		$result[0]['loginUserId'] = '1';

		$result[1]['userId'] = '2';
		$result[1]['loginUserId'] = '1';

		$result[2]['userId'] = '4';
		$result[2]['loginUserId'] = '1';

		$result[3]['userId'] = '5';
		$result[3]['loginUserId'] = '1';

		$result[4]['userId'] = '7';
		$result[4]['loginUserId'] = '1';

		$result[5]['userId'] = '9999';
		$result[5]['loginUserId'] = '1';

		$result[6]['userId'] = '1';
		$result[6]['loginUserId'] = '2';

		$result[7]['userId'] = '2';
		$result[7]['loginUserId'] = '2';

		$result[8]['userId'] = '4';
		$result[8]['loginUserId'] = '2';

		$result[9]['userId'] = '5';
		$result[9]['loginUserId'] = '2';

		$result[10]['userId'] = '7';
		$result[10]['loginUserId'] = '2';

		$result[11]['userId'] = '9999';
		$result[11]['loginUserId'] = '2';

		$result[12]['userId'] = '1';
		$result[12]['loginUserId'] = '4';

		$result[13]['userId'] = '2';
		$result[13]['loginUserId'] = '4';

		$result[14]['userId'] = '4';
		$result[14]['loginUserId'] = '4';

		$result[15]['userId'] = '5';
		$result[15]['loginUserId'] = '4';

		$result[16]['userId'] = '7';
		$result[16]['loginUserId'] = '4';

		$result[17]['userId'] = '9999';
		$result[17]['loginUserId'] = '4';

		return $result;
	}

/**
 * view()アクションのGetリクエストテスト
 *
 * @param int $userId ユーザID
 * @param int $loginUserId ログイン中のUserId
 * @return void
 * @dataProvider dataProvider
 */
	public function testViewGet($userId, $loginUserId) {
		//ログイン
		if ($loginUserId === '2') {
			TestAuthGeneral::login($this, UserRole::USER_ROLE_KEY_ADMINISTRATOR);
		} elseif ($loginUserId === '4') {
			TestAuthGeneral::login($this, UserRole::USER_ROLE_KEY_COMMON_USER);
		} else {
			TestAuthGeneral::login($this);
		}

		//テストデータ
		putenv('HTTP_X_REQUESTED_WITH=XMLHttpRequest');

		//テスト実行
		if ($userId === '7' || $userId === '9999') {
			$this->_testGetAction(
				array('action' => 'view', 'user_id' => $userId), null, 'BadRequestException', 'json'
			);
		} else {
			$this->_testGetAction(
				array('action' => 'view', 'user_id' => $userId), null, null, 'view'
			);

			//チェック
			$expected = $this->__getExpected($userId);
			$this->__assertUser($expected);

			$this->__assertUserView($expected, $loginUserId);
			$this->__assertRoomsView($expected, $loginUserId);
			$this->__assertGroupsView($expected, $loginUserId);
		}

		//後処理
		putenv('HTTP_X_REQUESTED_WITH=');
	}

/**
 * $this->viewVars['user']のチェック
 *
 * @param array $expected 期待値
 * @return void
 */
	private function __assertUser($expected) {
		//チェック
		$this->assertEquals($this->vars['user']['User']['id'], Hash::get($expected, 'User.id'));
		$this->assertEquals($this->vars['user']['UsersLanguage'][0]['id'], Hash::get($expected, 'UsersLanguage.0.id'));
		//$this->assertEquals($this->vars['user']['UsersLanguage'][1]['id'], Hash::get($expected, 'UsersLanguage.1.id'));
		$this->assertEquals($this->vars['user']['UsersLanguage'][0]['language_id'], '2');
		//$this->assertEquals($this->vars['user']['UsersLanguage'][1]['language_id'], '1');
		$this->assertEquals($this->vars['user']['UsersLanguage'][0]['user_id'], Hash::get($expected, 'User.id'));
		//$this->assertEquals($this->vars['user']['UsersLanguage'][1]['user_id'], Hash::get($expected, 'User.id'));
		$this->assertEquals($this->vars['user']['TrackableCreator']['id'], Hash::get($expected, 'TrackableCreator.id'));
		$this->assertEquals($this->vars['user']['TrackableUpdater']['id'], Hash::get($expected, 'TrackableUpdater.id'));
		$this->assertEquals($this->vars['user']['Role']['id'], Hash::get($expected, 'Role.id'));
		$this->assertEquals($this->vars['user']['UserRoleSetting']['id'], Hash::get($expected, 'UserRoleSetting.id'));

		if (isset($expected['UploadFile'])) {
			$this->assertEquals(
				$this->vars['user']['UploadFile']['avatar']['id'], Hash::get($expected, 'UploadFile.avatar.id')
			);
			$this->assertEquals(
				$this->vars['user']['UploadFile']['avatar']['content_key'], Hash::get($expected, 'UploadFile.avatar.content_key')
			);
		}
	}

/**
 * viewのUserチェック
 *
 * @param array $expected 期待値
 * @param int $loginUserId ログインしているユーザID
 * @return void
 */
	private function __assertUserView($expected, $loginUserId) {
		$this->assertTextContains(
			'/users/users/download/' . Hash::get($expected, 'User.id') . '/avatar/medium', $this->view
		);
		$this->assertTextContains(
			'>ハンドル</div><div class="form-control nc-data-label">' . Hash::get($expected, 'User.handlename') . '<', $this->view
		);
		if ($loginUserId === '1' || $loginUserId === '2') {
			$this->assertTextContains(
				'>ログインID</div><div class="form-control nc-data-label">' . Hash::get($expected, 'User.username') . '<', $this->view
			);
			$this->assertTextContains(
				'>' . Hash::get($expected, 'UsersLanguage.0.name') . '<', $this->view
			);
		} elseif ($loginUserId === Hash::get($expected, 'User.id')) {
			$this->assertTextNotContains(
				'>ログインID</div><div class="form-control nc-data-label">' . Hash::get($expected, 'User.username') . '<', $this->view
			);
			$this->assertTextContains(
				'>' . Hash::get($expected, 'UsersLanguage.0.name') . '<', $this->view
			);
		} else {
			$this->assertTextNotContains(
				'>ログインID</div><div class="form-control nc-data-label">' . Hash::get($expected, 'User.username') . '<', $this->view
			);
			$this->assertTextNotContains(
				'>' . Hash::get($expected, 'UsersLanguage.0.name') . '<', $this->view
			);
		}

		//管理者のみしか見れない項目は、会員管理で表示させるため、ここでは見せない。
		$this->assertTextNotContains(
			'>' . Hash::get($expected, 'UsersLanguage.0.search_keywords') . '<', $this->view
		);

		//自分自身の会員情報のチェック
		if (Hash::get($expected, 'User.id') === $loginUserId) {
			$this->assertTextContains(
				'/users/users/edit/' . Hash::get($expected, 'User.id'), $this->view
			);
		} else {
			$this->assertTextNotContains(
				'/users/users/edit/' . Hash::get($expected, 'User.id'), $this->view
			);
		}
	}

/**
 * viewのRoomsチェック
 *
 * @param array $expected 期待値
 * @param int $loginUserId ログインしているユーザID
 * @return void
 */
	private function __assertRoomsView($expected, $loginUserId) {
		$view = preg_replace('/[>][\s]+([^a-z])/u', '>$1', $this->view);
		$view = preg_replace('/[\s]+</u', '<', $view);

		if ($loginUserId === '1' || $loginUserId === '2' || $loginUserId === Hash::get($expected, 'User.id')) {
			$this->assertTextContains('>' . __d('users', 'Rooms') . '<', $view);
			$this->assertTextContains('<td>Public</td>', $view);
			$this->assertTextContains('<td><span class="rooms-tree"></span>Public room</td>', $view);

			if (Hash::get($expected, 'User.id') === '1' || Hash::get($expected, 'User.id') === '2') {
				$this->assertTextContains('<td>Community room 1</td>', $view);
				$this->assertTextContains('<td>Community room 2</td>', $view);

			} elseif (Hash::get($expected, 'User.id') === '4') {
				$this->assertTextNotContains('<td>Community room 1</td>', $view);
				$this->assertTextContains('<td>Community room 2</td>', $view);

			} elseif (Hash::get($expected, 'User.id') === '5') {
				$this->assertTextNotContains('<td>Community room 2</td>', $view);
				$this->assertTextContains('<td>' . __d('rooms', 'Not found.') . '</td>', $view);
			}
		} else {
			$this->assertTextNotContains('>' . __d('users', 'Rooms') . '<', $view);
			$this->assertTextNotContains('<td>Public</td>', $view);
			$this->assertTextNotContains('<td>Public room</td>', $view);
			$this->assertTextNotContains('<td>Community room 1</td>', $view);
			$this->assertTextNotContains('<td>Community room 2</td>', $view);
		}
	}

/**
 * viewのGroupsチェック
 *
 * @param array $expected 期待値
 * @param int $loginUserId ログインしているユーザID
 * @return void
 */
	private function __assertGroupsView($expected, $loginUserId) {
		$view = preg_replace('/[>][\s]+([^a-z])/u', '>$1', $this->view);
		$view = preg_replace('/[\s]+</u', '<', $view);

		if ($loginUserId === '1' && Hash::get($expected, 'User.id') === '1') {
			$this->assertTextContains('>' . __d('groups', 'Groups management') . '<', $view);
			$this->assertTextContains('/groups/groups/add', $view);
			$this->assertTextContains('Group 1<span class="badge">1</span>', $view);
			$this->assertTextContains('Group 2<span class="badge">2</span>', $view);
			$this->assertTextNotContains('Group 3', $view);
		} else {
			$this->assertTextNotContains('>' . __d('groups', 'Groups management') . '<', $view);
			$this->assertTextNotContains('/groups/groups/add', $view);
			$this->assertTextNotContains('Group 1<span class="badge">1</span>', $view);
			$this->assertTextNotContains('Group 2<span class="badge">2</span>', $view);
			$this->assertTextNotContains('Group 3', $view);
		}
	}

/**
 * 期待値の取得
 *
 * @param int $userId 期待値
 * @return array
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
	private function __getExpected($userId) {
		if ($userId === '1') {
			$expected = array(
				'User' => array(
					'id' => $userId,
					'username' => 'system_administrator',
					'handlename' => 'System Administrator',
				),
				'UsersLanguage' => array(
					0 => array(
						'id' => '1', 'name' => 'System Administrator Name', 'search_keywords' => 'default',
					)
				),
				'TrackableCreator' => array(
					'id' => '1',
				),
				'TrackableUpdater' => array(
					'id' => '1',
				),
				'Role' => array(
					'id' => '1',
				),
				'UserRoleSetting' => array(
					'id' => '1',
				),
			);
		} elseif ($userId === '2') {
			$expected = array(
				'User' => array(
					'id' => $userId,
					'username' => 'site_manager',
					'handlename' => 'Site Manager',
				),
				'UsersLanguage' => array(
					0 => array(
						'id' => '2', 'name' => 'Site Manager Name', 'search_keywords' => 'default',
					)
				),
				'TrackableCreator' => array(
					'id' => '1',
				),
				'TrackableUpdater' => array(
					'id' => '1',
				),
				'Role' => array(
					'id' => '2',
				),
				'UserRoleSetting' => array(
					'id' => '2',
				),
				'UploadFile' => array(
					'avatar' => array(
						'id' => '1',
						'content_key' => '2',
					),
				),
				'UploadFile' => array(
					'avatar' => array(
						'id' => '1',
						'content_key' => '2',
					),
				),
			);
		} elseif ($userId === '4') {
			$expected = array(
				'User' => array(
					'id' => $userId,
					'username' => 'editor',
					'handlename' => 'Editor',
				),
				'UsersLanguage' => array(
					0 => array(
						'id' => '4', 'name' => 'Editor Name', 'search_keywords' => 'default',
					)
				),
				'TrackableCreator' => array(
					'id' => '1',
				),
				'TrackableUpdater' => array(
					'id' => '1',
				),
				'Role' => array(
					'id' => '3',
				),
				'UserRoleSetting' => array(
					'id' => '3',
				),
			);
		} elseif ($userId === '5') {
			$expected = array(
				'User' => array(
					'id' => $userId,
					'username' => 'general_user',
					'handlename' => 'General User',
				),
				'UsersLanguage' => array(
					0 => array(
						'id' => '5', 'name' => 'General User Name', 'search_keywords' => 'default',
					)
				),
				'TrackableCreator' => array(
					'id' => '1',
				),
				'TrackableUpdater' => array(
					'id' => '1',
				),
				'Role' => array(
					'id' => '3',
				),
				'UserRoleSetting' => array(
					'id' => '3',
				),
			);
		}

		return $expected;
	}

}
