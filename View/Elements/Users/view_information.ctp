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

if (! isset($editLink)) {
	$editLink = false;
}
?>

<div class="panel panel-default">
	<div class="panel-body">
		<div class="text-right">
			<?php if ($editLink) : ?>
				<?php echo $this->Button->editLink('',
						array('block_id' => null, 'key' => $user['User']['id']),
						array('tooltip' => true, 'iconSize' => ' btn-xs')
					); ?>
			<?php endif; ?>
		</div>

		<div class="tab-content">
			<div ng-init="activeLangId = '<?php echo h($activeLangId); ?>'">
				<?php echo $this->UserAttributeLayout->renderRow('Users.Users/view_information_row'); ?>
			</div>
		</div>
	</div>
</div>
