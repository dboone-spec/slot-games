
<?php if (isset($error) and count($error)>0):?>
<div style="color:red">
	<?php foreach ($error as $e):?>
		<?php echo $e?><br/>
	<?php endforeach;?>
</div>
<?php endif?>
<input type="button" value="Нераспакованная жвачка" class="<?php echo $name?>new" />
<input type="button" value="Обертка" class="<?php echo $name?>new" />
<input type="button" value="Вкладыш" class="<?php echo $name?>new" />
<input type="button" value="Что-то ещё" class="<?php echo $name?>new" />
<table class="admin-item" id='<?php echo $name?>content'>
	<tr>
		<?php foreach ($show as $s):?>
			<?php if ($vidgets[$s]->show):?>
				<td><?php echo isset($label[$s]) ? $label[$s] : $s ?></td>
			<?php endif?>
		<?php endforeach?>
	</tr>

	<?php $num=1; foreach ($data as $r ): ?>
		<tr>
			<?php  foreach ($show as $s):?>
				
					<?php if ($vidgets[$s]->show):?><td><?php endif;?>
					<?php echo $vidgets[$s]->render(isset($r->$s)? $r->$s : 0,$num)?> 
					<?php if ($vidgets[$s]->show):?> </td> <?php endif;?>
				
			<?php endforeach  ?>
		</tr>
	<?php $num++;  endforeach  ?>
		
</table>



<script>
$(document).ready(function(){

var <?php echo $name?>num;
<?php echo $name?>num=<?php echo $num?>;
var <?php echo $name?>new_el;
<?php echo $name?>new_el='<?php echo $new_el?>';


$('.<?php echo $name?>new').click(function(){
		
		
		$('#<?php echo $name?>content').append(<?php echo $name?>new_el.replace(/::n::/g, <?php echo $name?>num));
		$('#<?php echo $name?>_'+<?php echo $name?>num+'_name').val(this.value)
		<?php echo $name?>num++;
		
   
   });

	  
});
</script>