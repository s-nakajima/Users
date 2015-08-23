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
 * Called before the Controller::beforeFilter().
 *
 * @param Controller $controller Controller with components to initialize
 * @return void
 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::initialize
 */
	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->controller->Paginator = $this->controller->Components->load('Paginator');

		//Modelの呼び出し
		$this->controller->User = ClassRegistry::init('Users.User');
		$this->controller->UsersLanguage = ClassRegistry::init('Users.UsersLanguage');
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
			'fields' => $this->controller->User->searchFields($fields),
			'conditions' => $this->controller->User->searchConditions($conditions),
			'joins' => $this->controller->User->searchJoinTables(),
			'order' => array($this->controller->User->alias . '.id' => 'asc')
		);
		$results = $this->controller->Paginator->paginate('User');

		return $results;
	}

}
