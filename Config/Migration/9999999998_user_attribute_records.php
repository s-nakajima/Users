<?php
/**
 * Insert records migration
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('NetCommonsMigration', 'NetCommons.Config/Migration');

/**
 * Insert records migration
 *
 * @package NetCommons\Users\Config\Migration
 */
class UserAttributeRecords extends NetCommonsMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'user_attribute_records';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(),
		'down' => array(),
	);

/**
 * Insert records
 *
 * @var array $migration
 */
	public $records = array(
		'UserAttribute' => array(
			array('id' => '1', 'language_id' => '2', 'key' => 'avatar', 'data_type_template_key' => 'img', 'name' => 'アバター', 'row' => '1', 'col' => '1', 'weight' => '1', 'required' => '0', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '2', 'language_id' => '2', 'key' => 'username', 'data_type_template_key' => 'username', 'name' => 'ログインID', 'row' => '1', 'col' => '2', 'weight' => '1', 'required' => '1', 'is_system' => '1', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '3', 'language_id' => '2', 'key' => 'password', 'data_type_template_key' => 'password', 'name' => 'パスワード', 'row' => '1', 'col' => '2', 'weight' => '2', 'required' => '1', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '4', 'language_id' => '2', 'key' => 'handle', 'data_type_template_key' => 'handle', 'name' => 'ハンドル', 'row' => '1', 'col' => '2', 'weight' => '3', 'required' => '1', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '1', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '5', 'language_id' => '2', 'key' => 'name', 'data_type_template_key' => 'name', 'name' => '氏名', 'row' => '1', 'col' => '2', 'weight' => '4', 'required' => '0', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '1', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '6', 'language_id' => '2', 'key' => 'identifier', 'data_type_template_key' => 'identifier', 'name' => 'リンク識別子', 'row' => '1', 'col' => '2', 'weight' => '5', 'required' => '1', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '7', 'language_id' => '2', 'key' => 'email', 'data_type_template_key' => 'email', 'name' => 'eメール', 'row' => '2', 'col' => '1', 'weight' => '1', 'required' => '1', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '8', 'language_id' => '2', 'key' => 'moblie_mail', 'data_type_template_key' => 'email', 'name' => '携帯メール', 'row' => '2', 'col' => '1', 'weight' => '2', 'required' => '0', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '9', 'language_id' => '2', 'key' => 'sex', 'data_type_template_key' => 'radio', 'name' => '性別', 'row' => '2', 'col' => '1', 'weight' => '3', 'required' => '1', 'is_system' => '1', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '10', 'language_id' => '2', 'key' => 'timezone', 'data_type_template_key' => 'timezone', 'name' => 'タイムゾーン', 'row' => '2', 'col' => '1', 'weight' => '4', 'required' => '0', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '11', 'language_id' => '2', 'key' => 'role_key', 'data_type_template_key' => 'role_key', 'name' => '権限', 'row' => '2', 'col' => '1', 'weight' => '5', 'required' => '1', 'is_system' => '1', 'display_label' => '1', 'display_search_list' => '1', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '12', 'language_id' => '2', 'key' => 'status', 'data_type_template_key' => 'status', 'name' => '状態', 'row' => '2', 'col' => '1', 'weight' => '6', 'required' => '1', 'is_system' => '1', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '13', 'language_id' => '2', 'key' => 'created', 'data_type_template_key' => 'created', 'name' => '作成日時', 'row' => '2', 'col' => '2', 'weight' => '1', 'required' => '0', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '1', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '14', 'language_id' => '2', 'key' => 'created_user', 'data_type_template_key' => 'created_user', 'name' => '作成者', 'row' => '2', 'col' => '2', 'weight' => '2', 'required' => '0', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '15', 'language_id' => '2', 'key' => 'modified', 'data_type_template_key' => 'modified', 'name' => '更新日時', 'row' => '2', 'col' => '2', 'weight' => '3', 'required' => '0', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '16', 'language_id' => '2', 'key' => 'modified_user', 'data_type_template_key' => 'modified_user', 'name' => '更新者', 'row' => '2', 'col' => '2', 'weight' => '4', 'required' => '0', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '17', 'language_id' => '2', 'key' => 'password_modified', 'data_type_template_key' => 'password_modified', 'name' => 'パスワード変更日時', 'row' => '2', 'col' => '2', 'weight' => '5', 'required' => '0', 'is_system' => '1', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '18', 'language_id' => '2', 'key' => 'last_login', 'data_type_template_key' => 'last_login', 'name' => '最終ログイン日時', 'row' => '2', 'col' => '2', 'weight' => '6', 'required' => '0', 'is_system' => '1', 'display_label' => '1', 'display_search_list' => '1', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '19', 'language_id' => '2', 'key' => 'previous_login', 'data_type_template_key' => 'previous_login', 'name' => '前回ログイン日時', 'row' => '2', 'col' => '2', 'weight' => '7', 'required' => '0', 'is_system' => '1', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '20', 'language_id' => '2', 'key' => 'profile', 'data_type_template_key' => 'textarea', 'name' => 'プロフィール', 'row' => '3', 'col' => '1', 'weight' => '1', 'required' => '0', 'is_system' => '0', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
			array('id' => '21', 'language_id' => '2', 'key' => 'search_keywords', 'data_type_template_key' => 'text', 'name' => '検索キーワード', 'row' => '3', 'col' => '1', 'weight' => '2', 'required' => '0', 'is_system' => '1', 'display_label' => '1', 'display_search_list' => '0', 'self_publicity' => '0', 'self_email_reception_possibility' => '0', ),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		if ($direction === 'down') {
			return true;
		}
		foreach ($this->records as $model => $records) {
			if (!$this->updateRecords($model, $records)) {
				return false;
			}
		}
		return true;
	}
}
