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

<ul class="nav nav-tabs" role="tablist">
	<li class="active">
		<a href="#user-information" aria-controls="user-infomation" role="tab" data-toggle="tab">
			<?php echo __d('users', 'User information'); ?>
		</a>
	</li>

	<?php if (Hash::get($user, 'User.id') === Current::read('User.id')) : ?>
		<li>
			<a href="#user-rooms" aria-controls="user-rooms" role="tab" data-toggle="tab">
				<?php echo __d('users', 'Rooms'); ?>
			</a>
		</li>
	<?php endif; ?>
</ul>
<br>

<div class="tab-content">
	<div class="tab-pane active" id="user-information">
		<?php echo $this->element('Users/view_information'); ?>
	</div>

	<?php if (Hash::get($user, 'User.id') === Current::read('User.id')) : ?>
		<div class="tab-pane" id="user-rooms">
			<?php echo $this->element('Users/view_rooms'); ?>
		</div>
	<?php endif; ?>
</div>
