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
class UserSearchComponent extends Component {

/**
 * SessionKey
 *
 * @var string
 */
	public static $sessionKey = 'users/users/search';

/**
 * Limit定数
 *
 * @var const
 */
	const DEFAULT_LIMIT = 20;

/**
 * more_than_days定数
 * ○日以上前(○日以上ログインしていない)
 *
 * @var const
 */
	const MORE_THAN_DAYS = 'more_than_days';

/**
 * within_days定数
 * ○日以内(○日以内ログインしている)
 *
 * @var const
 */
	const WITHIN_DAYS = 'within_days';

/**
 * Other Components this component uses.
 *
 * @var array
 */
	public $components = array(
		'UserAttributes.UserAttributeLayout',
	);

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
		$controller->User = ClassRegistry::init('Users.User');
		$controller->UsersLanguage = ClassRegistry::init('Users.UsersLanguage');
		$controller->Space = ClassRegistry::init('Rooms.Space');
	}

/**
 * 条件フォーム出力(モーダル表示固定)
 *
 * @return void
 */
	public function conditions() {
		$controller = $this->controller;

		//検索フォーム表示
		$controller->helpers[] = 'Users.UserSearchForm';
		$controller->viewClass = 'View';
		$controller->layout = 'NetCommons.modal';
		$controller->view = 'Users.UserSearch/conditions';

		//自分自身のグループデータ取得
		$controller->set('groups', $controller->User->getOriginalUserField('group_id', 'options'));

		//参加ルームデータ取得
		$controller->set('rooms', $controller->User->getOriginalUserField('room_id', 'options'));

		$controller->request->data['UserSearch'] = $controller->request->query;
	}

/**
 * 検索処理
 *
 * @param array $conditions 条件
 * @param array $joins JOIN時の条件
 * @param array $orders ソート条件
 * @param int $limit 表示件数
 * @return array void
 */
	public function search($conditions = [], $joins = [], $orders = [], $limit = self::DEFAULT_LIMIT) {
		$controller = $this->controller;

		$defaultConditions = $controller->User->cleanSearchFields($controller->request->query);
		if (! $defaultConditions) {
			$defaultConditions = array();
		}
		$conditions = Hash::merge($defaultConditions, $conditions);
		$fieldKeys = array_keys($conditions);
		foreach ($fieldKeys as $field) {
			if ($field === 'group_id') {
				$joins = Hash::merge(array('Group' => true), $joins);
			} elseif ($field === 'created_user') {
				$joins = Hash::merge(array('TrackableCreator' => true), $joins);
			} elseif ($field === 'modified_user') {
				$joins = Hash::merge(array('TrackableUpdater' => true), $joins);
			} elseif ($controller->User->getOriginalUserField($field) ===
						$controller->User->UploadFile->alias . Inflector::classify($field) . '.field_name') {
				$modelName = $controller->User->UploadFile->alias . Inflector::classify($field);
				$joins = Hash::merge(array($modelName => array(
					'table' => $controller->User->UploadFile->table,
					'alias' => $modelName,
					'type' => 'LEFT',
					'conditions' => array(
						$modelName . '.content_key' . ' = ' . $controller->User->alias . '.id',
						$modelName . '.plugin_key' => 'users',
						$modelName . '.field_name' => $field,
					),
				)), $joins);
			}
		}

		//ユーザデータ取得
		$controller->Paginator->settings = array(
			'recursive' => -1,
			'fields' => $controller->User->getSearchFields(),
			'conditions' => $controller->User->getSearchConditions($conditions),
			'joins' => $controller->User->getSearchJoinTables($joins),
			'group' => 'User.id',
			'order' => Hash::merge($orders, array('Role.id' => 'asc')),
			'limit' => $limit
		);
		$results = $controller->Paginator->paginate('User');

		$controller->set('users', $results);
		$controller->request->data['UserSearch'] = $defaultConditions;
		if (isset($controller->request->query['search'])) {
			$defaultConditions['search'] = '1';
		}
		$controller->request->query = $defaultConditions;
	}
}
