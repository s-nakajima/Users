<?php
/**
 * SaveUser Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');
App::uses('CurrentSystem', 'NetCommons.Utility');

/**
 * SaveUser Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model\Behavior
 */
class SaveUserBehavior extends ModelBehavior {

/**
 * beforeValidate is called before a model is validated, you can use this callback to
 * add behavior validation rules into a models validate array. Returning false
 * will allow you to make the validation fail.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False or null will abort the operation. Any other result will continue.
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = array()) {
		if (! Configure::read('NetCommons.installed')) {
			return true;
		}

		$model->loadModels([
			'UserAttribute' => 'UserAttributes.UserAttribute',
			'UserAttributesRole' => 'UserRoles.UserAttributesRole',
			'UsersLanguage' => 'Users.UsersLanguage',
		]);

		//UserAttributesRoleデータ取得
		$userAttributesRoles = $model->UserAttributesRole->getUserAttributesRole(
			Current::read('User.role_key')
		);

		//バリデーションルールのセット
		foreach ($model->userAttributeData as $userAttribute) {
			if ($userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_LABEL) {
				continue;
			}

			//強制エラーのセット
			$this->__setInvalidates($model, $userAttribute, $userAttributesRoles);

			//バリデーションセット
			if ($userAttribute['UserAttribute']['key'] === 'password') {
				//パスワードは、呼び出し元で行う
				continue;
			}

			$userAttributeKey = $userAttribute['UserAttribute']['key'];
			if ($model->data[$model->alias]['id'] &&
					! isset($model->data[$model->alias][$userAttributeKey])) {
				continue;
			}
			$this->__setValidates($model, $userAttribute);
		}

		//emailの重複チェック
		$emails = $this->__getEmailFields($model);
		$model->validate = Hash::merge($model->validate, array(
			'email' => array(
				'notDuplicate' => array(
					'rule' => array('notDuplicate', $emails),
					'message' => sprintf(
						__d('net_commons', '%s is already in use. Please choose another.'),
						__d('users', 'E-mail')
					),
					'allowEmpty' => true,
					'required' => false,
				),
			)
		));
		return true;
	}

/**
 * Emailのフィールド取得
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @return array
 */
	private function __getEmailFields(Model $model) {
		$model->loadModels([
			'DataType' => 'DataTypes.DataType',
			'UserAttributeSetting' => 'UserAttributes.UserAttributeSetting',
		]);

		$result = $model->UserAttributeSetting->find('list', array(
			'recursive' => -1,
			'fields' => array('id', 'user_attribute_key'),
			'conditions' => array(
				'data_type_key' => DataType::DATA_TYPE_EMAIL
			),
		));

		return array_values($result);
	}

/**
 * invalidatesのセット
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $userAttribute UserAttributeデータ
 * @param array $userAttributesRoles UserAttributesRoleデータ
 * @return void
 * @throws BadRequestException
 */
	private function __setInvalidates(Model $model, $userAttribute, $userAttributesRoles) {
		$userAttributeKey = $userAttribute['UserAttribute']['key'];
		if ($model->UsersLanguage->hasField($userAttributeKey)) {
			$modelName = $model->UsersLanguage->alias;
		} else {
			$modelName = $model->alias;
		}

		$userAttributesRole = Hash::extract($userAttributesRoles,
				'{n}.UserAttributesRole[user_attribute_key=' . $userAttributeKey . ']');
		$userAttributesRole = $userAttributesRole[0];

		//他人でother_editable=falseの場合、自分でself_editable=falseは、不正エラー
		if ($model->data[$model->alias]['id'] !== Current::read('User.id') &&
				! $userAttributesRole['other_editable'] &&
				$model->data[$model->alias]['id'] === Current::read('User.id') &&
				! $userAttributesRole['self_editable']) {

			throw new BadRequestException(__d('net_commons', 'Bad Request 1'));
		}

		//管理者しか強化しない項目のチェック⇒不正エラーとする
		if ($userAttribute['UserAttributeSetting']['only_administrator_editable'] &&
				! Current::allowSystemPlugin('user_manager') &&
				isset($model->data[$modelName][$userAttributeKey])) {

			throw new BadRequestException(__d('net_commons', 'Bad Request 2'));
		}
	}

/**
 * validatesのセット
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $userAttribute UserAttributeデータ
 * @return void
 */
	private function __setValidates(Model $model, $userAttribute) {
		$userAttributeKey = $userAttribute['UserAttribute']['key'];
		$userAttributeName = $userAttribute['UserAttribute']['name'];

		$validates = array();

		//必須チェック
		if ($userAttribute['UserAttributeSetting']['required']) {
			$validates['notBlank'] = array(
				'rule' => array('notBlank'),
				'message' => sprintf(__d('net_commons', 'Please input %s.'), $userAttributeName),
				'required' => false
			);
		}

		//重複チェック
		if (in_array($userAttributeKey, ['username', 'handlename', 'key'], true)) {
			$validates['notDuplicate'] = array(
				'rule' => array('notDuplicate', array($userAttributeKey)),
				'message' => sprintf(
					__d('net_commons', '%s is already in use. Please choose another.'),
					$userAttributeName
				),
				'allowEmpty' => true,
				'required' => false,
			);
		}

		//メールアドレスチェック
		if ($userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_EMAIL) {
			$validates['email'] = array(
				'rule' => array('email'),
				'message' => sprintf(
					__d('net_commons', 'Unauthorized pattern for %s.'), $userAttributeName
				),
				'allowEmpty' => true,
				'required' => false,
			);
		}

		//選択肢チェック
		if (isset($userAttribute['UserAttributeChoice'])) {
			if ($userAttributeKey === 'role_key') {
				$valuePath = '{n}.key';
			} else {
				$valuePath = '{n}.code';
			}
			$inList = array_values(
				Hash::combine($userAttribute['UserAttributeChoice'], '{n}.key', $valuePath)
			);
			$validates['inList'] = array(
				'rule' => array('inList', $inList),
				'message' => __d('net_commons', 'Invalid request.'),
				'allowEmpty' => true,
				'required' => false,
			);
		}

		if ($model->UsersLanguage->hasField($userAttributeKey)) {
			$model->UsersLanguage->validate[$userAttributeKey] = $validates;
		} else {
			$model->validate[$userAttributeKey] = $validates;
		}
	}

/**
 * beforeSave is called before a model is saved. Returning false from a beforeSave callback
 * will abort the save operation.
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False if the operation should abort. Any other result will continue.
 * @see Model::save()
 */
	public function beforeSave(Model $model, $options = array()) {
		//インストール時は、言語のCurrentデータをセットする
		if (! Configure::read('NetCommons.installed')) {
			(new CurrentSystem())->setLanguage();
		}

		return true;
	}

/**
 * afterSave is called after a model is saved.
 *
 * @param Model $model Model using this behavior
 * @param bool $created True if this save created a new record
 * @param array $options Options passed from Model::save().
 * @return bool
 * @see Model::save()
 * @throws InternalErrorException
 */
	public function afterSave(Model $model, $created, $options = array()) {
		//UsersLanguage登録
		$usersLanguages = Hash::get($model->data, 'UsersLanguage', array());
		if ($created) {
			$usersLanguages = Hash::insert($usersLanguages, '{n}.user_id', $model->data['User']['id']);
		}
		foreach ($usersLanguages as $index => $usersLanguage) {
			if (! $ret = $model->UsersLanguage->save($usersLanguage, false, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$model->data['UsersLanguage'][$index] = Hash::extract($ret, 'UsersLanguage');
		}

		if ($created) {
			//プライベートルームの登録
			$model->loadModels([
				'PrivateSpace' => 'PrivateSpace.PrivateSpace',
				'Room' => 'Rooms.Room',
			]);
			$room = $model->PrivateSpace->createRoom();
			$room['RolesRoomsUser']['user_id'] = $model->data['User']['id'];
			$room = $model->Room->saveRoom($room);
			if (! $room) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//プライベートルームのデフォルトでプラグイン設置
			$result = $model->PrivateSpace->saveDefaultFrames($room);
			if (! $result) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}

			//参加ルームの登録
			$this->__saveDefaultRolesRoomsUser($model);
		}

		//コミュニティ（会員全員）の参加者データ生成
		$this->__saveCommunityRolesRoomsUser($model);

		return true;
	}

/**
 * 参加ルームの登録
 *
 * @param Model $model Model using this behavior
 * @return bool
 * @throws InternalErrorException
 */
	private function __saveDefaultRolesRoomsUser(Model $model) {
		$model->loadModels([
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
			'Room' => 'Rooms.Room',
		]);

		//参加ルームの登録
		if (! isset($model->data['RolesRoomsUser'])) {
			$model->data['RolesRoomsUser'] = $model->Room->getDefaultRolesRoomsUser();
		}
		$model->data['RolesRoomsUser'] = Hash::remove(
			$model->data['RolesRoomsUser'], '{n}[roles_room_id=0]'
		);
		$model->data['RolesRoomsUser'] = Hash::insert(
			$model->data['RolesRoomsUser'], '{n}.user_id', $model->data['User']['id']
		);
		if ($model->data['RolesRoomsUser']) {
			$result = $model->RolesRoomsUser->saveMany($model->data['RolesRoomsUser']);
			if (! $result) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		return true;
	}

/**
 * コミュニティのRolesRoomsUserデータ登録
 *
 * @param Model $model Model using this behavior
 * @return bool
 * @throws InternalErrorException
 */
	private function __saveCommunityRolesRoomsUser(Model $model) {
		$model->loadModels([
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
			'RolesRoom' => 'Rooms.RolesRoom',
			'Room' => 'Rooms.Room',
			'PluginsRole' => 'PluginManager.PluginsRole',
		]);

		//参加ルームの登録
		$count = $model->PluginsRole->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				'plugin_key' => 'rooms',
				'role_key' => Hash::get($model->data, 'User.role_key')
			)
		));

		if ($count > 0) {
			$roomRoleKey = Role::ROOM_ROLE_KEY_ROOM_ADMINISTRATOR;
		} else {
			$room = $model->Room->find('first', array(
				'recursive' => -1,
				'fields' => array('id', 'default_role_key'),
				'conditions' => array(
					'id' => Room::ROOM_PARENT_ID,
				)
			));
			$roomRoleKey = Hash::get($room, 'Room.default_role_key');
		}

		$rolesRoomsUserId = $model->RolesRoomsUser->find('first', array(
			'recursive' => -1,
			'fields' => array('id', 'roles_room_id'),
			'conditions' => array(
				'room_id' => Room::ROOM_PARENT_ID,
				'user_id' => $model->data['User']['id']
			)
		));
		$rolesRooms = $model->RolesRoom->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'room_id' => Room::ROOM_PARENT_ID,
				'role_key' => $roomRoleKey
			),
		));

		$rolesRoomsUser = array(
			'id' => Hash::get($rolesRoomsUserId, 'RolesRoomsUser.id'),
			'room_id' => Room::ROOM_PARENT_ID,
			'user_id' => $model->data['User']['id'],
			'role_key' => $roomRoleKey,
			'roles_room_id' => Hash::get($rolesRooms, 'RolesRoom.id')
		);

		if ($rolesRoomsUser['roles_room_id'] !== Hash::get($rolesRoomsUserId, 'RolesRoomsUser.roles_room_id')) {
			$result = $model->RolesRoomsUser->save($rolesRoomsUser);
			if (! $result) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		return true;
	}

/**
 * ユーザの編集出来るかどうか
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $user ユーザデータ
 * @return bool
 */
	public function canUserEdit(Model $model, $user) {
		if (Current::read('User.role_key') !== UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR &&
				(! $user || $user['User']['role_key'] === UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR)) {
			return false;
		}

		return true;
	}

/**
 * UserのValidateチェック
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $data data
 * @return bool True:正常、False:不正
 */
	public function validateUser(Model $model, $data) {
		$model->prepare();

		//バリデーション
		$model->set($data);
		return $model->validates();
	}

/**
 * ユーザの登録処理
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param int $userId ユーザID
 * @return mixed On success Model::$data, false on failure
 * @throws InternalErrorException
 */
	public function updateLoginTime(Model $model, $userId) {
		//トランザクションBegin
		$model->begin();

		try {
			$update = array(
				'User.previous_login' => 'User.last_login',
				'User.last_login' => '\'' . date('Y-m-d H:i:s') . '\''
			);
			$conditions = array('User.id' => (int)$userId);
			if (! $model->updateAll($update, $conditions)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			$model->commit();

		} catch (Exception $ex) {
			$model->rollback($ex);
		}

		return true;
	}

/**
 * field1とfield2が同じかチェックする
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $field1 field1 parameters
 * @param string $field2 field2 key
 * @return bool
 */
	public function equalToField(Model $model, $field1, $field2) {
		$keys = array_keys($field1);
		return $model->data[$model->alias][$field2] === $model->data[$model->alias][array_pop($keys)];
	}

/**
 * 重複チェック
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $check チェック値
 * @param array $fields フィールドリスト
 * @return bool
 */
	public function notDuplicate(Model $model, $check, $fields) {
		$User = ClassRegistry::init('Users.User');

		$value = array_shift($check);
		$conditions = array();
		if ($model->data[$model->alias]['id']) {
			$conditions['id !='] = $model->data[$model->alias]['id'];
		}
		$conditions['is_deleted'] = false;
		foreach ($fields as $field) {
			$conditions['OR'][$field] = $value;
		}

		return !(bool)$User->find('count', array(
			'recursive' => -1,
			'conditions' => $conditions
		));
	}

}
