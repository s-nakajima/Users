<?php
/**
 * UsersShell::startup()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsConsoleTestCase', 'NetCommons.TestSuite');

/**
 * UsersShell::startup()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Console\Command\UsersShell
 */
class ConsoleCommandUsersShellStartupTest extends NetCommonsConsoleTestCase {

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'users';

/**
 * Shell name
 *
 * @var string
 */
	protected $_shellName = 'UsersShell';

/**
 * startup()のテスト
 *
 * @return void
 */
	public function testStartup() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadShell($shell);

		//チェック
		$this->$shell->expects($this->at(0))->method('out')
			->with(__d('users', 'NetCommons Users Shell'));

		//テスト実施
		$this->$shell->startup();
	}

}
