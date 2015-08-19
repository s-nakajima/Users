<?php
/**
 * UserValue Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');

/**
 * UserValue Helper
 *
 * @package NetCommons\Users\View\Helper
 */
class UserValueHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array('Html');

/**
 * UserAttributes data
 *
 * @var array
 */
	public $userAttributes;

/**
 * User data
 *
 * @var array
 */
	public $user;

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);

		//Modelの呼び出し
		$this->User = ClassRegistry::init('Users.User');
		$this->UsersLanguage = ClassRegistry::init('Users.UsersLanguage');

		if (! isset($settings['userAttributes'])) {
			$this->UserAttribute = ClassRegistry::init('UserAttributes.UserAttribute');
			$settings['userAttributes'] = $this->UserAttribute->getUserAttributesForLayout(Configure::read('Config.languageId'));
		}
		//$this->userAttributes = Hash::extract(, '{n}.{n}.{n}');
		$this->userAttributes = Hash::combine($settings['userAttributes'], '{n}.{n}.{n}.UserAttribute.key', '{n}.{n}.{n}');

		if (isset($settings['user'])) {
			$this->user = $settings['user'];
		}
	}

/**
 * Set user data
 *
 * @param array $user User data
 * @return void
 */
	public function set($user) {
		$this->user = $user;
	}

/**
 * Output user value
 *
 * @param array $user User data
 * @param string $fieldName Name of user field
 * @return string User value
 */
	public function label($fieldName) {
		$userAttribute = Hash::extract($this->userAttributes, '{s}.UserAttribute[key=' . $fieldName . ']');
		return $this->_View->Paginator->sort($fieldName, $userAttribute[0]['name']);
	}

/**
 * Output user value
 *
 * @param array $user User data
 * @param string $fieldName Name of user field
 * @return string User value
 */
	public function display($fieldName) {
		$userAttribute = $this->userAttributes[$fieldName];

		$modelName = '';
		if ($this->User->hasField($fieldName)) {
			$modelName = $this->User->alias;
		} elseif ($this->UsersLanguage->hasField($fieldName)) {
			$modelName = $this->UsersLanguage->alias;
		}
//var_dump($userAttribute);
		if ($fieldName === 'handlename') {
			$value = $this->Html->link($this->user[$modelName][$fieldName], '/user_manager/user_manager/edit/' . $this->user['User']['id'] . '/');
		} elseif (isset($userAttribute['UserAttributeChoice']) && $this->user[$modelName][$fieldName]) {
			$values = Hash::extract($userAttribute['UserAttributeChoice'], '{n}[key=' . $this->user[$modelName][$fieldName] . ']');
			if ($values = Hash::extract($userAttribute['UserAttributeChoice'], '{n}[key=' . $this->user[$modelName][$fieldName] . ']')) {
				$value = $values[0]['name'];
			} else {
				$value = '';
			}
		} else {
			$value = $this->user[$modelName][$fieldName];
		}

		return $value;
	}

}
