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

$searchResultsJson = array();
foreach ($searchResults as $searchResult) {
	$searchResultsJson[] = $this->UserSearch->convertUserArrayByUserSelection($searchResult, 'User');
}

$data = array(
	'User' => array('id' => Current::read('User.id')),
	'Room' => array('id' => $roomId),
	'UserSelectCount' => array('user_id' => array()),
);
$tokenFields = Hash::flatten($data);
$hiddenFields = $tokenFields;
unset($hiddenFields['UserSelectCount.user_id']);
$hiddenFields = array_keys($hiddenFields);

$this->request->data = $data;
$this->Token->unlockField('UserSelectCount.user_id');
$tokens = $this->Token->getToken('User', '/users/users/select/' . Current::read('User.id'), $tokenFields, $hiddenFields);
$data += $tokens;

?>

<?php $this->start('title_for_modal'); ?>
<?php echo __d('users', 'User select'); ?>
<?php $this->end(); ?>

<div ng-init="initialize('<?php echo $this->NetCommonsForm->domId('UserSearch.keyword'); ?>',
			<?php echo h(json_encode($searchResultsJson)); ?>,
			<?php echo h(json_encode($data)); ?>,
			<?php echo h(json_encode(UsersController::$displaField[0])); ?>)">

	<?php echo $this->Form->create('UserSearch', array('type' => 'get', 'onsubmit' => 'return false;')); ?>
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
	<?php $this->end(); ?>

	<br ng-if="(searched || searchResults.length > 0)">
	<div class="panel panel-default" ng-if="(searched || searchResults.length > 0)"
			ng-class="{'panel-danger': (searchError)}">

		<div class="panel-heading" ng-if="(! searched)">
			<?php echo __d('users', 'Members who choose well'); ?>
		</div>
		<div class="panel-heading" ng-if="(! searchError && searched)">
			<?php echo __d('users', 'Result search'); ?>
		</div>

		<div class="panel-heading" ng-if="(searchError && paginator && paginator.endPage > 1)">
			<?php echo sprintf(__d('users', 'Too many results to show. Can you think of more specific search keywords?'), UserSelectCount::LIMIT); ?>
		</div>
		<div class="panel-heading" ng-if="(searchError && !searchResults.length)">
			<?php echo __d('users', 'Not found the search result.'); ?>
		</div>
		<div class="panel-body pre-scrollable user-selection-list-group" ng-if="(searchResults.length)">
			<div ng-if="searchResults.length">
				<?php echo $this->element('Users/select_users', array('userType' => 'searchResults')); ?>
			</div>
		</div>
	</div>

	<div class="panel panel-default" ng-if="(searched || searchResults.length > 0)">
		<div class="panel-heading">
			<?php echo __d('users', 'Selected users'); ?>
		</div>

		<div class="panel-body pre-scrollable user-selection-list-group">
			<div ng-if="selectors.length">
				<?php echo $this->element('Users/select_users', array('userType' => 'selectors')); ?>
			</div>
			<div ng-if="!selectors.length">
				<?php echo __d('users', 'Not found the select user.'); ?>
			</div>
		</div>
	</div>
</div>

<?php
$this->start('footer_for_modal');
echo $this->Button->cancelAndSave(
	__d('net_commons', 'Cancel'), __d('net_commons', 'Select'), false,
	array('type' => 'button', 'ng-click' => 'cancel()'),
	array('type' => 'button', 'ng-click' => 'save()', 'ng-disabled' => '!selectors.length')
);
$this->end();
