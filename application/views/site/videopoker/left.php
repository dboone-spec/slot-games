<div class="cnt-promo">

	<div class="user-bar__block _status">

		<div class="user-bar__data">
			<div class="user-bar__data-title">
				Цена монеты
			</div>

		</div>
	</div>

	<?php foreach($tables as $num=>$table):?>
		<div class="user-bar__block _points <?php if ($num==$active) echo 'table_active' ?>">
			<a id="points_container_game_ui" href="<?php echo th::gamelink('cards',$name,$num) ?>" >
				<div class="user-bar__ico ico ico-balls-sm"></div>
				<div class="user-bar__data">
					<div class="hint-bottom hover-hint">
						<div class="user-bar__data-title">Монета </div>
						<div class="user-bar__data-value">
							  <?php echo $table['min'] ?> RUB
						</div>
					</div>
				</div>
			</a>
		</div>
	<?php endforeach ?>



</div>

