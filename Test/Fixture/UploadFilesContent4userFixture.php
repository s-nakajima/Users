<?php
/**
 * UploadFile4testFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('UploadFilesContentFixture', 'Files.Test/Fixture');

/**
 * UploadFile4testFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Fixture
 */
class UploadFilesContent4userFixture extends UploadFilesContentFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'UploadFilesContent';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'upload_files_contents';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'plugin_key' => 'users',
			'content_id' => 2,
			'upload_file_id' => 1,
			'created_user' => 1,
			'created' => '2015-10-29 08:50:56',
			'modified_user' => 1,
			'modified' => '2015-10-29 08:50:56'
		),
		array(
			'id' => 2,
			'plugin_key' => 'users',
			'content_id' => 3,
			'upload_file_id' => 2,
			'created_user' => 1,
			'created' => '2015-10-29 08:50:56',
			'modified_user' => 1,
			'modified' => '2015-10-29 08:50:56'
		),
		array(
			'id' => 3,
			'plugin_key' => 'users',
			'content_id' => 7,
			'upload_file_id' => 3,
			'created_user' => 1,
			'created' => '2015-10-29 08:50:56',
			'modified_user' => 1,
			'modified' => '2015-10-29 08:50:56'
		),
	);

}
