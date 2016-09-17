<?php
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('UsersAppController', 'Users.Controller');
App::uses('UserSelectCount', 'Users.Model');
App::uses('UserAttribute', 'UserAttributes.Model');

/**
 * Users Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Controller
 */
class UsersController extends UsersAppController {

/**
 * 会員一覧の表示する項目
 */
	public static $displaField = array('handlename');

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Rooms.Space',
		'Rooms.RolesRoomsUser',
		'Users.User',
		'Users.UserSelectCount',
		'Groups.Group',
		'Groups.GroupsUser',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Files.Download',
		'M17n.SwitchLanguage',
		'NetCommons.Permission' => array(
			'type' => PermissionComponent::CHECK_TYEP_NOCHECK_PLUGIN,
		),
		'Rooms.Rooms',
		'UserAttributes.UserAttributeLayout',
		'Users.UserSearchComp',
		'Groups.Groups',
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Token',
		'UserAttributes.UserAttributeLayout',
		'Users.UserLayout',
		'Groups.GroupUserList',
	);

/**
 * アクションの前処理
 * Controller::beforeFilter()のあと、アクション前に実行する
 *
 * @return bool
 */
	private function __prepare() {
		$this->Auth->deny('index', 'view');

		//ユーザデータ取得
		if ($this->request->is('put') || $this->request->is('delete')) {
			$userId = $this->data['User']['id'];
		} else {
			$userId = $this->params['user_id'];
		}

		$user = $this->User->getUser($userId);
		$this->set('user', $user);
		if (! $this->User->canUserRead($user)) {
			if ($this->params['action'] === 'download') {
				return false;
			} else {
				return $this->throwBadRequest();
			}
		}

		$this->set('title', false);

		//ルームデータチェック
		if (Hash::get($this->data, 'Room.id')) {
			$roomId = Hash::get($this->data, 'Room.id');
		} elseif (Hash::get($this->request->query, 'room_id')) {
			$roomId = Hash::get($this->request->query, 'room_id');
		} else {
			$roomId = null;
		}
		if ($roomId) {
			//ルームデータ取得
			$conditions = array('Room.id' => $roomId);
			$count = $this->Room->find('count', $this->Room->getReadableRoomsConditions($conditions));
			if (! $count) {
				return $this->setAction('throwBadRequest');
			}
			$this->set('roomId', $roomId);
		}

		return true;
	}

/**
 * view method
 *
 * @return void
 */
	public function view() {
		if (! $this->__prepare()) {
			return;
		}

		//レイアウトの設定
		if ($this->request->is('ajax')) {
			$this->viewClass = 'View';
			$this->layout = 'NetCommons.modal';
		} elseif (Current::isControlPanel()) {
			$this->ControlPanelLayout = $this->Components->load('ControlPanel.ControlPanelLayout');
		} else {
			$this->PageLayout = $this->Components->load('Pages.PageLayout');
		}

		if (! Hash::get($this->request->query, 'tab')) {
			$this->request->query = Hash::insert($this->request->query, 'tab', 'user-infomation');
		}

		//自分自身の場合、ルーム・グループデータ取得する
		if (Hash::get($this->viewVars['user'], 'User.id') === Current::read('User.id')) {
			//ルームデータ取得
			$this->Rooms->setReadableRooms(Hash::get($this->viewVars['user'], 'User.id'));

			// グループデータ取得・設定
			$this->Groups->setGroupList($this);
		} else {
			if (Current::allowSystemPlugin('rooms')) {
				//ルームデータ取得
				$this->Rooms->setReadableRooms(Hash::get($this->viewVars['user'], 'User.id'));
			}
		}
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		$this->helpers[] = 'Users.UserEditForm';

		$this->__prepare();

		if (Current::isControlPanel()) {
			$this->ControlPanelLayout = $this->Components->load('ControlPanel.ControlPanelLayout');
		} else {
			$this->PageLayout = $this->Components->load('Pages.PageLayout');
		}
		if (Hash::get($this->viewVars['user'], 'User.id') !== Current::read('User.id')) {
			return $this->throwBadRequest();
		}

		if ($this->request->is('put')) {
			$redirectUrl = Hash::get($this->request->data, '_user.redirect');
			if (array_key_exists('cancel', $this->request->data)) {
				$this->NetCommons->setAppendHtml(
					'<div class="hidden" ng-controller="Users.controller" ' .
						'ng-init="showUser(null, ' . Current::read('User.id') . ')"></div>'
				);
				return $this->redirect($redirectUrl);
			}

			//登録処理
			if ($this->User->saveUser($this->request->data, true)) {
				//正常の場合
				$this->NetCommons->setFlashNotification(
					__d('net_commons', 'Successfully saved.'), array('class' => 'success')
				);
				$this->NetCommons->setAppendHtml(
					'<div class="hidden" ng-controller="Users.controller" ' .
						'ng-init="showUser(null, ' . Current::read('User.id') . ')"></div>'
				);
				return $this->redirect($redirectUrl);
			}
			$this->NetCommons->handleValidationError($this->User->validationErrors);
			$this->request->data = Hash::merge($this->viewVars['user'], $this->request->data);

		} else {
			//表示処理
			$this->User->languages = $this->viewVars['languages'];
			$this->request->data = $this->viewVars['user'];
			$redirectUrl = $this->request->referer(true);
		}

		$this->set('userCancel', SiteSettingUtil::read('UserCancel.use_cancel_feature', false));
		$this->set('redirectUrl', $redirectUrl);
		$this->set('activeUserId', Hash::get($this->viewVars['user'], 'User.id'));
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete() {
		$this->__prepare();

		if (! SiteSettingUtil::read('UserCancel.use_cancel_feature', false)) {
			return $this->throwBadRequest();
		}

		if (Hash::get($this->viewVars['user'], 'User.id') !== Current::read('User.id') ||
				$this->viewVars['user']['User']['role_key'] === UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR) {
			return $this->throwBadRequest();
		}

		if (! $this->request->is('delete')) {
			return $this->throwBadRequest();
		}

		$this->User->deleteUser($this->viewVars['user']);
		$this->redirect('/auth/logout');
	}

/**
 * download method
 *
 * @return void
 * @throws NotFoundException
 */
	public function download() {
		if (! $this->__prepare()) {
			return $this->downloadNoImage();
		}

		$user = $this->viewVars['user'];
		$fieldName = $this->params['field_name'];
		$fieldSize = $this->params['size'];

		$fileSetting = Hash::extract(
			$this->viewVars['userAttributes'],
			'{n}.{n}.{n}.UserAttributeSetting[user_attribute_key=' . $fieldName . ']'
		);

		if (! $fileSetting) {
			return $this->downloadNoImage();
		}
		$userAttribute = Hash::get($this->viewVars['userAttributes'],
			$fileSetting[0]['row'] . '.' . $fileSetting[0]['col'] . '.' . $fileSetting[0]['weight']
		);

		if (! Hash::get($user, 'UploadFile.' . $fieldName . '.field_name')) {
			return $this->downloadNoImage();
		}

		//以下の場合、アバター表示
		// * 自分自身
		if (Hash::get($user, 'User.id') === Current::read('User.id')) {
			return $this->Download->doDownload($user['User']['id'],
				array('field' => $fieldName, 'size' => $fieldSize)
			);
		}

		// 以下の条件の場合、ハンドル画像を表示する(他人)
		// * 各自で公開・非公開が設定可 && 非公開
		// * 権限設定の個人情報設定で閲覧不可、
		// * 会員項目設定で非表示(display=OFF)項目、
		if ($userAttribute['UserAttributeSetting']['self_public_setting'] &&
					! Hash::get($user, 'User.' . sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $fieldName)) ||
				! $userAttribute['UserAttributesRole']['other_readable'] ||
				! $userAttribute['UserAttributeSetting']['display']) {
			return $this->downloadNoImage();
		} else {
			return $this->Download->doDownload($user['User']['id'],
				array('field' => $fieldName, 'size' => $fieldSize)
			);
		}
	}

/**
 * download method
 *
 * @return void
 */
	public function downloadNoImage() {
		$user = $this->viewVars['user'];
		$fieldName = $this->params['field_name'];
		$fieldSize = $this->params['size'];

		$this->response->file(
			$this->User->temporaryAvatar($user, $fieldName, $fieldSize),
			array('name' => 'No Image')
		);
		return $this->response;
	}

/**
 * search method
 *
 * @return void
 */
	public function search() {
		//$this->layout = 'NetCommons.default';
		$this->viewClass = 'View';
		$this->view = 'Users.Users/json/search';
		$this->__prepare();

		if (Hash::get($this->viewVars['user'], 'User.id') !== Current::read('User.id')) {
			return;
		}

		$roomId = Hash::get($this->request->query, 'room_id');
		$query = Hash::remove($this->request->query, 'room_id');

		if ($roomId === Room::ROOM_PARENT_ID) {
			$conditions = array(
				'Room.id' => Room::ROOM_PARENT_ID
			);
		} else {
			$conditions = array(
				'Room.page_id_top NOT' => null
			);
		}

		$this->UserSearchComp->search(array(
			'fields' => self::$displaField,
			'conditions' => Hash::merge(array(), $query),
			'joins' => array('Room' => array(
				'conditions' => $conditions
			)),
			'order' => array(),
			'limit' => UserSelectCount::LIMIT
		));
	}

/**
 * select method
 *
 * @return void
 */
	public function select() {
		$this->__prepare();

		if (Hash::get($this->viewVars['user'], 'User.id') !== Current::read('User.id')) {
			$this->throwBadRequest();
			return;
		}

		$roomId = Hash::get($this->viewVars, 'roomId');
		if (! $roomId) {
			$this->throwBadRequest();
			return;
		}

		if ($this->request->is('post')) {
			//登録処理
			//** ロールルームユーザデータ取得
			$rolesRoomsUsers = $this->RolesRoomsUser->getRolesRoomsUsers(array(
				'RolesRoomsUser.user_id' => $this->request->data['UserSelectCount']['user_id'],
				'Room.id' => $roomId
			));
			$userIds = Hash::extract($rolesRoomsUsers, '{n}.RolesRoomsUser.user_id');
			sort($userIds);
			sort($this->request->data['UserSelectCount']['user_id']);

			//** user_idのチェック
			if (Hash::diff($userIds, $this->request->data['UserSelectCount']['user_id'])) {
				//diffがあった場合は、不正ありと判断する
				$this->throwBadRequest();
				return;
			}
			$data = array_map(function ($userId) {
				return array('UserSelectCount' => array(
					'user_id' => $userId, 'created_user' => Current::read('User.id')
				));
			}, $this->request->data['UserSelectCount']['user_id']);

			//** 登録処理
			if (! $this->UserSelectCount->saveUserSelectCount($data)) {
				$this->NetCommons->handleValidationError($this->UserSelectCount->validationErrors);
			}
			return;
		} else {
			//表示処理
			//** レイアウトの設定
			$this->viewClass = 'View';
			$this->layout = 'NetCommons.modal';

			//** 選択したユーザ取得
			$users = $this->UserSelectCount->getUsers($roomId);
			if (! $users) {
				$users = array();
			}
			$this->set('searchResults', $users);
		}
	}

}
