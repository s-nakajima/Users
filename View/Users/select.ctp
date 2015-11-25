<?php
/**
 * User select template
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
			'ng-keydown' => 'search($event)',
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
			<?php echo $this->element('Users/select_users', array('userType' => 'candidates')) ?>
		</div>

		<div ng-if="!candidates.length">
			<p><?php echo __d('users', 'Not found the candidate user.'); ?></p>
		</div>
	</div>

	<div ng-if="(!searched && favorites.length)">
		<br>
		<div class="pre-scrollable">
			<?php echo $this->element('Users/select_users', array('userType' => 'favorites')) ?>
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
