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
					<table class="table table-striped <?php echo $dir ?>" >
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
							<?php if(Person::user()->can_edit($model)): ?>
								<td>Действия</td>
							<?php endif; ?>
                                
							<?php // foreach($list as $l): ?>
								<!--<td><?php // echo isset($label[$l]) ? $label[$l] : $l ?></td>-->
							<?php // endforeach ?>
							<!--<td>Действия</td>-->
						</tr>
						<?php foreach($data as $c): ?>
							<tr>
								<?php foreach($list as $l): ?>
									<td>
										<a href="<?php echo $dir ?>/<?php echo $model . '/item/' . $c->id ?>"><?php echo $vidgets[$l]->render($c, 'list') ?></a>
									</td>
								<?php endforeach ?>
								<td>
									<a class="btn btn-default" href="<?php echo $dir ?>/<?php echo $model . '/item/' . $c->id ?>">Просмотр</a>
                                        <?php if($c->amount < 0): ?>
                                            <?php if($c->status < PAY_APPROVED): ?>
                                                    <a class="btn btn-success" href="<?php echo $dir ?>/<?php echo $model . '/approved/' . $c->id ?>"  onclick="return confirm('Действительно поставить на выплату?')">К выплате</a>
                                            <?php endif; ?>
                                            <?php if($c->status < PAY_SUCCES) : ?>
                                                    <a class="btn btn-danger" href="<?php echo $dir ?>/<?php echo $model . '/cancel/' . $c->id ?>"  onclick="return confirm('Действительно отменить?')">Отмена</a>
                                            <?php endif; ?>
                                            <?php if($c->status <= PAY_BEGIN) : ?>
                                                    <a class="btn btn-warning" href="<?php echo $dir ?>/<?php echo $model . '/end/' . $c->id ?>"  onclick="return confirm('Действительно пометить как выплаченный?')">Пометить как выплаченный</a>
                                            <?php endif ?>
                                        <?php endif ?>
								</td>
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
			<div class="row">
				<div class="col-sm-12">
					<a class="btn btn-success" href="<?php echo $dir ?>/<?php echo $model ?>/item">Создать</a><br>
				</div>
			</div>
		</div>
	</div>
</div>
