<?php
/**
 * User Model
 *
 * @property Role $Role
 * @property RolesRoom $RolesRoom
 * @property Language $Language
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('UsersAppModel', 'Users.Model');

/**
 * User Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model
 */
class User extends UsersAppModel {

/**
 * language data.
 *
 * @var array
 */
	public $languages = null;

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'Users.SaveUser',
		'Users.DeleteUser',
		'Users.UserSearch',
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
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'UsersLanguage' => array(
			'className' => 'Users.UsersLanguage',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
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
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
					//'allowEmpty' => false,
					//'required' => false,
					//'last' => false, // Stop validation after this rule
					//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
			),
		));

		if (isset($this->data['User']['password']) && $this->data['User']['password'] !== '' || ! isset($this->data['User']['id'])) {
			$this->validate = Hash::merge($this->validate, array(
				'password' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('users', 'password')),
						'required' => true,
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
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
					)
				),
			));
		}

		return parent::beforeValidate($options);
	}

/**
 * Create user
 *
 * @return array
 */
	public function createUser() {
		$this->UserRole = ClassRegistry::init('UserRoles.UserRole');

		if (! isset($this->languages)) {
			$this->languages = $this->Language->find('list', array(
				'recursive' => -1,
				'fields' => array('Language.id', 'Language.code'),
				'order' => 'weight'
			));
		}

		$results['UsersLanguage'] = array();
		foreach (array_keys($this->languages) as $langId) {
			$index = count($results['UsersLanguage']);

			$usersLanguage = $this->UsersLanguage->create(array(
				'id' => null,
				'language_id' => $langId,
			));
			$results['UsersLanguage'][$index] = $usersLanguage['UsersLanguage'];
		}
		$results = Hash::merge($results,
			$this->create(array(
				'id' => null,
				'role_key' => UserRole::USER_ROLE_KEY_COMMON_USER
			))
		);

		return $results;
	}

/**
 * Get user
 *
 * @param int $userId users.id
 * @return array
 */
	public function getUser($userId) {
		$user = $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'id' => $userId
			),
		));
		unset($user['User']['password']);

		$usersLanguage = $this->UsersLanguage->find('all', array(
			'recursive' => 0,
			'fields' => array(
				'UsersLanguage.*'
			),
			'conditions' => array(
				'UsersLanguage.user_id' => $userId
			),
		));
		$user['UsersLanguage'] = Hash::extract($usersLanguage, '{n}.UsersLanguage');

		return $user;
	}

/**
 * Save user
 *
 * @param array $data data
 * @param bool $created True is created(add action), false is updated(edit action)
 * @return mixed On success Model::$data, false on failure
 * @throws InternalErrorException
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function saveUser($data, $created = true) {
		$this->loadModels([
			'User' => 'Users.User',
			'UsersLanguage' => 'Users.UsersLanguage',
		]);

		//トランザクションBegin
		$this->begin();

		if (isset($data[$this->alias]['password']) && $data[$this->alias]['password'] !== '') {
			App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
			$passwordHasher = new SimplePasswordHasher();
			$data[$this->alias]['password'] = $passwordHasher->hash($data[$this->alias]['password']);
			$data[$this->alias]['password_again'] = $passwordHasher->hash($data[$this->alias]['password_again']);
		}

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

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
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
