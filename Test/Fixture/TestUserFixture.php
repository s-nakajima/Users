<?php
/**
 * SaveUserBehaviorテスト用Fixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('User4userFixture', 'Users.Test/Fixture');

/**
 * SaveUserBehaviorテスト用Fixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Fixture
 */
class TestUserFixture extends User4userFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'User';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'users';

}
