<?php
/**
 * 承認待ち⇒承認、承認済みの場合、再送ボタン表示
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->NetCommonsForm->create('User', array(
		'type' => 'put',
		'class' => 'form-inline',
		'url' => NetCommonsUrl::actionUrlAsArray(array('controller' => 'user_manager', 'action' => 'status'))
	)); ?>

	<?php echo $this->NetCommonsForm->hidden('User.id', array('value' => $user['User']['id'])); ?>
	<?php echo $this->NetCommonsForm->hidden('User.status', array('value' => $user['User']['status'])); ?>

	<?php echo $this->Button->button($label, $options); ?>
<?php echo $this->NetCommonsForm->end();