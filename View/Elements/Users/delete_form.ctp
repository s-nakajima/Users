<?php
/**
 * 会員削除テンプレート
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

echo $this->NetCommonsHtml->script([
	'/users/js/user_delete.js'
]);
?>

<div class="nc-danger-zone" ng-init="dangerZone=false;" ng-controller="UserDelete.controller">
	<uib-accordion close-others="false">
		<div uib-accordion-group is-open="dangerZone" class="panel-danger">
			<uib-accordion-heading class="clearfix">
				<span style="cursor: pointer">
					<?php echo __d('users', 'Danger Zone'); ?>
				</span>
				<span class="pull-right glyphicon" ng-class="{'glyphicon-chevron-down': dangerZone, 'glyphicon-chevron-right': ! dangerZone}"></span>
			</uib-accordion-heading>

			<div class="pull-left">
				<?php echo sprintf(__d('net_commons', 'Delete all data associated with the %s.'), __d('users', 'User')); ?>
			</div>

			<?php echo $this->Button->delete(
					__d('net_commons', 'Delete'),
					'',
					array(
						'type' => 'button',
						'addClass' => 'pull-right',
						'onclick' => null,
						'ng-click' => 'showDisclaimer($event, ' . $user['User']['id'] . ')'
					)
				); ?>
		</div>
	</uib-accordion>
</div>
