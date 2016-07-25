<?php
/**
 * User Import/Export Behavior
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
 * User Import/Export Behavior
 * このビヘイビアは、Userモデルに付与されるもの
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model\Behavior
 */
class ImportExportBehavior extends ModelBehavior {

/**
 * エクスポート用のランダム文字列
 *
 * @var const
 */
	const RANDAMSTR = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#$%&=-~+*?@_';

/**
 * エクスポート用のランダム文字列
 *
 * @var const
 */
	const MAX_LIMIT = 1000;

/**
 * エクスポート用のランダム文字列
 *
 * @var const
 */
	public static $unexportFileds = array(
		'id', 'key', 'avatar', 'is_deleted', 'created', 'created_user', 'modified', 'modified_user',
		'password_modified', 'last_login', 'previous_login'
	);

/**
 * インポート処理
 * 後で、ちゃんと仕様を考えて作る
 *
 * @param Model $model Model using this behavior
 * @param string $filePath ファイルのパス
 * @return bool True on success, false on failure
 */
	public function importUsers(Model $model, $filePath) {
		App::uses('CsvFileReader', 'Files.Utility');

		//$model->begin();
		$model->prepare(true);

		$reader = new CsvFileReader($filePath);
		foreach ($reader as $i => $row) {
			if ($i === 0) {
				$header = $row;
				continue;
			}
			$row = array_combine($header, $row);
			$row['User.id'] = null;
			$row['User.password_again'] = $row['User.password'];

			$data = Hash::expand($row);
			$data = Hash::insert($data, 'UsersLanguage.{n}.id');
			$data = Hash::insert($data, 'UsersLanguage.0.language_id', '1');
			$data = Hash::insert($data, 'UsersLanguage.1.language_id', '2');

			CakeLog::debug(var_export($data, true));

			if (! $model->saveUser($data)) {
				//バリデーションエラーの場合
				//CakeLog::debug(var_export($data, true));
				return false;
			}
		}

		//$model->commit();
		//$model->rollback();

		return true;
	}

/**
 * エクスポート処理
 *
 * @param Model $model 呼び出しもとのModel
 * @param array $options エクスポートのオプション
 * @return bool
 */
	public function exportUsers(Model $model, $options = array()) {
		App::uses('CsvFileWriter', 'Files.Utility');

		$model->loadModels([
			'UserSearch' => 'Users.UserSearch',
			'UsersLanguage' => 'Users.UsersLanguage',
			'UserAttribute' => 'UserAttributes.UserAttribute',
		]);

		$userAttributes = $model->UserAttribute->getUserAttriburesForAutoUserRegist();
		$userAttributes = Hash::combine($userAttributes, '{n}.UserAttribute.key', '{n}');
		foreach (self::$unexportFileds as $field) {
			$userAttributes = Hash::remove($userAttributes, $field);
		}
		$header = $this->_getCsvHeader($model, $userAttributes);

		$conditions = Hash::get($options, 'conditions', []);
		$joins = $model->UserSearch->getSearchJoinTables(
			Hash::get($options, 'joins', array()), $conditions
		);
		$conditions = $model->UserSearch->getSearchConditions(Hash::get($options, 'conditions', []));

		$userIds = $model->find('list', array(
			'recursive' => -1,
			'conditions' => $conditions,
			'joins' => $joins,
			'group' => 'User.id',
			'limit' => self::MAX_LIMIT,
			'order' => array('Role.id' => 'asc')
		));

		$this->_bindModel($model);
		$users = $model->find('all', array(
			'fields' => array_keys($header),
			'recursive' => 0,
			'conditions' => array(
				'User.id' => array_values($userIds),
			),
			'order' => array('Role.id' => 'asc')
		));

		$csvWriter = new CsvFileWriter(array('header' => $header));
		if (! $users && ! is_array($users)) {
			$csvWriter->close();
			return false;
		}
		foreach ($users as $user) {
			$user = Hash::insert($user, 'User.password', '');
			$csvWriter->addModelData($user);
		}

		$csvWriter->close();
		return $csvWriter;
	}

/**
 * Modelのバインド
 *
 * @param Model $model 呼び出しもとのModel
 * @return void
 */
	protected function _bindModel(Model $model) {
		$languages = (new CurrentSystem())->getLanguages();
		foreach ($languages as $lang) {
			$modelName = 'UsersLanguage' . ucwords($lang['Language']['code']);

			$model->bindModel(array(
				'belongsTo' => array(
					$modelName => array(
						'className' => 'User.UsersLanguage',
						'foreignKey' => false,
						'conditions' => array(
							$modelName . '.user_id = User.id',
							$modelName . '.language_id' => $lang['Language']['id']
						),
						'fields' => '',
						'order' => ''
					),
				)
			), true);
		}
	}

/**
 * Modelのバインド
 *
 * @param Model $model 呼び出しもとのModel
 * @param array $userAttributes 会員項目リスト
 * @return void
 */
	protected function _getCsvHeader(Model $model, $userAttributes) {
		$model->loadModels([
			'UsersLanguage' => 'Users.UsersLanguage',
		]);
		$languages = (new CurrentSystem())->getLanguages();

		$header = array();
		foreach ($userAttributes as $attrKey => $userAttribute) {
			//Userテーブル
			if ($model->hasField($attrKey)) {
				$key = $model->alias . '.' . $attrKey;
				$header[$key] = Hash::get($userAttribute, 'UserAttribute.name');
			}
			//UsersLanguageテーブル
			if ($model->UsersLanguage->hasField($attrKey)) {
				foreach ($languages as $lang) {
					$alias = $model->UsersLanguage->alias . ucwords($lang['Language']['code']);
					$name = Hash::get($userAttribute, 'UserAttribute.name') .
							__d('m17n', '(' . $lang['Language']['code'] . ')');
					$header[$alias . '.' . $attrKey] = $name;
				}
			}
			//公開設定
			if ($model->hasField(sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $attrKey))) {
				$key = $model->alias . '.' . sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $attrKey);
				$header[$key] = Hash::get($userAttribute, 'UserAttribute.name') .
								__d('user_manager', '[Public setting]');
			}
			//公開設定
			if ($model->hasField(sprintf(UserAttribute::MAIL_RECEPTION_FIELD_FORMAT, $attrKey))) {
				$key = $model->alias . '.' . sprintf(UserAttribute::MAIL_RECEPTION_FIELD_FORMAT, $attrKey);
				$header[$key] = Hash::get($userAttribute, 'UserAttribute.name') .
								__d('user_manager', '[Public setting]');
			}
		}
		return $header;
	}

}
