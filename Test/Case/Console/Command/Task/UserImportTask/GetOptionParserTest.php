<?php
/**
 * UserImportTask::getOptionParser()のテスト
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsConsoleTestCase', 'NetCommons.TestSuite');

/**
 * UserImportTask::getOptionParser()のテスト
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Case\Console\Command\Task\ImportTask
 */
class UsersConsoleCommandTaskUserImportTaskGetOptionParserTest extends NetCommonsConsoleTestCase {

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'users';

/**
 * Task name
 *
 * @var string
 */
	protected $_shellName = 'UserImportTask';

/**
 * getOptionParser()のテスト
 *
 * @return void
 */
	public function testGetOptionParser() {
		$shell = $this->_shellName;
		$this->$shell = $this->loadTask($shell);

		//テスト実施
		$result = $this->$shell->getOptionParser();

		//チェック
		$this->assertEquals('ConsoleOptionParser', get_class($result));
	}

}
