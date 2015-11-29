<?php
/**
 * ImportTask
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppShell', 'Console/Command');
App::uses('AuthComponent', 'Controller/Component/Auth');

/**
 * UserのImportによるシェル
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Console\Command
 */
class ImportTask extends AppShell {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Users.User',
	);

/**
 * Execution method always used for tasks
 *
 * @return void
 */
	public function execute() {
		$file = Hash::get($this->args, '0');
		if (! $file) {
			$this->args[0] = $this->in(__d('users', 'Enter import file path.'));
			$this->execute();
			return;
		}
		if (! file_exists($file)) {
			$this->out(__d('users', '<warning>Not found file.</warning>'));
			$this->args[0] = null;
			$this->execute();
			return;
		}

		$user = $this->User->findById(1);
		CakeSession::write(AuthComponent::$sessionKey, $user['User']);
		Current::initialize(new CakeRequest());
		$this->User->prepare();

		if (! $this->User->importUsers($file)) {
			//バリデーションエラーの場合
			//$this->NetCommons->handleValidationError($this->User->validationErrors);
			$this->out(__d('users', '<warning>Import error</warning>'));
			$this->out(var_export($this->User->validationErrors, true));
		}
	}

/**
 * Gets the option parser instance and configures it.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->description(__d('users', 'NetCommons UserImport shell'))
			->addArgument('file', array(
				'short' => 'f',
				'help' => __d('user_manager', 'Import file path.'),
				'required' => true
			));

		return $parser;
	}
}
