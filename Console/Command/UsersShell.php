<?php
/**
 * UsersShell
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Shell', 'Console');
App::uses('AppShell', 'Console/Command');
App::uses('Current', 'NetCommons.Utility');

/**
 * Userデータに関するシェル
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Console\Command
 */
class UsersShell extends AppShell {

/**
 * Contains tasks to load and instantiate
 *
 * @var array
 */
	public $tasks = array(
		'Users.UserImport',
	);

/**
 * Override startup
 *
 * @return void
 */
	public function startup() {
		$this->out(__d('users', 'NetCommons Users Shell'));
		$this->hr();
	}

/**
 * Override main
 *
 * @return void
 */
	public function main() {
		$this->out(__d('users', '[I]mport'));
		$this->out(__d('users', '[H]elp'));
		$this->out(__d('users', '[Q]uit'));

		$choice = strtolower(
			$this->in(__d('net_commons', 'What would you like to do?'), ['I', 'H', 'Q'], 'Q')
		);
		switch ($choice) {
			case 'i':
				$this->UserImport->execute();
				return $this->_stop();
			case 'h':
				$this->out($this->getOptionParser()->help());
				break;
			case 'q':
				return $this->_stop();
			default:
				$this->out(
					__d('net_commons', 'You have made an invalid selection. ' .
								'Please choose a command to execute by entering %s.', '[I, H, Q]')
				);
		}
		$this->hr();
	}

/**
 * Get the option parser.
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('users', 'The Users shell.'))
			->addSubcommand('user_import', array(
				'help' => __d('user_manager', 'Import description'),
				'parser' => $this->UserImport->getOptionParser(),
			));
	}

}
