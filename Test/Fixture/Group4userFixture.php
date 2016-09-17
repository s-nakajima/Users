<?php
/**
 * 会員情報、会員管理用 GroupFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('GroupFixture', 'Groups.Test/Fixture');

/**
 * 会員情報、会員管理用 GroupFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Fixture
 */
class Group4userFixture extends GroupFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'Group';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'groups';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'name' => 'Group 1',
			'created_user' => '1',
			'created' => '2016-02-28 04:57:50',
			'modified_user' => '1',
			'modified' => '2016-02-28 04:57:50'
		),
		array(
			'id' => '2',
			'name' => 'Group 2',
			'created_user' => '1',
			'created' => '2016-02-28 04:57:50',
			'modified_user' => '1',
			'modified' => '2016-02-28 04:57:50'
		),
		array(
			'id' => '3',
			'name' => 'Group 3',
			'created_user' => '2',
			'created' => '2016-02-28 04:57:50',
			'modified_user' => '2',
			'modified' => '2016-02-28 04:57:50'
		),
	);

}
