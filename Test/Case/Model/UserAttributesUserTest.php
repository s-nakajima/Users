<?php
/**
 * UserAttributesUser Test Case
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('UserAttributesUser', 'Model');

/**
 * Summary for UserAttributesUser Test Case
 */
class UserAttributesUserTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.users.user_attributes_user',
		'plugin.users.user',
		'plugin.users.user_attribute',
		'app.created_user',
		'app.modified_user',
		'plugin.users.user_select_attribute',
		'app.language',
		'app.languages_user_attribute',
		'app.role',
		'app.languages_role',
		'app.plugin',
		'app.frame',
		'app.box',
		'app.container',
		'app.page',
		'app.room',
		'app.group',
		'app.groups_language',
		'app.groups_user',
		'app.space',
		'app.top_page',
		'app.block',
		'app.blocks_language',
		'app.boxes_page',
		'app.containers_page',
		'app.languages_page',
		'app.frames_language',
		'app.roles_plugin',
		'app.roles_user_attribute',
		'app.languages_user_attributes_user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UserAttributesUser = ClassRegistry::init('UserAttributesUser');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserAttributesUser);

		parent::tearDown();
	}

}
