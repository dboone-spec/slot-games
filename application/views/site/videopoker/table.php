<div id="contentarea">
	<div class="section01">
    	<h2>Выберите стол для игры</h2>
        <div class="game_box">
			
			
			<?php $i=1; foreach($game['table'] as $num=>$table): ?>
				<a href="<?php echo "/videopoker/{$name}/$num/game" ?>">
					<div class="box0<?php echo $i?>">
						<h3><?php echo $table['name']?></h3>
						<p>Ставки от <?php echo $table['min']?> до <?php echo $table['min']*5?></p>
						<div class="container"><img alt="" src="/images/poker.jpg"></div>
					</div>
				</a>
			
			
				
			<?php $i++; if ($i>=5) { $i=1;} endforeach;?>
            <div class="clr"></div>
        </div>
    </div>
    
   
</div>

