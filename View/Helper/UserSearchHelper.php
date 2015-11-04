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
	public $helpers = array(
		'NetCommons.NetCommonsHtml',
		'NetCommons.Date'
	);

/**
 * UserAttributes data
 *
 * @var array
 */
	public $userAttributes;

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
 * テーブルヘッダーの出力
 *
 * @return string User value
 */
	public function tableHeaders() {
		$output = '';

		foreach ($this->_View->viewVars['displayFields'] as $fieldName) {
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
 * 項目の有無
 *
 * @param string $fieldName 表示フィールド
 * @return string User value
 */
	public function hasUserAttribute($fieldName) {
		return isset($this->userAttributes[$fieldName]);
	}

/**
 * テーブル行の出力
 *
 * @param array $user ユーザデータ
 * @param bool $isEdit 編集の有無
 * @return string 行のHTMLタグ
 */
	public function tableRow($user, $isEdit) {
		$output = '';

		foreach ($this->_View->viewVars['displayFields'] as $fieldName) {
			if ($this->hasUserAttribute($fieldName)) {
				$modelName = '';
				if ($this->User->hasField($fieldName)) {
					$modelName = $this->User->alias;
				} elseif ($this->UsersLanguage->hasField($fieldName)) {
					$modelName = $this->UsersLanguage->alias;
				}
				$output .= $this->tableCell($user, $modelName, $fieldName, $isEdit);
			} else {
				$output .= '<td></td>';
			}
		}

		return $output;
	}

/**
 * テーブルセルの出力
 *
 * @param array $user ユーザデータ
 * @param string $modelName モデル名
 * @param string $fieldName 表示フィールド
 * @param bool $isEdit 編集の有無
 * @return string セルのHTMLタグ
 */
	public function tableCell($user, $modelName, $fieldName, $isEdit) {
		$userAttribute = $this->userAttributes[$fieldName];

		$value = '';
		if ($fieldName === 'handlename') {
			$value = $this->linkHandlename($user, $modelName, $fieldName, $isEdit);
		} elseif (isset($userAttribute['UserAttributeChoice']) && $user[$modelName][$fieldName]) {
			if ($fieldName === 'role_key') {
				$values = Hash::extract($userAttribute['UserAttributeChoice'], '{n}[key=' . $user[$modelName][$fieldName] . ']');
			} else {
				$values = Hash::extract($userAttribute['UserAttributeChoice'], '{n}[code=' . $user[$modelName][$fieldName] . ']');
			}
			$value = h($values[0]['name']);
		} elseif ($userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_DATETIME ||
				in_array($userAttribute['UserAttribute']['key'], ['created', 'modified', 'last_login', 'password_modified'])) {
			$value = h($this->Date->dateFormat($user[$modelName][$fieldName]));
		} else {
			$value = h($user[$modelName][$fieldName]);
		}

		return '<td>' . $value . '</td>';
	}

/**
 * ハンドルの出力
 *
 * @param array $user ユーザデータ
 * @param string $modelName モデル名
 * @param string $fieldName 表示フィールド
 * @param bool $isEdit 編集の有無
 * @return string ハンドルのHTMLタグ
 */
	public function linkHandlename($user, $modelName, $fieldName, $isEdit) {
		if (! $isEdit) {
			return h($user[$modelName][$fieldName]);
		} elseif (Current::read('User.role_key') === UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR ||
				$user[$this->User->alias]['role_key'] !== UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR) {

			return $this->NetCommonsHtml->link($user[$modelName][$fieldName],
				array('plugin' => 'user_manager', 'controller' => 'user_manager', 'action' => 'edit', $user['User']['id'])
			);
		}
	}

}
