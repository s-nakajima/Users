<?php
/**
 * UserSelectAttribute Test Case
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

App::uses('UserSelectAttribute', 'Model');

/**
 * Summary for UserSelectAttribute Test Case
 */
class UserSelectAttributeTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.users.user_select_attribute',
		'plugin.users.user_attribute',
		'app.language',
		'app.languages_user_attribute',
		'app.role',
		'plugin.users.user',
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
		'app.block',
		'app.blocks_language',
		'app.boxes_page',
		'app.containers_page',
		'app.languages_page',
		'app.frames_language',
		'app.roles_plugin',
		'app.roles_user_attribute',
		'plugin.users.user_attributes_user',
		'app.languages_user_select_attribute',
		'plugin.users.user_select_attributes_user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UserSelectAttribute = ClassRegistry::init('UserSelectAttribute');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserSelectAttribute);

		parent::tearDown();
	}

/**
 * test mock
 *
 * @return void
 *
 * @author Jun Nishikawa <topaz2@m0n0m0n0.com>
 */
	public function test() {
	}
}
