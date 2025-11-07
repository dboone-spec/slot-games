

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




<table class="<?php echo $dir?>" >
<tr>
<?php foreach ($list as $l):?>
	<td><?php echo isset($label[$l]) ? $label[$l] : $l ?></td>
<?php endforeach?>
	<td>Действия</td>
</tr>
<?php foreach ($data as $c):?>
<tr>
	<?php foreach ($list as $l):?>
		<td><a href="<?php echo $dir?>/<?php echo $model.'/item/'.$c->id?>"><?php echo $vidgets[$l]->render($c,'list')  ?></a> </td>
	<?php endforeach?>
	<td>
		<a href="<?php echo $dir?>/<?php echo $model.'/item/'.$c->id?>">Редактировать</a>
		<a href="<?php echo $dir?>/<?php echo $model.'/delete/'.$c->id?>"  onclick="return confirm('Действительно удалить?')">Удалить</a>
	</td>
</tr>	
<?php endforeach?>

</table>
<?php echo $page?>
<a href="<?php echo $dir ?>/<?php echo $model?>/item">Создать</a><br>