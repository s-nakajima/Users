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
 * @param array $attributes リンクタグの属性。array('avatar' => true)とするとアバターも表示する
 * @param array $options リンクタグのオプション
 * @param array $model モデル名(TrackableCreatorやTrackableUpdaterなど)
 * @return string HTMLタグ
 */
	public function handleLink($user, $attributes = [], $options = [], $model = 'TrackableCreator') {
		$html = '';
		$handlename = $this->handle($user, $attributes, $model);
		if (! $handlename) {
			return $html;
		}

		unset($attributes['avatar']);

		if (isset($user['ngModel'])) {
			$userId = $user['ngModel']['id'];
		} else {
			$userId = '\'' . $user[$model]['id'] . '\'';
		}
		if (! Current::read('User.id')) {
			$attributes['ng-click'] = null;
		}
		$html .= $this->NetCommonsHtml->link($handlename, '#',
			array_merge(array(
				'escape' => false,
				'ng-controller' => 'Users.controller',
				'ng-click' => 'showUser($event, ' . $userId . ')',
				'title' => h(Hash::get($user, $model . '.handlename')),
			), $attributes),
			array_merge(array(
				'escape' => false
			), $options)
		);

		return $html;
	}

/**
 * 投稿者(TrackableCreator)や最終更新者(TrackableUpdater)などのハンドルの表示
 *
 * @param array $user ユーザデータ
 * @param array $attributes ハンドル表示の属性。array('avatar' => true)とするとアバターも表示する
 * @param array $model モデル名(TrackableCreatorやTrackableUpdaterなど)
 * @return string HTMLタグ
 */
	public function handle($user, $attributes = array(), $model = 'TrackableCreator') {
		$handlename = '';

		if (Hash::get($user, 'ngModel')) {
			if (Hash::get($attributes, 'avatar')) {
				$handlename .=
					'<img ng-src="{{' . Hash::get($user, 'ngModel') . '.avatar}}" class="user-avatar-xs"> ';
			}
			$handlename .= '{{' . Hash::get($user, 'ngModel') . '.handlename}}';
		} else {
			if (! Hash::get($user, $model . '.handlename')) {
				return '';
			}
			if (Hash::get($attributes, 'avatar')) {
				$attributes = Hash::remove($attributes, 'avatar');
				$handlename .=
					$this->avatar($user, Hash::get($attributes, 'avatar'), $model . '.id', true) . ' ';
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
 * @param string $modelId モデル名+id(TrackableCreator.idやTrackableUpdater.idなど)
 * @param bool $imgTag imgタグとするかのフラグ
 * @return string HTMLタグ
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
	public function avatar($user, $attributes = [], $modelId = 'TrackableCreator.id', $imgTag = true) {
		$html = '';

		if ($this->_View->request->params['plugin'] === 'user_manager' &&
				$this->_View->request->params['controller'] === 'user_manager') {
			$url = NetCommonsUrl::actionUrl(array(
				'plugin' => 'user_manager',
				'controller' => 'user_manager',
				'action' => 'download',
				'key' => Hash::get($user, $modelId),
				'key2' => UserAttribute::AVATAR_FIELD,
				'thumb'
			));
		} else {
			$url = NetCommonsUrl::actionUrl(array(
				'plugin' => 'users',
				'controller' => 'users',
				'action' => 'download',
				'key' => Hash::get($user, $modelId),
				'key2' => UserAttribute::AVATAR_FIELD,
				'thumb'
			));
		}

		if ($imgTag) {
			$html .= $this->NetCommonsHtml->image($url,
					Hash::merge(['class' => 'user-avatar-xs', 'alt' => '', 'hasBlock' => false], $attributes));
		} else {
			$html .= Router::url($url);
		}

		return $html;
	}

/**
 * 投稿者(TrackableCreator)や最終更新者(TrackableUpdater)などのアバターリンクの表示
 *
 * @param array $user ユーザデータ
 * @param array $attr リンクタグの属性
 * @param array $options リンクタグのオプション
 * @param string $modelId モデル名+id(TrackableCreator.idやTrackableUpdater.idなど)
 * @return string HTMLタグ
 */
	public function avatarLink($user, $attr = [], $options = [], $modelId = 'TrackableCreator.id') {
		if (!$user) {
			return '';
		}
		$html = '';

		$avatar = $this->avatar($user, $attr, $modelId);

		if (isset($user['ngModel'])) {
			$userId = $user['ngModel']['id'];
		} else {
			$userId = '\'' . Hash::get($user, $modelId) . '\'';
		}
		if (! Current::read('User.id')) {
			$attr['ng-click'] = null;
		}
		$html .= $this->NetCommonsHtml->link($avatar, '#',
			array_merge(array(
				'escape' => false,
				'ng-controller' => 'Users.controller',
				'ng-click' => 'showUser($event, ' . $userId . ')'
			), $attr),
			array_merge(array(
				'escape' => false
			), $options)
		);

		return $html;
	}

}
