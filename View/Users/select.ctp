<?php
/**
 * 後で見直す
 * UserManager index template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

$favoritesJson = array();
foreach ($favorites as $favorite) {
	$favoritesJson[] = $this->UserSearch->convertUserArrayByUserSelection($favorite, 'User');
}
?>

<?php $this->start('title_for_modal'); ?>
<?php echo __d('users', 'User select'); ?>
<?php $this->end(); ?>

<div ng-init="initialize('<?php echo $this->NetCommonsForm->domId('UserSearch.keyword'); ?>',
			<?php echo h(json_encode($favoritesJson)); ?>)">

	<?php echo $this->Form->create('UserSearch', array(
		'type' => 'get',
		'onsubmit' => 'return false;'
	)); ?>

	<div class="input-group">
		<?php echo $this->NetCommonsForm->input('keyword', array(
			'placeholder' => __d('users', 'Please enter handle name.'),
			'label' => false,
			'div' => false,
			'class' => 'form-control input-sm',
		)); ?>

		<span class="input-group-btn">
			<button class="btn btn-info btn-sm" type="button" ng-click="search()">
				<span class="glyphicon glyphicon-search"> </span>
			</button>
		</span>
	</div>

	<div ng-if="searched">
		<br>
		<div class="pre-scrollable" ng-if="candidates.length">
			<ul class="list-group user-selection-list-group">
				<li class="list-group-item clearfix" ng-repeat="user in candidates track by $index">
					<div class="pull-left">
						<?php echo $this->Button->add('', array(
							'iconSize' => 'btn-xs',
							'ng-click' => 'selectedFromCandidates($index)'
						)); ?>
					</div>
					<div class="user-selection-avatar-outer">
						<img ng-src="{{user.avatar}}" class="user-avatar-xs">
						{{user.handlename}}
					</div>
				</li>
			</ul>
		</div>

		<div ng-if="!candidates.length">
			<p><?php echo __d('users', 'Not found the candidate user.'); ?></p>
		</div>
	</div>

	<div ng-if="(!searched && favorites.length)">
		<br>
		<div class="pre-scrollable">
			<ul class="list-group user-selection-list-group">
				<li class="list-group-item clearfix" ng-repeat="user in favorites track by $index">
					<div class="pull-left">
						<?php echo $this->Button->add('', array(
							'iconSize' => 'btn-xs',
							'ng-click' => 'selectedFromFavorites($index)'
						)); ?>
					</div>
					<div class="user-selection-avatar-outer">
						<img ng-src="{{user.avatar}}" class="user-avatar-xs">
						{{user.handlename}}
					</div>
				</li>
			</ul>
		</div>
	</div>

	<hr>

	<div class="text-center">
		<?php echo $this->Button->cancel(__d('net_commons', 'Close'), '', array(
			'type' => 'button',
			'ng-click' => 'cancel()'
		)); ?>
	</div>

	<?php $this->end(); ?>
</div>
