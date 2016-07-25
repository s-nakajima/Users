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
 * UserSearch Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model
 */
class UserSearch extends UserSearchAppModel {

/**
 * Alias name for model.
 *
 * @var string
 */
	public $alias = 'User';

/**
 * Table name for this Model.
 *
 * @var string
 */
	public $table = 'users';

/**
 * Custom database table name, or null/false if no table association is desired.
 *
 * @var string
 * @link http://book.cakephp.org/2.0/ja/models/model-attributes.html#usetable
 */
	public $useTable = 'users';

/**
 * 閲覧できるフィールドリスト
 *
 * @var array
 */
	public $readableFields = null;

/**
 * Constructor. Binds the model's database table to the object.
 *
 * @param bool|int|string|array $id Set this ID for this model on startup,
 * can also be an array of options, see above.
 * @param string $table Name of database table to use.
 * @param string $ds DataSource connection name.
 * @see Model::__construct()
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->loadModels([
			'Group' => 'Groups.Group',
			'GroupsUser' => 'Groups.GroupsUser',
			'Role' => 'Roles.Role',
			'RolesRoom' => 'Rooms.RolesRoom',
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
			'Room' => 'Rooms.Room',
			'RoomRole' => 'Rooms.RoomRole',
			'UsersLanguage' => 'Users.UsersLanguage',
			'UserAttribute' => 'UserAttributes.UserAttribute',
			'UserAttributesRole' => 'UserRoles.UserAttributesRole',
			'UploadFile' => 'Files.UploadFile',
			'UserAttribute' => 'UserAttributes.UserAttribute',
			'UserAttributeSetting' => 'UserAttributes.UserAttributeSetting',
			'DataType' => 'DataTypes.DataType',
		]);

		$this->__prepare();
	}

/**
 * 事前準備
 *
 * @return void
 */
	private function __prepare() {
		if (isset($this->readableFields)) {
			return;
		}

		$results = $this->UserAttributesRole->find('list', array(
			'recursive' => -1,
			'fields' => array('user_attribute_key', 'user_attribute_key'),
			'conditions' => array(
				'role_key' => AuthComponent::user('role_key'),
				'other_readable' => true,
			)
		));

		$userAttributes = $this->UserAttribute->getUserAttributesForLayout();

		//通常フィールド
		$this->readableFields = array('id' => ['field' => 'id']);
		$this->readableFields = array('user_id' => ['field' => 'User.id']);
		foreach ($results as $field) {
			$this->_setReadableField($field, $userAttributes);
		}
		$this->readableFields['created_user']['field'] = 'TrackableCreator.handlename';
		$this->readableFields['modified_user']['field'] = 'TrackableUpdater.handlename';
		$this->readableFields['role_key']['order'] = 'Role.id';

		//参加ルーム
		$this->readableFields['room_id']['field'] = $this->Room->alias . '.id';
		$this->readableFields['room_id']['label'] = __d('user_manager', 'Rooms');
		$result = $this->Room->find('all', $this->Room->getReadableRoomsConditions(array(
			'Room.space_id !=' => Space::PRIVATE_SPACE_ID
		)));
		$this->readableFields['room_id']['options'] = Hash::combine(
			$result,
			'{n}.Room.id',
			'{n}.RoomsLanguage.{n}[language_id=' . Current::read('Language.id') . '].name'
		);

		//自分自身のグループ
		$this->readableFields['group_id']['field'] = $this->GroupsUser->alias . '.group_id';
		$this->readableFields['group_id']['label'] = __d('user_manager', 'Groups');
		$result = $this->Group->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'name'),
			'conditions' => array(
				'created_user' => Current::read('User.id'),
			),
			'order' => array('id'),
		));
		$this->readableFields['group_id']['options'] = $result;

		//ラベルなし
		$this->readableFields['space_id']['field'] = $this->Room->alias . '.space_id';
		$this->readableFields['room_role_key']['field'] = $this->RolesRoom->alias . '.role_key';
		$this->readableFields['room_role_level']['field'] = $this->RoomRole->alias . '.level';

		$this->readableFields['roles_room_id']['field'] = $this->RolesRoom->alias . '.id';
		$this->readableFields['roles_room_room_id']['field'] = $this->RolesRoom->alias . '.room_id';
		$this->readableFields['roles_room_role_key']['field'] = $this->RolesRoom->alias . '.role_key';

		$this->readableFields['roles_rooms_user_id']['field'] = $this->RolesRoomsUser->alias . '.id';
		$this->readableFields['roles_rooms_user_roles_room_id']['field'] =
				$this->RolesRoomsUser->alias . '.roles_room_id';
		$this->readableFields['roles_rooms_user_user_id']['field'] =
				$this->RolesRoomsUser->alias . '.user_id';
		$this->readableFields['roles_rooms_user_room_id']['field'] =
				$this->RolesRoomsUser->alias . '.room_id';
	}

/**
 * JOINテーブルを取得
 *
 * @param array $joinModels JOINモデルリスト
 * @param array $conditions 条件(Conditions)リスト
 * @return array Findで使用するJOIN配列
 */
	public function getSearchJoinTables($joinModels, $conditions = array()) {
		$joinModels = Hash::merge(
			$joinModels,
			$this->_getSearchJoinTablesByConditions($conditions)
		);

		$joins = array(
			array(
				'table' => $this->UsersLanguage->table,
				'alias' => $this->UsersLanguage->alias,
				'type' => 'INNER',
				'conditions' => array(
					$this->UsersLanguage->alias . '.user_id' . ' = ' . $this->alias . '.id',
					$this->UsersLanguage->alias . '.language_id' => Current::read('Language.id'),
				),
			),
			Hash::merge(array(
				'table' => $this->Role->table,
				'alias' => $this->Role->alias,
				'type' => 'INNER',
				'conditions' => array(
					$this->alias . '.role_key' . ' = ' . $this->Role->alias . '.key',
					$this->Role->alias . '.language_id' => Current::read('Language.id'),
				),
			), Hash::get($joinModels, 'Role', array())),
			Hash::merge(array(
				'table' => $this->RolesRoomsUser->table,
				'alias' => $this->RolesRoomsUser->alias,
				'type' => 'LEFT',
				'conditions' => array(
					$this->RolesRoomsUser->alias . '.user_id' . ' = ' . $this->alias . '.id',
				),
			), Hash::get($joinModels, 'RolesRoomsUser', array())),
			Hash::merge(array(
				'table' => $this->RolesRoom->table,
				'alias' => $this->RolesRoom->alias,
				'type' => 'LEFT',
				'conditions' => array(
					$this->RolesRoomsUser->alias . '.roles_room_id' . ' = ' . $this->RolesRoom->alias . '.id',
				),
			), Hash::get($joinModels, 'RolesRoom', array())),
			Hash::merge(array(
				'table' => $this->RoomRole->table,
				'alias' => $this->RoomRole->alias,
				'type' => 'LEFT',
				'conditions' => array(
					$this->RolesRoom->alias . '.role_key' . ' = ' . $this->RoomRole->alias . '.role_key',
				),
			), Hash::get($joinModels, 'RolesRoom', array())),
			Hash::merge(array(
				'table' => $this->Room->table,
				'alias' => $this->Room->alias,
				'type' => 'LEFT',
				'conditions' => array(
					$this->RolesRoomsUser->alias . '.room_id' . ' = ' . $this->Room->alias . '.id',
				),
			), Hash::get($joinModels, 'Room', array()))
		);

		if (Hash::get($joinModels, 'Group')) {
			$joins[] = array(
				'table' => $this->GroupsUser->table,
				'alias' => $this->GroupsUser->alias,
				'type' => 'INNER',
				'conditions' => array(
					$this->GroupsUser->alias . '.user_id' . ' = ' . $this->alias . '.id',
					$this->GroupsUser->alias . '.created_user' => Current::read('User.id'),
				),
			);
		}

		if (Hash::get($joinModels, 'TrackableCreator')) {
			$joins[] = array(
				'table' => $this->table,
				'alias' => 'TrackableCreator',
				'type' => 'INNER',
				'conditions' => array(
					$this->alias . '.created_user' . ' = ' . 'TrackableCreator.id',
				),
			);
		}
		if (Hash::get($joinModels, 'TrackableUpdater')) {
			$joins[] = array(
				'table' => $this->table,
				'alias' => 'TrackableUpdater',
				'type' => 'INNER',
				'conditions' => array(
					$this->alias . '.modified_user' . ' = ' . 'TrackableUpdater.id',
				),
			);
		}

		$uploads = Hash::extract($joinModels, '{s}[table=' . $this->UploadFile->table . ']');
		foreach ($uploads as $upload) {
			$joins[] = $upload;
		}

		return $joins;
	}

/**
 * 条件(Conditions)を取得
 *
 * @param array $conditions 条件(Conditions)リスト
 * @return array 実際に条件を含められるリスト
 */
	public function getSearchConditions($conditions = array()) {
		$fieldKeys = array_keys($conditions);
		foreach ($fieldKeys as $key) {
			list($field, $setting) = $this->_parseRequestKey($key);

			list($sign, $value) = $this->_creanSearchCondtion($field, $setting, $conditions[$key]);
			unset($conditions[$key]);

			if (! isset($this->readableFields[$field])) {
				continue;
			}

			if ($setting === self::MORE_THAN_DAYS) {
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
 * paginate メソッド
 *
 * @param array $conditions 条件配列
 * @param array $fields フィールド配列
 * @param array $order ソート配列
 * @param int $limit 取得件数
 * @param int $page ページ番号
 * @param int $recursive findのrecursive
 * @param array $extra findのオプション
 * @return array 検索結果
 */
	public function paginate($conditions, $fields, $order, $limit, $page = 1,
			$recursive = null, $extra = array()) {
		$displayRooms = Hash::get($extra, 'extra.plugin') === 'rooms';

		$joins = $this->getSearchJoinTables(Hash::get($extra, 'joins', []), $conditions);
		$conditions = $this->getSearchConditions($conditions);
		$recursive = -1;
		$group = 'User.id';
		if (! $order) {
			$order = array();
		}
		$order += Hash::get($extra, 'defaultOrder', array('Role.id' => 'asc'));

		if ($displayRooms) {
			$result = $this->__paginateByRoomRoleKey(
				$conditions, $fields, $joins, $order, $limit, $page, $recursive, $group, $extra
			);
		} else {
			$fields = $this->_getSearchFields($fields);
			$result = $this->find(
				'all',
				compact('conditions', 'fields', 'joins', 'order', 'limit', 'page', 'recursive', 'group')
			);
		}

		return $result;
	}

/**
 * paginate メソッド
 *
 * @param array $conditions 条件配列
 * @param array $fields フィールド配列
 * @param array $joins JOINテーブル配列
 * @param array $order ソート配列
 * @param int $limit 取得件数
 * @param int $page ページ番号
 * @param int $recursive findのrecursive
 * @param string $group GROUP BY
 * @param array $extra findのオプション
 * @return array 検索結果
 */
	private function __paginateByRoomRoleKey($conditions, $fields, $joins, $order,
			$limit, $page, $recursive, $group, $extra) {
		$dbSource = $this->getDataSource();
		$sql = '';

		$roles = array(
			Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			Role::ROOM_ROLE_KEY_CHIEF_EDITOR,
			Role::ROOM_ROLE_KEY_EDITOR,
			Role::ROOM_ROLE_KEY_GENERAL_USER,
			Role::ROOM_ROLE_KEY_VISITOR,
		);
		if (Hash::get($extra, 'extra.search', false)) {
			$roles[] = null;
		}

		$roomRoles = $this->RoomRole->find('list', array(
			'recursive' => -1,
			'fields' => array('role_key', 'level')
		));

		$selectedUsers = Hash::get($extra, 'extra.selectedUsers', array());

		//UNIONでデータ取得する
		$fields = $this->_getSearchFieldsByRoomRoleKey($fields);
		foreach ($roles as $roleKey) {
			if ($sql) {
				$sql .= ' UNION ';
			}

			$addConditions = array();
			if ($roleKey) {
				$addConditions['OR']['RolesRoom.role_key'] = $roleKey;
				$addConditions['OR']['User.id'] = Hash::extract(
					$selectedUsers, '{n}[role_key=' . $roleKey . '].user_id'
				);
			} else {
				$addConditions['RolesRoom.role_key'] = $roleKey;
				$addConditions['User.id NOT'] = Hash::extract(
					$selectedUsers, '{n}.user_id'
				);
			}
			$conditions[99] = $addConditions;
			$fields['room_role_level'] = Hash::get($roomRoles, $roleKey, 0) . ' AS ' . 'room_role_level';

			$query = $this->buildQuery('all',
				compact('conditions', 'fields', 'joins')
			);
			$query['table'] = $this->table;
			$query['alias'] = $this->alias;

			$sql .= $dbSource->buildStatement($query, $this);
		}
		$query = $this->buildQuery('all',
			compact('order', 'limit', 'page', 'recursive', 'group')
		);
		$sql .= $dbSource->group($query['group'], $this);
		$sql .= $dbSource->order($query['order'], 'ASC', $this);
		$sql .= $dbSource->limit($query['limit'], $query['offset']);

		$queryResult = $this->query($sql);
		$queryResult = Hash::extract($queryResult, '{n}.{n}');

		$results = array();
		foreach ($queryResult as $result) {
			$index = count($results);
			$results[$index] = array();
			foreach ($result as $column => $value) {
				$results[$index] = Hash::insert($results[$index], $this->getOriginalField($column), $value);
			}
		}

		return $results;
	}

/**
 * paginateCount メソッド
 *
 * @param array $conditions 条件配列
 * @param int $recursive findのrecursive
 * @param array $extra findのオプション
 * @return array 検索結果の件数
 */
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$displayRooms = Hash::get($extra, 'extra.plugin') === 'rooms';

		$joins = $this->getSearchJoinTables(Hash::get($extra, 'joins', []), $conditions);
		$conditions = $this->getSearchConditions($conditions);
		$recursive = -1;
		$group = 'User.id';

		if ($displayRooms) {
			$count = $this->__paginateCountByRoomRoleKey($conditions, $joins, $recursive, $group, $extra);
		} else {
			$count = $this->find('count', compact('conditions', 'joins', 'recursive', 'group'));
		}

		return $count;
	}

/**
 * paginateCount メソッド
 *
 * @param array $conditions 条件配列
 * @param array $joins JOINテーブル配列
 * @param int $recursive findのrecursive
 * @param string $group GROUP BY
 * @param array $extra findのオプション
 * @return array 検索結果の件数
 */
	private function __paginateCountByRoomRoleKey($conditions, $joins, $recursive, $group, $extra) {
		$roles = array(
			Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR,
			Role::ROOM_ROLE_KEY_CHIEF_EDITOR,
			Role::ROOM_ROLE_KEY_EDITOR,
			Role::ROOM_ROLE_KEY_GENERAL_USER,
			Role::ROOM_ROLE_KEY_VISITOR,
		);
		if (Hash::get($extra, 'extra.search', false)) {
			$roles[] = null;
		}

		$count = 0;
		foreach ($roles as $roleKey) {
			$conditions['RolesRoom.role_key'] = $roleKey;
			$count += $this->find('count', compact('conditions', 'joins', 'recursive', 'group'));
		}

		return $count;
	}

}


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
