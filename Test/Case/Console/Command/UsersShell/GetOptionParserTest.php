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
class ConsoleCommandUsersShellGetOptionParserTest extends NetCommonsConsoleTestCase {

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
		$this->$shell = $this->loadShell($shell, 'h');
		$this->$shell->Import = $this->getMock('ImportTask',
				array('getOptionParser'), array(), '', false);

		//チェック
		$this->$shell->Import->expects($this->once())->method('getOptionParser')
			->will($this->returnValue(''));

		//テスト実施
		$result = $this->$shell->getOptionParser();

		//チェック
		$this->assertEquals('ConsoleOptionParser', get_class($result));
	}

}
