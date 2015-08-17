<?php
/**
 * UserEditForm Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('FormHelper', 'View/Helper');
App::uses('CakeNumber', 'Utility');

/**
 * UserEditForm Helper
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\View\Helper
 */
class UserEditFormHelper extends FormHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array('Form', 'DataTypes.DataTypeForm');

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->User = ClassRegistry::init('Users.User');
		$this->UsersLanguage = ClassRegistry::init('Users.UsersLanguage');
	}

/**
 * Generates a form input element complete with label and wrapper div
 *
 * @param array $userAttribute user_attribute data
 * @return string Completed form widget.
 */
	public function userEditInput($userAttribute) {
		$html = '';
//var_dump($userAttribute);

		$userAttributeKey = $userAttribute['UserAttribute']['key'];

		if ($this->User->hasField($userAttributeKey)) {
			$html .= '<div class="form-group">';
			$html .= $this->__input('User.' . $userAttributeKey, $userAttribute);
			$html .= '</div>';
		} elseif ($this->UsersLanguage->hasField($userAttributeKey)) {
			foreach ($this->_View->request->data['UsersLanguage'] as $index => $usersLanguage) {
				$html .= '<div class="form-group"' . ' ng-show="activeLangId === \''. $usersLanguage['language_id'] .'\'" ng-cloak>';
				$html .= $this->__input('UsersLanguage.' . $index . '.' . $userAttributeKey, $userAttribute);
				$html .= '</div>';
			}

		} else {
			$html .= h($userAttribute['UserAttribute']['name']);
			return $html;
		}

		return $html;
	}

/**
 * Generates a form input element complete with label and wrapper div
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param array $userAttribute user_attribute data
 * @return string Completed form widget.
 */
	private function __input($fieldName, $userAttribute) {
		$html = '';

		$dataTypeTemplateKey = $userAttribute['DataTypeTemplate']['key'];
		$userAttributeKey = $userAttribute['UserAttribute']['key'];

		//必須項目ラベルの設定
		if ($userAttribute['UserAttributeSetting']['required']) {
			$requireLabel = $this->_View->element('NetCommons.required');
		} else {
			$requireLabel = '';
		}

		$attributes = array();

		//選択肢の設定
		if (isset($userAttribute['UserAttributeChoice'])) {
			$attributes['options'] = Hash::combine($userAttribute['UserAttributeChoice'], '{n}.key', '{n}.name');
			if (! $userAttribute['UserAttributeSetting']['required']) {
				$attributes['empty'] = ! $userAttribute['UserAttributeSetting']['required'];
			}
		}

		$html .= $this->DataTypeForm->inputDataType(
				$dataTypeTemplateKey,
				$fieldName,
				$userAttribute['UserAttribute']['name'] . $requireLabel,
				$attributes);

		return $html;
	}

}
