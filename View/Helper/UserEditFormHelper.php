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
	public $helpers = array('Form');

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
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param array $userAttribute user_attribute data
 * @return string Completed form widget.
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#creating-form-elements
 */
	public function userEditInput($userAttribute) {
		$html = '';
var_dump($userAttribute);

		//必須項目ラベルの設定
		if ($userAttribute['UserAttributeSetting']['required']) {
			$requireLabel = $this->_View->element('NetCommons.required');
		} else {
			$requireLabel = '';
		}

		$fieldName = '';
		if ($this->User) {

		}


		$html .= '<ul class="user-attribute-edit">';
		$html .= '<li class="list-group-item form-group">';





		$dataTypeKey = $userAttribute['DataTypeTemplate']['data_type_key'];
		switch ($dataTypeKey) {
			case 'text':
//				$this->Form->input('UserRole.' . $index . '.name', array(
//					'type' => 'text',
//					'label' => __d('user_roles', 'User role name') . $requireLabel,
//					'class' => 'form-control',
//				));

				break;
		}

		//$html .= h($userAttribute['UserAttribute']['name']);

		$html .= '</li>';
		$html .= '</ul>';


		return $html;
	}

}
