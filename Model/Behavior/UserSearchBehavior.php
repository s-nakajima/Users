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
			'UserAttribute' => 'UserAttributes.UserAttribute',
			'UserAttributesRole' => 'UserRoles.UserAttributesRole',
			'RolesRoom' => 'Rooms.RolesRoom',
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
			'Room' => 'Rooms.Room',
			'RoomRole' => 'Rooms.RoomRole',
		]);

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

		$this->readableFields = array('id');
		foreach ($results as $key => $field) {
			//Fieldのチェック
			if ($model->hasField($field)) {
				$this->readableFields[$key] = $model->alias . '.' . $field;
			}
			if ($model->UsersLanguage->hasField($field)) {
				$this->readableFields[$key] = $model->UsersLanguage->alias . '.' . $field;
			}
			//Field(is_xxxx_public)のチェック
			$fieldKey = sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $field);
			if ($model->hasField($fieldKey)) {
				$this->readableFields[$fieldKey] = $model->alias . '.' . $fieldKey;
			}
		}
		$this->readableFields['room_id'] = $model->Room->alias . '.id';
		$this->readableFields['space_id'] = $model->Room->alias . '.space_id';
		$this->readableFields['room_role_key'] = $model->RolesRoom->alias . '.role_key';
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

		$userAttributes = $model->UserAttribute->getUserAttributesForLayout();

		$fieldKeys = array_keys($conditions);
		foreach ($fieldKeys as $key) {
			$dataType = Hash::extract($userAttributes, '{n}.{n}.{n}.UserAttributeSetting[user_attribute_key=' . $key . ']');
			$dataType = Hash::get($dataType, '0.data_type_key', '');
			if (in_array($dataType, [DataType::DATA_TYPE_TEXT, DataType::DATA_TYPE_TEXTAREA, DataType::DATA_TYPE_EMAIL], true)) {
				$sign = ' LIKE';
				$value = '%' . $conditions[$key] . '%';
			} else {
				$sign = '';
				$value = $conditions[$key];
			}

			if (isset($this->readableFields[$key])) {
				$conditions[$this->readableFields[$key] . $sign] = $value;
				unset($conditions[$key]);
			} else {
				$conditions[$key . $sign] = $value;
			}
		}

		if (! isset($this->readableFields['role_key'])) {
			$conditions['User.status'] = '1';
		}
		$conditions['User.is_deleted'] = false;

		return $conditions;
	}

/**
 * JOINテーブルを取得
 *
 * @param Model $model Model ビヘイビア呼び出し前のモデル
 * @param array $joinModels JOINモデルリスト
 * @return array Findで使用するJOIN配列
 */
	public function getSearchJoinTables(Model $model, $joinModels = array()) {
		$joins[] = array(
			'table' => $model->UsersLanguage->table,
			'alias' => $model->UsersLanguage->alias,
			'type' => 'INNER',
			'conditions' => array(
				$model->UsersLanguage->alias . '.user_id' . ' = ' . $model->alias . '.id',
				$model->UsersLanguage->alias . '.language_id' => Current::read('Language.id'),
			),
		);

		$joins[] = Hash::merge(array(
			'table' => $model->RolesRoomsUser->table,
			'alias' => $model->RolesRoomsUser->alias,
			'type' => 'LEFT',
			'conditions' => array(
				$model->RolesRoomsUser->alias . '.user_id' . ' = ' . $model->alias . '.id',
			),
		), Hash::get($joinModels, 'RolesRoomsUser', array()));

		$joins[] = Hash::merge(array(
			'table' => $model->RolesRoom->table,
			'alias' => $model->RolesRoom->alias,
			'type' => 'LEFT',
			'conditions' => array(
				$model->RolesRoomsUser->alias . '.roles_room_id' . ' = ' . $model->RolesRoom->alias . '.id',
			),
		), Hash::get($joinModels, 'RolesRoom', array()));

		$joins[] = Hash::merge(array(
			'table' => $model->RoomRole->table,
			'alias' => $model->RoomRole->alias,
			'type' => 'LEFT',
			'conditions' => array(
				$model->RolesRoom->alias . '.role_key' . ' = ' . $model->RoomRole->alias . '.role_key',
			),
		), Hash::get($joinModels, 'RolesRoom', array()));

		$joins[] = Hash::merge(array(
			'table' => $model->Room->table,
			'alias' => $model->Room->alias,
			'type' => 'LEFT',
			'conditions' => array(
				$model->RolesRoomsUser->alias . '.room_id' . ' = ' . $model->Room->alias . '.id',
			),
		), Hash::get($joinModels, 'Room', array()));

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
