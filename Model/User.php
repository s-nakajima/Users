<?php
/**
 * User Model
 *
 * @property Role $Role
 * @property RolesRoom $RolesRoom
 * @property Language $Language
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('UsersAppModel', 'Users.Model');

/**
 * User Model
 */
class User extends UsersAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'Users.SaveUser',
		'Users.DeleteUser',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Role' => array(
			'className' => 'Roles.Role',
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
		'RolesRoom' => array(
			'className' => 'Rooms.RolesRoom',
			'joinTable' => 'roles_rooms_users',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'roles_room_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Language' => array(
			'className' => 'M17n.Language',
			'joinTable' => 'users_languages',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'language_id',
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
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'username' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('users', 'username')),
					'required' => true
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'regex' => array(
					'rule' => array('custom', '/[\w]+/'),
					'message' => sprintf(__d('net_commons', 'Only alphabets and numbers are allowed to use for %s.'), __d('users', 'username')),
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'role_key' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'password' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('users', 'password')),
					'required' => true
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
				'regex' => array(
					'rule' => array('custom', '/[\w]+/'),
					'message' => sprintf(__d('net_commons', 'Only alphabets and numbers are allowed to use for %s.'), __d('users', 'password')),
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
			'password_again' => array(
				'equalToField' => array(
					'rule' => array('equalToField', 'password'),
					'message' => 'Password does not match. Please try again.',
					'allowEmpty' => false,
					'required' => true,
					//'last' => false, // Stop validation after this rule
				)
			),
		));

		return parent::beforeValidate($options);
	}

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
 * Save user
 *
 * @param array $data data
 * @param bool $created True is created(add action), false is updated(edit action)
 * @return mixed On success Model::$data, false on failure
 */
	public function saveUser($data, $created) {
		$this->loadModels([
			'User' => 'Users.User',
			'UsersLanguage' => 'Users.UsersLanguage',
		]);

		//トランザクションBegin
		$this->setDataSource('master');
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		//バリデーション
		if (! $this->validateUser($data['User'])) {
			return false;
		}
		$usersLanguage = $data['UsersLanguage'];
		if (! $this->UsersLanguage->validateMany($usersLanguage)) {
			$this->validationErrors = Hash::merge($this->validationErrors, $this->UsersLanguage->validationErrors);
			return false;
		}

		try {
			//Userデータの登録
			if (! $user = $this->save(null, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//UsersLanguageデータの登録
			$data = Hash::insert($data, 'UsersLanguage.{n}.user_id', $user['User']['id']);

			foreach ($data['UsersLanguage'] as $index => $usersLanguage) {
				if (! $ret = $this->UsersLanguage->save($usersLanguage, false, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				$user['UsersLanguage'][$index] = Hash::extract($ret, 'UsersLanguage');
			}

			$dataSource->commit();
			//$dataSource->rollback();
			//return false;

		} catch (Exception $ex) {
			$dataSource->rollback();
			CakeLog::error($ex);
			throw $ex;
		}

		return $user;
	}

/**
 * Validate of User
 *
 * @param array $data received post data
 * @return bool True on success, false on validation errors
 */
	public function validateUser($data) {
		$this->set($data);
		$this->validates();
		if ($this->validationErrors) {
			return false;
		}
		return true;
	}

}
