<div id=<?php echo "div_{$element_id}" ?>>
<?php	if ($data) : ?>
		
		<?php foreach($data as $pic) :?>
			<div id="<?php echo "{$element_id}_divpicture_{$num}"?>">
				<img id="<?php echo "{$element_id}_img_{$num}"?>" src="<?php echo "{$imgurl}{$pic->file}"?>" /> <br>
				<input type="checkbox" id="<?php echo "{$element_id}_del_{$num}"?>" name="<?php echo "{$element_name}[del][{$num}]"?>"/>
				<label for="<?php echo "{$element_id}_del_{$num}"?>">Удалить</label><br>
				<input type="text" name="<?php echo "{$element_name}[name][{$num}]"?>" value="<?php echo "{$pic->name}"?>"/><br>
				<input type="hidden" id="<?php echo "{$element_id}_fileload_{$num}"?>" name="<?php echo "{$element_name}[file][{$num}]"?>" value="<?php echo "{$pic->file}"?>" />
			</div><br>
		<?php $num++; endforeach ?>
<?php endif ?>			


</div>
	
<input type="button" id="<?php echo "{$element_id}_new"?>" value="Добавить"> <br><br><br>

<script>
$(document).ready(function(){

var num;
num=<?php echo $num ?>;

$('#<?php echo $element_id ?>_new').click(function(){

	   num++;
		var html;
		html='<div id="<?php echo $element_id ?>_divpicture_'+num+'">';
			html+='<input type="button" id="<?php echo $element_id ?>_load_'+num+'" value="Загрузить"/>';
			html+='<input type="checkbox" id="<?php echo $element_id ?>_del_'+num+'" name="<?php echo $element_name ?>[del]['+num+']"/>';
			html+='<label for="<?php echo $element_id ?>_del_'+num+'">Удалить</label><br>';
			html+='<input type="text" name="<?php echo $element_name ?>[name]['+num+']"/><br>';
			html+='<input type="hidden" id="<?php echo $element_id ?>_fileload_'+num+'" name="<?php echo $element_name ?>[file]['+num+']" /><br>';
		
		html+='</div><br><br>';
		$('#div_<?php echo $element_id ?>').append(html);

		var local_num;
		local_num=num;
		
		
		new AjaxUpload('#<?php echo $element_id ?>_load_'+local_num, {
			action: '/my/image/<?php echo $m_name ?>',
			name: 'filedata',
			// авто submit
			autoSubmit: true,
			responseType: 'json',
			onComplete: function(file,json) {
					$('#<?php echo $element_id ?>_img_'+local_num).remove();
					$('#<?php echo $element_id ?>_divpicture_'+local_num).prepend('<img id="<?php echo $element_id ?>_img_'+local_num+'" src="<?php echo $imgurl ?>'+json.name+'"/>');
					$('#<?php echo $element_id ?>_fileload_'+local_num).val(json.name);
			  }
		  });
		
   
   });

	  
});
</script>


