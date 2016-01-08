<?php
/**
 * DisplayUser Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');
App::uses('UserAttribute', 'UserAttributes.Model');

/**
 * DisplayUser Helper
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\View\Helper
 */
class DisplayUserHelper extends AppHelper {

/**
 * Other helpers used by FormHelper
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.NetCommonsHtml',
	);

/**
 * 投稿者(TrackableCreator)や最終更新者(TrackableUpdater)などのハンドルリンクの表示
 *
 * @param array $user ユーザデータ
 * @param array $attributes リンクタグの属性
 * @param array $options リンクタグのオプション
 * @param array $model モデル名(TrackableCreatorやTrackableUpdaterなど)
 * @return string HTMLタグ
 */
	public function handleLink($user, $attributes = array(), $options = array(), $model = 'TrackableCreator') {
		$html = '';
		$handlename = $this->handle($user, $attributes, $model);
		$attributes = Hash::remove($attributes, 'avatar');

		if (Hash::get($user, 'ngModel')) {
			$userId = Hash::get($user, 'ngModel') . '.id';
		} else {
			$userId = '\'' . Hash::get($user, $model . '.id') . '\'';
		}
		if (! Current::read('User.id')) {
			$attributes['ng-click'] = null;
		}
		$html .= $this->NetCommonsHtml->link($handlename, '#',
			Hash::merge(array(
				'escape' => false,
				'ng-controller' => 'Users.controller',
				'ng-click' => 'showUser(' . $userId . ')'
			), $attributes),
			Hash::merge(array(
				'escape' => false
			), $options)
		);

		return $html;
	}

/**
 * 投稿者(TrackableCreator)や最終更新者(TrackableUpdater)などのハンドルの表示
 *
 * @param array $user ユーザデータ
 * @param array $attributes ハンドル表示の属性
 * @param array $model モデル名(TrackableCreatorやTrackableUpdaterなど)
 * @return string HTMLタグ
 */
	public function handle($user, $attributes = array(), $model = 'TrackableCreator') {
		$handlename = '';

		if (Hash::get($user, 'ngModel')) {
			if (Hash::get($attributes, 'avatar')) {
				$attributes = Hash::remove($attributes, 'avatar');
				$handlename .= '<img ng-src="{{' . Hash::get($user, 'ngModel') . '.avatar}}" class="user-avatar-xs"> ';
			}
			$handlename .= '{{' . Hash::get($user, 'ngModel') . '.handlename}}';
		} else {
			if (Hash::get($attributes, 'avatar')) {
				$attributes = Hash::remove($attributes, 'avatar');
				$handlename .= $this->avatar($user, Hash::get($attributes, 'avatar'), $model, true) . ' ';
			}
			$handlename .= h(Hash::get($user, $model . '.handlename'));
		}

		return $handlename;
	}

/**
 * 投稿者(TrackableCreator)や最終更新者(TrackableUpdater)などのアバターの表示
 *
 * @param array $user ユーザデータ
 * @param array $attributes imgタグの属性
 * @param array $model モデル名(TrackableCreatorやTrackableUpdaterなど)
 * @param bool $asImageTag imgタグとするかのフラグ
 * @return string HTMLタグ
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function avatar($user, $attributes = array(), $model = 'TrackableCreator', $asImageTag = true) {
		$html = '';

		//if (Hash::check($user, 'UploadFile.' . UserAttribute::AVATAR_FIELD . '.field_name')) {
		//	$keyPath = 'UploadFile.' . UserAttribute::AVATAR_FIELD . '.field_name';
		//} else {
		//	$keyPath = 'UploadFile.field_name';
		//}

		//$avatar = Hash::get(Hash::extract($user, $keyPath), '0');
		//if ($avatar) {
			$url = NetCommonsUrl::actionUrl(array(
				'plugin' => 'users',
				'controller' => 'users',
				'action' => 'download',
				'key' => Hash::get($user, $model . '.id'),
				UserAttribute::AVATAR_FIELD,
				'thumb'
			));
		//} else {
		//	$url = '/users/img/avatar.PNG';
		//}

		if ($asImageTag) {
			$html .= $this->NetCommonsHtml->image($url,
					Hash::merge(array('class' => 'user-avatar-xs', 'alt' => ''), $attributes));
		} else {
			$html .= $url;
		}

		return $html;
	}

}
