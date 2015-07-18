<?php
/**
 * User Model
 *
 * @property Role $Role
 * @property CreatedUser $CreatedUser
 * @property ModifiedUser $ModifiedUser
 * @property Group $Group
 * @property UserAttribute $UserAttribute
 * @property UserSelectAttribute $UserSelectAttribute
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('AppModel', 'Model');

/**
 * Summary for User Model
 */
class User extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'username' => array(
			'regex' => array(
				'rule' => array('custom', '/[\w]+/'),
				'message' => 'Invalid value',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password' => array(
			'regex' => array(
				'rule' => array('custom', '/[\w]+/'),
				'message' => 'Invalid value',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password_again' => array(
			'equalToField' => array(
				'rule' => array('equalToField', 'password'),
				'message' => 'Password does not match',
				'allowEmpty' => false,
				'required' => true,
				//'last' => false, // Stop validation after this rule
			)
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => false,
			'conditions' => array('User.role_key = Role.key', 'Role.type = 1'),
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Group' => array(
			'className' => 'Group',
			'joinTable' => 'groups_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'group_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'UserAttribute' => array(
			'className' => 'UserAttribute',
			'joinTable' => 'user_attributes_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'user_attribute_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'UserSelectAttribute' => array(
			'className' => 'UserSelectAttribute',
			'joinTable' => 'user_select_attributes_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'user_select_attribute_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

/**
 * Check field1 matches field2
 *
 * @param array $field1 field1 parameters
 * @param string $field2 field2 key
 * @return bool
 */
	public function equalToField($field1, $field2) {
		$keys = array_keys($field1);
		return $this->data[$this->name][$field2] === $this->data[$this->name][array_pop($keys)];
	}

/**
 * beforeSave
 *
 * @param array $options options
 * @return bool
 */
	public function beforeSave($options = array()) {
		App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
		if (isset($this->data[$this->alias]['password'])) {
			$passwordHasher = new SimplePasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash(
				$this->data[$this->alias]['password']
			);
		}
		return true;
	}

/**
 * Save admin user
 *
 * @param array $data data
 * @return mixed On success Model::$data, false on failure
 */
	public function saveUser($data = array()) {
		$this->loadModels([
			//'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
			'RoomRolePermission' => 'Rooms.RoomRolePermission',
			'User' => 'Users.User',
			'UserAttribute' => 'Users.UserAttribute',
			'UserAttributesUser' => 'Users.UserAttributesUser',
		]);

		$con = $this->getDataSource();
		$con->begin();
		try {
			$stored = $this->User->find('first', array(
				'conditions' => array(
					'User.username' => $data[$this->alias]['username']
				),
			));

			if ($stored) {
				$this->User->set($data[$this->alias]);
				$this->User->save();
				foreach ($stored['UserAttribute'] as $userAttribute) {
					$this->UserAttribute->set($userAttribute);
					$this->UserAttribute->save();
					$this->UserAttributesUser->set($userAttribute['UserAttributesUser']);
					$this->UserAttributesUser->save();
				}
			} else {
				$this->User->set($data);
				$this->User->save();
				/* $this->RolesRoomsUser->create(array( */
				/* 	'roles_room_id' => 1, */
				/* 	'user_id' => $this->User->id, */
				/* 	/\* 'created_user' => $this->User->id, *\/ */
				/* 	/\* 'modified_user' => $this->User->id, *\/ */
				/* )); */
				/* $this->RolesRoomsUser->save(); */
				/* foreach (RoomRolePermission::$DEFAULT_PERMISSIONS[$data[$this->alias]['role_key']] as $permission => $boolean) { */
				/* 	$this->RoomRolePermission->create(array( */
				/* 		'roles_room_id' => $this->RolesRoom->id, */
				/* 		'permission' => $permission, */
				/* 		'value' => $boolean, */
				/* 		/\* 'created_user' => $this->User->id, *\/ */
				/* 		/\* 'modified_user' => $this->User->id, *\/ */
				/* 	)); */
				/* 	$this->RoomRolePermission->save(); */
				/* } */
				$this->UserAttribute->set(array(
					'type' => 1,
					'required' => true,
					'is_each_language' => true,
					'can_read_self' => true,
					'can_edit_self' => true,
					'position' => 1,
					/* 'created_user' => $this->User->id, */
					/* 'modified_user' => $this->User->id, */
				));
				$this->UserAttribute->save();
				$this->UserAttributesUser->set(array(
					'user_id' => $this->User->id,
					'language_id' => 2,
					'key' => 'nickname',
					'value' => $data[$this->alias]['handlename'],
					/* 'created_user' => $this->User->id, */
					/* 'modified_user' => $this->User->id, */
					'user_attribute_id' => $this->UserAttribute->id
				));
				$this->UserAttributesUser->save();
			}
			$con->commit();
		} catch (Exception $e) {
			CakeLog::error($e->getTraceAsString());
			$con->rollback();
			return false;
		}

		return $this->{$this->alias};
	}
}
