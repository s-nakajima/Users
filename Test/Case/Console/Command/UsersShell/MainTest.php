<?php
/**
 * UsersShell::main()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsConsoleTestCase', 'NetCommons.TestSuite');

/**
 * UsersShell::main()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Console\Command\UsersShell
 */
class ConsoleCommandUsersShellMainTest extends NetCommonsConsoleTestCase {

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
	public $_shellName = 'UsersShell';

/**
 * main()のチェック
 *
 * @return void
 */
	private function __expectsMain() {
		$shell = $this->_shellName;

		$this->$shell->expects($this->at(0))->method('out')
			->with(__d('users', '[I]mport'));
		$this->$shell->expects($this->at(1))->method('out')
			->with(__d('users', '[H]elp'));
		$this->$shell->expects($this->at(2))->method('out')
			->with(__d('users', '[Q]uit'));
	}

/**
 * main()のテスト
 *
 * @return void
 */
	public function testMain() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadShell($shell);

		//チェック
		$this->__expectsMain();
		$this->$shell->expects($this->exactly(4))->method('out')
			->will($this->returnValue(true));

		//テスト実施
		$this->$shell->main();
	}

/**
 * main()のテスト[Import]
 *
 * @return void
 */
	public function testMainImport() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadShell($shell, 'i');
		$this->$shell->Import = $this->getMock('ImportTask',
				array('execute'), array(), '', false);

		//チェック
		$this->__expectsMain();

		$this->$shell->Import->expects($this->once())->method('execute')
			->will($this->returnValue(true));
		$this->$shell->expects($this->once())->method('_stop')
			->will($this->returnValue(true));

		//テスト実施
		$this->$shell->main();
	}

/**
 * main()のテスト[Help]
 *
 * @return void
 */
	public function testMainHelp() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadShell($shell, 'h');
		$this->$shell->Import = $this->getMock('ImportTask',
				array('getOptionParser'), array(), '', false);

		//チェック
		$this->__expectsMain();
		$this->$shell->expects($this->exactly(4))->method('out')
			->will($this->returnValue(true));
		$this->$shell->Import->expects($this->once())->method('getOptionParser')
			->will($this->returnValue(''));

		//テスト実施
		$this->$shell->main();
	}

/**
 * main()のテスト[Quit]
 *
 * @return void
 */
	public function testMainQuit() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadShell($shell, 'q');

		//チェック
		$this->__expectsMain();
		$this->$shell->expects($this->exactly(3))->method('out')
			->will($this->returnValue(true));
		$this->$shell->expects($this->once())->method('_stop')
			->will($this->returnValue(true));

		//テスト実施
		$this->$shell->main();
	}

}
