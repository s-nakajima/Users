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
		'M17n.SwitchLanguage',
		'Rooms.Rooms',
		'UserAttributes.UserAttributeLayout',
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
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
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
		$rooms = array();
		if (! Current::allowSystemPlugin('rooms')) {
			$conditions = array('Room.active' => true);
		} else {
			$conditions = array();
		}
		$result = $this->Room->find('all', $this->Room->getReadableRoomsCondtions(Space::PUBLIC_SPACE_ID, $conditions));
		$rooms = Hash::merge($rooms, Hash::combine($result, '{n}.Room.id', '{n}'));

		$result = $this->Room->find('all', $this->Room->getReadableRoomsCondtions(Space::ROOM_SPACE_ID, $conditions));
		$rooms = Hash::merge($rooms, Hash::combine($result, '{n}.Room.id', '{n}'));
		$this->set('rooms', $rooms);

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
				$this->redirect('/user_manager/user_manager/index/');
				return;
			}
			$this->NetCommons->handleValidationError($this->User->validationErrors);

		} else {
			//表示処理
			$this->User->languages = $this->viewVars['languages'];
			$this->request->data = $this->viewVars['user'];
		}

		$this->set('activeUserId', Hash::get($this->viewVars['user'], 'User.id'));

		//if (!$this->User->exists($id)) {
		//	throw new NotFoundException(__('Invalid user'));
		//}
		//if ($this->request->is(array('post', 'put'))) {
		//	if ($this->User->save($this->request->data)) {
		//		$this->Session->setFlash(__('The user has been saved.'));
		//		return $this->redirect(array('action' => 'index'));
		//	} else {
		//		$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
		//	}
		//} else {
		//	$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		//	$this->request->data = $this->User->find('first', $options);
		//}
		//$authorities = $this->User->Authority->find('list');
		//$this->set(compact('authorities'));
	}

/**
 * delete method
 *
 * @param string $id id
 * @throws NotFoundException
 * @return void
 */
	public function delete($id = null) {
		//$this->User->id = $id;
		//if (!$this->User->exists()) {
		//	throw new NotFoundException(__('Invalid user'));
		//}
		//$this->request->onlyAllow('post', 'delete');
		//if ($this->User->delete()) {
		//	$this->Session->setFlash(__('The user has been deleted.'));
		//} else {
		//	$this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
		//}
		//return $this->redirect(array('action' => 'index'));
	}
}
