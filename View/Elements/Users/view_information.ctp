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

<div class="panel-body">
	<div class="text-right">
		<?php if ($user['User']['id'] === Current::read('User.id') && ! $this->request->is('ajax')) : ?>
			<?php echo $this->Button->editLink('', array('block_id' => null, 'key' => $user['User']['id']), array(
					'tooltip' => true,
				)); ?>
		<?php endif; ?>
	</div>
	<div class="tab-content">
		<div ng-init="activeLangId = '<?php echo h($activeLangId); ?>'">
			<?php echo $this->UserAttributeLayout->renderRow('Users/view_information_row'); ?>
		</div>
	</div>
</div>
