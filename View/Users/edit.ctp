<?php
/**
 * User edit form template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<ul class="nav nav-tabs" role="tablist">
	<li class="active">
		<a href="#user-information" aria-controls="user-infomation" role="tab" data-toggle="tab">
			<?php echo __d('users', 'User information'); ?>
		</a>
	</li>

	<li class="disabled">
		<a href="">
			<?php echo __d('users', 'Rooms'); ?>
		</a>
	</li>
</ul>
<br>

<div class="tab-content">
	<div class="tab-pane active" id="user-information">
		<div class="panel panel-default">
			<?php echo $this->NetCommonsForm->create('User'); ?>

			<?php echo $this->element('Users.Users/edit_form', array('element' => 'Users.Users/render_edit_row')); ?>

			<div class="panel-footer text-center">
				<?php echo $this->Button->cancelAndSave(
						__d('net_commons', 'Cancel'),
						__d('net_commons', 'OK'),
						$this->NetCommonsHtml->url(array('action' => 'view', 'block_id' => null, 'key' => Hash::get($this->data, 'User.id')))
					); ?>
			</div>

			<?php echo $this->NetCommonsForm->end(); ?>
		</div>

		<?php if ($this->params['action'] === 'edit') : ?>
			<?php echo $this->element('Users.Users/delete_form'); ?>
		<?php endif; ?>
	</div>
</div>
