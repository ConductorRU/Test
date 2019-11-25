<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

$plans = $arResult['plans'];
$sets = Settings::Get();
?>
<div id="plans">
	<div class="back">
		<div class="row">
			<div class="col-6"></div>
			<div class="col-6 gray"></div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-12">
				<h2><?= $sets['yandexTextTypes'] ?></h2>
				<div class="tabs">
					<div class="tab">
						<ul>
						<?php $i = 0; foreach($plans as $plan):
						?><li class="<?= !$i ? 'active' : '' ?>" data-tab="<?= ($i + 1) ?>"><?= $plan['name'] ?></li><?php ++$i; endforeach ?>
						</ul>
					</div>
					<div class="tabText">
						<?php $i = 0; foreach($plans as $plan): ?>
						<div class="<?= !$i ? 'active' : '' ?>" data-tab="<?= ($i + 1) ?>">
							<?php $y = 0; foreach($plan['plans'] as $p): 
							?><span class="item tabItem">
								<span data-id="<?= $y ?>" data-tab="<?= ($i + 1) ?>" style="background:url('<?= $p['thumbnail'] ?>') center center no-repeat;background-size:contain;"></span>
							</span><?php ++$y; endforeach ?>
						</div><?php ++$i; endforeach ?>
					</div>
				</div>
				<??>
				<div class="plans-btn-wrap"><a class="but butg" href="/catalog/"><?= strlen($arParams['CATALOG_BTN_TEXT']) ? $arParams['CATALOG_BTN_TEXT'] : Loc::getMessage('ARTEAST_PLANS_T_ALL_APARTS') ?></a></div>
				<div class="plans-btn-wrap"><span class="but butg call" data-toggle="modal" data-target="#callForm" onclick="<?= $sets['yandexCall'] ?>" <?= isset($arParams['CALL_BTN_COLOR']) ? "style='background:".$arParams['CALL_BTN_COLOR'].";'":"";?>><?= !empty($arParams['CALL_BTN_TEXT']) ? $arParams['CALL_BTN_TEXT'] : Loc::getMessage('ARTEAST_PLANS_T_GET_PRICE') ?></span></div>
			</div>
			<div class="col-sm-6 col-12 slider">
				<?php $i = 0; foreach($plans as $plan): ?>
				<div class="owl-carousel owl-theme <?= !$i ? 'active' : '' ?>" data-tab="<?= ($i + 1) ?>">
					<?php $y = 0; foreach($plan['plans'] as $p): 
					?><div class="item" data-id="<?= $y ?>" data-tab="<?= ($i + 1) ?>">
						<a class="img" <?php if($p['url']): ?>href="<?= $p['url'] ?>"<?php endif ?> style="background: url('<?= $p['image'] ?>') center center no-repeat;background-size:contain;"></a>
						<div class="container"><div class="row"><div class="col-6"><?= Loc::getMessage('ARTEAST_PLANS_T_SQUARE') ?> <?= $p['square'] ?> <?= Loc::getMessage('ARTEAST_PLANS_T_SQUARED_M') ?></div><div class="col-6"><?php if($p['minprice'] && !$sets['remove_cost']): ?><?= Loc::getMessage('ARTEAST_PLANS_T_PRICE_FOR') ?> <?= number_format($p['minprice'], 0, ',', ' ') ?> <?= Loc::getMessage('ARTEAST_PLANS_T_RUB') ?><?php endif ?></div></div></div>
					</div><?php ++$y; endforeach ?>
				</div><?php ++$i; endforeach ?>
			</div>
		</div>
		<div class="mob"><a class="but butg" href="/catalog/"><?= strlen($arParams['CATALOG_BTN_TEXT']) ? $arParams['CATALOG_BTN_TEXT'] : Loc::getMessage('ARTEAST_PLANS_T_ALL_APARTS') ?></a></div>
	</div>
</div>