<?php
	use \yii\helpers\StringHelper;
	use frontend\models\Image;
	$base = StringHelper::basename($model->className());
	$basep = $base . '[' . $attribute . ']';
	$birth = $model->$attribute;
	$matches = [];
	$year = 0;
	$month = 0;
	$day = 0;
	if(preg_match('/([0-9]{4})-(0[0-9]|10|11|12)-(0[0-9]|1[0-9]|2[0-9]|30|31)/', $birth, $matches))
	{
		$year = (int)$matches[1];
		$month = (int)$matches[2];
		$day = (int)$matches[3];
	}
?>
<div class="form-date" id="<?= $id ?>">
	<div>
		<input type="hidden" name="<?= $basep ?>" value="<?= $birth ?>" />
		<select class="form-control form-date-day" onchange="date.ChangeDay(this)">
			<option value="0">День</option>
			<?php foreach($days as $k => $v): ?><option value="<?= $k ?>"<?= ($day == $k) ? ' selected="selected"' : '' ?>><?= $v ?></option><?php endforeach ?>
		</select><select class="form-control form-date-month" onchange="date.ChangeMonth(this)">
			<option value="0">Месяц</option>
			<?php foreach($months as $k => $v): ?><option value="<?= $k ?>"<?= ($month == $k) ? ' selected="selected"' : '' ?>><?= $v ?></option><?php endforeach ?>
		</select><select class="form-control form-date-year" onchange="date.ChangeYear(this)">
			<option value="0">Год</option>
			<?php foreach($years as $k => $v): ?><option value="<?= $k ?>"<?= ($year == $k) ? ' selected="selected"' : '' ?>><?= $v ?></option><?php endforeach ?>
		</select>
	</div>
</div>