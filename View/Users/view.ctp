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
<?php echo __d('users', 'User information'); ?>
<?php $this->end(); ?>

<?php if (isset($rooms)) : ?>
	<ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a href="#user-information" aria-controls="user-infomation" role="tab" data-toggle="tab">
				<?php echo __d('users', 'User information'); ?>
			</a>
		</li>

		<li>
			<a href="#user-rooms" aria-controls="user-rooms" role="tab" data-toggle="tab">
				<?php echo __d('users', 'Rooms'); ?>
			</a>
		</li>

		<li>
			<a href="#user-groups" aria-controls="user-groups" role="tab" data-toggle="tab">
				<?php echo __d('groups', 'Groups management'); ?>
			</a>
		</li>
	</ul>
<?php endif; ?>

<div class="tab-content">
	<div class="tab-pane active" id="user-information">
		<?php
			if ($user['User']['id'] === Current::read('User.id')) {
				$editLink = true;
			} else {
				$editLink = false;
			}
		?>

		<?php echo $this->element('Users/view_information', array('editLink' => $editLink)); ?>
	</div>

	<?php if (isset($rooms)) : ?>
		<div class="tab-pane" id="user-rooms">
			<?php echo $this->element('Users/view_rooms'); ?>
		</div>
	<?php endif; ?>

	<div class="tab-pane" id="user-groups">
		<?php echo $this->element('Groups.list'); ?>
	</div>
</div>
