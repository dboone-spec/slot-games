<h1><?php echo $mark?></h1>
<form method="POST" enctype="multipart/form-data">
<?php if (isset($error) and count($error)>0):?>
<div style="color:red">
	<?php foreach ($error as $e):?>
		<?php echo $e?><br/>
	<?php endforeach;?>
</div>
<?php endif?>
<table class="admin-item">
<?php foreach ($show as $s):?>
<tr>
	<td><?php echo isset($label[$s]) ? $label[$s] : $s ?></td>
	<td><?php echo $vidgets[$s]->render($item,'item')?> </td>
</tr>
<?php endforeach?>

</table>

<?php echo form::submit('submit','submit')?>
</form>
<a href="<?php echo $dir?>/<?php echo $model?>"><?php echo __('Вернуться к списку'); ?></a>