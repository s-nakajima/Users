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
class UserLayoutHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'M17n.SwitchLanguage',
		'NetCommons.NetCommonsHtml',
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
 * ユーザ属性の表示
 *
 * @param array $userAttribute ユーザ属性データ
 * @return string HTMLタグ
 */
	public function display($userAttribute) {
		$html = '';

		if (! $this->isDisplayable($userAttribute)) {
			return $html;
		}

		$userAttributeKey = $userAttribute['UserAttribute']['key'];

		if ($userAttributeKey === 'created_user') {
			$fieldName = 'TrackableCreator.handlename';
		} elseif ($userAttributeKey === 'modified_user') {
			$fieldName = 'TrackableUpdater.handlename';
		} elseif ($this->User->hasField($userAttributeKey) ||
				$userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_IMG) {
			$fieldName = 'User.' . $userAttributeKey;
		} elseif ($this->UsersLanguage->hasField($userAttributeKey)) {
			$fieldName = 'UsersLanguage.%s.' . $userAttributeKey;
		} else {
			$fieldName = '';
		}

		$element = $this->userElement($fieldName, $userAttribute);
		if ($element) {
			$html .= '<div class="form-group">';
			$html .= $this->userLabelElement($userAttribute);
			if ($userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_IMG) {
				$html .= $element;
			} else {
				$html .= '<div class="form-control nc-data-label">';
				$html .= $element;
				$html .= '</div>';
			}
			$html .= '</div>';
		}

		return $html;
	}

/**
 * ユーザ属性のラベル表示
 *
 * @param array $userAttribute ユーザ属性データ
 * @return string HTMLタグ
 */
	public function userLabelElement($userAttribute) {
		$element = '';
		$userAttributeKey = $userAttribute['UserAttribute']['key'];

		if ($userAttribute['UserAttributeSetting']['display_label']) {
			//言語の表示
			if ($this->UsersLanguage->hasField($userAttributeKey)) {
				$element .= $this->SwitchLanguage->label($userAttribute['UserAttribute']['name'], array(
					'user-attribute-label'
				));
			} else {
				$element .= '<div class="user-attribute-label">' . h($userAttribute['UserAttribute']['name']) . '</div>';
			}
		}

		return $element;
	}

/**
 * ユーザ属性の表示
 *
 * @param string $fieldName モデルのフィールド名
 * @param array $userAttribute ユーザ属性データ
 * @return string HTMLタグ
 */
	public function userElement($fieldName, $userAttribute) {
		$element = '';
		$userAttributeKey = $userAttribute['UserAttribute']['key'];

		if ($userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_IMG) {
			if (Hash::get($this->_View->viewVars['user'], 'UploadFile.' . $userAttributeKey . '.id')) {
				$imageUrl = NetCommonsUrl::actionUrl(array(
					'plugin' => 'users',
					'controller' => 'users',
					'action' => 'download',
					'key' => Hash::get($this->_View->viewVars['user'], 'User.id'),
					Hash::get($this->_View->viewVars['user'], 'UploadFile.' . $userAttributeKey . '.field_name'),
					'medium',
				));
			} else {
				$imageUrl = $this->NetCommonsHtml->url('/users/img/noimage.gif');
			}
			$element .= '<div class="thumbnail data-type-thumbnail">';
			$element .= $this->NetCommonsHtml->image($imageUrl, array(
				'class' => 'img-responsive img-rounded',
			));
			$element .= '</div>';
		} elseif (isset($userAttribute['UserAttributeChoice'])) {
			if ($userAttributeKey === 'role_key') {
				$keyPath = '{n}[key=' . Hash::get($this->_View->viewVars['user'], $fieldName) . ']';
			} else {
				$keyPath = '{n}[code=' . Hash::get($this->_View->viewVars['user'], $fieldName) . ']';
			}
			$option = Hash::extract($userAttribute['UserAttributeChoice'], $keyPath);
			$element .= $option[0]['name'];

		} elseif ($this->UsersLanguage->hasField($userAttributeKey)) {
			foreach ($this->_View->viewVars['user']['UsersLanguage'] as $index => $usersLanguage) {
				$el = Hash::get($this->_View->viewVars['user'], sprintf($fieldName, $index));
				if ($el) {
					$element .= '<div ng-show="activeLangId === \'' . $usersLanguage['language_id'] . '\'" ng-cloak>';
					$element .= $el;
					$element .= '</div>';
				}
			}

		} elseif (isset($fieldName)) {
			$element .= Hash::get($this->_View->viewVars['user'], $fieldName);

		} else {
			$element .= '';
		}

		return $element;
	}

/**
 * 表示可能な項目かどうかチェック
 *
 * @param array $userAttribute ユーザ属性データ
 * @return bool 表示有無
 */
	public function isDisplayable($userAttribute) {
		//表示しない条件
		// * 非表示項目の場合
		// * パスワード項目
		// * 他人の項目が読めない && 他人
		// * 本人の項目が読めない && 本人
		if (! $userAttribute['UserAttributeSetting']['display'] ||
				$userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_PASSWORD ||
				(! $userAttribute['UserAttributesRole']['other_readable'] && Current::read('User.id') !== $this->_View->viewVars['user']['User']['id']) ||
				(! $userAttribute['UserAttributesRole']['self_readable'] && Current::read('User.id') === $this->_View->viewVars['user']['User']['id'])) {

			return false;
		} else {
			return true;
		}
	}

}
