<?php
/**
 * User select element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="clearfix">
	<div class="user-selection-list-group pull-left" ng-repeat="user in <?php echo $userType; ?> track by $index">
		<?php echo $this->Button->add('', array(
			'type' => 'button',
			'iconSize' => 'btn-xs',
			'ng-click' => 'selectedFrom' . Inflector::camelize($userType) . '($index)'
		)); ?>

		<span class="user-selection-avatar-outer">
			<?php echo $this->DisplayUser->handle(array('ngModel' => 'user'), array('avatar' => true)); ?>
		</span>
	</div>
</div>
