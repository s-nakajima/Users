<?php
/**
 * 検索結果出力JSON
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

$results['users'] = array();
foreach ($users as $user) {
	$result = $this->UserSearch->convertUserArrayByUserSelection($user, 'User');
	$results['users'][] = $result;
}

echo $this->NetCommonsHtml->json($results);
