<?php
/**
 * UsersShell::getOptionParser()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsConsoleTestCase', 'NetCommons.TestSuite');

/**
 * UsersShell::getOptionParser()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Console\Command\UsersShell
 */
class UsersConsoleCommandUsersShellGetOptionParserTest extends NetCommonsConsoleTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array();

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
 * getOptionParser()のテスト
 *
 * @return void
 */
	public function testGetOptionParser() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadShell($shell);

		//事前準備
		$task = 'UserImport';
		$this->$shell->$task = $this->getMock($task,
				array('getOptionParser'), array(), '', false);
		$this->$shell->$task->expects($this->once())->method('getOptionParser')
			->will($this->returnValue(true));

		//テスト実施
		$result = $this->$shell->getOptionParser();

		//チェック
		$this->assertEquals('ConsoleOptionParser', get_class($result));

		//サブタスクヘルプのチェック
		$expected = array();
		$actual = array();
		$subCommands = array(
			'user_import' => __d('user_manager', 'Import description'),
		);
		foreach ($subCommands as $subCommand => $helpMessage) {
			$expected[] = $subCommand . ' ' . $helpMessage;
			$actual[] = $result->subcommands()[$subCommand]->help(strlen($subCommand) + 1);
		}
		$this->assertEquals($expected, $actual);
	}

}
