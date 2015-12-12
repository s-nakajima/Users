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
		//imagickdraw オブジェクトを作成します
		$draw = new ImagickDraw();

		//文字色のセット
		$draw->setfillcolor('white');

		//フォントサイズを 160 に設定します
		$draw->setFontSize(160);

		//テキストを追加します
		$draw->setFont(CakePlugin::path($model->plugin) . 'webroot' . DS . 'fonts' . DS . 'ipag.ttf');
		$draw->annotation(10, 152, mb_substr(mb_convert_kana($user['User']['handlename'], 'KVA'), 0, 1));

		//新しいキャンバスオブジェクトを作成する
		$canvas = new Imagick();

		//ランダムで背景色を指定する
		$red = strtolower(dechex(mt_rand(3, 12)));
		$green = strtolower(dechex(mt_rand(3, 12)));
		$blue = strtolower(dechex(mt_rand(3, 12)));
		$canvas->newImage(180, 180, '#' . $red . $red . $green . $green . $blue . $blue);

		//ImagickDraw をキャンバス上に描画します
		$canvas->drawImage($draw);

		//フォーマットを PNG に設定します
		$canvas->setImageFormat('png');

		App::uses('TemporaryFolder', 'Files.Utility');
		$folder = new TemporaryFolder();
		$folder->cd(APP . WEBROOT_DIR);

		$filePath = $folder->path . DS . Security::hash($user['User']['handlename'], 'md5') . '.png';
		$canvas->writeImages($filePath, true);

		$model->attachFile($user, User::$avatarField, $filePath, 'id');

		$folder->cd(TMP);

		return true;
	}

}
