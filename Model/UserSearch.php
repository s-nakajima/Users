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

App::uses('UserSearchAppModel', 'Users.Model');

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
 * 閲覧できるフィールドリスト
 *
 * @var array
 */
	public $convRealToFieldKey = null;

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
		$this->readableFields['role_id']['field'] = $this->Role->alias . '.id';
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

		foreach ($this->readableFields as $key => $value) {
			$this->readableFields[$key]['key'] = $key;
			if (isset($this->readableFields[$key]['field'])) {
				$this->convRealToFieldKey[] = $value;
			}
		}
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
				'type' => 'LEFT',
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

		$convOrder = array();
		foreach ($order as $key => $sort) {
			if (isset($this->convRealToFieldKey[$key])) {
				$convKey = $this->convRealToFieldKey[$key]['key'];
			} else {
				$convKey = $key;
			}
			$convOrder[$convKey] = $sort;
		}
		$order = $convOrder;

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
		$allSelected = Hash::extract(
			$selectedUsers, '{n}.user_id'
		);

		//UNIONでデータ取得する
		$fields = $this->_getSearchFieldsByRoomRoleKey($fields);
		foreach ($roles as $roleKey) {
			$sql .= ' UNION ';
			if ($roleKey) {
				$selectUserIds = Hash::extract(
					$selectedUsers, '{n}[role_key=' . $roleKey . '].user_id'
				);
			} else {
				$selectUserIds = Hash::extract(
					$selectedUsers, '{n}[delete=true].user_id'
				);
			}

			$addConditions = array();
			$addConditions['OR']['AND']['RolesRoom.role_key'] = $roleKey;
			$addConditions['OR']['AND']['User.id NOT'] = array_diff($allSelected, $selectUserIds);
			if ($selectUserIds) {
				$addConditions['OR']['User.id'] = $selectUserIds;
			}

			$conditions[99] = $addConditions;
			$fields['room_role_level'] = Hash::get($roomRoles, $roleKey, 0) . ' AS ' . 'room_role_level';

			$query = $this->buildQuery('all',
				compact('conditions', 'fields', 'joins')
			);
			$query['table'] = $dbSource->fullTableName($this);
			$query['alias'] = $this->alias;

			$sql .= $dbSource->buildStatement($query, $this);
		}
		$query = $this->buildQuery('all',
			compact('order', 'limit', 'page', 'recursive', 'group')
		);
		$sql .= $dbSource->group($query['group'], $this);
		$sql .= $dbSource->order($query['order'], 'ASC', $this);
		$sql .= $dbSource->limit($query['limit'], $query['offset']);

		$queryResult = $this->query(substr($sql, 6));
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
