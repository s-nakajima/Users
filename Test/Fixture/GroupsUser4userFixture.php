<?php
/**
 * 会員情報、会員管理用 GroupsUserFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupsUserFixture', 'Groups.Test/Fixture');

/**
 * 会員情報、会員管理用 GroupsUserFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Fixture
 */
class GroupsUser4userFixture extends GroupsUserFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'GroupsUser';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'groups_users';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'group_id' => '1',
			'user_id' => '1',
			'created_user' => '1',
			'created' => '2016-02-28 04:58:01',
			'modified_user' => '1',
			'modified' => '2016-02-28 04:58:01'
		),
		array(
			'id' => '2',
			'group_id' => '2',
			'user_id' => '1',
			'created_user' => '1',
			'created' => '2016-02-28 04:58:01',
			'modified_user' => '1',
			'modified' => '2016-02-28 04:58:01'
		),
		array(
			'id' => '3',
			'group_id' => '2',
			'user_id' => '2',
			'created_user' => '1',
			'created' => '2016-02-28 04:58:01',
			'modified_user' => '1',
			'modified' => '2016-02-28 04:58:01'
		),
		array(
			'id' => '4',
			'group_id' => '3',
			'user_id' => '2',
			'created_user' => '1',
			'created' => '2016-02-28 04:58:01',
			'modified_user' => '1',
			'modified' => '2016-02-28 04:58:01'
		),
	);

}
