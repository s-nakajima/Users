<?php
/**
 * UserSelectCountFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * UserSelectCountFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model
 */
class UserSelectCountFixture extends CakeTestFixture {

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'select_count' => 1,
			'created_user' => 1,
			'created' => '2015-11-29 15:45:41',
			'modified_user' => 1,
			'modified' => '2015-11-29 15:45:41'
		),
	);

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		require_once App::pluginPath('Users') . 'Config' . DS . 'Schema' . DS . 'schema.php';
		$this->fields = (new UsersSchema())->tables['user_select_counts'];
		parent::init();
	}

}
