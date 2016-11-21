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

<?php echo $this->Rooms->spaceTabs(Space::COMMUNITY_SPACE_ID, 'pills', array(
	Space::PUBLIC_SPACE_ID => array(
		'url' => '#user-public-space',
		'attributes' => array(
			'aria-controls' => 'user-public-space',
			'role' => 'tab',
			'data-toggle' => 'tab',
		),
	),
	Space::COMMUNITY_SPACE_ID => array(
		'url' => '#user-room-space',
		'attributes' => array(
			'aria-controls' => 'user-room-space',
			'role' => 'tab',
			'data-toggle' => 'tab',
		),
	),
)); ?>

<div class="tab-content">
	<div id="user-public-space" class="tab-pane">
		<article class="rooms-manager">
			<?php echo $this->Rooms->roomsRender(Space::PUBLIC_SPACE_ID,
					array(
						'dataElemen' => 'Users.Users/view_rooms_index',
						'headElement' => 'Users.Users/view_rooms_header'
					),
					array(
						'paginator' => false,
						'displaySpace' => true,
						'roomTreeList' => $roomTreeLists[Space::PUBLIC_SPACE_ID]
					)
				); ?>
		</article>
	</div>

	<div id="user-room-space" class="tab-pane active">
		<article class="rooms-manager">
			<?php echo $this->Rooms->roomsRender(Space::COMMUNITY_SPACE_ID,
					array(
						'dataElemen' => 'Users.Users/view_rooms_index',
						'headElement' => 'Users.Users/view_rooms_header'
					),
					array(
						'paginator' => false,
						'displaySpace' => false,
						'roomTreeList' => $roomTreeLists[Space::COMMUNITY_SPACE_ID]
					)
				); ?>
		</article>
	</div>
</div>
