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
 * @param array $user ユーザデータ配列
 * @return mixed On success Model::$data, false on failure
 * @throws InternalErrorException
 */
	public function createAvatarAutomatically(Model $model, $user) {
		//imagickdraw オブジェクトを作成します
		$draw = new ImagickDraw();

		//文字色のセット
		$draw->setfillcolor('white');

		//フォントサイズを 160 に設定します
		$draw->setFontSize(140);

		//テキストを追加します
		$draw->setFont(CakePlugin::path($model->plugin) . 'webroot' . DS . 'fonts' . DS . 'ipag.ttf');
		$draw->annotation(19, 143, mb_substr(mb_convert_kana($user['User']['handlename'], 'KVA'), 0, 1));

		//新しいキャンバスオブジェクトを作成する
		$canvas = new Imagick();

		//ランダムで背景色を指定する
		$red1 = strtolower(dechex(mt_rand(3, 12)));
		$red2 = strtolower(dechex(mt_rand(0, 15)));
		$green1 = strtolower(dechex(mt_rand(3, 12)));
		$green2 = strtolower(dechex(mt_rand(0, 15)));
		$blue1 = strtolower(dechex(mt_rand(3, 12)));
		$blue2 = strtolower(dechex(mt_rand(0, 15)));
		$canvas->newImage(179, 179, '#' . $red1 . $red2 . $green1 . $green2 . $blue1 . $blue2);

		//ImagickDraw をキャンバス上に描画します
		$canvas->drawImage($draw);

		//フォーマットを PNG に設定します
		$canvas->setImageFormat('png');

		App::uses('TemporaryFolder', 'Files.Utility');
		$folder = new TemporaryFolder();
		$filePath = $folder->path . DS . Security::hash($user['User']['handlename'], 'md5') . '.png';
		$canvas->writeImages($filePath, true);

		return $filePath;
	}

/**
 * アバター自動生成チェック
 *
 * * 削除がチェックONになっている ||
 * * アップロードファイルがない &&
 *		アバターを自動生成する場合 &&
 *		ハンドルを登録(POSTに含まれている)する場合 &&
 *		登録前のハンドル名と登録後のハンドル名が異なる場合
 *
 * @param Model $model ビヘイビア呼び出し元モデル
 * @param array $data リクエストデータ配列
 * @param array $user ユーザデータ配列
 * @param array $beforeUser 変更前ユーザデータ配列
 * @return mixed On success Model::$data, false on failure
 * @throws InternalErrorException
 */
	public function validAvatarAutomatically(Model $model, $data, $user, $beforeUser) {
		return Hash::get($data, 'User.' . User::$avatarField . '.remove') ||
			$data['User']['is_avatar_auto_created'] &&
			! Hash::get($user, 'User.' . User::$avatarField . '.name') &&
			Hash::get($user, 'User.handlename') &&
			Hash::get($beforeUser, 'User.handlename') !== Hash::get($user, 'User.handlename');
	}

}
