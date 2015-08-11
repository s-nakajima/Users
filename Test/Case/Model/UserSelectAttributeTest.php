<?php
/**
 * UserSelectAttribute Test Case
 *
 * @author   Jun Nishikawa <topaz2@m0n0m0n0.com>
 * @link     http://www.netcommons.org NetCommons Project
 * @license  http://www.netcommons.org/license.txt NetCommons License
 */

//App::uses('UserSelectAttribute', 'Model');

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
		//'plugin.blocks.block',
		//'plugin.boxes.box',
		//'plugin.boxes.boxes_page',
		//'plugin.frames.frame',
		//'plugin.containers.container',
		//'plugin.containers.containers_page',
		//'plugin.groups.group',
		////'plugin.groups.groups_language',
		////'plugin.groups.groups_user',
		//'plugin.m17n.language',
		//'plugin.pages.languages_page',
		//'plugin.pages.page',
		//'plugin.plugin_manager.plugin',
		//'plugin.public_space.space',
		//'plugin.roles.role',
		////'plugin.roles.roles_plugin',
		////'plugin.roles.roles_user_attribute',
		//'plugin.rooms.room',
		'plugin.users.user',
		//'plugin.users.user_select_attribute',
		//'plugin.users.user_select_attributes_user',
		//'plugin.users.user_attributes_user',
		'plugin.user_attributes.user_attribute',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		//$this->UserSelectAttribute = ClassRegistry::init('UserSelectAttribute');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		//unset($this->UserSelectAttribute);

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
