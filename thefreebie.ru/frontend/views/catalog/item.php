<?php
use yii\widgets\Breadcrumbs;
use common\models\Settings;
use common\models\NotifyPack;
/* @var $this yii\web\View */
$this->title = $pack->name . ' | Frebie';
$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => '/catalog'];
$this->params['breadcrumbs'][] = $pack->name;

$payments = [];
$icons = [];
Settings::GetPayments($payments, $icons);
?>
<div class="slider pack">
	<div class="sliderImg" style="background-image:url(/img/test-back.png);"></div>
	<div class="sliderText">
		<div class="info">
			<h1><?= $pack->name ?></h1>
			<div class="icon"><img src="<?= $pack->getTypeUrl() ?>" alt="" /></div>
			<div class="desc"><?= $pack->getDescHtml(true) ?></div>
		</div>
		<div class="container bread"><?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]); ?></div>
	</div>
</div>
<div id="pageItem" class="container page">
	<input type="hidden" name="pack_id" value="<?= $pack->id ?>" />
	<div class="breadMob"><?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]); ?></div>
	<div class="ribbon">
		<ul class="ulClear tabs nosel">
		<?php foreach($childs as $child):
			if($child == $pack)
				continue;
		?><li><a href="#set_<?= $child->id ?>" class="ani"><?= $child->name ?></a></li><?php endforeach ?>
		</ul>
		<a class="but ani" href="#buySet">Плати сколько хочешь</a>
		<div class="dateRemain">
		<?php
			if($pack->discount_to):
				$t = strtotime($pack->discount_to);
				if($t + 60*60*24 > time()):
			?>
			<span>Действительно по <span>акции</span> до:</span>
			<div class="date"><span><?= date('d', $t) ?></span><span><?= date('m', $t) ?></span><span><?= date('y', $t) ?></span></div>
			<div class="notify nosel <?= NotifyPack::IsNotify($pack->id) ? 'active' : '' ?>"><img class="svg" src="/img/bell.svg" alt="" /></div>
			<?php endif; endif ?>
		</div>
	</div>
	<div class="sets">
	<?php
	foreach($packs as $child):
		$cPack = $child['pack'];
	?>
		<div class="set" id="set_<?= $cPack->id ?>">
			<h2><?= $cPack->name ?><span class="bgCount bgCountG"><?= count($child['prods']) ?></span></h2>
			<div class="price">от <span><?= $child['price_from'] ?></span> руб.<?php if($child['price_to']): ?> до <span><?= $child['price_to'] ?></span> руб.<?php endif ?></div>
			<div class="desc"><?= $child['desc'] ?></div>
			<div class="row">
			<?php foreach($child['prods'] as $prod):
			?>
				<div class="col-md-4 col-xm-8 col-xs-16">
					<div class="item">
						<div class="name"><span><?= $prod->name ?></span></div>
						<div class="info">
							<div class="img" style="background-image:url('<?= $prod->GetImageUrl() ?>');"></div>
							<div class="text"><?= $prod->description ?></div>
						<?php if($prod->pack): ?>
							<div class="platform">
								<div class="os"><?= $prod->pack->GetOsHtml() ?></div>
								<div class="shop"><?= $prod->pack->GetPlatformHtml() ?></div>
							</div>
						<?php endif ?>
						</div>
					</div>
				</div>
			<?php
			endforeach ?>
			</div>
		</div>
	<?php endforeach; ?>
		<div class="prods">
			<div class="owl-carousel owl-theme owlCards">
				<?php foreach($prods as $prod): ?>
				<div class="owlCard">
					<div class="name"><span><?= $prod->name ?></span></div>
					<div class="img" style="background-image:url('<?= $prod->GetImageUrl() ?>');"></div>
					<div class="desc"><?= $prod->description ?></div>
				</div>
				<?php endforeach ?>
			</div>
		</div>
	</div>
	<div id="buySet" class="buyForm">
		<input type="hidden" name="parent_id" value="<?= $pack->id ?>" />
		<input type="hidden" name="pack_id" value="<?= ($pack->packs && count($pack->packs)) ? $pack->packs[0]->id : $pack->id ?>" />
		<div class="info">
			<h2>Приобрести один из пакетов “<?= trim($pack->altername) ? $pack->altername : $pack->name ?>”</h2>
			<div class="row">
				<div class="col-md-5 col-xm-8 col-xs-16">
					<div class="title">Что я получаю:</div>
					<div class="name"><?= $pack->name ?></div>
					<div class="img" style="background-image:url('<?= $pack->getSmallUrl(true) ?>')"></div>
				</div>
				<div class="col-md-11 col-xm-8 col-xs-16">
					<div class="row">
						<div class="col-md-6 col-xs-16">
							<div class="title">Описание:</div>
							<div class="desc"><?= $firstDesc ?></div>
						</div>
						<div class="col-md-10 col-xs-16">
							<div class="prices">
								<div class="title">Выбери сколько заплатить:</div>
								<div class="vars">
									<div class="fixPrices">
										<?php
										$i = 0;
										$firstPrice = 0;
										foreach($childs as $child): 
											$price = $child->GetRealPrice();
											if(!$i)
												$firstPrice = $price;
										?><button <?php if(!$i): ?>class="active"<?php endif ?> data-id="<?= $child->id ?>" data-value="<?= $price ?>"><?= $price ?> руб.</button><?php ++$i; endforeach ?>
									</div>
									<div class="varPrice">
										<span class="varTitle">Мой выбор</span>
										<input type="text" name="price" value="<?= $firstPrice ?>" onkeypress="OnlyNumber(event)" />
										<span class="varCur">руб.</span>
										<i class="nosel plus"><img class="svg" src="/img/input-up.svg" alt="" /></i>
										<i class="nosel minus"><img class="svg" src="/img/input-down.svg" alt="" /></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="paymentInfo">
						<div class="paymentInfoT">Методы оплаты:</div>
						<div class="paymentInfoD">
							<ul class="ulClear">
								<?php foreach($icons as $icon): ?><li title="<?= $icon['name'] ?>"><img src="<?= $icon['img'] ?>" alt="" /></li><?php endforeach ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="total">
			<div class="row">
				<div class="col-sm-4 col-xs-8">
					<div class="title">Стоимость</div>
					<div id="sumFinal" class="sum"><?= $firstPrice ?> руб</div>
				</div>
				<div class="col-sm-12 col-xs-8">
					<div class="buy">
						<div class="selectBox nosel paymentBox" data-tips="Выберите платежную систему">
							<select name="payment">
								<option value="0" data-hide="1">Выберите метод оплаты</option>
								<?php foreach($payments as $k => $v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach ?>
							</select>
						</div><button id="buyButton" class="but">Приобрести</button>
					</div>
					<div class="out"></div>
				</div>
			</div>
		</div>
	</div>
	<?= $this->render('../site/_subscribe'); ?>
</div>