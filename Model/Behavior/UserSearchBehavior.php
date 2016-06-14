<?php
/**
 * UserSearch Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');
App::uses('UserSearchComponent', 'Users.Controller/Component');

/**
 * UserSearch Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model\Behavior
 */
class UserSearchBehavior extends ModelBehavior {

/**
 * Readable Fields
 *
 * @var array
 */
	public $readableFields = null;

/**
 * 事前準備
 *
 * @param Model $model Model using this behavior
 * @return void
 */
	private function __prepare(Model $model) {
		$model->loadModels([
			'Group' => 'Groups.Group',
			'GroupsUser' => 'Groups.GroupsUser',
			'Role' => 'Roles.Role',
			'RolesRoom' => 'Rooms.RolesRoom',
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
			'Room' => 'Rooms.Room',
			'RoomRole' => 'Rooms.RoomRole',
			'UserAttribute' => 'UserAttributes.UserAttribute',
			'UserAttributesRole' => 'UserRoles.UserAttributesRole',
			'UploadFile' => 'Files.UploadFile',
		]);

		$model->prepare();
		if (isset($this->readableFields)) {
			return;
		}

		$results = $model->UserAttributesRole->find('list', array(
			'recursive' => -1,
			'fields' => array('user_attribute_key', 'user_attribute_key'),
			'conditions' => array(
				'role_key' => AuthComponent::user('role_key'),
				'other_readable' => true,
			)
		));

		$userAttributes = $model->UserAttribute->getUserAttributesForLayout();

		//通常フィールド
		$this->readableFields = array('id' => ['field' => 'id']);
		foreach ($results as $field) {
			$this->__setReadableField($model, $field, $userAttributes);
		}

		//参加ルーム
		$this->readableFields['room_id']['field'] = $model->Room->alias . '.id';
		$this->readableFields['room_id']['label'] = __d('user_manager', 'Rooms');
		$result = $model->Room->find('all', $model->Room->getReadableRoomsConditions(array(
			'Room.space_id !=' => Space::PRIVATE_SPACE_ID
		)));
		$this->readableFields['room_id']['options'] = Hash::combine(
			$result,
			'{n}.Room.id',
			'{n}.RoomsLanguage.{n}[language_id=' . Current::read('Language.id') . '].name'
		);

		//自分自身のグループ
		$this->readableFields['group_id']['field'] = $model->GroupsUser->alias . '.group_id';
		$this->readableFields['group_id']['label'] = __d('user_manager', 'Groups');
		$result = $model->Group->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'name'),
			'conditions' => array(
				'created_user' => Current::read('User.id'),
			),
			'order' => array('id'),
		));
		$this->readableFields['group_id']['options'] = $result;

		//ラベルなし
		$this->readableFields['space_id']['field'] = $model->Room->alias . '.space_id';
		$this->readableFields['room_role_key']['field'] = $model->RolesRoom->alias . '.role_key';
	}

/**
 * 閲覧可のフィールドセット
 *
 * @param Model $model Model using this behavior
 * @param string $attrKey 会員項目キー
 * @param array $userAttributes 会員項目データ
 * @return void
 */
	private function __setReadableField(Model $model, $attrKey, $userAttributes) {
		$userAttrSetting = Hash::extract(
			$userAttributes, '{n}.{n}.{n}.UserAttributeSetting[user_attribute_key=' . $attrKey . ']'
		);
		$dataTypeKey = Hash::get($userAttrSetting, '0.data_type_key', '');

		$userAttr = Hash::extract(
			$userAttributes, '{n}.{n}.{n}.UserAttribute[key=' . $attrKey . ']'
		);
		$label = Hash::get($userAttr, '0.name', '');

		//Fieldのチェック
		if ($attrKey === 'created_user') {
			$this->readableFields[$attrKey]['field'] = 'TrackableCreator.handlename';
			$this->readableFields[$attrKey]['label'] = $label;

		} elseif ($attrKey === 'modified_user') {
			$this->readableFields[$attrKey]['field'] = 'TrackableUpdater.handlename';
			$this->readableFields[$attrKey]['label'] = $label;

		} elseif ($dataTypeKey === DataType::DATA_TYPE_IMG) {
			$this->readableFields[$attrKey]['field'] =
					$model->UploadFile->alias . Inflector::classify($attrKey) . '.field_name';
			$this->readableFields[$attrKey]['label'] = $label;
			$this->readableFields[$attrKey]['options'] = array(
				'0' => __d('user_manager', 'No avatar.'),
				'1' => __d('user_manager', 'Has avatar.')
			);

		} elseif (in_array($attrKey, UserAttribute::$typeDatetime, true) ||
				$dataTypeKey === DataType::DATA_TYPE_DATETIME) {

			if (in_array($attrKey, ['last_login', 'previous_login'], true)) {
				//最終ログイン日時の場合、ラベル変更(○日以上ログインしていない、○日以内ログインしている)
				$moreThanDays =
					__d('user_manager', 'Not logged more than <span style="color:#ff0000;">%s</span>days ago');
				$withinDays =
					__d('user_manager', 'Have logged in within <span style="color:#ff0000;">%s</span>days');
			} else {
				//○日以上前、○日以内
				$moreThanDays =
					__d('user_manager', 'more than <span style="color:#ff0000;">%s</span>days ago');
				$withinDays =
					__d('user_manager', 'within <span style="color:#ff0000;">%s</span>days');
			}

			//日時型の場合
			$this->readableFields[$attrKey]['field'] = $model->alias . '.' . $attrKey;

			$fieldKey = $attrKey . '_' . UserSearchComponent::MORE_THAN_DAYS;
			$this->readableFields[$fieldKey]['field'] = $model->alias . '.' . $attrKey;
			$this->readableFields[$fieldKey]['label'] = $label;
			$this->readableFields[$fieldKey]['format'] = $moreThanDays;

			$fieldKey = $attrKey . '_' . UserSearchComponent::WITHIN_DAYS;
			$this->readableFields[$fieldKey]['field'] = $model->alias . '.' . $attrKey;
			$this->readableFields[$fieldKey]['label'] = $label;
			$this->readableFields[$fieldKey]['format'] = $withinDays;

		} elseif ($model->hasField($attrKey)) {
			//Userモデル
			$this->readableFields[$attrKey]['field'] = $model->alias . '.' . $attrKey;
			$this->readableFields[$attrKey]['label'] = $label;

		} elseif ($model->UsersLanguage->hasField($attrKey)) {
			//UsersLanguageモデル
			$this->readableFields[$attrKey]['field'] = $model->UsersLanguage->alias . '.' . $attrKey;
			$this->readableFields[$attrKey]['label'] = $label;
		}

		$userAttrChoices = Hash::extract(
			$userAttributes,
			'{n}.{n}.{n}.UserAttributeChoice.{n}[user_attribute_id=' . Hash::get($userAttr, '0.id', '') . ']'
		);
		if ($userAttrChoices) {
			$this->readableFields[$attrKey]['options'] = Hash::combine(
				$userAttrChoices, '{n}.key', '{n}.name'
			);
		}

		////Field(is_xxxx_public)のチェック
		//$fieldKey = sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $field);
		//if ($model->hasField($fieldKey)) {
		//	$this->readableFields[$fieldKey] = $model->alias . '.' . $fieldKey;
		//}
	}

/**
 * リクエストキーのパース処理
 *
 * @param string $requestKey リクエストキー
 * @return void
 */
	private function __parseRequestKey($requestKey) {
		if (preg_match('/' . UserSearchComponent::MORE_THAN_DAYS . '$/', $requestKey)) {
			$field = substr($requestKey, 0, (strlen(UserSearchComponent::MORE_THAN_DAYS) + 1) * -1);
			$setting = UserSearchComponent::MORE_THAN_DAYS;
		} elseif (preg_match('/' . UserSearchComponent::WITHIN_DAYS . '$/', $requestKey)) {
			$field = substr($requestKey, 0, (strlen(UserSearchComponent::WITHIN_DAYS) + 1) * -1);
			$setting = UserSearchComponent::WITHIN_DAYS;
		} else {
			$field = $requestKey;
			$setting = null;
		}

		return array($field, $setting);
	}

/**
 * 検索可能のフィールドをチェックして、検索不可なフィールドは削除する
 *
 * @param Model $model Model ビヘイビア呼び出し前のモデル
 * @param array $fields 表示するフィールドリスト
 * @return array 実際に表示できるフィールドリスト
 */
	public function cleanSearchFields(Model $model, $fields) {
		$this->__prepare($model);

		$fieldKeys = array_keys($fields);

		foreach ($fieldKeys as $key) {
			list($field, ) = $this->__parseRequestKey($key);

			if (! isset($this->readableFields[$field])) {
				unset($fields[$key]);
			}
		}
		return $fields;
	}

/**
 * 条件(Conditions)を取得
 *
 * @param Model $model Model ビヘイビア呼び出し前のモデル
 * @param array $conditions 条件(Conditions)リスト
 * @return array 実際に条件を含められるリスト
 */
	public function getSearchConditions(Model $model, $conditions = array()) {
		$this->__prepare($model);

		$fieldKeys = array_keys($conditions);
		foreach ($fieldKeys as $key) {
			list($field, $setting) = $this->__parseRequestKey($key);

			list($sign, $value) = $this->__creanSearchCondtion($model, $field, $setting, $conditions[$key]);
			unset($conditions[$key]);

			if (! isset($this->readableFields[$field])) {
				continue;
			}

			if ($setting === UserSearchComponent::MORE_THAN_DAYS) {
				$conditions[count($conditions)]['OR'] = array(
					$this->readableFields[$field]['field'] => null,
					$this->readableFields[$field]['field'] . $sign => $value
				);
			} else {
				$conditions[$this->readableFields[$field]['field'] . $sign] = $value;
			}
		}

		if (! isset($this->readableFields['role_key'])) {
			$conditions['User.status'] = '1';
		}
		$conditions['User.is_deleted'] = false;

		return $conditions;
	}

/**
 * 検索可能のフィールドをチェックして、検索不可なフィールドは削除する
 *
 * @param Model $model Model ビヘイビア呼び出し前のモデル
 * @param array $field フィールド
 * @param array $setting セッティングモード(日時型のみ使用)
 * @param array $value 値
 * @return array array(符号, SQL値)
 */
	private function __creanSearchCondtion(Model $model, $field, $setting, $value) {
		$userAttributes = $model->UserAttribute->getUserAttributesForLayout();

		$dataType = Hash::extract(
			$userAttributes, '{n}.{n}.{n}.UserAttributeSetting[user_attribute_key=' . $field . ']'
		);
		$dataTypeKey = Hash::get($dataType, '0.data_type_key', '');

		$forwardTypes = array(
			DataType::DATA_TYPE_TEXT, DataType::DATA_TYPE_TEXTAREA, DataType::DATA_TYPE_EMAIL
		);

		if ($dataTypeKey === DataType::DATA_TYPE_IMG) {
			if ($value) {
				$sign = ' NOT';
			} else {
				$sign = '';
			}
			$value = null;
		} elseif (in_array($field, UserAttribute::$typeDatetime, true) ||
								$dataTypeKey === DataType::DATA_TYPE_DATETIME) {
			//日付型の場合
			if ($setting === UserSearchComponent::MORE_THAN_DAYS) {
				//○日以上前(○日以上ログインしていない)
				$sign = ' <=';
			} else {
				//○日以内(○日以内ログインしている)
				$sign = ' >=';
			}
			$date = new DateTime(NetCommonsTime::getNowDatetime());
			$date->sub(new DateInterval(sprintf('P%dD', (int)$value)));
			$value = $date->format('Y-m-d H:i:s');

		} elseif (in_array($dataTypeKey, $forwardTypes, true) ||
					in_array($field, ['created_user', 'modified_user'], true)) {
			// テキスト型、テキストエリア型、メールアドレス型、作成者、更新者の場合
			// ->あいまい検索※今後、MatchAgainstもしくは、前方一致にする必要あり。
			$sign = ' LIKE';
			$value = '%' . $value . '%';
		} else {
			$sign = '';
		}

		return array($sign, $value);
	}

/**
 * JOINテーブルを取得
 *
 * @param Model $model Model ビヘイビア呼び出し前のモデル
 * @param array $joinModels JOINモデルリスト
 * @return array Findで使用するJOIN配列
 */
	public function getSearchJoinTables(Model $model, $joinModels = array()) {
		$joins = array(
			array(
				'table' => $model->UsersLanguage->table,
				'alias' => $model->UsersLanguage->alias,
				'type' => 'INNER',
				'conditions' => array(
					$model->UsersLanguage->alias . '.user_id' . ' = ' . $model->alias . '.id',
					$model->UsersLanguage->alias . '.language_id' => Current::read('Language.id'),
				),
			),
			Hash::merge(array(
				'table' => $model->Role->table,
				'alias' => $model->Role->alias,
				'type' => 'INNER',
				'conditions' => array(
					$model->alias . '.role_key' . ' = ' . $model->Role->alias . '.key',
					$model->Role->alias . '.language_id' => Current::read('Language.id'),
				),
			), Hash::get($joinModels, 'Role', array())),
			Hash::merge(array(
				'table' => $model->RolesRoomsUser->table,
				'alias' => $model->RolesRoomsUser->alias,
				'type' => 'LEFT',
				'conditions' => array(
					$model->RolesRoomsUser->alias . '.user_id' . ' = ' . $model->alias . '.id',
				),
			), Hash::get($joinModels, 'RolesRoomsUser', array())),
			Hash::merge(array(
				'table' => $model->RolesRoom->table,
				'alias' => $model->RolesRoom->alias,
				'type' => 'LEFT',
				'conditions' => array(
					$model->RolesRoomsUser->alias . '.roles_room_id' . ' = ' . $model->RolesRoom->alias . '.id',
				),
			), Hash::get($joinModels, 'RolesRoom', array())),
			Hash::merge(array(
				'table' => $model->RoomRole->table,
				'alias' => $model->RoomRole->alias,
				'type' => 'LEFT',
				'conditions' => array(
					$model->RolesRoom->alias . '.role_key' . ' = ' . $model->RoomRole->alias . '.role_key',
				),
			), Hash::get($joinModels, 'RolesRoom', array())),
			Hash::merge(array(
				'table' => $model->Room->table,
				'alias' => $model->Room->alias,
				'type' => 'LEFT',
				'conditions' => array(
					$model->RolesRoomsUser->alias . '.room_id' . ' = ' . $model->Room->alias . '.id',
				),
			), Hash::get($joinModels, 'Room', array()))
		);

		if (Hash::get($joinModels, 'Group')) {
			$joins[] = array(
				'table' => $model->GroupsUser->table,
				'alias' => $model->GroupsUser->alias,
				'type' => 'INNER',
				'conditions' => array(
					$model->GroupsUser->alias . '.user_id' . ' = ' . $model->alias . '.id',
					$model->GroupsUser->alias . '.created_user' => Current::read('User.id'),
				),
			);
		}

		if (Hash::get($joinModels, 'TrackableCreator')) {
			$joins[] = array(
				'table' => $model->table,
				'alias' => 'TrackableCreator',
				'type' => 'INNER',
				'conditions' => array(
					$model->alias . '.created_user' . ' = ' . 'TrackableCreator.id',
				),
			);
		}
		if (Hash::get($joinModels, 'TrackableUpdater')) {
			$joins[] = array(
				'table' => $model->table,
				'alias' => 'TrackableUpdater',
				'type' => 'INNER',
				'conditions' => array(
					$model->alias . '.modified_user' . ' = ' . 'TrackableUpdater.id',
				),
			);
		}

		$uploads = Hash::extract($joinModels, '{s}[table=' . $model->UploadFile->table . ']');
		foreach ($uploads as $upload) {
			$joins[] = $upload;
		}

		return $joins;
	}

/**
 * 検索フィールドを取得する
 *
 * @param Model $model Model ビヘイビア呼び出し前のモデル
 * @return array 実際に検索できるフィールドリスト
 */
	public function getSearchFields(Model $model) {
		$this->__prepare($model);

		return array(
			$model->alias . '.*',
			$model->UsersLanguage->alias . '.*',
			$model->RolesRoomsUser->alias . '.*',
			$model->RolesRoom->alias . '.*',
			$model->Room->alias . '.*'
		);
	}

/**
 * 検索フィールドを取得する
 *
 * @param Model $model Model ビヘイビア呼び出し前のモデル
 * @param array $field 表示するフィールドリスト
 * @return string 実際のフィールド
 */
	public function getOriginalUserField(Model $model, $field, $key = 'field') {
		$this->__prepare($model);
		return Hash::get($this->readableFields, $field . '.' . $key);
	}

/**
 * 検索フィールドの値をフォーマットに当てはめて出力する。
 *
 * @param Model $model Model ビヘイビア呼び出し前のモデル
 * @param array $field 表示するフィールドリスト
 * @param array $value 値
 * @return string 実際のフィールド
 */
	public function getSearchFieldValue(Model $model, $field, $value) {
		$this->__prepare($model);

		if (Hash::get($this->readableFields, $field . '.' . 'format')) {
			return sprintf(Hash::get($this->readableFields, $field . '.' . 'format'), h($value));
		} elseif (Hash::get($this->readableFields, $field . '.' . 'options')) {
			$options = Hash::get($this->readableFields, $field . '.' . 'options', array());
			return Hash::get($options, $value);
		} else {
			return h($value);
		}
	}

}
