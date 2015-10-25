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
		//Userモデルのバリデーションルールのセット
		$this->validate = Hash::merge($this->validate, array(
			//ログインID
			'username' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('users', 'username')),
					'required' => true
				),
				'regex' => array(
					'rule' => array('custom', '/[\w]+/'),
					'message' => sprintf(__d('net_commons', 'Only alphabets and numbers are allowed to use for %s.'), __d('users', 'username')),
					'allowEmpty' => false,
					'required' => true,
				),
			),
			//会員権限
			'role_key' => array(
				'notBlank' => array(
					'rule' => array('notBlank'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),
		));

		//パスワード
		if (isset($this->data['User']['password']) && $this->data['User']['password'] !== '' || ! isset($this->data['User']['id'])) {
			App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
			$passwordHasher = new SimplePasswordHasher();
			$this->data['User']['password'] = $passwordHasher->hash($this->data['User']['password']);
			$this->data['User']['password_again'] = $passwordHasher->hash($this->data['User']['password_again']);

			$this->validate = Hash::merge($this->validate, array(
				'password' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('users', 'password')),
						'required' => true,
					),
					'regex' => array(
						'rule' => array('custom', '/[\w]+/'),
						'message' => sprintf(__d('net_commons', 'Only alphabets and numbers are allowed to use for %s.'), __d('users', 'password')),
						'allowEmpty' => false,
						'required' => true,
					),
				),
				'password_again' => array(
					'equalToField' => array(
						'rule' => array('equalToField', 'password'),
						'message' => __d('users', 'Password does not match. Please try again.'),
						'allowEmpty' => false,
						'required' => true,
					)
				),
			));
		}

		//可変のバリデーションルールは、Users.SaveUserビヘイビアで行う。

		//UsersLanguageのバリデーション実行
		$usersLanguage = $this->data['UsersLanguage'];
		if (! $this->UsersLanguage->validateMany($usersLanguage)) {
			$this->validationErrors = Hash::merge(
				$this->validationErrors,
				$this->UsersLanguage->validationErrors
			);
			return false;
		}

		return parent::beforeValidate($options);
	}

/**
 * Called after each successful save operation.
 *
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return void
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#aftersave
 * @see Model::save()
 */
	public function afterSave($created, $options = array()) {
		//UsersLanguage登録
		if (isset($this->data['UsersLanguage'])) {
			if ($created) {
				$this->data = Hash::insert($this->data, 'UsersLanguage.{n}.user_id', $this->data['User']['id']);
			}
			foreach ($this->data['UsersLanguage'] as $index => $usersLanguage) {
				if (! $ret = $this->UsersLanguage->save($usersLanguage, false, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
				$this->data['UsersLanguage'][$index] = Hash::extract($ret, 'UsersLanguage');
			}
		}
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
			'recursive' => 0,
			'conditions' => array(
				$this->alias . '.id' => $userId
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
 * @return mixed On success Model::$data, false on failure
 * @throws InternalErrorException
 */
	public function saveUser($data) {
		$this->loadModels([
			'User' => 'Users.User',
			'UsersLanguage' => 'Users.UsersLanguage',
		]);

		//トランザクションBegin
		$this->begin();

		//バリデーション
		$this->set($data);
		if (! $this->validates()) {
			return false;
		}

		try {
			//Userデータの登録
			if (! $user = $this->save(null, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
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

}
