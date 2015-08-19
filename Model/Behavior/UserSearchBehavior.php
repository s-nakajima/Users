<?php
/**
 * UserSearch Behavior
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
class UserSearchBehavior extends ModelBehavior {

/**
 * Readable Fields
 *
 * @var array
 */
	private $__readableFields = null;

/**
 * Return readable fields
 *
 * @param Model $model Model using this behavior
 * @return void
 */
	private function __prepare(Model $model) {
		$this->UserAttribute = ClassRegistry::init('UserAttributes.UserAttribute');
		$this->UserAttributesRole = ClassRegistry::init('UserRoles.UserAttributesRole');

		if (! isset($this->__readableFields)) {
			$results = $this->UserAttributesRole->find('list', array(
				'recursive' => -1,
				'fields' => array('user_attribute_key', 'user_attribute_key'),
				'conditions' => array(
					'role_key' => AuthComponent::user('role_key'),
					'other_readable' => true,
				)
			));

			$this->__readableFields = array('id');
			foreach ($results as $key => $field) {
				//Fieldのチェック
				if ($model->hasField($field)) {
					$this->__readableFields[$key] = $model->escapeField($field);
				}
				if ($model->UsersLanguage->hasField($field)) {
					$this->__readableFields[$key] = $model->UsersLanguage->escapeField($field);
				}
				//Field(is_xxxx_public)のチェック
				$fieldKey = sprintf(UserAttribute::PUBLIC_FIELD_FORMAT, $field);
				if ($model->hasField($fieldKey)) {
					$this->__readableFields[$fieldKey] = $model->escapeField($fieldKey);
				}
				//Field(xxxx_file_id)のチェック
				$fieldKey = sprintf(UserAttribute::FILE_FIELD_FORMAT, $field);
				if ($model->hasField($fieldKey)) {
					$this->__readableFields[$fieldKey] = $model->escapeField($fieldKey);
				}
			}
		}
	}

/**
 * Return search fields
 *
 * @param Model $model Model using this behavior
 * @param array $fields Display fields
 * @return array Return fields
 */
	public function searchFields(Model $model, $fields = array()) {
		$this->__prepare($model);

		$fieldKeys = array_keys($fields);
		foreach ($fieldKeys as $key) {
			if (! isset($this->__readableFields[$key])) {
				unset($fields[$key]);
			}
		}

		if (! $fields) {
			$fields = $this->__readableFields;
		}
		return array_values($fields);
	}


/**
 * Return search fields
 *
 * @param Model $model Model using this behavior
 * @param array $fields Display fields
 * @return array Return fields
 */
	public function dispayFields(Model $model, $sessionKey, $fields = array()) {
		$this->__prepare($model);

		if (! $fields) {
			$fields = CakeSession::read($sessionKey);
			if (! $fields || ! is_array($fields)) {
				$fields = array(
					'handlename',
					'name',
					'role_key',
					'status',
					'modified',
					'last_login'
				);
				$fields = array_combine($fields, $fields);
			}
		}

		$fieldKeys = array_keys($fields);
		foreach ($fieldKeys as $key) {
			if (! isset($this->__readableFields[$key])) {
				unset($fields[$key]);
			}
		}

		CakeSession::write($sessionKey, $fields);

		return $fields;
	}

/**
 * Return search conditions
 *
 * @param Model $model Model using this behavior
 * @param array $conditions Searchable codtions
 * @return array Return search conditions
 */
	public function searchConditions(Model $model, $conditions = array()) {
		$this->__prepare($model);

		$fieldKeys = array_keys($conditions);
		foreach ($fieldKeys as $key) {
			if (! isset($this->__readableFields[$key])) {
				unset($conditions[$key]);
			}
		}

		if (! isset($this->__readableFields['role_key'])) {
			$conditions['role_key'] = 'status_1';
		}

		return $conditions;
	}

/**
 * Return search join tables
 *
 * @param Model $model Model using this behavior
 * @return array Return search joins
 */
	public function searchJoinTables(Model $model, $joinModels = array()) {
		$joinModels = array_merge(array('UsersLanguage'), $joinModels);

		$joins = array();
		foreach ($joinModels as $joinModel) {
			switch ($joinModel) {
				case 'UsersLanguage':
					$joins[] = array(
						'table' => $model->UsersLanguage->table,
						'alias' => $model->UsersLanguage->alias,
						'type' => 'INNER',
						'conditions' => array(
							$model->UsersLanguage->alias . '.user_id' . ' = ' . $model->alias . '.id',
							$model->UsersLanguage->alias . '.language_id' => Configure::read('Config.languageId'),
						),
					);

					break;
				case 'Room':

					break;
			}
		}

		return $joins;
	}

}
