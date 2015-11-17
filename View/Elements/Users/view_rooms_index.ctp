<?php
/**
 * Rooms index template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<tr class="<?php echo $this->Rooms->statusCss($room); ?>">
	<td>
		<?php echo $this->Rooms->roomName($room, $nest); ?>
	</td>

	<td>
		<?php echo $this->Rooms->roomRoleName($room); ?>
	</td>

	<td>
		<?php
			if ($nest !== 0) {
				echo $this->Rooms->statusLabel($room, '%s', true);
			}
		?>
	</td>

	<td>
		<?php echo $this->Rooms->roomAccessed($room); ?>
	</td>
</tr>
