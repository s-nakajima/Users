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
		'M17n.SwitchLanguage',
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
 * After render file callback.
 * Called after any view fragment is rendered.
 *
 * Overridden in subclasses.
 *
 * @param string $viewFile The file just be rendered.
 * @param string $content The content that was rendered.
 * @return void
 */
	public function afterRenderFile($viewFile, $content) {
		$content = $this->NetCommonsHtml->css('/data_types/css/style.css') . $content;

		parent::afterRenderFile($viewFile, $content);
	}

/**
 * 会員の入力フォームの表示
 *
 * @param array $userAttribute UserAttributeデータ
 * @return string HTMLタグ
 */
	public function userInput($userAttribute) {
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
		} elseif ($userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_IMG) {
			//$html .= '<div class="form-group">';
			$html .= $this->__input('User.' . $userAttributeKey, $userAttribute);
			//$html .= '</div>';
		} elseif ($this->User->hasField($userAttributeKey)) {
			$html .= '<div class="form-group">';
			$html .= $this->__input('User.' . $userAttributeKey, $userAttribute);
			$html .= '</div>';
		} elseif ($this->UsersLanguage->hasField($userAttributeKey)) {
			foreach ($this->_View->request->data['UsersLanguage'] as $index => $usersLanguage) {
				$html .= '<div class="form-group"' . ' ng-show="activeLangId === \'' . $usersLanguage['language_id'] . '\'" ng-cloak>';
				$html .= $this->__input('UsersLanguage.' . $index . '.' . $userAttributeKey, $userAttribute, $usersLanguage['language_id']);
				$html .= '</div>';
			}

		} else {
			$html .= h($userAttribute['UserAttribute']['name']);
			return $html;
		}

		return $html;
	}

/**
 * 会員の入力フォームの表示
 *
 * @param array $userAttribute UserAttributeデータ
 * @return string HTMLタグ
 */
	public function userInputForSelf($userAttribute) {
		$html = '';

		if ($userAttribute['UserAttributeSetting']['only_administrator'] ||
				Current::read('User.id') !== $this->_View->viewVars['user']['User']['id'] ||
				! $userAttribute['UserAttributesRole']['self_readable'] ||
				! $userAttribute['UserAttributesRole']['self_editable']) {

			return $html;
		}

		$html .= $this->userInput($userAttribute);

		return $html;
	}

/**
 * 会員の入力フォームの表示
 *
 * @param array $userAttribute UserAttributeデータ
 * @return string HTMLタグ
 */
	public function userPublicForSelf($userAttribute) {
		$html = '';

		if (! $userAttribute['UserAttributeSetting']['self_publicity'] ||
				Current::read('User.id') !== Hash::get($this->_View->viewVars, 'user.User.id') ||
				! $userAttribute['UserAttributesRole']['self_readable'] ||
				! $userAttribute['UserAttributesRole']['self_editable']) {

			return $html;
		}

		$fieldName = 'User.' . sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $userAttribute['UserAttribute']['key']);

		$html .= '<div class="form-control user-public-type-form-control nc-data-label">';
		$html .= $this->NetCommonsForm->radio($fieldName, User::$publicTypes, array(
			'div' => array('class' => 'form-control form-inline'),
			'separator' => '<span class="radio-separator"></span>'
		));
		$html .= '</div>';
		return $html;
	}

/**
 * inputタグの生成
 *
 * @param string $fieldName フィールド名("Modelname.fieldname"形式)
 * @param array $userAttribute UserAttributeデータ
 * @param int $languageId 言語ID
 * @return string HTMLタグ
 */
	private function __input($fieldName, $userAttribute, $languageId = null) {
		$html = '';
		$dataTypeKey = $userAttribute['UserAttributeSetting']['data_type_key'];
		$userAttributeKey = $userAttribute['UserAttribute']['key'];

		$name = $this->SwitchLanguage->inputLabel($userAttribute['UserAttribute']['name'], $languageId);

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

		$html .= $this->DataTypeForm->inputDataType(
				$dataTypeKey,
				$fieldName,
				$name . $requireLabel,
				$attributes);

		$html .= $this->userPublicForSelf($userAttribute);

		return $html;
	}

}
