<div class="row">
	<div class="col-sm-12">
		<div class="white-box">
			<h1><?php echo __($mark) ?></h1>
			<div class="row">
				<div class="col-sm-12">
					<form method="GET" class="form-horizontal">
							<div class="form-group" style="display: flex;flex: 1;flex-flow: wrap;">
							<?php foreach($search as $s): ?>
								<label class="col-md-2"><?php echo isset($label[$s]) ? $label[$s] : $s ?>:</label>
								<div class="col-md-<?php echo ($vidgets[$s] instanceof Vidget_Timestamp)?'12':'2'; ?>">
									<?php echo $vidgets[$s]->render($search_vars, 'search') ?>
								</div>
							<?php endforeach; ?>
							</div>
						<div class="form-group">
							<input class="btn btn-primary" type="submit" value="<?php echo __('Поиск') ?>" />
							<a class="btn btn-default" href="<?php echo $dir ?>/<?php echo $model ?>"><?php echo __('Очистить') ?></a>
						</div>
					</form>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12" style="overflow: scroll; max-height: 55vh;">
					<table class="table <?php echo $dir ?>" style="table-layout: fixed;">
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
                            
                            <td style="<?php echo $style; ?>">
                                <?php $sortas = $q['sortas']??'asc'; ?>
                                <a <?php if(isset($q['sortby']) && $q['sortby']==$l): ?>class="<?php echo $q['sortas']; ?>"<?php endif; ?> href="/<?php echo Request::current()->uri().'?'.http_build_query(array_merge($q,['sortby'=>$l,'sortas'=>$sortas=='asc'?'desc':'asc'])); ?> ">
                                    <?php echo isset($label[$l]) ? $label[$l] : $l ?>&nbsp;<?php if(isset($q['sortby']) && $q['sortby']==$l): ?><?php echo $q['sortas']=='asc'?'&dArr;':'&uArr;'; ?><?php endif; ?>
                                </a>
                            </td>
							<?php endforeach ?>
							<?php if(Person::user()->can_edit($model)): ?>
								<td><?php echo __('Действия'); ?></td>
							<?php endif; ?>
						</tr>
						<?php foreach($data as $c): ?>
							<tr>
								<?php foreach($list as $l): ?>
									<td style="text-overflow: ellipsis;vertical-align: middle;overflow: hidden;<?php echo ($vidgets[$l] instanceof Vidget_Slotresult)?'width: 200px':''; ?>">
										<?php if(Person::user()->can_edit($model)): ?>
                                            <?php if($model=='bonus_code'): ?>
                                                <a href="<?php echo $dir ?>/<?php echo '/bonuscode/item/' . $c->name ?>">
                                            <?php else: ?>
                                                <a href="<?php echo $dir ?>/<?php echo $model . '/item/' . $c->id ?>">
                                            <?php endif; ?>
                                                <?php echo $vidgets[$l]->render($c, 'list') ?>
                                            </a>
										<?php else: ?>
											<?php echo $vidgets[$l]->render($c, 'list') ?>
										<?php endif; ?>
									</td>
								<?php endforeach ?>
								<?php if(Person::user()->can_edit($model)): ?>
								<td style="vertical-align:middle">
									<a class="btn btn-default btn-sm" href="<?php echo $dir ?>/<?php echo $model . '/item/' . $c->id ?>"><?php echo __('Редактировать') ?></a>
									<a class="btn btn-danger btn-sm" href="<?php echo $dir ?>/<?php echo $model . '/delete/' . $c->id ?>"  onclick="return confirm('<?php echo __('Действительно удалить?') ?>')"><?php echo __('Удалить') ?></a>
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
			<?php if(Person::user()->can_edit($model)): ?>
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