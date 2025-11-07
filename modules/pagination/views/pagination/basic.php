<div class="paginator">
    <div class="wrap">
        <div class="insert">
        
		
		
        
        
        
        
        <?php if ($previous_page !== FALSE): ?>
			<a class="p_prew" href="<?php echo HTML::chars($page->url($previous_page)) ?>" rel="prev"><div class="dot_left"></div></a>
		<?php else: ?>
			<div class="dot_left"></div>
		<?php endif ?>
        

        
        <?php if ($next_page !== FALSE): ?>
			<a class="p_next"  href="<?php echo HTML::chars($page->url($next_page)) ?>" rel="next"><div class="dot_right"></div></a>
		<?php else: ?>
			<div class="dot_right"></div>
		<?php endif ?>
        
        
        
			<?php for ($i = 1; $i <= $total_pages; $i++): ?>
				<?php if ($i == $current_page): ?>
					 <div class="active"><?php echo $i ?></div>
				<?php else: ?>
					<a href="<?php echo HTML::chars($page->url($i)) ?>"><?php echo $i ?></a>
				<?php endif ?>
			<?php endfor ?>        			
        			           
            <div class="clear"></div>
        </div>
    </div>




</div>