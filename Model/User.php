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
App::uses('NetCommonsTime', 'NetCommons.Utility');

/**
 * User Model
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model
 */
class User extends UsersAppModel {

/**
 * 表示ページ数の定数
 *
 * @var const
 */
	const DISPLAY_PAGE_NUMBER = 9;

/**
 * 非公開の定数
 *
 * @var const
 */
	const PUBLIC_TYPE_CONFIDENTIAL = '0';

/**
 * 公開の定数
 *
 * @var const
 */
	const PUBLIC_TYPE_DISCLOSE_TO_ALL = '1';

/**
 * アバターNoimageサムネイル画像
 *
 * @var const
 */
	const AVATAR_THUMB = 'noimage_thumbnail.gif';

/**
 * アバターNoimage画像
 *
 * @var const
 */
	const AVATAR_IMG = 'noimage.gif';

/**
 * 公開・非公開のリスト
 * __constructでセットする
 *
 * @var array
 */
	public static $publicTypes = array();

/**
 * language data.
 *
 * @var array
 */
	public $languages = null;

/**
 * user attribute data.
 *
 * @var array
 */
	public $userAttributeData = array();

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.OriginalKey',
		'Files.Attachment',
		'Users.Avatar',
		'Users.DeleteUser',
		'Users.ImportExport',
		'Users.SaveUser',
		//'Users.UserSearch',
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
		'UserRoleSetting' => array(
			'className' => 'UserRoles.UserRoleSetting',
			'foreignKey' => false,
			'conditions' => array('User.role_key = UserRoleSetting.role_key'),
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

		self::$publicTypes = array(
			self::PUBLIC_TYPE_CONFIDENTIAL => __d('users', 'Confidential'),
			self::PUBLIC_TYPE_DISCLOSE_TO_ALL => __d('users', 'Disclose to all'),
		);

		//アバターの設定
		if (! Configure::read('NetCommons.installed')) {
			//インストール時は、アップロードビヘイビアを削除する
			$this->Behaviors->unload('Files.Attachment');
			$this->Behaviors->unload('Users.Avatar');
		}
	}

/**
 * UserModelの前準備
 *
 * 自動登録の場合、この処理を呼び出す前に$this->userAttributeDataをセットする。詳しくは、
 * [Auth.AutoUserRegist::saveAutoUserRegist()](../../Auth/classes/AutoUserRegist.html#method_saveAutoUserRegist)
 * を参照してください。
 *
 * @param bool $force 強制的に取得するフラグ
 * @return void
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function prepare($force = false) {
		$this->loadModels([
			'UserAttribute' => 'UserAttributes.UserAttribute',
			'UserAttributeSetting' => 'UserAttributes.UserAttributeSetting',
			'DataType' => 'DataTypes.DataType',
		]);

		if ($force || ! $this->userAttributeData) {
			$userAttributes = $this->UserAttribute->getUserAttributesForLayout($force);
			$this->userAttributeData = Hash::combine($userAttributes,
				'{n}.{n}.{n}.UserAttribute.id', '{n}.{n}.{n}'
			);
		}

		if (Configure::read('NetCommons.installed')) {
			$uploads = $this->UserAttributeSetting->find('list', array(
				'recursive' => -1,
				'fields' => array('id', 'user_attribute_key'),
				'conditions' => array(
					'data_type_key' => DataType::DATA_TYPE_IMG
				),
			));

			foreach ($uploads as $upload) {
				$this->uploadSettings($upload, array('contentKeyFieldName' => 'id'));
			}
		}
	}

/**
 * バリデーションのセット
 *
 * - ログインIDとパスワードのバリデーションルールをセットする。<br>
 * その他のUserモデルのバリデーションルールのセットは、
 * [Users.SaveUserBehavior::beforeValidate](../../Users/classes/SaveUserBehavior.html#method_beforeValidate)
 * で行う。<br>
 * また、UsersLanguageのバリデーションも実施する。
 *
 * - 自動登録のバリデーションの初期値のセットは、
 * [Auth.AutoUserRegist::validateRequest](../../Users/classes/SaveUserBehavior.html#method_validateRequest)
 * で行う。
 *
 * @param array $options Model::save()のオプション
 * @return bool
 * @link http://book.cakephp.org/2.0/ja/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->loadModels([
			'UsersLanguage' => 'Users.UsersLanguage',
		]);

		//ログインID
		if (! isset($this->data['User']['id'])) {
			$this->validate = Hash::merge($this->validate, array(
				'username' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('users', 'username')),
						'required' => true
					),
					'regex' => array(
						'rule' => array('custom', '/[\w]+/'),
						'message' => sprintf(
							__d('net_commons', 'Only alphabets and numbers are allowed to use for %s.'),
							__d('users', 'username')
						),
						'allowEmpty' => false,
						'required' => true,
					),
				),
			));
		}

		//パスワード
		if (Hash::get($this->data['User'], 'password') || ! isset($this->data['User']['id']) ||
				Hash::get($options, 'validatePassword', false)) {

			$this->validate = Hash::merge($this->validate, array(
				'password' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'message' => sprintf(
							__d('net_commons', 'Please input %s.'), __d('users', 'password')
						),
						'allowEmpty' => false,
						'required' => true,
					),
					'regex' => array(
						'rule' => array('custom', '/[\w]+/'),
						'message' => sprintf(
							__d('net_commons', 'Only alphabets and numbers are allowed to use for %s.'),
							__d('users', 'password')
						),
						'allowEmpty' => false,
						'required' => true,
					),
				),
				'password_again' => array(
					'notBlank' => array(
						'rule' => array('notBlank'),
						'allowEmpty' => false,
						'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('net_commons', 'Re-enter')),
						'required' => true,
					),
					'equalToField' => array(
						'rule' => array('equalToField', 'password'),
						'message' => __d('net_commons', 'The input data does not match. Please try again.'),
						'allowEmpty' => false,
						'required' => true,
					)
				),
			));
		} elseif (isset($this->data['User']['password'])) {
			unset($this->data['User']['password']);
		}

		//ログイン、パスワード以外のUserモデルのバリデーションルールのセットは、ビヘイビアで行う
		//（ログインとパスワードは、インストール時に使用するため）

		//UsersLanguageのバリデーション実行
		if (isset($this->data['UsersLanguage'])) {
			$usersLanguage = $this->data['UsersLanguage'];
			if (! $this->UsersLanguage->validateMany($usersLanguage)) {
				$this->validationErrors = Hash::merge(
					$this->validationErrors,
					$this->UsersLanguage->validationErrors
				);
				return false;
			}
		}

		return parent::beforeValidate($options);
	}

/**
 * Called after data has been checked for errors
 *
 * @return void
 */
	public function afterValidate() {
		//パスワードのハッシュ化
		if (Hash::get($this->data['User'], 'password')) {
			App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
			$passwordHasher = new SimplePasswordHasher();
			$this->data['User']['password'] = $passwordHasher->hash($this->data['User']['password']);

			$passwordAgain = $this->data['User']['password_again'];
			$this->data['User']['password_again'] = $passwordHasher->hash($passwordAgain);

			//パスワード変更日時セット
			$this->data['User']['password_modified'] = NetCommonsTime::getNowDatetime();
		}
	}

/**
 * Userの生成
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
				'role_key' => UserRole::USER_ROLE_KEY_COMMON_USER,
				'timezone' => (new NetCommonsTime())->getSiteTimezone(),
				'language' => Current::read('Language.code'),
			))
		);

		return $results;
	}

/**
 * Userの存在チェック
 *
 * @param int|array $userId ユーザID
 * @return bool True:正常、False:不正
 */
	public function existsUser($userId) {
		if (! $userId) {
			return false;
		}

		if (! is_array($userId)) {
			$userId = (array)$userId;
		}

		$count = $this->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias . '.id' => $userId,
				$this->alias . '.is_deleted' => false,
			),
		));

		return count($userId) === $count;
	}

/**
 * Userの取得
 *
 * @param int $userId ユーザID
 * @param int $languageId 言語ID
 * @return array
 */
	public function getUser($userId, $languageId = null) {
		$this->prepare();

		$user = $this->find('first', array(
			'recursive' => 0,
			'conditions' => array(
				$this->alias . '.id' => $userId
			),
		));

		unset($user['User']['password']);

		$conditions = array(
			$this->UsersLanguage->alias . '.user_id' => $userId
		);
		if (isset($languageId)) {
			$conditions[$this->UsersLanguage->alias . '.language_id'] = $languageId;
		}

		$usersLanguage = $this->UsersLanguage->find('all', array(
			'recursive' => 0,
			'fields' => array(
				$this->UsersLanguage->alias . '.*'
			),
			'conditions' => $conditions,
		));
		$user[$this->UsersLanguage->alias] = Hash::extract(
			$usersLanguage, '{n}.' . $this->UsersLanguage->alias
		);

		return $user;
	}

/**
 * ユーザの登録処理
 *
 * @param array $data data
 * @return mixed On success Model::$data, false on failure
 * @throws InternalErrorException
 */
	public function saveUser($data) {
		//トランザクションBegin
		$this->begin();
		$this->prepare();

		//プライベートルームの登録
		$this->loadModels([
			//'Space' => 'Rooms.Space',
			'Room' => 'Rooms.Room',
		]);

		$currentRoom = Current::read('Room');
		Current::$current['Room'] = null;

		$beforeUser = $this->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias . '.id' => Hash::get($data, 'User.id')
			),
		));

		if (Hash::get($data, 'User.' . UserAttribute::AVATAR_FIELD . '.remove')) {
			$data['User']['is_avatar_auto_created'] = true;
		} elseif (Hash::get($data, 'User.' . UserAttribute::AVATAR_FIELD . '.name')) {
			$data['User']['is_avatar_auto_created'] = false;
		} else {
			$isAvatarAutoCreated = Hash::get($beforeUser, 'User.is_avatar_auto_created', true);
			$data['User']['is_avatar_auto_created'] = (bool)$isAvatarAutoCreated;
		}

		$data['User']['language'] = Hash::get($data, 'User.language', Current::read('Language.code'));

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

			if ($this->Behaviors->hasMethod('createAvatarAutomatically')) {
				//下記の条件の場合、自動的にアバターを生成する
				// * 削除がチェックONになっている ||
				// * アップロードファイルがない &&
				//     アバターを自動生成する場合 &&
				//     ハンドルを登録(POSTに含まれている)する場合 &&
				//     登録前のハンドル名と登録後のハンドル名が異なる場合
				if ($this->validAvatarAutomatically($data, $user, $beforeUser)) {
					$filePath = $this->createAvatarAutomatically($user);

					$currentDir = getcwd();
					chdir(APP . WEBROOT_DIR);
					$this->attachFile($user, UserAttribute::AVATAR_FIELD, $filePath, 'id');
					chdir($currentDir);
				}
			}

			//トランザクションCommit
			$this->commit();

		} catch (Exception $ex) {
			//トランザクションRollback
			$this->rollback($ex);
		}

		Current::$current['Room'] = $currentRoom;

		return $user;
	}

}
