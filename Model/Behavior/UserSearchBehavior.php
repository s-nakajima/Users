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
			//'Group' => 'Groups.Group',
			'GroupsUser' => 'Groups.GroupsUser',
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

		$this->readableFields = array('id' => 'id');
		foreach ($results as $field) {
			$dataType = Hash::extract(
				$userAttributes, '{n}.{n}.{n}.UserAttributeSetting[user_attribute_key=' . $field . ']'
			);
			$dataTypeKey = Hash::get($dataType, '0.data_type_key', '');

			//Fieldのチェック
			if ($dataTypeKey === DataType::DATA_TYPE_IMG) {
				$this->readableFields[$field] =
						$model->UploadFile->alias . Inflector::classify($field) . '.field_name';

			} elseif (in_array($field, UserAttribute::$typeDatetime, true) ||
					$dataTypeKey === DataType::DATA_TYPE_DATETIME) {
				//日時型の場合
				$this->readableFields[$field] = $model->alias . '.' . $field;

				$fieldKey = $field . '.' . UserSearchComponent::MORE_THAN_DAYS;
				$this->readableFields[$fieldKey] = $model->alias . '.' . $field;

				$fieldKey = $field . '.' . UserSearchComponent::WITHIN_DAYS;
				$this->readableFields[$fieldKey] = $model->alias . '.' . $field;

			} elseif ($model->hasField($field)) {
				//Userモデル
				$this->readableFields[$field] = $model->alias . '.' . $field;

			} elseif ($model->UsersLanguage->hasField($field)) {
				//UsersLanguageモデル
				$this->readableFields[$field] = $model->UsersLanguage->alias . '.' . $field;
			}
			////Field(is_xxxx_public)のチェック
			//$fieldKey = sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $field);
			//if ($model->hasField($fieldKey)) {
			//	$this->readableFields[$fieldKey] = $model->alias . '.' . $fieldKey;
			//}
		}
		$this->readableFields['room_id'] = $model->Room->alias . '.id';
		$this->readableFields['space_id'] = $model->Room->alias . '.space_id';
		$this->readableFields['room_role_key'] = $model->RolesRoom->alias . '.role_key';
		$this->readableFields['group_id'] = $model->GroupsUser->alias . '.group_id';

		$this->readableFields['created_user'] = 'TrackableCreator.handlename';
		$this->readableFields['modified_user'] = 'TrackableUpdater.handlename';
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
			if (! isset($this->readableFields[$key])) {
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
			$explode = explode('.', $key);
			$field = Hash::get($explode, '0', null);
			$setting = Hash::get($explode, '1', null);

			list($sign, $value) = $this->__creanSearchCondtion($model, $field, $setting, $conditions[$key]);
			unset($conditions[$key]);

			if (! isset($this->readableFields[$key])) {
				continue;
			}

			if ($setting === UserSearchComponent::MORE_THAN_DAYS) {
				$conditions[count($conditions)]['OR'] = array(
					$this->readableFields[$key] => null,
					$this->readableFields[$key] . $sign => $value
				);
			} else {
				$conditions[$this->readableFields[$key] . $sign] = $value;
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
	public function getOriginalUserField(Model $model, $field) {
		$this->__prepare($model);
		return Hash::get($this->readableFields, $field);
	}

}
