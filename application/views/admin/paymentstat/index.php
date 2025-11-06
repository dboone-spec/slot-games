<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo $mark ?></h1>
            <div class="row">
				<div class="col-sm-12">
					<form method="GET" class="form-horizontal">
						<div class="form-group">
								<label class="col-md-2">Период:</label>
								<div class="col-md-2">с
                                    <input type="text" id="time_start" name="time_from" value="<?php echo date('Y-m-d', $time_from) ?>" >&nbsp; по
                                    <input type="text" id="time_end" name="time_to" value="<?php echo date('Y-m-d', $time_to) ?>" >
                                <script>
                                    $(function(){
                                        $("#time_start").datepicker({ dateFormat:"yy-mm-dd"});
                                        $("#time_end").datepicker({ dateFormat:"yy-mm-dd"});
                                    });
                                </script>
                            </div>
						</div>
						<div class="form-group">
							<input class="btn btn-primary" type="submit" value="Поиск" />
							<a class="btn btn-default" href="<?php echo $dir ?>/paymentstat">Очистить</a>
						</div>
					</form>
				</div>
			</div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped <?php // echo $dir   ?>" >
                        <tr>
                            <?php foreach ($headers as $header): ?>
                                <td><?php echo $header; ?></td>
                            <?php endforeach ?>
                        </tr>
                        <?php foreach ($data as $date => $value): ?>
                            <?php foreach ($value as $currency => $val): ?>
                                <?php foreach ($val as $row_type => $data_day): ?>
                                    <?php if ($row_type == 'rows'): ?>
                                        <?php foreach ($data_day as $row): ?>
                                            <tr>
                                                <?php foreach ($headers as $header => $name): ?>
                                                    <?php if (isset($row[$header])): ?>
                                                        <?php if ($header == 'payed'): ?>
                                                            <td><?php echo date('Y-m-d', $row[$header]); ?></td>
                                                        <?php else: ?>
                                                            <td><?php echo in_array($header, ['amount','total', 'payment_in', 'payment_out', 'payment_with_in', 'payment_with_out', 'total_with_comission']) ? th::number_format($row[$header]) : $row[$header]; ?></td>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endforeach ?>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php else: ?>
<!--                                        <td>Итого:</td>
                                        <td><?php echo th::number_format($data_day['total']); ?></td>
                                        <td><?php echo $currency; ?></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>Итого с учетом комиссии:</td>
                                        <td><?php echo th::number_format($data_day['total_with_comission']); ?></td>-->
                                    <?php endif; ?>
                                <?php endforeach ?>
                            <?php endforeach ?>
                        <?php endforeach ?>

                        <?php foreach ($all as $currency => $val): ?>
                            <?php foreach ($val as $type => $v): ?>
                                <tr style="font-weight: bold;">
                                    <td>Итого <?php echo $type . ' ' . $currency; ?>:</td>
                                    <td><?php echo th::number_format($v['payment_in']); ?></td>
                                    <td><?php echo th::number_format($v['payment_out']); ?></td>
                                    <td><?php echo th::number_format($v['total']); ?></td>
                                    <td><?php echo $currency; ?></td>
                                    <td>&nbsp;</td>
                                    <td>Итого <?php echo $type . ' ' . $currency; ?> с учетом комиссии:</td>
                                    <td><?php echo th::number_format($v['total_with_comission']); ?></td>
                                </tr>
                            <?php endforeach ?>
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
