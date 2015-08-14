<?php
/**
 * UserFixture
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

/**
 * Summary for UserFixture
 */
class UserFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'username' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'ID | ログインID'),
		'password' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Password | パスワード'),
		'handlename' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'comment' => 'Handle | ハンドル'),
		'role_key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'username' => 'Lorem ipsum dolor sit amet',
			'password' => 'Lorem ipsum dolor sit amet',
			'handlename' => 'system_administrator',
			'role_key' => 'system_administrator',
			'created_user' => 1,
			'created' => '2015-07-25 21:45:12',
			'modified_user' => 1,
			'modified' => '2015-07-25 21:45:12'
		),
		array(
			'id' => 2,
			'username' => 'Lorem ipsum dolor sit amet',
			'password' => 'Lorem ipsum dolor sit amet',
			'handlename' => 'user_administrator',
			'role_key' => 'user_administrator',
			'created_user' => 1,
			'created' => '2014-06-02 16:18:08',
			'modified_user' => 1,
			'modified' => '2014-06-02 16:18:08'
		),
		array(
			'id' => 3,
			'username' => 'Lorem ipsum dolor sit amet',
			'password' => 'Lorem ipsum dolor sit amet',
			'handlename' => 'chief_user',
			'role_key' => 'chief_user',
			'created_user' => 1,
			'created' => '2014-06-02 16:18:08',
			'modified_user' => 1,
			'modified' => '2014-06-02 16:18:08'
		),
		array(
			'id' => 4,
			'username' => 'Lorem ipsum dolor sit amet',
			'password' => 'Lorem ipsum dolor sit amet',
			'handlename' => 'common_user',
			'role_key' => 'common_user',
			'created_user' => 1,
			'created' => '2014-06-02 16:18:08',
			'modified_user' => 1,
			'modified' => '2014-06-02 16:18:08'
		),
		array(
			'id' => 5,
			'username' => 'Lorem ipsum dolor sit amet',
			'password' => 'Lorem ipsum dolor sit amet',
			'handlename' => 'guest_user',
			'role_key' => 'guest_user',
			'created_user' => 1,
			'created' => '2014-06-02 16:18:08',
			'modified_user' => 1,
			'modified' => '2014-06-02 16:18:08'
		),
	);

}
