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

		if (! $controller->request->query && ! $controller->request->named) {
			$controller->Session->delete(self::$sessionKey);
		}
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
	public function search($conditions = array(), $joins = array(), $orders = array(), $limit = self::DEFAULT_LIMIT) {
		$defaultConditions = $this->controller->Session->read(self::$sessionKey);
		if (! $defaultConditions) {
			$defaultConditions = array();
		}
		$conditions = Hash::merge($defaultConditions, $conditions);

		//ユーザデータ取得
		$this->controller->Paginator->settings = array(
			'recursive' => -1,
			'fields' => $this->controller->User->getSearchFields(),
			'conditions' => $this->controller->User->getSearchConditions($conditions),
			'joins' => $this->controller->User->getSearchJoinTables($joins),
			'order' => Hash::merge($orders, array($this->controller->User->alias . '.id' => 'asc')),
			'limit' => $limit
		);
		$results = $this->controller->Paginator->paginate('User');

		$this->controller->set('users', $results);
	}
}
