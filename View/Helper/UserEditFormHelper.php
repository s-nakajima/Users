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
		'NetCommons.NetCommonsHtml',
		'NetCommons.NetCommonsForm',
//		'DataTypes.DataTypeForm'
	);

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
//	public function __construct(View $View, $settings = array()) {
//		parent::__construct($View, $settings);
//		$this->User = ClassRegistry::init('Users.User');
//		$this->UsersLanguage = ClassRegistry::init('Users.UsersLanguage');
//	}


/**
 * 会員項目設定のHTMLを出力する(段目)
 *
 * @return string HTML
 */
	public function renderRow() {
		$output = '';

		foreach ($this->_View->viewVars['userAttributeLayouts'] as $layout) {
			$output .= $this->_View->element('Users/render_edit_row', array(
				'layout' => $layout
			));
		}
		return $output;
	}

/**
 * 会員項目設定のHTMLを出力する(列)
 *
 * @param array $layout userAttributeLayoutデータ配列
 * @return string HTML
 */
	public function renderCol($layout) {
		$output = '';

		$row = $layout['UserAttributeLayout']['id'];
		for ($col = 1; $col <= UserAttributeLayout::LAYOUT_COL_NUMBER; $col++) {
			if ($layout['UserAttributeLayout']['col'] === '2' &&
					! isset($this->_View->viewVars['userAttributes'][$row][1]) &&
					isset($this->_View->viewVars['userAttributes'][$row][2])) {
				$output .= '<div class="col-xs-12 col-sm-offset-6 col-sm-6">';
			} else {
				$output .= '<div class="col-xs-12 col-sm-' . (12 / $layout['UserAttributeLayout']['col']) . '">';
			}

			if (isset($this->_View->viewVars['userAttributes'][$row][$col])) {
				foreach ($this->_View->viewVars['userAttributes'][$row][$col] as $userAttribute) {
					$output .= $this->_View->element('Users/render_edit_col', array(
						'layout' => $layout,
						'userAttribute' => $userAttribute
					));
				}
			}

			$output .= '</div>';
		}
		return $output;
	}

/**
 * Generates a form input element complete with label and wrapper div
 *
 * @param array $userAttribute user_attribute data
 * @return string Completed form widget.
 */
//	public function userEditInput($userAttribute) {
//		$html = '';
//
//		$userAttributeKey = $userAttribute['UserAttribute']['key'];
//
//		if ($this->User->hasField($userAttributeKey)) {
//			$html .= '<div class="form-group">';
//			$html .= $this->__input('User.' . $userAttributeKey, $userAttribute);
//			$html .= '</div>';
//		} elseif ($this->UsersLanguage->hasField($userAttributeKey)) {
//			foreach ($this->_View->request->data['UsersLanguage'] as $index => $usersLanguage) {
//				$html .= '<div class="form-group"' . ' ng-show="activeLangId === \'' . $usersLanguage['language_id'] . '\'" ng-cloak>';
//				$html .= $this->__input('UsersLanguage.' . $index . '.' . $userAttributeKey, $userAttribute);
//				$html .= '</div>';
//			}
//
//		} else {
//			$html .= h($userAttribute['UserAttribute']['name']);
//			return $html;
//		}
//
//		return $html;
//	}

/**
 * Generates a form input element complete with label and wrapper div
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param array $userAttribute user_attribute data
 * @return string Completed form widget.
 */
//	private function __input($fieldName, $userAttribute) {
//		$html = '';
//
//		$dataTypeTemplateKey = $userAttribute['DataTypeTemplate']['key'];
//		$userAttributeKey = $userAttribute['UserAttribute']['key'];
//
//		//必須項目ラベルの設定
//		if ($userAttribute['UserAttributeSetting']['required']) {
//			$requireLabel = $this->_View->element('NetCommons.required');
//		} else {
//			$requireLabel = '';
//		}
//
//		$attributes = array();
//
//		//選択肢の設定
//		if (isset($userAttribute['UserAttributeChoice'])) {
//			$attributes['options'] = Hash::combine($userAttribute['UserAttributeChoice'], '{n}.key', '{n}.name');
//			if (! $userAttribute['UserAttributeSetting']['required']) {
//				$attributes['empty'] = ! $userAttribute['UserAttributeSetting']['required'];
//			}
//		}
//
//		if ($userAttributeKey === 'avatar') {
//			$attributes['noimage'] = '/users/img/noimage.gif';
//		}
//
//		$html .= $this->DataTypeForm->inputDataType(
//				$dataTypeTemplateKey,
//				$fieldName,
//				$userAttribute['UserAttribute']['name'] . $requireLabel,
//				$attributes);
//
//		return $html;
//	}

}
