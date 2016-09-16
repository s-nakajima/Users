<?php
/**
 * UserPermissionBehaviorテスト用Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

/**
 * UserPermissionBehaviorテスト用Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\test_app\Plugin\TestUsers\Model
 */
class TestUserPermissionBehaviorModel extends AppModel {

/**
 * テーブル名
 *
 * @var mixed
 */
	public $useTable = false;

/**
 * 使用ビヘイビア
 *
 * @var array
 */
	public $actsAs = array(
		'Users.UserPermission'
	);

}
