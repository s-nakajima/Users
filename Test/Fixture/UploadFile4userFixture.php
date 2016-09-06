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

App::uses('UploadFileFixture', 'Files.Test/Fixture');

/**
 * UploadFile4testFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Fixture
 */
class UploadFile4userFixture extends UploadFileFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'UploadFile';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'upload_files';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1, // user avatarパターン
			'plugin_key' => 'users',
			'content_key' => '2',
			'field_name' => 'avatar',
			'original_name' => 'logo.gif',
			'path' => 'files/upload_file/real_file_name//',
			'real_file_name' => '38bfb11bf48fc2f56d2ca2d796d0b0af.gif',
			'extension' => 'gif',
			'mimetype' => 'image/gif',
			'size' => 5873,
			'download_count' => 6,
			'total_download_count' => 6,
			'room_id' => null,
			'block_key' => null,
			'created_user' => 1,
			'created' => '2016-02-25 03:44:14',
			'modified_user' => 1,
			'modified' => '2016-02-25 03:44:14'
		),
		array(
			'id' => 2, // user avatarパターン
			'plugin_key' => 'users',
			'content_key' => '3',
			'field_name' => 'avatar',
			'original_name' => 'logo.gif',
			'path' => 'files/upload_file/real_file_name//',
			'real_file_name' => '7bb5a56eb63531bcb40bda56aafceef3.png',
			'extension' => 'png',
			'mimetype' => 'image/png',
			'size' => 943,
			'download_count' => 6,
			'total_download_count' => 6,
			'room_id' => null,
			'block_key' => null,
			'created_user' => 1,
			'created' => '2016-02-25 03:44:14',
			'modified_user' => 1,
			'modified' => '2016-02-25 03:44:14'
		),
		array(
			'id' => 3, // user avatarパターン
			'plugin_key' => 'users',
			'content_key' => '7',
			'field_name' => 'avatar',
			'original_name' => 'logo.gif',
			'path' => 'files/upload_file/real_file_name//',
			'real_file_name' => '794a7e194c02fda0e867c4e796aad32d.gif',
			'extension' => 'gif',
			'mimetype' => 'image/gif',
			'size' => 201,
			'download_count' => 6,
			'total_download_count' => 6,
			'room_id' => null,
			'block_key' => null,
			'created_user' => 1,
			'created' => '2016-02-25 03:44:14',
			'modified_user' => 1,
			'modified' => '2016-02-25 03:44:14'
		),
	);

}
