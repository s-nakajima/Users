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
	}

/**
 * Search user
 *
 * @return array Return search
 */
	public function search() {
		$fields = array();
		$conditions = array();

		//ユーザデータ取得
		$this->controller->Paginator->settings = array(
			'recursive' => -1,
			'fields' => $this->controller->User->getSearchFields($fields),
			'conditions' => $this->controller->User->getSearchConditions($conditions),
			'joins' => $this->controller->User->getSearchJoinTables(),
			'order' => array($this->controller->User->alias . '.id' => 'asc'),
			//'limit' => 1
		);
		$results = $this->controller->Paginator->paginate('User');

		$this->controller->set('users', $results);
		$this->controller->set('displayFields', $this->controller->User->getDispayFields());
	}

}
