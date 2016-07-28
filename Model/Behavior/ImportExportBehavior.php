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
 * 最大LIMIT
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
 * インポートタイプ(重複エラーとする)
 *
 * @var const
 */
	const IMPORT_TYPE_NEW = '0';

/**
 * インポートタイプ(重複は上書きとする)
 *
 * @var const
 */
	const IMPORT_TYPE_UPDATE = '1';

/**
 * インポートタイプ(重複はスキップする)
 *
 * @var const
 */
	const IMPORT_TYPE_SKIP = '2';

/**
 * インポート処理
 *
 * @param Model $model Model using this behavior
 * @param string $filePath ファイルのパス
 * @param int $importType インポートタイプ
 * @return bool True on success, false on failure
 */
	public function importUsers(Model $model, $filePath, $importType = self::IMPORT_TYPE_NEW) {
		App::uses('CsvFileReader', 'Files.Utility');

		$model->begin();
		$model->prepare(true);

		$reader = new CsvFileReader($filePath);

		$saveData = array();
		$validationErrors = array();

		//入力チェック
		foreach ($reader as $i => $row) {
			if ($i === 0) {
				$fileHeader = $this->_parseCsvHeader($model, $row);
				continue;
			}

			$data = $this->_getCsvData($model, $row, $fileHeader, $importType);
			if (! $data) {
				//falseの場合、スキップする
				continue;
			}

			$data = $this->_convSaveData($model, $data);

			$model->set($data);
			if (! $model->validates()) {
				//バリデーションエラーの場合
				$validationErrors[$i] = $this->_getValidationErrors($model, $i);
				$model->validationErrors = array();
				continue;
			}
			$saveData[$i] = $data;
		}

		//バリデーションエラー
		if ($validationErrors) {
			$model->validationErrors = $validationErrors;
			return false;
		}

		foreach ($saveData as $i => $data) {
			if (! $model->saveUser($data)) {
				//ここでバリデーションエラーになった場合、登録中に発生したエラー
				//可能性があるのは、ファイル内での問題か、処理実行中に他プロセスで登録されたかのどちらか
				$validationErrors[$i] = $this->_getValidationErrors($model, $i);
				$model->validationErrors = $validationErrors;

				$model->rollback();
				return false;
			}
		}

		$model->commit();
		return true;
	}

/**
 * バリデーションエラー
 *
 * @param Model $model 呼び出しもとのModel
 * @param int $line 行数
 * @return array
 */
	protected function _getValidationErrors(Model $model, $line) {
		$flatten = Hash::flatten($model->validationErrors);
		foreach ($flatten as $key => $message) {
			$flatten[$key] = sprintf(__d('user_manager', 'Line %s: %s'), $line, $message);
		}
		return Hash::expand($flatten);
	}

/**
 * CSVファイルのヘッダーの整形
 *
 * @param Model $model 呼び出しもとのModel
 * @param array $fileHeader CSVファイルのヘッダー
 * @return void
 */
	protected function _parseCsvHeader(Model $model, $fileHeader) {
		$baseHeader = $this->_getCsvHeader($model);

		$result = array();

		foreach ($fileHeader as $i => $header) {
			if (isset($baseHeader[$header])) {
				$result[$i] = $header;
			} elseif (in_array($header, $baseHeader, true)) {
				$result[$i] = array_search($header, $baseHeader, true);
			}
		}

		return $result;
	}

/**
 * CSVファイルの取得
 *
 * @param Model $model 呼び出しもとのModel
 * @param array $csvData CSVファイルのデータ
 * @param array $fileHeader ファイルのヘッダー
 * @param int $importType インポートタイプ
 * @return array Userデータ
 */
	protected function _getCsvData(Model $model, $csvData, $fileHeader, $importType) {
		$model->loadModels([
			'UserAttribute' => 'UserAttributes.UserAttribute',
			'UsersLanguage' => 'Users.UsersLanguage',
		]);

		$data = array();
		foreach ($csvData as $i => $value) {
			if (isset($fileHeader[$i])) {
				$data = Hash::insert($data, $fileHeader[$i], $value);
			}
		}
		$data = Hash::insert($data, 'User.password_again', Hash::get($data, 'User.password'));

		//新規のみ。重複データはエラーとなる
		if ($importType === self::IMPORT_TYPE_NEW) {
			$data = Hash::insert($data, 'User.id', null);
			return $data;
		}

		$userLangIdfields = $this->_bindModel($model, true, true);

		$user = $model->find('first', array(
			'recursive' => 0,
			'fields' => array_merge(array('User.id'), $userLangIdfields, $fileHeader),
			'conditions' => array(
				$model->alias . '.username' => Hash::get($data, 'User.username'),
				$model->alias . '.is_deleted' => false,
			),
		));

		//重複データは無視する
		if ($importType === self::IMPORT_TYPE_SKIP && $user) {
			return false;
		}
		if (! $user) {
			$user = Hash::insert($user, 'User.id', null);
		}

		//重複データは上書きするのでマージする
		$data = Hash::merge($user, $data);

		return $data;
	}

/**
 * saveUserを実行するための形式にコンバートする
 *
 * @param Model $model 呼び出しもとのModel
 * @param array $data CSVファイルのヘッダー
 * @return void
 */
	protected function _convSaveData(Model $model, $data) {
		$convData = array();

		$convData['User'] = Hash::get($data, 'User');

		$languages = (new CurrentSystem())->getLanguages();
		foreach ($languages as $i => $lang) {
			$modelName = 'UsersLanguage' . ucwords($lang['Language']['code']);
			if (Hash::get($data, $modelName)) {
				$convData['UsersLanguage'][$i] = Hash::get($data, $modelName);
				$convData['UsersLanguage'][$i]['language_id'] = $lang['Language']['id'];
				$convData['UsersLanguage'][$i]['user_id'] = Hash::get($data, 'User.id');
			}
		}

		return $convData;
	}

/**
 * エクスポート処理
 *
 * @param Model $model 呼び出しもとのModel
 * @param array $options エクスポートのオプション
 * @param array $queryParams 絞り込みによる条件配列(リクエストデータ)
 * @return bool
 */
	public function exportUsers(Model $model, $options = array(), $queryParams = array()) {
		App::uses('CsvFileWriter', 'Files.Utility');

		$model->loadModels([
			'UserSearch' => 'Users.UserSearch',
		]);
		$this->_bindModel($model, false);

		$header = $this->_getCsvHeader($model);

		$defaultConditions = $model->UserSearch->cleanSearchFields($queryParams);
		$conditions = Hash::merge($defaultConditions, Hash::get($options, 'conditions', []));
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
 * @param bool $reset リセットするかどうか
 * @param bool $retIdFields 戻り値にidのフィールド名を含めるか
 * @return void
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @link http://book.cakephp.org/2.0/ja/models/associations-linking-models-together.html#dynamic-associations
 */
	protected function _bindModel(Model $model, $reset = true, $retIdFields = false) {
		$languages = (new CurrentSystem())->getLanguages();
		$idFields = array();
		foreach ($languages as $lang) {
			$modelName = 'UsersLanguage' . ucwords($lang['Language']['code']);
			$idFields[] = $modelName . '.id';

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
			), $reset);
		}

		if ($retIdFields) {
			return $idFields;
		}
	}

/**
 * Modelのバインド
 *
 * @param Model $model 呼び出しもとのModel
 * @return void
 */
	protected function _getCsvHeader(Model $model) {
		$model->loadModels([
			'UserAttribute' => 'UserAttributes.UserAttribute',
			'UsersLanguage' => 'Users.UsersLanguage',
		]);
		$languages = (new CurrentSystem())->getLanguages();

		$userAttributes = $model->UserAttribute->getUserAttriburesForAutoUserRegist();
		$userAttributes = Hash::combine($userAttributes, '{n}.UserAttribute.key', '{n}');
		foreach (self::$unexportFileds as $field) {
			$userAttributes = Hash::remove($userAttributes, $field);
		}

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
