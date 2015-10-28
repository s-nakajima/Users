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

App::uses('AppHelper', 'View/Helper');

/**
 * UserEditForm Helper
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\View\Helper
 */
class UserEditFormHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'DataTypes.DataTypeForm',
		'NetCommons.NetCommonsHtml',
		'NetCommons.NetCommonsForm',
	);

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
		$userAttributeKey = $userAttribute['UserAttribute']['key'];

		if ($userAttributeKey === 'created_user') {
			$html .= '<div class="form-group">';
			$html .= $this->__input('TrackableCreator.handlename', $userAttribute);
			$html .= '</div>';
		} elseif ($userAttributeKey === 'modified_user') {
			$html .= '<div class="form-group">';
			$html .= $this->__input('TrackableUpdater.handlename', $userAttribute);
			$html .= '</div>';
		} elseif ($this->User->hasField($userAttributeKey)) {
			$html .= '<div class="form-group">';
			$html .= $this->__input('User.' . $userAttributeKey, $userAttribute);
			$html .= '</div>';
		} elseif ($this->UsersLanguage->hasField($userAttributeKey)) {
			foreach ($this->_View->request->data['UsersLanguage'] as $index => $usersLanguage) {
				$html .= '<div class="form-group"' . ' ng-show="activeLangId === \'' . $usersLanguage['language_id'] . '\'" ng-cloak>';
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
		$dataTypeKey = $userAttribute['UserAttributeSetting']['data_type_key'];
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
			if ($userAttributeKey === 'role_key') {
				$keyPath = '{n}.key';
			} else {
				$keyPath = '{n}.code';
			}
			$attributes['options'] = Hash::combine($userAttribute['UserAttributeChoice'], $keyPath, '{n}.name');
			if (! $userAttribute['UserAttributeSetting']['required']) {
				$attributes['empty'] = !(bool)$userAttribute['UserAttributeSetting']['required'];
			}
		}

		if ($userAttributeKey === 'avatar') {
			$attributes['noimage'] = '/users/img/noimage.gif';
		}

		$html .= $this->DataTypeForm->inputDataType(
				$dataTypeKey,
				$fieldName,
				$userAttribute['UserAttribute']['name'] . $requireLabel,
				$attributes);

		return $html;
	}

}
