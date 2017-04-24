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
	__d('users', 'Confirm the contents of your withdrawal agreement, check "I agree", and click [NEXT].')
); ?>

<?php
$data = array(
	'User' => array('id' => $this->data['User']['id']),
	'_UserDelete' => array('disclaimer' => true),
);
$tokenFields = Hash::flatten($data);
$hiddenFields = $tokenFields;
$hiddenFields = array_keys($hiddenFields);

$this->request->data = $data;
$tokens = $this->Token->getToken('User', '/users/users/delete_disclaimer/' . $this->data['User']['id'], $tokenFields, $hiddenFields);
$data += $tokens;
?>

<div class="panel panel-danger" ng-init="initialize(<?php echo h(json_encode($data)); ?>)">
	<div class="panel-heading">
		<?php echo __d('users', 'User cancel disclaimer'); ?>
	</div>

	<?php echo $this->NetCommonsForm->create('User'	); ?>
		<div class="panel-body user-cancel-disclaimer">
			<?php echo $userCancelDisclaimer; ?>
		</div>

		<div class="panel-footer text-center" ng-init="userCancelDisclaimer=false">
			<?php echo $this->NetCommonsForm->hidden('User.id'); ?>

			<?php echo $this->Button->cancelAndSave(
					__d('net_commons', 'Cancel'),
					__d('net_commons', 'NEXT'),
					false,
					array('ng-click' => 'cancel()'),
					array('type' => 'button', 'icon' => 'chevron-right',
						'ng-disabled' => '!userCancelDisclaimer', 'ng-click' => 'disclaimer()')
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
