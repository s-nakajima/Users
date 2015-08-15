<?php
/**
 * UsersLanguageFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

/**
 * UsersLanguageFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model
 */
class UsersLanguageFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 6, 'unsigned' => false),
		'avatar' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Avatar | アバター', 'charset' => 'utf8'),
		'avatar_file_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Name | 氏名', 'charset' => 'utf8'),
		'profile' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Profile | プロフィール', 'charset' => 'utf8'),
		'search_keywords' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'Keywords | 検索キーワード', 'charset' => 'utf8'),
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
			'user_id' => 1,
			'language_id' => 2,
			'avatar' => 'Lorem ipsum dolor sit amet',
			'avatar_file_id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
		array(
			'id' => 2,
			'user_id' => 2,
			'language_id' => 2,
			'avatar' => 'Lorem ipsum dolor sit amet',
			'avatar_file_id' => 2,
			'name' => 'Lorem ipsum dolor sit amet',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
		array(
			'id' => 3,
			'user_id' => 3,
			'language_id' => 3,
			'avatar' => 'Lorem ipsum dolor sit amet',
			'avatar_file_id' => 3,
			'name' => 'Lorem ipsum dolor sit amet',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
		array(
			'id' => 4,
			'user_id' => 4,
			'language_id' => 4,
			'avatar' => 'Lorem ipsum dolor sit amet',
			'avatar_file_id' => 4,
			'name' => 'Lorem ipsum dolor sit amet',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
		array(
			'id' => 5,
			'user_id' => 5,
			'language_id' => 5,
			'avatar' => 'Lorem ipsum dolor sit amet',
			'avatar_file_id' => 5,
			'name' => 'Lorem ipsum dolor sit amet',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
	);

}
