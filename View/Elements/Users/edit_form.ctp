<?php
/**
 * Edit user form template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="panel-body">
	<?php echo $this->SwitchLanguage->tablist('users-'); ?>
	<br>

	<div class="tab-content">
		<?php echo $this->NetCommonsForm->hidden('User.id'); ?>
		<?php foreach (array_keys($this->data['UsersLanguage']) as $index) : ?>
			<?php echo $this->NetCommonsForm->hidden('UsersLanguage.' . $index . '.id'); ?>
			<?php echo $this->NetCommonsForm->hidden('UsersLanguage.' . $index . '.language_id'); ?>
		<?php endforeach; ?>

		<input type="password" value="" style="display: none;">
		<?php echo $this->UserAttributeLayout->renderRow($element); ?>
	</div>
</div>
