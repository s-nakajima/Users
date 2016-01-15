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
 * 使用するヘルパー
 * ただし、Roomヘルパーを使用する場合は、RoomComponentを呼び出している必要がある。
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsHtml',
		'NetCommons.Date',
		'Rooms.Rooms',
		'Users.DisplayUser'
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
				$output .= $this->_View->Paginator->sort('RoomRole.level', __d('rooms', 'Room role'));
			} else {
				$userAttribute = Hash::extract($this->userAttributes, '{s}.UserAttribute[key=' . $fieldName . ']');
				$output .= $this->_View->Paginator->sort($this->User->getOriginalUserField($fieldName), $userAttribute[0]['name']);
			}
			$output .= '</th>';
		}

		return $output;
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
			$modelName = '';
			if ($this->User->hasField($fieldName)) {
				$modelName = $this->User->alias;
			} elseif ($this->UsersLanguage->hasField($fieldName)) {
				$modelName = $this->UsersLanguage->alias;
			} elseif ($fieldName === 'room_role_key') {
				$modelName = 'RolesRoom';
			}

			if ($modelName) {
				$output .= $this->tableCell($user, $modelName, $fieldName, $isEdit, true);
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
 * @param bool $tdElement tdタグの出力
 * @return string セルのHTMLタグ
 */
	public function tableCell($user, $modelName, $fieldName, $isEdit, $tdElement) {
		$userAttribute = Hash::get($this->userAttributes, $fieldName);

		$value = '';
		if ($fieldName === 'handlename') {
			//ハンドル
			$value = $this->linkHandlename($user, $isEdit);
		} elseif ($fieldName === 'room_role_key') {
			//ルーム権限
			$value = $this->Rooms->roomRoleName($user[$modelName]['role_key']);
		} elseif (isset($userAttribute['UserAttributeChoice']) && $user[$modelName][$fieldName]) {
			//選択肢
			if ($fieldName === 'role_key') {
				$values = Hash::extract($userAttribute['UserAttributeChoice'], '{n}[key=' . $user[$modelName][$fieldName] . ']');
			} else {
				$values = Hash::extract($userAttribute['UserAttributeChoice'], '{n}[code=' . $user[$modelName][$fieldName] . ']');
			}
			$value = h(Hash::get($values, '0.name'));
		} elseif ($userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_DATETIME ||
				in_array($userAttribute['UserAttribute']['key'], UserAttribute::$typeDatetime, true)) {
			//日付型
			$value = h($this->Date->dateFormat($user[$modelName][$fieldName]));
		} else {
			//その他
			$value = h($user[$modelName][$fieldName]);
		}

		if ($tdElement) {
			return '<td>' . $value . '</td>';
		} else {
			return $value;
		}
	}

/**
 * ハンドルの出力
 *
 * @param array $user ユーザデータ
 * @param bool $isEdit 編集の有無
 * @return string ハンドルのHTMLタグ
 */
	public function linkHandlename($user, $isEdit) {
		if (! $isEdit) {
			return $this->DisplayUser->handleLink($user, array('avatar' => true), array(), 'User');
		} elseif (Current::read('User.role_key') === UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR ||
				$user[$this->User->alias]['role_key'] !== UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR) {

			return $this->NetCommonsHtml->link($this->DisplayUser->handle($user, array('avatar' => true), 'User'),
				array('plugin' => 'user_manager', 'controller' => 'user_manager', 'action' => 'edit', $user['User']['id']),
				array('escape' => false)
			);
		} else {
			return $this->DisplayUser->handle($user, array('avatar' => true), 'User');
		}
	}

/**
 * ユーザ選択画面でJSONでユーザを表示する
 *
 * @param array $user ユーザデータ
 * @param array $model モデル名(TrackableCreatorやTrackableUpdaterなど)
 * @return string JSON形式
 */
	public function convertUserArrayByUserSelection($user, $model = 'TrackableCreator') {
		$result = array(
			'id' => Hash::get($user, $model . '.id'),
			'handlename' => Hash::get($user, $model . '.handlename'),
			'avatar' => $this->DisplayUser->avatar($user, array(), $model . '.id', false),
			'link' => NetCommonsUrl::userActionUrl(array('key' => Hash::get($user, $model . '.id'))),
		);

		return $result;
	}

}
