<div class="row">
	<div class="col-sm-12">
		<div class="white-box">
			<h1><?php echo $mark ?></h1>
			<div class="row">
				<div class="col-sm-12">
					<form method="GET" class="form-horizontal">
							<div class="form-group">
							<?php foreach($search as $s): ?>
								<label class="col-md-2"><?php echo isset($label[$s]) ? $label[$s] : $s ?>:</label>
								<div class="col-md-2">
									<?php echo $vidgets[$s]->render($search_vars, 'search') ?>
								</div>
							<?php endforeach; ?>
							</div>
						<div class="form-group">
							<input class="btn btn-primary" type="submit" value="Поиск" />
							<a class="btn btn-default" href="<?php echo $dir ?>/<?php echo $model ?>">Очистить</a>
						</div>
					</form>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<table class="table <?php echo $dir ?>" >
						<tr>
                            <?php $q = Request::current()->query(); ?>
							<?php foreach($list as $l): ?>
                            <td>
                                <?php $sortas = $q['sortas']??'asc'; ?>
                                <a <?php if(isset($q['sortby']) && $q['sortby']==$l): ?>class="<?php echo $q['sortas']; ?>"<?php endif; ?> href="/<?php echo Request::current()->uri().'?'.http_build_query(array_merge($q,['sortby'=>$l,'sortas'=>$sortas=='asc'?'desc':'asc'])); ?> ">
                                    <?php echo isset($label[$l]) ? $label[$l] : $l ?>&nbsp;<?php if(isset($q['sortby']) && $q['sortby']==$l): ?><?php echo $q['sortas']=='asc'?'&dArr;':'&uArr;'; ?><?php endif; ?>
                                </a>
                            </td>
							<?php endforeach ?>
						</tr>
						<?php foreach($data as $c): ?>
							<tr>
								<?php foreach($list as $l): ?>
									<td style="vertical-align:middle">
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
							</tr>
						<?php endforeach ?>
                        <?php  if(isset($total_row)): ?>
                            <?php foreach ($total_row as $name => $v): ?>
                                <tr>
                                    <?php foreach ($list as $l): ?>
                                        <?php if(isset($v[$l])): ?>
                                            <td><?php echo $v[$l] ?></td>
                                        <?php else: ?>
                                            <td><?php echo $l=='date'?'Итого: ':'' ?></td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>