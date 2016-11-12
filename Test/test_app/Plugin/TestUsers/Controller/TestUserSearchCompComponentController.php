<?php
/**
 * UserSearchCompComponentテスト用Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * UserSearchCompComponentテスト用Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\test_app\Plugin\TestUsers\Controller
 */
class TestUserSearchCompComponentController extends AppController {

/**
 * 使用コンポーネント
 *
 * @var array
 */
	public $components = array(
		'Users.UserSearchComp'
	);

/**
 * index
 *
 * @return void
 */
	public function index() {
		$this->autoRender = true;
	}

}
