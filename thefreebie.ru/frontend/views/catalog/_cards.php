<div class="cards">
	<div class="row">
	<?php
	foreach($packs as $pack):
		$lr = ['left', 'right'];
		foreach($lr as $pos):
			if($pack[$pos]['h'] == 2):
			?>
			<div class="col-md-8 col-sm-16">
				<?= isset($pack[$pos]['childs'][0]) ? $this->render('_itemBig', ['item' => $pack[$pos]['childs'][0]]) : '' ?>
			</div>
			<?php else: ?>
				<div class="col-md-8 col-sm-16">
					<div class="row">
						<?php
						foreach($pack[$pos]['childs'] as $child): ?>
						<div class="col-xm-8 col-xs-16">
							<?= $this->render('_itemSmall', ['item' => $child]) ?>
						</div>
						<?php endforeach ?>
					</div>
				</div>
		<?	endif;
		endforeach;
	endforeach ?>
	</div>
</div>