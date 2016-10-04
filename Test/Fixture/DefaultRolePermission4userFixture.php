<?php
/**
 * 会員情報、会員管理用 DefaultRolePermissionFixture
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('DefaultRolePermissionFixture', 'Roles.Test/Fixture');

/**
 * 会員情報、会員管理用 DefaultRolePermissionFixture
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Test\Fixture
 */
class DefaultRolePermission4userFixture extends DefaultRolePermissionFixture {

/**
 * Model name
 *
 * @var string
 */
	public $name = 'DefaultRolePermission';

/**
 * Full Table Name
 *
 * @var string
 */
	public $table = 'default_role_permissions';

/**
 * Initialize the fixture.
 *
 * @return void
 */
	public function init() {
		parent::init();

		foreach ($this->records as $i => $record) {
			if ($record['permission'] === 'group_creatable' &&
					in_array($record['role_key'], ['administrator', 'common_user'], true)) {
				$record['value'] = '0';
			}
			$this->records[$i] = $record;
		}
	}

}
