<?php
/**
 * UsersLanguage4testFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('UsersLanguageFixture', 'Users.Test/Fixture');

/**
 * UsersLanguage4testFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Fixture
 */
class UsersLanguage4testFixture extends UsersLanguageFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'UsersLanguage';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'users_languages';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'user_id' => '1',
			'language_id' => '2',
			'name' => 'System administrator',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
		array(
			'id' => '2',
			'user_id' => '2',
			'language_id' => '2',
			'name' => 'Chief editor',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
		array(
			'id' => '3',
			'user_id' => '3',
			'language_id' => '2',
			'name' => 'Editor',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
		array(
			'id' => '4',
			'user_id' => '4',
			'language_id' => '2',
			'name' => 'General user',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
		array(
			'id' => '5',
			'user_id' => '5',
			'language_id' => '2',
			'name' => 'Visitor',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
		array(
			'id' => '6',
			'user_id' => '6',
			'language_id' => '2',
			'name' => 'Lorem ipsum dolor sit amet',
			'profile' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'search_keywords' => 'Lorem ipsum dolor sit amet'
		),
	);

}
