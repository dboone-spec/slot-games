<div class="row">
	<div class="col-sm-12">
		<div class="white-box">
			<h1><?php echo $mark ?></h1>
			<div class="row">
				<div class="col-sm-12">
					<table class="table table-striped <?php echo $dir ?>" >
						<tr>
							<?php foreach($list as $l): ?>
								<td><?php echo isset($label[$l]) ? $label[$l] : $l ?></td>
							<?php endforeach ?>
						</tr>
						<?php foreach($data as $c): ?>
							<tr>
								<?php foreach($list as $l): ?>
									<td>
										<?php echo $vidgets[$l]->render($c, 'list') ?>
									</td>
								<?php endforeach ?>
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
		</div>
	</div>
</div>
