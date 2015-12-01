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


<div class="row">
	<div class="col-xs-12 user-selection-list" ng-repeat="user in <?php echo $userType; ?> track by $index" ng-class="{'user-selection-list-offset': $odd}">
		<?php if ($userType === 'candidates') : ?>
			<?php echo $this->Button->add('', array(
				'type' => 'button',
				'class' => 'btn btn-success btn-xs user-select-button',
				'ng-click' => 'select($index)',
				'ng-disabled' => 'selected(candidates[$index])',
				'ng-class' => '{active: selected(candidates[$index])}',
			)); ?>
		<?php endif; ?>

		<span class="user-selection-avatar-outer">
			<?php echo $this->DisplayUser->handleLink(array('ngModel' => 'user'), array('avatar' => true)); ?>
		</span>

		<?php if ($userType === 'selectors') : ?>
			<?php echo $this->Button->cancel('', false, array(
				'type' => 'button',
				'class' => 'btn btn-default btn-xs pull-right user-delete-button',
				'ng-click' => 'remove($index)'
			)); ?>
		<?php endif; ?>
	</div>

	<div class="clearfix visible-xs-block" ng-if="$odd"></div>
</div>
