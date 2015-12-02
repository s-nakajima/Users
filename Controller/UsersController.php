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

App::uses('AppController', 'Controller');

/**
 * Users Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Controller
 */
class UsersController extends UsersAppController {

/**
 * 会員一覧の表示する項目
 *
 * @var const
 */
	public static $displaField = 'handlename';

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		//'Groups.GroupsUser',
		'Rooms.Space',
		'Rooms.RolesRoomsUser',
		'Users.User',
		'Users.UserSelectCount',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Files.Download',
		'M17n.SwitchLanguage',
		'Rooms.Rooms',
		'UserAttributes.UserAttributeLayout',
		'Users.UserSearch',
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
	);

/**
 * アクションの前処理
 * Controller::beforeFilter()のあと、アクション前に実行する
 *
 * @return void
 */
	private function __prepare() {
		$this->Auth->deny('index', 'view');

		//ユーザデータ取得
		if ($this->request->isPut() || $this->request->isDelete()) {
			$userId = $this->data['User']['id'];
		} else {
			$userId = $this->params['pass'][0];
		}

		$user = $this->User->getUser($userId);
		if (! $user || $user['User']['is_deleted']) {
			$this->setAction('throwBadRequest');
			return;
		}
		$this->set('user', $user);
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
			$count = $this->Room->find('count', $this->Room->getReadableRoomsCondtions($conditions));
			if (! $count) {
				$this->setAction('throwBadRequest');
				return;
			}
			$this->set('roomId', $roomId);
		}
	}

/**
 * view method
 *
 * @return void
 */
	public function view() {
		$this->__prepare();

		//レイアウトの設定
		if ($this->request->is('ajax')) {
			$this->viewClass = 'View';
			$this->layout = 'NetCommons.modal';
		} elseif (Current::isControlPanel()) {
			$this->ControlPanelLayout = $this->Components->load('ControlPanel.ControlPanelLayout');
		} else {
			$this->PageLayout = $this->Components->load('Pages.PageLayout');
		}

		if (Hash::get($this->viewVars['user'], 'User.id') !== Current::read('User.id')) {
			return;
		}

		//ルームデータ取得
		$result = $this->Room->find('all', $this->Room->getReadableRoomsCondtions());
		$this->set('rooms', Hash::combine($result, '{n}.Room.id', '{n}'));

		//ルームのTreeリスト取得
		$roomTreeLists[Space::PUBLIC_SPACE_ID] = $this->Room->generateTreeList(
				array('Room.space_id' => Space::PUBLIC_SPACE_ID), null, null, Room::$treeParser);

		$roomTreeLists[Space::ROOM_SPACE_ID] = $this->Room->generateTreeList(
				array('Room.space_id' => Space::ROOM_SPACE_ID), null, null, Room::$treeParser);
		$this->set('roomTreeLists', $roomTreeLists);
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
			$this->throwBadRequest();
			return;
		}

		if ($this->request->isPut()) {
			//不要パラメータ除去
			unset($this->request->data['save'], $this->request->data['active_lang_id']);

			//登録処理
			if ($this->User->saveUser($this->request->data)) {
				//正常の場合
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array('class' => 'success'));
				$this->redirect('/users/users/view/' . Hash::get($this->viewVars['user'], 'User.id'));
				return;
			}
			$this->NetCommons->handleValidationError($this->User->validationErrors);

		} else {
			//表示処理
			$this->User->languages = $this->viewVars['languages'];
			$this->request->data = $this->viewVars['user'];
		}

		$this->set('activeUserId', Hash::get($this->viewVars['user'], 'User.id'));
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete() {
		$this->__prepare();

		if (Hash::get($this->viewVars['user'], 'User.id') !== Current::read('User.id') ||
				$this->viewVars['user']['User']['role_key'] === UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR) {
			$this->throwBadRequest();
			return;
		}

		if (! $this->request->isDelete()) {
			$this->throwBadRequest();
			return;
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
		$this->__prepare();

		$fieldName = $this->params['pass'][1];
		$fileSetting = Hash::extract(
			$this->viewVars['userAttributes'],
			'{n}.{n}.{n}.UserAttributeSetting[user_attribute_key=' . $fieldName . ']'
		);
		if (! $fileSetting) {
			throw new NotFoundException();
		}

		// 以下の条件の場合、noimageを表示する
		// * 非公開 && 自分以外、
		// * 個人情報設定で閲覧不可、
		// * ユーザ項目属性の管理者のみ許可する場合で会員管理が使えない

		//	$this->response->file(Router::url('/users/img/noimage.gif'), array('name' => 'No Image'));
		//	return $this->response;

		return $this->Download->doDownload($this->viewVars['user']['User']['id'], array(
			'field' => $this->params['pass'][1],
			'size' => $this->params['pass'][2])
		);
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

		$query = Hash::remove($this->request->query, 'room_id');

		$this->UserSearch->search(
			Hash::merge(array('space_id' => Space::PRIVATE_SPACE_ID), $query),
			array('Room' => array(
				'conditions' => array(
					'Room.page_id_top NOT' => null,
				)
			))
		);

		$fields = array(self::$displaField => self::$displaField);
		$this->set('displayFields', $this->User->cleanSearchFields($fields));

		//CakeLog::debug('UsersController::search() ' . print_r($this->viewVars['users'], true));
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

		if ($this->request->isPost()) {
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
