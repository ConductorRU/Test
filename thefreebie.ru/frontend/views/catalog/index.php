<?php
use yii\widgets\Breadcrumbs;
/* @var $this yii\web\View */
$this->title = 'Каталог Frebie';
$this->params['breadcrumbs'][] = 'Каталог';
?>
<div id="pageCatalog" class="container page">
	<?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]); ?>
	<div class="main catalog">
		<div class="row head">
			<div class="col-md-12 col-xm-10 col-xs-16"><h1>Каталог<span class="bgCount bgCountG"><?= $count ?></span></h1></div>
			<div class="col-xs-16 breadMob"><?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]); ?></div>
			<div class="col-md-4 col-xm-6 col-xs-16">
			<?php if(count($types) > 1): ?>
				<div class="catBut">
					<div class="selectBox nosel catSel">
						<select onchange="main.ChangeCatalogType(this);">
							<option value="0" <?= $selType ? '' : 'data-hide="1"' ?> data-url="" data-value="Показывать все<span class='bgCount bgCountS'><?= $allCount ?></span>">Показывать все</option>
							<?php foreach($types as $k => $v):
							?><option <?= $selType == $v['url'] ? 'selected' : '' ?> value="<?= $k ?>" data-url="<?= $v['url'] ?>" data-value="<?= $v['name'] ?><span class='bgCount bgCountS'><?= $v['count'] ?></span>"><?= $v['name'] ?></option><?php
							endforeach ?>
						</select>
					</div>
				</div>
			<?php endif ?>
			</div>
		</div>	
	</div>
	<?= $this->render('_cards', ['packs' => $packs]) ?>
</div>