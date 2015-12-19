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

/**
 * UserSearch Behavior
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
	public static $unexportFileds = array(
		'id', 'key', 'is_deleted', 'created', 'created_user', 'modified', 'modified_user', 'password_modified', 'last_login', 'previous_login'
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
 * 後で、ちゃんと仕様を考えて作る
 *
 * @param Model $model Model using this behavior
 * @return bool True on success, false on failure
 */
	public function exportUsers(Model $model) {
		App::uses('CsvFileWriter', 'Files.Utility');

		$schema = array_flip(array_keys($model->schema(true)));
		foreach (self::$unexportFileds as $field) {
			$schema = Hash::remove($schema, $field);
		}

		$header = array_keys(Hash::flatten(array('User' => $schema)));
		$csvWriter = new CsvFileWriter(array('header' => array_combine($header, $header)));
		$users = $model->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				'role_key !=' => UserRole::USER_ROLE_KEY_SYSTEM_ADMINISTRATOR,
				'is_deleted' => false,
			),
		));

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

}
