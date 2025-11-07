<div class="row">
	<div class="col-sm-12">
		<div class="white-box">
			<h1><?php echo __($mark) ?></h1>
			<div class="row">
				<div class="col-sm-12">
					<form method="POST" class="form-horizontal" enctype="multipart/form-data">

						<?php if(isset($error) and count($error) > 0): ?>
							<div style="color:red">
								<?php foreach($error as $e): ?>
									<?php echo $e ?><br/>
								<?php endforeach; ?>
							</div>
						<?php endif ?>
						<div class="row">
							<div class="col-sm-12">
								<table class="table admin-item">
									<?php foreach($show as $s): ?>
										<tr>
											<td>
												<?php echo isset($label[$s]) ? $label[$s] : $s ?>
											</td>
											<td>
												<?php echo $vidgets[$s]->render($item, 'item') ?>
											</td>
										</tr>
									<?php endforeach ?>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<?php echo form::submit('submit', __('Сохранить'), array('class' => 'btn btn-success')) ?>
								<a class="btn btn-back" href="<?php echo $dir ?>/<?php echo $model ?>"><?php echo __('Вернуться к списку'); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>