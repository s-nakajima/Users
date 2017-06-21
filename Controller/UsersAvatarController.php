<?php
/**
 * UsersAvatarController
 *
 * @copyright Copyright 2014, NetCommons Project
 * @author Kohei Teraguchi <kteraguchi@commonsnet.org>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('Controller', 'Controller');
App::uses('AuthComponent', 'Controller/Component');
App::uses('AppModel', 'Model');

/**
 * UsersAvatarController
 *
 */
class UsersAvatarController extends Controller {

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommons',
		'Files.Download',
	);

/**
 * download method
 *
 * @return void
 * @throws NotFoundException
 */
	public function download() {
		/* @var $User AppModel */
		/* @var $UserAttributeSetting AppModel */
		// シンプルにしたかったためAppModelを利用。インスタンス生成時少し速かった。
		$User = $this->__getSimpleModel('User');
		$User->Behaviors->load('Users.Avatar');
		$UserAttributeSetting = $this->__getSimpleModel('UserAttributeSetting');

		$fieldName = $this->request->params['field_name'];
		$userId = $this->request->params['user_id'];
		$params = [
			'hasOne' => [
				'UploadFile' => [
					'className' => 'UploadFile',
					'foreignKey' => false,
					'conditions' => [
						'UploadFile.plugin_key' => $this->plugin,
						'UploadFile.content_key = User.id',
						'UploadFile.field_name' => $fieldName,
					],
					'fields' => ['id']
				]
			],
		];
		$User->bindModel($params);

		$query = [
			'conditions' => [
				'User.id' => $userId,
				//@see https://github.com/NetCommons3/Users/blob/3.1.2/Controller/UsersController.php#L105-L111
				//@see https://github.com/NetCommons3/Users/blob/3.1.2/Model/Behavior/UserPermissionBehavior.php#L31-L33
				'User.is_deleted' => '0',
			],
			'recursive' => 0,
			'callbacks' => false,
		];
		$user = $User->find('first', $query);
		ClassRegistry::removeObject('User');
		ClassRegistry::removeObject('UploadFile');

		if (!$user) {
			return;
		}

		if (!$user['UploadFile']['id']) {
			return $this->__downloadNoImage($User, $user);
		}

		$options = [
			'size' => $this->params['size'],
		];

		if ($user['User']['id'] === AuthComponent::user('id')) {
			return $this->Download->doDownloadByUploadFileId($user['UploadFile']['id'], $options);
		}

		$params = [
			'hasOne' => [
				'UserAttributesRole' => [
					'className' => 'UserAttributesRole',
					'foreignKey' => false,
					'conditions' => [
						'UserAttributesRole.role_key' => $user['User']['role_key'],
						'UserAttributesRole.user_attribute_key = UserAttributeSetting.user_attribute_key',
					]
				]
			],
		];
		$UserAttributeSetting->bindModel($params);

		$fieldName = $this->request->params['field_name'];
		$query = [
			'conditions' => [
				'UserAttributeSetting.user_attribute_key' => $fieldName,
			],
			'recursive' => 0,
			'callbacks' => false,
		];
		$userAttributeSetting = $UserAttributeSetting->find('first', $query);
		ClassRegistry::removeObject('UserAttributeSetting');
		ClassRegistry::removeObject('UserAttributesRole');

		App::uses('UserAttribute', 'UserAttributes.Model');
		$fieldName = sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $fieldName);

		// 以下の条件の場合、ハンドル画像を表示する(他人)
		// * 各自で公開・非公開が設定可 && 非公開
		// * 権限設定の個人情報設定で閲覧不可、
		// * 会員項目設定で非表示(display=OFF)項目、
		if (($userAttributeSetting['UserAttributeSetting']['self_public_setting'] &&
				!$user['User'][$fieldName]) ||
			!$userAttributeSetting['UserAttributeSetting']['display'] ||
			!$userAttributeSetting['UserAttributesRole']['other_readable']
		) {
			return $this->__downloadNoImage($User, $user);
		}

		return $this->Download->doDownloadByUploadFileId($user['UploadFile']['id'], $options);
	}

/**
 * download method
 *
 * @param Model $User User model(AppModel)
 * @param array $user User data
 * @return void
 */
	private function __downloadNoImage($User, $user) {
		$fieldName = $this->request->params['field_name'];
		$fieldSize = $this->request->params['size'];

		// @see https://github.com/NetCommons3/Users/blob/3.1.2/Model/Behavior/AvatarBehavior.php#L123-L125
		App::uses('User', 'Users.Model');

		$this->response->file(
			$User->temporaryAvatar($user, $fieldName, $fieldSize),
			array('name' => 'No Image')
		);
		ClassRegistry::removeObject('User');

		return $this->response;
	}

/**
 * download method
 *
 * @param string $modelName Model name
 * @return void
 */
	private function __getSimpleModel($modelName) {
		$Model = ClassRegistry::init($modelName);
		$params = [
			'belongsTo' => [
				'TrackableCreator',
				'TrackableUpdater',
			]
		];
		$Model->unbindModel($params);
		$Model->Behaviors->unload('Trackable');

		ClassRegistry::removeObject($modelName);

		return $Model;
	}
}
