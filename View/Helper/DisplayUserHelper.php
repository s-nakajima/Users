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

		if (! Current::read('User.id')) {
			$attributes['ng-click'] = null;
		}
		$html .= $this->NetCommonsHtml->link($handlename, '#',
			Hash::merge(array(
				'escape' => false,
				'ng-click' => 'showUser(\'' . Hash::get($user, $model . '.id') . '\')'
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
		if (Hash::get($attributes, 'avatar')) {
			$handlename .= $this->avatar($user, Hash::get($attributes, 'avatar'), $model);
		}
		$handlename .= h(Hash::get($user, $model . '.handlename'));

		return $handlename;
	}

/**
 * 投稿者(TrackableCreator)や最終更新者(TrackableUpdater)などのアバターの表示
 *
 * @param array $user ユーザデータ
 * @param array $attributes imgタグの属性
 * @param array $model モデル名(TrackableCreatorやTrackableUpdaterなど)
 * @return string HTMLタグ
 */
	public function avatar($user, $attributes = array(), $model = 'TrackableCreator') {
		$html = '';

		//後で対応する
		if (! is_array($attributes)) {
			$attributes = array();
		}
		if (! Hash::get($user, $model . '.avarar')) {
			$html .= $this->NetCommonsHtml->image('/users/img/avatar.PNG',
				Hash::merge(array('alt' => ''), $attributes)
			);
		}

		return $html;
	}

}
