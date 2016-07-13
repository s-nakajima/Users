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
		'NetCommons.LinkButton',
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
		$this->UserSearch = ClassRegistry::init('Users.UserSearch');
		$this->UsersLanguage = ClassRegistry::init('Users.UsersLanguage');

		$this->userAttributes = Hash::combine(
			Hash::get($this->_View->viewVars, 'userAttributes', array()),
			'{n}.{n}.{n}.UserAttribute.key', '{n}.{n}.{n}'
		);
	}

/**
 * テーブルヘッダーの出力
 *
 * @param bool $isEdit 編集の有無
 * @param bool $isSort ソートの有無
 * @return string User value
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function tableHeaders($isEdit = false, $isSort = true) {
		$output = '';

		foreach ($this->_View->viewVars['displayFields'] as $fieldName) {
			$userAttribute = Hash::get($this->userAttributes, $fieldName);
			$dataTypeKey = $userAttribute['UserAttributeSetting']['data_type_key'];

			if ($dataTypeKey === DataType::DATA_TYPE_DATETIME ||
					in_array($userAttribute['UserAttribute']['key'], UserAttribute::$typeDatetime, true)) {
				//日付型
				$class = ' class="row-datetime"';
			} else {
				$class = '';
			}

			$output .= '<th' . $class . '>';
			if ($fieldName === 'room_role_key') {
				$label = __d('rooms', 'Room role');
				$key = 'RoomRole.level';
			} else {
				$userAttribute = Hash::extract(
					$this->userAttributes, '{s}.UserAttribute[key=' . $fieldName . ']'
				);
				$label = Hash::get($userAttribute, '0.name');
				$key = $this->UserSearch->getReadableFieldOrderKey($fieldName);
			}

			if ($isSort) {
				$output .= $this->_View->Paginator->sort($key, $label);
			} else {
				$output .= h($label);
			}

			$output .= '</th>';

			if ($isEdit) {
				if ($fieldName === 'handlename') {
					$output .= '<th></th>';
				}
				if ($fieldName === 'status') {
					$output .= '<th></th>';
				}
			}
		}

		return $output;
	}

/**
 * テーブル行の出力
 *
 * @param array $user ユーザデータ
 * @param bool $isEdit 編集の有無
 * @param array $editUrl 編集リンクURL
 * @param array $tdOptions tdのオプション
 * @return string 行のHTMLタグ
 */
	public function tableRow($user, $isEdit, $editUrl = array(), $tdOptions = array()) {
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
				$output .= $this->tableCell($user, $modelName, $fieldName, $isEdit, true, $tdOptions);
			} else {
				$output .= '<td></td>';
			}

			if ($isEdit) {
				if ($fieldName === 'handlename') {
					$output .= '<td>' . $this->__userEdit($user, $editUrl) . '</td>';
				}
				if ($fieldName === 'status') {
					$output .= '<td>' . $this->__editUserStatus($user, $modelName, $fieldName) . '</td>';
				}
			}
		}

		return $output;
	}

/**
 * 状態によって、行色を変更する
 *
 * @param array $user ユーザデータ
 * @return string 行のHTMLタグ
 */
	public function userActiveClass($user) {
		$output = '';

		if (Hash::get($user, 'User.status') === UserAttributeChoice::STATUS_CODE_NONACTIVE) {
			$output .= ' class="danger text-danger"';
		} elseif (Hash::get($user, 'User.status') === UserAttributeChoice::STATUS_CODE_WAITING ||
				Hash::get($user, 'User.status') === UserAttributeChoice::STATUS_CODE_APPROVED) {
			$output .= ' class="warning text-danger"';
		}

		return $output;
	}

/**
 * テーブル行の出力
 *
 * @param array $user ユーザデータ
 * @param array $url 編集リンクURL
 * @return string 編集のHTMLタグ
 */
	private function __userEdit($user, $url) {
		$output = '';

		if (Current::read('User.role_key') !== UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR &&
				($user['User']['role_key'] === UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR)) {
			return $output;
		}

		$url['key'] = $user['User']['id'];
		$output .= $this->LinkButton->edit('', $url, ['iconSize' => 'btn-xs']);

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
 * @param array $tdOptions tdのオプション
 * @return string セルのHTMLタグ
 */
	public function tableCell($user, $modelName, $fieldName, $isEdit, $tdElement, $tdOptions) {
		$userAttribute = Hash::get($this->userAttributes, $fieldName);
		$dataTypeKey = $userAttribute['UserAttributeSetting']['data_type_key'];
		$value = '';

		$tdOptions['escape'] = false;
		if ($fieldName === 'handlename') {
			//ハンドル
			$value = $this->linkHandlename($user, $isEdit);
		} elseif ($fieldName === 'room_role_key') {
			//ルーム権限
			$value = $this->Rooms->roomRoleName($user[$modelName]['role_key']);
		} elseif (isset($userAttribute['UserAttributeChoice']) && $user[$modelName][$fieldName]) {
			//選択肢
			if ($fieldName === 'role_key') {
				$values = Hash::extract(
					$userAttribute['UserAttributeChoice'], '{n}[key=' . $user[$modelName][$fieldName] . ']'
				);
			} else {
				$values = Hash::extract(
					$userAttribute['UserAttributeChoice'], '{n}[code=' . $user[$modelName][$fieldName] . ']'
				);
			}
			$value = h(Hash::get($values, '0.name'));
		} elseif ($dataTypeKey === DataType::DATA_TYPE_DATETIME ||
				in_array($userAttribute['UserAttribute']['key'], UserAttribute::$typeDatetime, true)) {
			//日付型
			$value = h($this->Date->dateFormat($user[$modelName][$fieldName]));
			$tdOptions['class'] = 'row-datetime';
		} else {
			//その他
			$value = h($user[$modelName][$fieldName]);
		}

		if ($tdElement) {
			return $this->NetCommonsHtml->tag('td', $value, $tdOptions);
		} else {
			return $value;
		}
	}

/**
 * 状態の値取得。編集可で承認待ち⇒承認、承認済みの場合、再送
 *
 * @param array $user ユーザデータ
 * @param string $modelName モデル名
 * @param string $fieldName 表示フィールド
 * @return string 編集のHTMLタグ
 */
	private function __editUserStatus($user, $modelName, $fieldName) {
		$output = '';

		if ($user[$modelName][$fieldName] === UserAttributeChoice::STATUS_CODE_WAITING) {
			$confirm = __d('user_manager', 'Do you approve?');
			$output .= $this->_View->element('Users.Users/edit_status_form', array(
				'user' => $user,
				'label' => __d('user_manager', 'Approval'),
				'options' => array(
					'icon' => 'glyphicon-ok',
					'class' => 'btn btn-xs btn-warning',
					'onclick' => 'return confirm(\'' . $confirm . '\')',
				),
			));
		} elseif ($user[$modelName][$fieldName] === UserAttributeChoice::STATUS_CODE_APPROVED) {
			$output .= $this->_View->element('Users.Users/edit_status_form', array(
				'user' => $user,
				'label' => __d('user_manager', 'Resend'),
				'options' => array(
					'icon' => 'glyphicon-envelope',
					'class' => 'btn btn-xs btn-info',
				),
			));
		}

		return $output;
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
		} else {

			return $this->NetCommonsHtml->link(
				$this->DisplayUser->handle($user, array('avatar' => true), 'User'), '#',
				array(
					'escape' => false,
					'ng-controller' => 'UserManagerController',
					'ng-click' => 'showUser(' . $user['User']['id'] . ')'
				),
				array(
					'escape' => false
				)
			);
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
