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
	public static $readableFields = null;

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

		if (! isset(self::$readableFields)) {
			$results = $model->UserAttributesRole->find('list', array(
				'recursive' => -1,
				'fields' => array('user_attribute_key', 'user_attribute_key'),
				'conditions' => array(
					'role_key' => AuthComponent::user('role_key'),
					'other_readable' => true,
				)
			));

			self::$readableFields = array('id');
			foreach ($results as $key => $field) {
				//Fieldのチェック
				if ($model->hasField($field)) {
					self::$readableFields[$key] = $model->alias . '.' . $field;
				}
				if ($model->UsersLanguage->hasField($field)) {
					self::$readableFields[$key] = $model->UsersLanguage->alias . '.' . $field;
				}
				//Field(is_xxxx_public)のチェック
				$fieldKey = sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $field);
				if ($model->hasField($fieldKey)) {
					self::$readableFields[$fieldKey] = $model->alias . '.' . $fieldKey;
				}
				//Field(xxxx_file_id)のチェック
				$fieldKey = sprintf(UserAttribute::FILE_FIELD_FORMAT, $field);
				if ($model->hasField($fieldKey)) {
					self::$readableFields[$fieldKey] = $model->alias . '.' . $fieldKey;
				}
			}
			self::$readableFields['room_id'] = $model->Room->alias . '.id';
			self::$readableFields['room_role_key'] = $model->RolesRoom->alias . '.role_key';
		}
	}

/**
 * 検索フィールドを取得する
 *
 * @param Model $model Model ビヘイビア呼び出し前のモデル
 * @param array $fields 表示するフィールドリスト
 * @return array 実際に検索できるフィールドリスト
 */
	public function getSearchFields(Model $model, $fields = array()) {
		$this->__prepare($model);
		return array_values(self::$readableFields);
	}

/**
 * 表示フィールドの取得
 *
 * @param Model $model Model ビヘイビア呼び出し前のモデル
 * @param array $fields 表示するフィールドリスト
 * @return array 実際に表示できるフィールドリスト
 */
	public function getDispayFields(Model $model, $fields = array()) {
		$this->__prepare($model);

		//if (! $fields) {
			//$fields = CakeSession::read($sessionKey);
			if (! $fields || ! is_array($fields)) {
				$fields = array(
					'handlename',
					'name',
					'role_key',
					'status',
					'modified',
					'last_login'
				);
				$fields = array_combine($fields, $fields);
			}
		//}

		$fieldKeys = array_keys($fields);
		foreach ($fieldKeys as $key) {
			if (! isset(self::$readableFields[$key])) {
				unset($fields[$key]);
			}
		}

		//CakeSession::write($sessionKey, $fields);

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
			if (isset(self::$readableFields[$key])) {
				$conditions[self::$readableFields[$key]] = $conditions[$key];
			}
			unset($conditions[$key]);
		}

		if (! isset(self::$readableFields['role_key'])) {
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

		if (Hash::check($joinModels, 'RolesRoomsUser')) {
			$conditions = Hash::get($joinModels, 'RolesRoomsUser');
		} else {
			$conditions = array();
		}
		$joins[] = array(
			'table' => $model->RolesRoomsUser->table,
			'alias' => $model->RolesRoomsUser->alias,
			'type' => 'LEFT',
			'conditions' => Hash::merge(
				array($model->RolesRoomsUser->alias . '.user_id' . ' = ' . $model->alias . '.id'),
				$conditions
			),
		);

		if (Hash::check($joinModels, 'RolesRoom')) {
			$conditions = Hash::get($joinModels, 'RolesRoom');
		} else {
			$conditions = array();
		}
		$joins[] = array(
			'table' => $model->RolesRoom->table,
			'alias' => $model->RolesRoom->alias,
			'type' => 'LEFT',
			'conditions' => Hash::merge(
				array($model->RolesRoomsUser->alias . '.roles_room_id' . ' = ' . $model->RolesRoom->alias . '.id'),
				$conditions
			),
		);
		$joins[] = array(
			'table' => $model->RoomRole->table,
			'alias' => $model->RoomRole->alias,
			'type' => 'LEFT',
			'conditions' => Hash::merge(
				array($model->RolesRoom->alias . '.role_key' . ' = ' . $model->RoomRole->alias . '.role_key'),
				$conditions
			),
		);

		if (Hash::check($joinModels, 'Room')) {
			$conditions = Hash::get($joinModels, 'Room');
		} else {
			$conditions = array();
		}
		$joins[] = array(
			'table' => $model->Room->table,
			'alias' => $model->Room->alias,
			'type' => 'LEFT',
			'conditions' => Hash::merge(
				array($model->RolesRoomsUser->alias . '.room_id' . ' = ' . $model->Room->alias . '.id'),
				$conditions
			),
		);

		return $joins;
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
		return Hash::get(self::$readableFields, $field);
	}

}
