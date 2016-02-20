<?php
/**
 * ImportTask::execute()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsConsoleTestCase', 'NetCommons.TestSuite');

/**
 * ImportTask::execute()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Console\Command\Task\ImportTask
 */
class UsersConsoleCommandTaskImportTaskExecuteTest extends NetCommonsConsoleTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.plugin_manager.plugins_role',
		'plugin.user_roles.user_role_setting',
	);

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
	protected $_shellName = 'ImportTask';

/**
 * execute()のテスト
 *
 * @return void
 */
	public function testExecute() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadTask($shell);
		$this->$shell->User = $this->getMockForModel('Users.User', array('importUsers'));

		//チェック
		$this->$shell->User->expects($this->once())->method('importUsers')
			->will($this->returnValue(true));

		$this->$shell->expects($this->at(0))->method('out')
			->with('<success>Import success.</success>');

		//テスト実施
		$this->$shell->args = array(__FILE__);
		$this->$shell->execute();

		Current::$current = array();
	}

/**
 * ImportErrorのテスト
 *
 * @return void
 */
	public function testExecuteImportError() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadTask($shell);
		$this->$shell->User = $this->getMockForModel('Users.User', array('importUsers'));

		//チェック
		$this->$shell->User->expects($this->once())->method('importUsers')
			->will($this->returnValue(false));

		$this->$shell->expects($this->at(0))->method('out')
			->with('<error>Import error.</error>');

		//テスト実施
		$this->$shell->args = array(__FILE__);
		$this->$shell->execute();

		Current::$current = array();
	}

/**
 * NotFoundFileのテスト
 *
 * @return void
 */
	public function testExecuteNotFoundFile() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadTask($shell);

		//チェック
		$this->$shell->expects($this->at(0))->method('out')
			->with('<warning>Not found file.</warning>');

		//テスト実施
		$this->$shell->args = array('aaaaa');
		$this->$shell->execute();
	}

/**
 * ファイルなしのテスト
 *
 * @return void
 */
	public function testExecuteWithoutFile() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadTask($shell);

		//チェック
		$this->$shell->expects($this->at(0))->method('out')
			->with('<warning>Not found file.</warning>');

		//テスト実施
		$this->$shell->args = array();
		$this->$shell->execute();
	}

}
