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
		$userAttributesRoles =
				$model->UserAttributesRole->getUserAttributesRole(Current::read('User.role_key'));

		//バリデーションルールのセット
		$emails = array();
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

			if ($userAttribute['UserAttributeSetting']['data_type_key'] === DataType::DATA_TYPE_EMAIL) {
				$emails[] = $userAttribute['UserAttribute']['key'];
			}
		}

		//emailの重複チェック
		$model->validate = Hash::merge($model->validate, array(
			'email' => array(
				'notDuplicate' => array(
					'rule' => array('notDuplicate', $emails),
					'message' => sprintf(
						__d('net_commons', 'Your request %s already exists. Please try a different one.'),
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

			throw new BadRequestException(__d('net_commons', 'Bad Request'));
		}

		//管理者しか強化しない項目のチェック⇒不正エラーとする
		if ($userAttribute['UserAttributeSetting']['only_administrator_editable'] &&
				! Current::allowSystemPlugin('user_manager') &&
				isset($model->data[$modelName][$userAttributeKey])) {

			throw new BadRequestException(__d('net_commons', 'Bad Request'));
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

		if ($userAttribute['UserAttributeSetting']['required']) {
			$validates['notBlank'] = array(
				'rule' => array('notBlank'),
				'message' => sprintf(__d('net_commons', 'Please input %s.'), $userAttributeName),
				'required' => false
			);
		}

		if (in_array($userAttributeKey, ['username', 'handlename', 'key'], true)) {
			$validates['notDuplicate'] = array(
				'rule' => array('notDuplicate', array($userAttributeKey)),
				'message' => sprintf(
					__d('net_commons', 'Your request %s already exists. Please try a different one.'),
					$userAttributeName
				),
				'allowEmpty' => true,
				'required' => false,
			);
		}

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

}
