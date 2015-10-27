<?php
/**
 * DeleteUser Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * DeleteUser Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model\Behavior
 */
class DeleteUserBehavior extends ModelBehavior {

/**
 * usersテーブルに関連するテーブル削除
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param int $userId ユーザID
 * @return bool True on success
 * @throws InternalErrorException
 */
	public function deleteUserAssociations(Model $model, $userId) {
		$models = array(
			//'UsersLanguage' => 'Users.UsersLanguage',
			'RolesRoomsUser' => 'Rooms.RolesRoomsUser',
		);
		$model->loadModels($models);

		$modelNames = array_keys($models);
		foreach ($modelNames as $modelName) {
			$conditions = array(
				$model->$modelName->alias . '.user_id' => $userId
			);
			if (! $model->$modelName->deleteAll($conditions, false)) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
		}

		////user_idがついているテーブルに対して削除する(必要かな？)
		//$tables = $model->query('SHOW TABLES');
		//foreach ($tables as $table) {
		//	$tableName = array_shift($table['TABLE_NAMES']);
		//	$columns = $model->query('SHOW COLUMNS FROM ' . $tableName);
		//	if (! Hash::check($columns, '{n}.COLUMNS[Field=user_id]')) {
		//		continue;
		//	}
		//
		//	$model->query('DELETE FROM ' . $tableName . ' WHERE user_id = ' . $userId);
		//}

		return true;
	}

}
