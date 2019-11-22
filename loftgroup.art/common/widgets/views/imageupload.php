<?php
	use \yii\helpers\StringHelper;
	use common\models\Image;
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
		$photo = isset($model->$attribute[$ind]) ? $model->$attribute[$ind] : '';
	else
		$photo = isset($model->$attribute) ? $model->$attribute : '';
	$pArray = [];
	$isSingle = 0;
	$url = '';
	$p = Image::find()->where(['id' => $photo])->one();
	if($p)
		$url = $p->GetUrl();
	$sizeY = (int)($size*$aspect);
	$sizeF = (int)min($size*0.5, $sizeY*0.5);
?>
<div class="form-group wgImage">
	<div>
		<div class="wgItem<?= $p ? ' isLoaded' : '' ?>" style="width:<?= $size ?>px;height:<?= $sizeY ?>px;line-height:<?= $sizeY ?>px;font-size:<?= $sizeF ?>px;<?= $url ? 'background-image:url(\'' . $url . '\');' : '' ?><?= $isCover ? 'background-size:cover;' : 'background-size:contain;' ?>">
			<i class="fa fa-camera nosel"></i>
			<i class="bar"><i></i></i>
			<i class="remove nosel" onclick="wgImage.Remove(this);"><i class="fa fa-times"></i></i>
			<input type="hidden" name="<?= $basep ?>" value="<?= $photo ?>" />
			<input type="file" name="photo" data-id="0" onchange="wgImage.Upload(this, <?= $width ?>)">
		</div>
	</div>
</div>