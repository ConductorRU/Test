<?php
	use common\models\Settings;
	use common\models\Image;
	$this->registerMetaTag(['name' => 'description','content' => Settings::Lang('bureau_xx_meta_desc', Settings::META | Settings::SLASH)]);
	$this->registerMetaTag(['name' => 'keywords','content' => Settings::Lang('bureau_xx_meta_keys', Settings::META | Settings::SLASH)]);

	$name = Settings::Lang('bureau_xx_name');
	$photo = Settings::GetImageUrl('bureau_photo');
	$descs = explode("\n", Settings::Lang('bureau_xx_full_desc'));
	$rewards = json_decode(Settings::Lang('bureau_rewards'));
	$rewards = $rewards ? $rewards : [];
	$team = json_decode(Settings::Lang('bureau_team'));
	$team = $team ? $team : [];

	$lg = Yii::$app->params['lang'];
	$this->title = $name . ' - Loft Group';

?>
<div class="bureau">
	<div class="container">
		<div class="projects bureau">
			<div class="row">
				<div class="col-2">

				</div>
				<div class="col-8">
					<div class="projDesc"><?= str_replace("\n", '<br />', Settings::Lang('bureau_xx_desc')) ?></div>
				</div>
			</div>
			<div class="row align-items-end">
				<div class="col-2">
					<a class="pgBack ajax" data-dir="left" href="/"><img class="svg" src="/img/arrow_back.svg" alt="<?= addslashes(Settings::Lang('lang_xx_back')) ?>"><span><?= Settings::Lang('lang_xx_back') ?></span></a>
				</div>
				<div class="col-8">
					<h1><?= $name ?></h1>
				</div>
			</div>
		</div>
	</div>
	<?php if($photo): ?>
	<div class="imgWide">
		<img src="<?= $photo ?>" alt="" />
	</div>
	<?php endif ?>
	<div class="container">
		<div class="projects">
			<div class="info">
				<div class="row">
					<div class="col-2">
						<div class="leftTitle"><?= Settings::Lang('bureau_xx_about') ?></div>
					</div>
					<div class="col-8">
						<div class="desc">
							<?php 
							foreach($descs as $desc)
								if(trim($desc))
									echo '<p>' . $desc . '</p>';
							?>
						</div>
						<div class="skills">
							<div class="sTitle"><?= Settings::Lang('bureau_xx_skills') ?></div>
							<div class="row">
								<div class="col-5">
									<div class="row list">
										<div class="col-5">
											<div>Проектирование</div>
											<div>Строительство</div>
										</div>
										<div class="col-5">
											<div>Дизайн</div>
											<div>Ремонт</div>
										</div>
									</div>
								</div>
								<div class="col-5"></div>
								<div class="col-5"></div>
								<div class="col-5">
									<div class="row nums">
										<div class="col-5">
											<div class="num"><?= Settings::Lang('bureau_rewards_count') ?></div>
											<div class="numDesc"><?= Settings::Lang('bureau_xx_rewards') ?></div>
										</div>
										<div class="col-5">
											<div class="num"><?= Settings::Lang('bureau_projects') ?></div>
											<div class="numDesc"><?= Settings::Lang('bureau_xx_projects') ?></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="about photos">
				<div class="row">
					<div class="col-2 leftTitle"><?= Settings::Lang('bureau_xx_our_group') ?></div>
					<div class="col-8">
						<div class="rows">
							<?php
							foreach($team as $t):
								if($t->photo):
									$img = Image::findOne($t->photo);
									if(!$img)
										continue;
								?><div>
									<div style="background-image:url(<?= $img->GetUrl() ?>);">
										<div>
											<div class="fio"><?= $t->name->$lg ? $t->name->$lg : $t->name->ru ?></div>
											<div class="prof"><?= $t->work->$lg ? $t->work->$lg : $t->work->ru ?></div>
										</div>
									</div>
								</div><?php else: ?><div class="empty"></div><?php endif;
							endforeach ?>
						</div>
					</div>
				</div>
			</div>
			<div class="about rewards">
				<div class="row">
					<div class="col-2 leftTitle"><?= Settings::Lang('bureau_xx_achievements') ?></div>
					<div class="col-8">
						<?php foreach($rewards as $reward): ?>
						<div><b><?= $reward->year ?></b> — <?= $reward->desc->$lg ? $reward->desc->$lg : $reward->desc->ru ?></div>
						<?php endforeach ?>
					</div>
				</div>
			</div>
			<div class="about">
				<div class="row">
					<div class="col-2">
						
					</div>
					<div class="col-8">
						<div class="row">
							<div class="col-5"></div>
							<div class="col-5 right">
								<a class="pgBack pgBackR pgBackRed ajax" data-dir="right" href="/calculate"><span><?= Settings::Lang('bureau_xx_calculate') ?></span><img class="svg" src="/img/arrow_next.svg" alt="<?= addslashes(Settings::Lang('lang_xx_forward')) ?>"></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>