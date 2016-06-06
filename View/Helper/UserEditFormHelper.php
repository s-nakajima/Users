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
 * Before render callback. beforeRender is called before the view file is rendered.
 *
 * Overridden in subclasses.
 *
 * @param string $viewFile The view file that is going to be rendered
 * @return void
 */
	public function beforeRender($viewFile) {
		$this->NetCommonsHtml->css('/users/css/style.css');
		$this->NetCommonsHtml->script('/data_types/js/data_types.jquery.js');
		parent::beforeRender($viewFile);
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
			$html .= $this->__input('TrackableCreator.handlename', $userAttribute);

		} elseif ($userAttributeKey === 'modified_user') {
			$html .= $this->__input('TrackableUpdater.handlename', $userAttribute);

		} elseif ($userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_IMG) {
			$html .= $this->__input('User.' . $userAttributeKey, $userAttribute);

		} elseif ($this->User->hasField($userAttributeKey)) {
			$html .= $this->__input('User.' . $userAttributeKey, $userAttribute);

		} elseif ($this->UsersLanguage->hasField($userAttributeKey)) {
			foreach ($this->_View->request->data['UsersLanguage'] as $index => $usersLanguage) {
				$html .= '<div ng-show="activeLangId === \'' . $usersLanguage['language_id'] . '\'" ng-cloak>';
				$html .= $this->__input(
						'UsersLanguage.' . $index . '.' . $userAttributeKey, $userAttribute,
						$usersLanguage['language_id']
				);
				$html .= '</div>';
			}

		} else {
			$html .= h($userAttribute['UserAttribute']['name']);
			return $html;
		}

		return $html;
	}

/**
 * 会員の入力フォームの表示(自分)
 *
 * @param array $userAttribute UserAttributeデータ
 * @return string HTMLタグ
 */
	public function userInputForSelf($userAttribute) {
		$html = '';

		//以下の条件の場合、何も表示しない
		// * 他人の場合
		// * 非表示(display=OFF)項目
		// * 自分が読めない && パスワード以外
		// * 自分自身が書けない && ラベルタイプ以外
		if (Current::read('User.id') !== $this->_View->viewVars['user']['User']['id'] ||
				! $userAttribute['UserAttributeSetting']['display'] ||
				! $userAttribute['UserAttributesRole']['self_readable'] &&
					$userAttribute['UserAttribute']['key'] !== UserAttribute::PASSWORD_FIELD ||
				! $userAttribute['UserAttributesRole']['self_editable'] &&
					$userAttribute['UserAttributeSetting']['data_type_key'] !== DataType::DATA_TYPE_LABEL) {

			return $html;
		}

		$html .= $this->userInput($userAttribute);

		return $html;
	}

/**
 * 会員の公開非公開の有無ラジオボタンの表示
 *
 * @param array $userAttribute UserAttributeデータ
 * @return string HTMLタグ
 */
	public function userPublicForSelf($userAttribute) {
		$html = '';

		//以下の条件の場合、何も表示しない
		// * 各自で公開非公開の設定ができない
		// * 他人の場合
		// * 非表示(display=OFF)項目
		// * 自分が読めない
		// * 自分自身が書けない && ラベルタイプ以外
		if (! $userAttribute['UserAttributeSetting']['self_public_setting'] ||
				Current::read('User.id') !== Hash::get($this->_View->viewVars, 'user.User.id') ||
				! $userAttribute['UserAttributeSetting']['display'] ||
				! $userAttribute['UserAttributesRole']['self_readable'] ||
				! $userAttribute['UserAttributesRole']['self_editable'] &&
					$userAttribute['UserAttributeSetting']['data_type_key'] !== DataType::DATA_TYPE_LABEL) {

			return $html;
		}

		$fieldName =
			'User.' . sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $userAttribute['UserAttribute']['key']);

		$html .= '<div class="form-control user-public-type-form-control nc-data-label">';
		$html .= $this->NetCommonsForm->radio($fieldName, User::$publicTypes, array(
			'div' => array('class' => 'form-inline'),
		));
		$html .= '</div>';
		return $html;
	}

/**
 * 会員のメールの受信可否のチェックボックスボタンの表示
 *
 * @param array $userAttribute UserAttributeデータ
 * @return string HTMLタグ
 */
	public function userMailReceptionForSelf($userAttribute) {
		$html = '';

		//以下の条件の場合、何も表示しない
		// * メール項目でない
		// * 各自でメール受信可否の設定ができない
		// * 他人の場合
		// * 非表示(display=OFF)項目
		// * 自分自身が書けない
		if ($userAttribute['UserAttributeSetting']['data_type_key'] !== DataType::DATA_TYPE_EMAIL ||
				! $userAttribute['UserAttributeSetting']['self_email_setting'] ||
				Current::read('User.id') !== Hash::get($this->_View->viewVars, 'user.User.id') ||
				! $userAttribute['UserAttributeSetting']['display'] ||
				! $userAttribute['UserAttributesRole']['self_editable']) {

			return $html;
		}

		$fieldName =
			'User.' .
			sprintf(UserAttribute::MAIL_RECEPTION_FIELD_FORMAT, $userAttribute['UserAttribute']['key']);

		$html .= '<div class="form-control nc-data-label">';
		$html .= $this->NetCommonsForm->inlineCheckbox($fieldName, array(
			'label' => __d('users', 'Yes, I receive by e-mail.')
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

		if ($dataTypeKey === DataType::DATA_TYPE_LABEL &&
				! Hash::get($this->_View->request->data, $fieldName)) {
			return $html;
		}

		$attributes = array();

		//ラベル
		$attributes['label'] = $this->SwitchLanguage->inputLabel(
			$userAttribute['UserAttribute']['name'], $languageId
		);

		//入力タイプ
		$attributes['type'] = $dataTypeKey;

		//必須項目ラベルの設定
		$attributes['required'] = (bool)$userAttribute['UserAttributeSetting']['required'];

		//選択肢の設定
		if (isset($userAttribute['UserAttributeChoice'])) {
			if ($userAttributeKey === 'role_key') {
				$keyPath = '{n}.key';
			} else {
				$keyPath = '{n}.code';
			}
			$attributes['options'] = Hash::combine(
				$userAttribute['UserAttributeChoice'], $keyPath, '{n}.name'
			);
			if (! $userAttribute['UserAttributeSetting']['required']) {
				$attributes['empty'] = !(bool)$userAttribute['UserAttributeSetting']['required'];
			}
		}

		$html .= $this->__inputDataType($fieldName, $userAttribute, $attributes);

		$html .= $this->userPublicForSelf($userAttribute);

		$html .= $this->userMailReceptionForSelf($userAttribute);

		return $html;
	}

/**
 * データタイプに対するinputタグのHTML出力
 *
 * @param string $fieldName フィールド名("Modelname.fieldname"形式)
 * @param array $userAttribute UserAttributeデータ
 * @param array $attributes HTMLタグ属性
 * @return string HTML 入力HTML
 */
	private function __inputDataType($fieldName, $userAttribute, $attributes = array()) {
		$output = '';

		switch ($attributes['type']) {
			case DataType::DATA_TYPE_IMG:
				$output .= $this->__image($fieldName, $userAttribute, $attributes);
				break;

			case DataType::DATA_TYPE_PASSWORD:
				$output .= '<div class="form-group row">';
				$output .= '<div class="col-xs-12 col-sm-3 user-edit-label">';
				$output .= $this->NetCommonsForm->label(
					$fieldName,
					$attributes['label'],
					['required' => Hash::get($attributes, 'required'), 'error' => true]
				);
				$output .= '</div>';

				$attributes = Hash::insert($attributes, 'label', false);
				$attributes = Hash::insert($attributes, 'div', false);
				$attributes = Hash::merge(['again' => true], $attributes);

				$output .= '<div class="col-xs-12 col-sm-9">';
				$output .= $this->NetCommonsForm->input($fieldName, $attributes);
				$output .= '</div>';
				$output .= '</div>';
				break;

			case DataType::DATA_TYPE_RADIO:
			case DataType::DATA_TYPE_CHECKBOX:
				if ($attributes['type'] === DataType::DATA_TYPE_CHECKBOX) {
					$default = Hash::get($this->_View->request->data, $fieldName, '');
					$attributes['default'] = explode(',', $default);
				}
				$output .= '<div class="form-group row">';
				$output .= '<div class="col-xs-12 col-sm-3 user-edit-label">';
				$output .= $this->NetCommonsForm->label(
					$fieldName,
					$attributes['label'],
					['required' => Hash::get($attributes, 'required'), 'error' => true]
				);
				$output .= '</div>';

				$attributes = Hash::insert($attributes, 'label', false);
				$attributes = Hash::insert($attributes, 'div', false);
				$attributes = Hash::merge(['inline' => true], $attributes);

				$output .= '<div class="col-xs-12 col-sm-9 user-edit-choice">';
				$output .= $this->NetCommonsForm->input($fieldName, $attributes);
				$output .= '</div>';
				$output .= '</div>';
				break;

			case DataType::DATA_TYPE_TEXTAREA:
				$attributes['rows'] = 3;
				$output .= $this->NetCommonsForm->input($fieldName, $attributes);
				break;

			default:
				$output .= '<div class="form-group row">';
				$output .= '<div class="col-xs-12 col-sm-3 user-edit-label">';
				$output .= $this->NetCommonsForm->label(
					$fieldName,
					$attributes['label'],
					['required' => Hash::get($attributes, 'required'), 'error' => true]
				);
				$output .= '</div>';

				$attributes = Hash::insert($attributes, 'label', false);
				$attributes = Hash::insert($attributes, 'div', false);

				$output .= '<div class="col-xs-12 col-sm-9">';
				$output .= $this->NetCommonsForm->input($fieldName, $attributes);
				$output .= '</div>';
				$output .= '</div>';
		}

		return $output;
	}

/**
 * Generates a form input element complete with label and wrapper div
 *
 * @param string $fieldName フィールド名("Modelname.fieldname"形式)
 * @param array $userAttribute UserAttributeデータ
 * @param array $attributes HTMLタグ属性
 * @return string imageタグ
 */
	private function __image($fieldName, $userAttribute, $attributes = array()) {
		$output = '';
		$userAttributeKey = $userAttribute['UserAttribute']['key'];

		if (Hash::get($this->_View->request->data, 'UploadFile.' . $userAttributeKey . '.id')) {
			$attributes['url'] = NetCommonsUrl::actionUrl(array(
				'plugin' => 'users',
				'controller' => 'users',
				'action' => 'download',
				'key' => Hash::get($this->_View->request->data, 'User.id'),
				'key2' => Hash::get(
					$this->_View->request->data, 'UploadFile.' . $userAttributeKey . '.field_name'
				),
				'medium',
			));
		} else {
			$attributes['url'] = $this->NetCommonsHtml->url('/users/img/noimage.gif');
		}
		if (Hash::get($this->_View->request->data, 'User.is_avatar_auto_created') &&
				$userAttributeKey === UserAttribute::AVATAR_FIELD) {
			$attributes['remove'] = false;
			$attributes['filename'] = false;
		}

		if (! isset($attributes['url'])) {
			return $output;
		}

		$label = $attributes['label'];
		if (Hash::get($attributes, 'required')) {
			$label .= $this->_View->element('NetCommons.required');
		}
		$attributes = Hash::remove($attributes, 'label');
		$attributes = Hash::remove($attributes, 'required');

		$output .= $this->NetCommonsForm->label($fieldName, $label);
		$output .= '<div class="thumbnail user-thumbnail user-edit-thumbnail">';

		$output .= $this->NetCommonsHtml->image($attributes['url'], array(
			'class' => 'img-responsive img-rounded',
			'alt' => Hash::get($attributes, 'alt'),
			'id' => $this->domId($fieldName . '_image')
		));

		$output .= '</div>';
		$output .= $this->NetCommonsForm->uploadFile($fieldName, Hash::merge(array(
			'label' => false,
			'data-type-key' => 'image',
			//'class' => false,
		), $attributes));

		return $output;
	}

}
