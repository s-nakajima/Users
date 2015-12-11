<?php
/**
 * SaveUser Behavior
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('ModelBehavior', 'Model');

/**
 * SaveUser Behavior
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Users\Model\Behavior
 */
class AvatarBehavior extends ModelBehavior {

/**
 * アバター自動生成処理
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param string $text 生成するテキスト
 * @return mixed On success Model::$data, false on failure
 * @throws InternalErrorException
 */
	public function createAvatarAutomatically(Model $model, $user) {
		if (Hash::get($user, 'UploadFile') ||
				Hash::get($user, 'User.' . User::$avatarField . '.name') ||
				! Hash::get($user, 'User.handlename')) {
			return true;
		}

		//imagickdraw オブジェクトを作成します
		$draw = new ImagickDraw();

		//文字色のセット
		$draw->setfillcolor('white');

		//フォントサイズを 200 に設定します
		$draw->setFontSize(200);

		//テキストを追加します
		$draw->setFont(CakePlugin::path($model->plugin) . 'webroot' . DS . 'fonts' . DS . 'NotoSansCJKjp-Regular.otf');
		$draw->annotation(50, 225, mb_substr(mb_convert_kana($user['User']['handlename'], 'KVA'), 0, 1));

		//新しいキャンバスオブジェクトを作成する
		$canvas = new Imagick();
		$canvas->newImage(300, 300, '#99CCCC');

		//ImagickDraw をキャンバス上に描画します
		$canvas->drawImage($draw);

		//フォーマットを PNG に設定します
		$canvas->setImageFormat('png');

		App::uses('TemporaryFile', 'Files.Utility');
		$file = new TemporaryFile();
		$canvas->writeImages('/var/www/app/app/webroot/bbb.png', true);
		$canvas->writeImages($file->path . '.png', TRUE);

		return $model->attachFile($user, User::$avatarField, $file->path . '.png', 'id');
	}

}
