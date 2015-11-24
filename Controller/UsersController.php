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
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Users.User',
		'Rooms.Space',
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
		if (! Current::allowSystemPlugin('rooms')) {
			$conditions = array('Room.active' => true);
		} else {
			$conditions = array();
		}
		$result = $this->Room->find('all', $this->Room->getReadableRoomsCondtions($conditions));
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
			$this->setAction('throwBadRequest');
			return;
		}

		if ($this->request->isPut()) {
			//不要パラメータ除去
			unset($this->request->data['save'], $this->request->data['active_lang_id']);

			//登録処理
			$this->User->userAttributeData = Hash::combine($this->viewVars['userAttributes'],
				'{n}.{n}.{n}.UserAttribute.id', '{n}.{n}.{n}'
			);
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
			$this->setAction('throwBadRequest');
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
		//$this->__prepare();
		//
		//if (Hash::get($this->viewVars['user'], 'User.id') !== Current::read('User.id')) {
		//	return;
		//}
		//CakeLog::debug('UsersController::search() ' . print_r($this->request->query, true));

		$query = array_map(function ($value) {
			return '%' . $value . '%';
		}, $this->request->query);
		//CakeLog::debug(print_r($query, true));

		$this->UserSearch->search(
			Hash::merge(array('space_id' => Space::PRIVATE_SPACE_ID), $query),
			array('Room' => array('Room.page_id_top NOT' => null))
		);
		$this->set('displayFields', $this->User->getDispayFields());

		//CakeLog::debug('UsersController::search() ' . print_r($this->viewVars['users'], true));
	}

/**
 * select method
 *
 * @return void
 */
	public function select() {
		$this->__prepare();

		//レイアウトの設定
		$this->viewClass = 'View';
		$this->layout = 'NetCommons.modal';

		if (Hash::get($this->viewVars['user'], 'User.id') !== Current::read('User.id')) {
			return;
		}
	}

}
