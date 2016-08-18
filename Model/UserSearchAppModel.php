<?php
/**
 * User Model
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('UsersAppModel', 'Users.Model');
App::uses('NetCommonsTime', 'NetCommons.Utility');

/**
 * UserSearchAppModel
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model
 */
class UserSearchAppModel extends UsersAppModel {

/**
 * more_than_days定数
 * ○日以上前(○日以上ログインしていない)
 *
 * @var const
 */
	const MORE_THAN_DAYS = 'more_than_days';

/**
 * within_days定数
 * ○日以内(○日以内ログインしている)
 *
 * @var const
 */
	const WITHIN_DAYS = 'within_days';

/**
 * 閲覧可のフィールドセット
 * self::__prepare()から実行される
 *
 * @param string $attrKey 会員項目キー
 * @param array $userAttributes 会員項目データ
 * @return void
 */
	protected function _setReadableField($attrKey, $userAttributes) {
		$userAttrSetting = Hash::extract(
			$userAttributes, '{n}.{n}.{n}.UserAttributeSetting[user_attribute_key=' . $attrKey . ']'
		);
		$dataTypeKey = Hash::get($userAttrSetting, '0.data_type_key', '');

		$userAttr = Hash::extract(
			$userAttributes, '{n}.{n}.{n}.UserAttribute[key=' . $attrKey . ']'
		);
		$label = Hash::get($userAttr, '0.name', '');

		//Fieldのチェック
		if ($dataTypeKey === DataType::DATA_TYPE_IMG) {
			$this->readableFields[$attrKey]['field'] =
					$this->UploadFile->alias . Inflector::classify($attrKey) . '.field_name';
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
			$this->readableFields[$attrKey]['field'] = $this->alias . '.' . $attrKey;

			$fieldKey = $attrKey . '_' . self::MORE_THAN_DAYS;
			$this->readableFields[$fieldKey]['field'] = $this->alias . '.' . $attrKey;
			$this->readableFields[$fieldKey]['label'] = $label;
			$this->readableFields[$fieldKey]['format'] = $moreThanDays;

			$fieldKey = $attrKey . '_' . self::WITHIN_DAYS;
			$this->readableFields[$fieldKey]['field'] = $this->alias . '.' . $attrKey;
			$this->readableFields[$fieldKey]['label'] = $label;
			$this->readableFields[$fieldKey]['format'] = $withinDays;

		} elseif ($this->hasField($attrKey)) {
			//Userモデル
			$this->readableFields[$attrKey]['field'] = $this->alias . '.' . $attrKey;
			$this->readableFields[$attrKey]['label'] = $label;

		} elseif ($this->UsersLanguage->hasField($attrKey)) {
			//UsersLanguageモデル
			$this->readableFields[$attrKey]['field'] = $this->UsersLanguage->alias . '.' . $attrKey;
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
		//if ($this->hasField($fieldKey)) {
		//	$this->readableFields[$fieldKey] = $this->alias . '.' . $fieldKey;
		//}
	}

/**
 * リクエストキーのパース処理
 *
 * @param string $requestKey リクエストキー
 * @return void
 */
	protected function _parseRequestKey($requestKey) {
		if (preg_match('/' . self::MORE_THAN_DAYS . '$/', $requestKey)) {
			$field = substr($requestKey, 0, (strlen(self::MORE_THAN_DAYS) + 1) * -1);
			$setting = self::MORE_THAN_DAYS;
		} elseif (preg_match('/' . self::WITHIN_DAYS . '$/', $requestKey)) {
			$field = substr($requestKey, 0, (strlen(self::WITHIN_DAYS) + 1) * -1);
			$setting = self::WITHIN_DAYS;
		} else {
			$field = $requestKey;
			$setting = null;
		}

		return array($field, $setting);
	}

/**
 * JOINテーブルを取得
 *
 * @param array $conditions 条件(Conditions)リスト
 * @return array Findで使用するJOIN配列
 */
	protected function _getSearchJoinTablesByConditions($conditions) {
		$joinModels = array();
		$fieldKeys = array_keys($conditions);
		if (in_array('group_id', $fieldKeys, true)) {
			$joinModels = Hash::merge(array('Group' => true), $joinModels);
		}
		if (in_array('created_user', $fieldKeys, true)) {
			$joinModels = Hash::merge(array('TrackableCreator' => true), $joinModels);
		}
		if (in_array('modified_user', $fieldKeys, true)) {
			$joinModels = Hash::merge(array('TrackableUpdater' => true), $joinModels);
		}
		foreach ($fieldKeys as $field) {
			$modelName = $this->UploadFile->alias . Inflector::classify($field);
			if ($this->getOriginalField($field) === $modelName . '.field_name') {
				$joinModels = Hash::merge(array($modelName => array(
					'table' => $this->UploadFile->table,
					'alias' => $modelName,
					'type' => 'LEFT',
					'conditions' => array(
						$modelName . '.content_key' . ' = ' . $this->alias . '.id',
						$modelName . '.plugin_key' => 'users',
						$modelName . '.field_name' => $field,
					),
				)), $joinModels);
			}
		}

		return $joinModels;
	}

/**
 * 検索可能のフィールドをチェックして、検索不可なフィールドは削除する
 *
 * @param array $fields 表示するフィールドリスト
 * @return array 実際に表示できるフィールドリスト
 */
	public function cleanSearchFields($fields) {
		$fieldKeys = array_keys($fields);

		foreach ($fieldKeys as $key) {
			list($field, ) = $this->_parseRequestKey($key);

			if (! isset($this->readableFields[$field])) {
				unset($fields[$key]);
			}
		}

		if (! $fields) {
			$fields = array();
		}
		return $fields;
	}

/**
 * 検索フィールドから実際のテーブルフィールドを取得する
 *
 * @param string $field 表示するフィールドリスト
 * @return string 実際のフィールド
 */
	public function getOriginalField($field) {
		return Hash::get($this->readableFields, $field . '.' . 'field');
	}

/**
 * 検索フィールド名(ラベル)を取得する
 *
 * @param string $field 表示するフィールド
 * @return string フィールド名(ラベル)
 */
	public function getReadableFieldName($field) {
		return Hash::get($this->readableFields, $field . '.' . 'label');
	}

/**
 * 検索フィールドのオプションを取得する
 *
 * @param string $field 表示するフィールド
 * @return string オプション
 */
	public function getReadableFieldOptions($field) {
		return Hash::get($this->readableFields, $field . '.' . 'options');
	}

/**
 * 検索フィールドのソートキーを取得する
 *
 * @param string $field 表示するフィールド
 * @return string ソートキー
 */
	public function getReadableFieldOrderKey($field) {
		$key = 'order';
		if (! Hash::get($this->readableFields, $field . '.' . $key)) {
			$key = 'field';
		}

		return Hash::get($this->readableFields, $field . '.' . $key);
	}

/**
 * 検索フィールドの値をフォーマットに当てはめて出力する。
 *
 * @param string $field 表示するフィールドリスト
 * @param string $value 値
 * @return string 値
 */
	public function getSearchFieldValue($field, $value) {
		if (Hash::get($this->readableFields, $field . '.' . 'format')) {
			return sprintf(Hash::get($this->readableFields, $field . '.' . 'format'), h($value));
		} elseif (Hash::get($this->readableFields, $field . '.' . 'options')) {
			$options = Hash::get($this->readableFields, $field . '.' . 'options', array());
			return Hash::get($options, $value);
		} else {
			return h($value);
		}
	}

/**
 * 検索可能のフィールドをチェックして、検索不可なフィールドは削除する
 *
 * @param array $field フィールド
 * @param array $setting セッティングモード(日時型のみ使用)
 * @param array $value 値
 * @return array array(符号, SQL値)
 */
	protected function _creanSearchCondtion($field, $setting, $value) {
		$userAttributes = $this->UserAttribute->getUserAttributesForLayout();

		$dataType = Hash::extract(
			$userAttributes, '{n}.{n}.{n}.UserAttributeSetting[user_attribute_key=' . $field . ']'
		);
		$dataTypeKey = Hash::get($dataType, '0.data_type_key', '');

		$forwardTypes = array(
			DataType::DATA_TYPE_TEXT, DataType::DATA_TYPE_TEXTAREA, DataType::DATA_TYPE_EMAIL
		);

		$optionTypes = array(
			DataType::DATA_TYPE_RADIO, DataType::DATA_TYPE_SELECT, DataType::DATA_TYPE_CHECKBOX,
			DataType::DATA_TYPE_PREFECTURE, DataType::DATA_TYPE_TIMEZONE, DataType::DATA_TYPE_MULTIPLE_SELECT
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
			if ($setting === self::MORE_THAN_DAYS) {
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

		} elseif (in_array($dataTypeKey, $optionTypes, true)) {
			$sign = '';
			$userAttribute = Hash::extract(
				$userAttributes, '{n}.{n}.{n}.UserAttribute[key=' . $field . ']'
			);
			$userAttrId = Hash::get($userAttribute, '0.id');
			$options = Hash::extract(
				$userAttributes,
				'{n}.{n}.{n}.UserAttributeChoice.{n}[user_attribute_id=' . $userAttrId . ']'
			);
			$value = Hash::get(Hash::extract($options, '{n}[key=' . $value . ']', array()), '0.code');

		} else {
			$sign = '';
		}

		return array($sign, $value);
	}

/**
 * 検索フィールドを取得する
 *
 * @param array $fields フィールド配列
 * @return array 実際に検索できるフィールドリスト
 */
	protected function _getSearchFields($fields) {
		$originalFields = array(
			'User.id'
		);

		foreach ($fields as $field) {
			$originalFields[] = $this->getOriginalField($field);
		}

		if (in_array('room_role_key', $fields, true)) {
			$originalFields = array_merge(
				$originalFields,
				array(
					$this->RolesRoomsUser->alias . '.id',
					$this->RolesRoomsUser->alias . '.roles_room_id',
					$this->RolesRoomsUser->alias . '.user_id',
					$this->RolesRoomsUser->alias . '.room_id',
				),
				array(
					$this->RolesRoom->alias . '.id',
					$this->RolesRoom->alias . '.room_id',
					$this->RolesRoom->alias . '.role_key',
				)
			);
		}

		$originalFields = array_unique($originalFields);
		return $originalFields;
	}

/**
 * 検索フィールドを取得する
 *
 * @param array $fields フィールド配列
 * @return array 実際に検索できるフィールドリスト
 */
	protected function _getSearchFieldsByRoomRoleKey($fields) {
		$fields = Hash::merge(array(
			'user_id',
			'role_id',
			'roles_room_id',
			'roles_room_room_id',
			'roles_room_role_key',
			'roles_rooms_user_id',
			'roles_rooms_user_roles_room_id',
			'roles_rooms_user_user_id',
			'roles_rooms_user_room_id'
		), $fields);

		$originalFields = array();
		foreach ($fields as $field) {
			$originalFields[$field] = $this->getOriginalField($field) . ' AS ' . $field;
		}

		$originalFields = array_unique($originalFields);
		return $originalFields;
	}

}