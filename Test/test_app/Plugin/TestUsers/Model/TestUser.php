<?php
/**
 * SaveUserBehaviorテスト用Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppModel', 'Model');

/**
 * SaveUserBehaviorテスト用Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\test_app\Plugin\TestUsers\Model
 */
class TestUser extends AppModel {

/**
 * Alias name for model.
 *
 * @var string
 */
	public $alias = 'User';

/**
 * Table name for this Model.
 *
 * @var string
 */
	public $table = 'users';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/ja/models/model-attributes.html#usetable
 */
	public $useTable = 'users';

/**
 * user attribute data.
 *
 * @var array
 */
	public $userAttributeData = array();

/**
 * 使用ビヘイビア
 *
 * @var array
 */
	public $actsAs = array(
		'Users.SaveUser'
	);

/**
 * UserModelの前準備
 *
 * @return void
 */
	public function prepare() {
		$this->loadModels([
			'UserAttribute' => 'UserAttributes.UserAttribute',
		]);

		$userAttributes = $this->UserAttribute->getUserAttributesForLayout();
		$this->userAttributeData = Hash::combine($userAttributes,
			'{n}.{n}.{n}.UserAttribute.id', '{n}.{n}.{n}'
		);
	}

}
