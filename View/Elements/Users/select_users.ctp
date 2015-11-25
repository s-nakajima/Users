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

<ul class="list-group user-selection-list-group">
	<li class="list-group-item clearfix" ng-repeat="user in <?php echo $userType; ?> track by $index">
		<div class="pull-left">
			<?php echo $this->Button->add('', array(
				'type' => 'button',
				'iconSize' => 'btn-xs',
				'ng-click' => 'selectedFrom' . Inflector::camelize($userType) . '($index)'
			)); ?>
		</div>
		<div class="user-selection-avatar-outer">
			<img ng-src="{{user.avatar}}" class="user-avatar-xs">
			{{user.handlename}}
		</div>
	</li>
</ul>
