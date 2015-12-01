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

$selectorsJson = array();
foreach ($selectors as $selector) {
	$selectorsJson[] = $this->UserSearch->convertUserArrayByUserSelection($selector, 'User');
}
?>

<?php $this->start('title_for_modal'); ?>
<?php echo __d('users', 'User select'); ?>
<?php $this->end(); ?>

<div ng-init="initialize('<?php echo $this->NetCommonsForm->domId('UserSearch.keyword'); ?>',
			<?php echo h(json_encode($selectorsJson)); ?>)">

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

	<br ng-if="searched">
	<div class="panel panel-default" ng-if="searched">
		<div class="panel-body pre-scrollable user-selection-list-group">
			<div ng-if="candidates.length">
				<?php echo $this->element('Users/select_users', array('userType' => 'candidates')); ?>
			</div>
			<div ng-if="!candidates.length">
				<?php echo __d('users', 'Not found the candidate user.'); ?>
			</div>
		</div>
		<div class="panel-footer text-center user-selection-panel-footer">
			<nav ng-if="paginator && searched">
				<ul class="pagination pagination-sm">
					<li ng-if="paginator.startPage !== 1">
						<a href="" aria-label="First" ng-click="movePage(1)">
							<span aria-hidden="true">&laquo;</span>
						</a>
					</li>

					<li ng-repeat="page in pages" ng-class="{active: page === paginator.page}">
						<a href="" ng-click="movePage(page)">
							{{page}}
						</a>
					</li>

					<li ng-if="paginator.endPage !== paginator.pageCount">
						<a href="" aria-label="Last" ng-click="movePage(paginator.pageCount)">
							<span aria-hidden="true">&raquo;</span>
						</a>
					</li>
				</ul>
			</nav>
		</div>
	</div>

	<div class="panel panel-default" ng-if="searched">
		<div class="panel-body pre-scrollable user-selection-list-group">
			<div ng-if="selectors.length">
				<?php echo $this->element('Users/select_users', array('userType' => 'selectors')); ?>
			</div>
			<div ng-if="!selectors.length">
				<?php echo __d('users', 'Not found the select user.'); ?>
			</div>
		</div>
	</div>

	<?php $this->end(); ?>
</div>

<?php
$this->start('footer_for_modal');

echo $this->Button->cancelAndSave(
	__d('net_commons', 'Cancel'),
	__d('net_commons', 'Select'),
	false,
	array(
		'type' => 'button',
		'ng-click' => 'cancel()'
	),
	array(
		'type' => 'button',
		'ng-click' => 'save()'
	)
);

$this->end();
