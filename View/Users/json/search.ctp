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
$results['paginator'] = $this->Paginator->params('User');
$results['paginator'] = array_map('intval', $results['paginator']);

if ($results['paginator']['limit'] >= $results['paginator']['page']) {
	$results['paginator']['startPage'] = 0;
} elseif ($results['paginator']['limit'] + $results['paginator']['page'] > $results['paginator']['pageCount']) {
	$results['paginator']['startPage'] = $results['paginator']['pageCount'] - User::DISPLAY_PAGE_NUMBER;
} else {
	$results['paginator']['startPage'] = $results['paginator']['page'] - $results['paginator']['pageCount'];
}

if ($results['paginator']['startPage'] + User::DISPLAY_PAGE_NUMBER > $results['paginator']['pageCount']) {
	$results['paginator']['endPage'] = $results['paginator']['pageCount'];
} else {
	$results['paginator']['endPage'] = $results['paginator']['startPage'] + User::DISPLAY_PAGE_NUMBER;
}
$results['paginator']['startPage'] += 1;

echo $this->NetCommonsHtml->json($results);
