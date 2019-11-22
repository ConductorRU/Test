<?php
	use \yii\helpers\StringHelper;
	use common\models\File;
	$base = StringHelper::basename($model->className());
	if(preg_match('/^(.*?)\[(.*?)\]$/', $attribute, $m))
		$basep = $base . '[' . $m[1] . '][' . $m[2] . ']';
	else
		$basep = $base . '[' . $attribute . ']';
	$ind = null;
	if(preg_match('/^(.*?)\[(.*?)\]$/', $attribute, $m))
	{
		$attribute = $m[1];
		$ind = $m[2];
	}
	if($ind)
		$fileId = isset($model->$attribute[$ind]) ? $model->$attribute[$ind] : '';
	else
		$fileId = isset($model->$attribute) ? $model->$attribute : '';
	$pArray = [];
	$isSingle = 0;
	$url = '';
	$p = File::findOne($fileId);
	if($p)
		$url = $p->url;
?>
<div class="form-group wgFile">
	<div>
		<div class="wgItem<?= $p ? ' isLoaded' : '' ?>">
			<div class="sel">
				<div class="btn btn-success">Загрузить файл</div>
				<input type="hidden" name="<?= $basep ?>" value="<?= $fileId ?>" />
				<input type="file" name="photo" data-id="0" onchange="wgFile.Upload(this)">
			</div>
			<div class="load">
				<i class="bar"><i></i><span></span></i>
			</div>
			<div class="view">
				<div><input type="text" class="form-control" value="<?= $p->url ?>" disabled /></div><div><i class="remove nosel" onclick="wgFile.Remove(this);">Удалить</i></div>
			</div>
			<div class="error">Ошибка загрузки</div>
		</div>
	</div>
</div>