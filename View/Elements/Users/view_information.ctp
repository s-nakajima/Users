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

<?php echo $this->SwitchLanguage->tablist('users-'); ?>
<br>
<div class="tab-content">
	<?php echo $this->UserAttributeLayout->renderRow('Users/view_information_row'); ?>
</div>
