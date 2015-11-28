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
		'Users.Import',
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

		$choice = strtolower($this->in(__d('users', 'What would you like to do?'), array('I', 'H', 'Q')));
		switch ($choice) {
			case 'i':
				$this->Import->execute();
				return $this->_stop();
				break;
			case 'h':
				$this->out($this->getOptionParser()->help());
				break;
			case 'q':
				return $this->_stop();
			default:
				$this->out(__d('cake_console', 'You have made an invalid selection. Please choose a command to execute by entering I, H, or Q.'));
		}
		$this->hr();
		$this->main();
	}

/**
 * Get the option parser.
 *
 * @return void
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__d('users', 'The Users shell.'))
			->addSubcommand('import', array(
				'help' => __d('user_manager', 'Import description'),
				'parser' => $this->Import->getOptionParser(),
			));
	}

}
