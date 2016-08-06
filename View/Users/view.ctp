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

<ul class="nav nav-tabs" role="tablist">
	<li<?php echo (Hash::get($this->request->query, 'tab') === 'user-infomation' ? ' class="active"' : ''); ?>>
		<a href="#user-information" aria-controls="user-infomation" role="tab" data-toggle="tab">
			<?php echo __d('users', 'User information'); ?>
		</a>
	</li>

	<?php if (isset($rooms)) : ?>
		<li<?php echo (Hash::get($this->request->query, 'tab') === 'user-rooms' ? ' class="active"' : ''); ?>>
			<a href="#user-rooms" aria-controls="user-rooms" role="tab" data-toggle="tab">
				<?php echo __d('users', 'Rooms'); ?>
			</a>
		</li>
	<?php endif; ?>

	<?php if (isset($groups)) : ?>
		<li<?php echo (Hash::get($this->request->query, 'tab') === 'user-groups' ? ' class="active"' : ''); ?>>
			<a href="#user-groups" aria-controls="user-groups" role="tab" data-toggle="tab">
				<?php echo __d('groups', 'Groups management'); ?>
			</a>
		</li>
	<?php endif; ?>
</ul>

<div class="tab-content">
	<div class="tab-pane<?php echo (Hash::get($this->request->query, 'tab') === 'user-infomation' ? ' active' : ''); ?>" id="user-information">
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
		<div class="tab-pane<?php echo (Hash::get($this->request->query, 'tab') === 'user-rooms' ? ' active' : ''); ?>" id="user-rooms">
			<?php echo $this->element('Users/view_rooms'); ?>
		</div>
	<?php endif; ?>

	<?php if (isset($groups)) : ?>
		<div class="tab-pane<?php echo (Hash::get($this->request->query, 'tab') === 'user-groups' ? ' active' : ''); ?>" id="user-groups">
			<?php echo $this->element('Groups.list'); ?>
		</div>
	<?php endif; ?>
</div>
