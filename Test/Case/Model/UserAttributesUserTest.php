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
		'plugin.users.user',
		'plugin.user_attributes.user_attribute',
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
