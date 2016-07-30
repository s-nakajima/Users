<?php
/**
 * UsersSearch Component
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Component', 'Controller');

/**
 * UsersSearch Component
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Controller\Component
 */
class UserSearchCompComponent extends Component {

/**
 * Limit定数
 *
 * @var const
 */
	const DEFAULT_LIMIT = 20;

/**
 * Called after the Controller::beforeFilter() and before the controller action
 *
 * @param Controller $controller Controller with components to startup
 * @return void
 */
	public function startup(Controller $controller) {
		$this->controller = $controller;

		$controller->Paginator = $controller->Components->load('Paginator');

		//Modelの呼び出し
		//$controller->UserSearch = ClassRegistry::init('Users.UserSearch');
		//$controller->User = ClassRegistry::init('Users.User');
		//$controller->UsersLanguage = ClassRegistry::init('Users.UsersLanguage');
		//$controller->Space = ClassRegistry::init('Rooms.Space');
	}

/**
 * 条件フォーム出力(モーダル表示固定)
 *
 * @return void
 */
	public function conditions() {
		$controller = $this->controller;
		$controller->UserSearch = ClassRegistry::init('Users.UserSearch');

		//検索フォーム表示
		$controller->helpers[] = 'Users.UserSearchForm';
		$controller->viewClass = 'View';
		$controller->layout = 'NetCommons.modal';
		$controller->view = 'Users.UserSearch/conditions';

		//自分自身のグループデータ取得
		$controller->set('groups', $controller->UserSearch->getReadableFieldOptions('group_id'));

		//参加ルームデータ取得
		$controller->set('rooms', $controller->UserSearch->getReadableFieldOptions('room_id'));

		$controller->request->data['UserSearch'] = $controller->request->query;
	}

/**
 * 検索処理
 *
 * @param array $options オプション配列
 * ```
 *	array(
 *		'fields' => 表示フィールド配列
 *		'conditions' => 条件配列
 *		'joins' => JOINテーブル配列
 *		'orders' => ソート配列
 *		'limit' => 表示件数(int)
 *	)
 * ```
 * @return array void
 */
	public function search($options) {
		$controller = $this->controller;
		$controller->UserSearch = ClassRegistry::init('Users.UserSearch');

		$fields = Hash::get($options, 'fields', []);
		$displayFields = Hash::get($options, 'displayFields', $fields);
		$conditions = Hash::get($options, 'conditions', []);
		$joins = Hash::get($options, 'joins', []);
		$orders = Hash::get($options, 'orders', []);
		$limit = Hash::get($options, 'limit', self::DEFAULT_LIMIT);
		$extra = Hash::get($options, 'extra', []);

		$defaultConditions = $controller->UserSearch->cleanSearchFields($controller->request->query);
		$conditions = Hash::merge($defaultConditions, $conditions);

		//ユーザデータ取得
		$controller->Paginator->settings = array(
			'fields' => $fields,
			'conditions' => $conditions,
			'joins' => $joins,
			'defaultOrder' => $orders,
			'limit' => $limit,
			'extra' => $extra
		);
		$results = $controller->Paginator->paginate('UserSearch');

		$controller->set('users', $results);
		$controller->request->data['UserSearch'] = $defaultConditions;
		if (isset($controller->request->query['search'])) {
			$defaultConditions['search'] = '1';
		}
		$controller->request->query = $defaultConditions;

		$controller->set(
			'displayFields',
			$controller->UserSearch->cleanSearchFields(array_combine($displayFields, $displayFields))
		);
	}
}
