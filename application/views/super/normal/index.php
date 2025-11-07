<div class="row">
	<div class="col-sm-12">
		<div class="white-box">
			<h1><?php echo __($mark) ?></h1>
			<div class="row">
				<div class="col-sm-12">
					<form method="GET" class="form-horizontal">
                                                <form method="GET" class="form-horizontal">
                                                <div >
                                                    <table> 
                                                        <tr>
                                                            <?php foreach($search as $s): ?>
                                                            <td style="width:100px">
                                                                <label><?php echo  $label[$s] ?? $s ?></label>
                                                            </td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                         <tr>
                                                           
                                                            <?php foreach($search as $s): ?>
                                                            <td>
                                                                <?php echo $vidgets[$s]->render($search_vars, 'search') ?>
                                                            </td>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </table>   
                                                    <br>

                                                </div>
                                            
                                            
                     
						<div class="form-group">
							<input class="btn btn-primary" type="submit" value="<?php echo __('Поиск') ?>" />
							<a class="btn btn-default" href="<?php echo $dir ?>/<?php echo $model ?>"><?php echo __('Очистить') ?></a>
						</div>
					</form>
				</div>
			</div>
                        <div class="row">
				<div class="col-sm-12">
				<?php echo $page ?>
				</div>
			</div>
                        
			<div class="row">
				<div style="overflow-x: scroll; ">
					<table class="table <?php echo $dir ?>" >
						<tr>
                            <?php $q = Request::current()->query(); ?>
							<?php foreach($list as $l): ?>
                            
                            <?php 
                            
                            $class = get_class($vidgets[$l]);
                            $style='';
                            switch($class) {  
                                case Vidget_Slotresult::class:
                                    $style = "width: 200px;";
                                    break;
                            }
                            if($l=='game') {
                                $style='width: 110px;';
                            }
                            
                            ?>
                            
                            <td style="">
                                <?php if (!in_array($l,$notSortable)): ?>
                                    <?php $sortas = $q['sortas']??'asc'; ?>
                                    <a <?php if(isset($q['sortby']) && $q['sortby']==$l): ?>class="<?php echo $q['sortas']; ?>"<?php endif; ?> href="/<?php echo Request::current()->uri().'?'.http_build_query(array_merge($q,['sortby'=>$l,'sortas'=>$sortas=='asc'?'desc':'asc'])); ?> ">
                                        <?php echo isset($label[$l]) ? $label[$l] : $l ?>&nbsp;<?php if(isset($q['sortby']) && $q['sortby']==$l): ?><?php echo $q['sortas']=='asc'?'&dArr;':'&uArr;'; ?><?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo isset($label[$l]) ? $label[$l] : $l ?>&nbsp;<?php if(isset($q['sortby']) && $q['sortby']==$l): ?><?php echo $q['sortas']=='asc'?'&dArr;':'&uArr;'; ?><?php endif; ?>
                                <?php endif ?>
                            </td>
							<?php endforeach ?>
							<?php if(Person::user()->can_edit($model)): ?>
								<td><?php echo __('Действия'); ?></td>
							<?php endif; ?>
						</tr>
						<?php foreach($data as $c): ?>
							<tr>
								<?php foreach($list as $l): ?>
									<td style="text-overflow: ellipsis;vertical-align: middle;overflow: hidden; ">
                                                                            <?php if($canItem):?>
                                                                                <a href="<?php echo $dir ?>/<?php echo $model . '/item/' . $c->id ?>">
                                                                                    <?php echo $vidgets[$l]->render($c, 'list') ?>
                                                                                </a>
                                                                            <?php else: ?>
                                                                                <?php echo $vidgets[$l]->render($c, 'list') ?>
                                                                            <?php endif ?>
									</td>
								<?php endforeach ?>
								<?php if(Person::user()->can_edit($model)): ?>
								<td style="vertical-align:middle">
                                                                        <?php if ($canEdit):?>
                                                                            <a class="btn btn-default btn-sm" href="<?php echo $dir ?>/<?php echo $model . '/item/' . $c->id ?>"><?php echo __('Редактировать') ?></a>
                                                                        <?php endif; ?>
                                                                        <?php if ($canDelete):?>
                                                                            <a class="btn btn-danger btn-sm" href="<?php echo $dir ?>/<?php echo $model . '/delete/' . $c->id ?>"  onclick="return confirm('<?php echo __('Действительно удалить?') ?>')"><?php echo __('Удалить') ?></a>
                                                                        <?php endif; ?>
								</td>
								<?php endif; ?>
							</tr>
						<?php endforeach ?>

					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
				<?php echo $page ?>
				</div>
			</div>
			<?php if($canCreate): ?>
			<div class="row">
				<div class="col-sm-12">
					<a class="btn btn-success" href="<?php echo $dir ?>/<?php echo $model ?>/item"><?php echo __('Создать') ?></a><br>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
        $('.calcable[name=sum_in]').each(function() {
            if(parseFloat($(this).text())==0) {
                $(this).parent().parent().parent().find('a').css('color','green');
            }
        });
    });
</script>