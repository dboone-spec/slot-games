

<h1><?php echo $mark?></h1>

<form method="GET" >
<?php foreach ($search as $s):?>
<?php echo isset($label[$s]) ? $label[$s] : $s ?>:
<?php echo $vidgets[$s]->render($search_vars,'search')?>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php endforeach;?>
<input type="submit" value="Поиск" />
<a href="<?php echo $dir?>/<?php echo $model ?>">Очистить</a>
</form>



<form method="POST" >
	<input type="submit" value="Сохранить" />
	  
	<table class="<?php echo $dir?>" >
	<tr>
	<?php foreach ($list as $l):?>
		<td><?php echo isset($label[$l]) ? $label[$l] : $l ?></td>
	<?php endforeach?>
		<td>Действия</td>
	</tr>
	<?php foreach ($data as $c):?>
	<input name="<?php echo $c->primary_key().'['.$c->pk().']'?>" type="hidden" value="<?php echo $c->pk() ?>" />
	<tr>
		<?php foreach ($list as $l):?>
			<td><?php echo $vidgets[$l]->render($c,'listedit')  ?> </td>
		<?php endforeach?>
		
	</tr>	
	<?php endforeach?>

	</table>
	<input type="submit" value="Сохранить" />
</form>
<?php echo $page?>
