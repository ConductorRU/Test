<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$url = '';
$isFull = (int)$arParams['IS_FULL'];
$gens = $arResult['gens'];
$houseR = isset($_REQUEST['house']) ? (int)$_REQUEST['house'] : 0;
$houseId = 0;
foreach($gens as $gen)
	if($gen['id'] = $houseR)
		$houseId = $houseR;
$rooms = Apart::GetRoomList();
$bals = Apart::GetBaloons();
?><div id="genplan" class="planner">
<?php if($isFull == 2): ?>
	<div class="genmap">
		<div class="plans">
			<div class="pHouses" style="z-index:3">
				<div class="plan" data-id="1" style="z-index:100;">
					<img class="img" src="/local/img/map_1.jpg" alt="" />
					<?php foreach($gens as $gen): ?>
					<a data-id="<?= $gen['id'] ?>" <?= $gen['active'] ? '' : 'class="dis"' ?> style="<?= $gen['css1'] ?>">
						<span><span><?= $gen['tip1'] ?><br><?= $gen['tip2'] ?></span></span>
					</a>
					<?php endforeach ?>
					<?php foreach($bals as $bal): ?>
					<div class="baloon <?= $bal['angle1'] ? 'top' : 'bottom' ?>" style="left:<?= $bal['x'] ?>%;top:<?= $bal['y'] ?>%;"><div><?= $bal['text'] ?></div></div>
					<?php endforeach ?>
				</div>
			</div>
			<div style="z-index:2;">
				<img data-id="0" src="/local/img/map_blank.png" style="display:block!important;" alt="" />
			</div>
		</div>
	</div>
<?php else: ?>
<?php if(!$isFull): ?>
	<div class="container">
		<h2>Генплан</h2>
	</div>
<?php endif ?>
	<div class="genmap">
		<div class="plans">
			<div class="pHouses" <?= $houseId ? 'style="display:none;"' : '' ?> style="z-index:3">
				<div class="plan" data-id="1" style="z-index:100;">
					<img class="img" src="/local/img/map_1.jpg" alt="" />
					<?php foreach($gens as $gen): ?>
					<a data-id="<?= $gen['id'] ?>" <?= $gen['active'] ? '' : 'class="dis"' ?> onclick="apart.SelectHouse(<?= $gen['id'] ?>, <?= $isFull ?>)" style="<?= $gen['css1'] ?>">
						<?php if($gen['tip']): ?><span><span><?= $gen['tip'] ?></span></span><?php endif ?>
						<img src="<?= $gen['image1'] ?>" alt="" />
					</a>
					<?php endforeach ?>
					<?php foreach($bals as $bal): ?>
					<div class="baloon <?= $bal['angle1'] ? 'top' : 'bottom' ?>" style="left:<?= $bal['x'] ?>%;top:<?= $bal['y'] ?>%;"><div><?= $bal['text'] ?></div></div>
					<?php endforeach ?>
				</div>
				<div class="plan" data-id="2" style="z-index:99;">
					<img class="img" src="/local/img/map_2.jpg" alt="" />
					<?php foreach($gens as $gen): ?>
					<a data-id="<?= $gen['id'] ?>" <?= $gen['active'] ? '' : 'class="dis"' ?> onclick="apart.SelectHouse(<?= $gen['id'] ?>, <?= $isFull ?>)" style="<?= $gen['css2'] ?>">
						<?php if($gen['tip']): ?><span><span><?= $gen['tip'] ?></span></span><?php endif ?>
						<img src="<?= $gen['image2'] ?>" alt="" />
					</a>
					<?php endforeach ?>
					<?php foreach($bals as $bal): ?>
					<div class="baloon <?= $bal['angle2'] ? 'top' : 'bottom' ?>" style="left:<?= $bal['x2'] ?>%;top:<?= $bal['y2'] ?>%;"><div><?= $bal['text'] ?></div></div>
					<?php endforeach ?>
				</div>
				<div style="z-index:98;">
					<img data-id="0" src="/local/img/map_blank.png" style="display:block!important;" alt="" />
				</div>
				<div class="buts" style="z-index:101;">
					<div class="container down">
						<span class="but butr" onclick="apart.Reverse()"><span>С другого ракурса</span> <img src="/local/img/revert.png" alt="" /></span>
						<a class="but" href="/catalog/">Подбор по параметрам</a>
					</div>
				</div>
			</div>
			<div class="pFloors" style="z-index:2">
				<div class="plan" style="z-index:98;">
					<?php $i = 100; foreach($gens as $gen):
						if(!$gen['active'])
							continue; ?>
						<object id="floor_<?= $gen['id'] ?>" data-id="<?= $gen['id'] ?>" type="image/svg+xml" data="<?= $gen['floor'] ?>" style="z-index:<?= $gen['id'] ?>; <?= ($houseId == $gen['id']) ? '' : 'display:none;' ?>" <?= ($houseId == $gen['id']) ? 'class="active"' : '' ?> ></object>
					<?php --$i; endforeach ?>
				</div>
				<div class="btns">
					<div class="container"><span class="but butr butHouse" onclick="apart.BackHouse()"><i class="fa fa-angle-left"></i><span>Назад</span></span> <span class="bTitle">Выберите этаж</span>
						<a class="but" style="float: right;" href="/catalog/">Подбор по параметрам</a>
                    </div>
				</div>
			</div>
			<div class="pPlan" style="z-index:1">
				<div class="container">
					<span class="back" onclick="apart.BackFloor()"><i class="fa fa-angle-left"></i> Выбрать другой этаж</span>
					<h2>Дом 2, 1 этаж</h2>
          <a class="but butg" style="position: absolute;top: 30px;right: 0;z-index: 10;" href="/catalog/">Подбор по параметрам</a>
				</div>				
				<div class="img"></div>
				<div class="params">
				<?php foreach($rooms as $k => $v): ?><span class="but nosel" data-id="<?= $k ?>" onclick="apart.FadeFloor(this)"><?= $v ?></span><?php endforeach ?>
				</div>
			</div>
			<img data-id="0" src="/local/img/map_blank.png" alt="" />
		</div>
	</div>
	<?php endif ?>
</div>
<?php if(isset($_GET['housen']) && isset($_GET['house_id']) && isset($_GET['floor'])): ?>
<script>
	$(document).ready(function()
	{
		$("#genplan .pFloors").hide();
		apart.SelectFloorIds(<?= (int)$_GET['housen'] ?>, <?= (int)$_GET['house_id'] ?>, <?= (int)$_GET['floor'] ?>, 0);
	});
</script>
<?php endif ?>