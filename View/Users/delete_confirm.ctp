<?php
/**
 * UserAttribute index template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php $this->start('title_for_modal'); ?>
<?php echo __d('users', 'User cancel'); ?>
<?php $this->end(); ?>

<?php echo $this->MessageFlash->description(
	__d('users', 'Check "I agree", and click [Delete].')
); ?>

<div class="panel panel-danger">
	<div class="panel-heading">
		<?php echo __d('users', 'User cancel confirm'); ?>
	</div>

	<?php echo $this->NetCommonsForm->create('User', array(
			'type' => 'delete',
			'url' => NetCommonsUrl::actionUrlAsArray(array('action' => 'delete'))
		)); ?>

		<div class="panel-body user-cancel-confirm">
			<?php echo __d('users',
					'By pressing [Delete] button below, you will be automatically logged out, and your user profile will be deleted. ' .
					'You will never be able to login this NetCommons.<br>Are you sure to proceed?'
				); ?>
		</div>
		<div class="panel-footer text-center" ng-init="userCancelDisclaimer=false">
			<?php echo $this->NetCommonsForm->hidden('User.id'); ?>

			<?php echo $this->Button->cancel(
					__d('net_commons', 'Cancel'),
					false,
					array('ng-click' => 'cancel()')
				); ?>
			<?php echo $this->Button->delete(
					__d('users', 'Delete'),
					sprintf(__d('net_commons', 'Deleting the %s. Are you sure to proceed?'), __d('users', 'User')),
					array('ng-disabled' => '!userCancelDisclaimer')
				); ?>

			<span class="well well-sm btn-workflow user-disclaimer-check">
				<?php echo $this->NetCommonsForm->checkbox('_UserDelete.disclaimer', array(
						'label' => __d('users', 'I agree'),
						'checked' => false,
						'inline' => true,
						'ng-click' => 'userCancelDisclaimer=!userCancelDisclaimer',
						'hiddenField' => false
					)); ?>
			</span>
		</div>
	<?php echo $this->NetCommonsForm->end(); ?>
</div>
