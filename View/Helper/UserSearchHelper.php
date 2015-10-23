<?php
/**
 * UserSearch Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');

/**
 * UserSearch Helper
 *
 * @package NetCommons\Users\View\Helper
 */
class UserSearchHelper extends AppHelper {

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

///**
// * User data
// *
// * @var array
// */
//	public $user;

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

		$this->userAttributes = Hash::combine($this->_View->viewVars['userAttributes'], '{n}.{n}.{n}.UserAttribute.key', '{n}.{n}.{n}');
	}

/**
 * Set user data
 *
 * @param array $user User data
 * @return void
 */
//	public function set($user) {
//		$this->user = $user;
//	}

/**
 * テーブルヘッダーの出力
 *
 * @param string $fieldName Name of user field
 * @return string User value
 */
	public function tableHeaders() {
		$output = '';

		foreach ($this->_View->viewVars['displayFields'] as $fieldName) {
			if (! isset($this->userAttributes[$fieldName])) {
				continue;
			}

			$output .= '<th>';
			if ($fieldName === 'room_role_key') {
				$output .= __d('rooms', 'Room role');
			} else {
				$userAttribute = Hash::extract($this->userAttributes, '{s}.UserAttribute[key=' . $fieldName . ']');
				$output .= $this->_View->Paginator->sort($fieldName, $userAttribute[0]['name']);
			}
			$output .= '</th>';
		}

		return $output;
	}

/**
 * テーブルセルの出力
 *
 * @param array $user ユーザデータ
 * @return string User value
 */
	public function tableCells($user) {
		$output = '';

		foreach ($this->_View->viewVars['displayFields'] as $fieldName) {
			if (! isset($this->userAttributes[$fieldName])) {
				continue;
			}

			$modelName = '';
			if ($this->User->hasField($fieldName)) {
				$modelName = $this->User->alias;
			} elseif ($this->UsersLanguage->hasField($fieldName)) {
				$modelName = $this->UsersLanguage->alias;
			}
			$userAttribute = $this->userAttributes[$fieldName];

			if ($fieldName === 'handlename' && $user[$this->User->alias]['role_key'] !== UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR) {
				$value = $this->Html->link($user[$modelName][$fieldName], '/user_manager/user_manager/edit/' . $user['User']['id'] . '/');
			} elseif (isset($userAttribute['UserAttributeChoice']) && $user[$modelName][$fieldName]) {
				$values = Hash::extract($userAttribute['UserAttributeChoice'], '{n}[key=' . $user[$modelName][$fieldName] . ']');
				if ($values = Hash::extract($userAttribute['UserAttributeChoice'], '{n}[key=' . $user[$modelName][$fieldName] . ']')) {
					$value = h($values[0]['name']);
				} else {
					$value = '';
				}
			} else {
				$value = h($user[$modelName][$fieldName]);
			}

			$output .= '<td>' . $value . '</td>';
		}

		return $output;
	}

}
