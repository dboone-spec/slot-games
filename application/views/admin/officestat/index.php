<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h1><?php echo $mark ?></h1>
            <div class="row">
				<div class="col-sm-12">
					<form method="GET" class="form-horizontal">
						<div class="form-group">
                            <label class="col-md-2">Период:</label>
                            <div class="col-md-10">с
                                    <input style="width:80px" type="text" id="time_start" name="time_from" value="<?php echo date('Y-m-d', $time_from) ?>" >&nbsp;<?php echo date('H:i:s',$time_from); ?>&nbsp; по
                                    <input style="width:80px" type="text" id="time_end" name="time_to" value="<?php echo date('Y-m-d', $time_to) ?>" >&nbsp;<?php echo date('H:i:s',$time_to); ?>
                                <script>
                                    $(function(){
                                        $("#time_start").datepicker({ dateFormat:"yy-mm-dd"});
                                        $("#time_end").datepicker({ dateFormat:"yy-mm-dd"});
                                    });
                                </script>
                                <label>ППС</label>
                                <select name="office_id">
                                    <?php foreach ($offices as $office_id): ?>
                                        <option value="<?php echo $office_id ?>" <?php echo $office_id == $current_office ? 'selected' : '' ?>>№ <?php echo $office_id ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
						</div>
						<div class="form-group">
							<input class="btn btn-primary" type="submit" value="Поиск" />
							<a class="btn btn-default" href="<?php echo $dir ?>/operationstat">Очистить</a>
						</div>
					</form>
				</div>
			</div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped <?php // echo $dir   ?>" >
                        <?php foreach ($all as $currency => $val): ?>
                            <?php foreach ($val as $type => $v): ?>
                                <tr>
                                    <td>Итого</td>
                                    <td>DROP: <?php echo th::number_format($v['total_in']); ?></td>
                                    <td>HANDPAY: <?php echo th::number_format($v['total_out']); ?></td>
                                    <td>WIN: <?php echo th::number_format($v['total']); ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </table>
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
                            <?php $i=1; ?>
                            <?php foreach ($value as $currency => $val): ?>
                                <?php foreach ($val as $row_type => $data_day): ?>
                                    <?php if ($row_type == 'rows'): ?>
                                        <?php foreach ($data_day as $row): ?>
                                            <tr>
                                                <?php foreach ($headers as $header => $name): ?>
                                                    <?php if (isset($row[$header])): ?>
                                                        <?php if ($header == 'payed'): ?>
                                                            <?php if($i==1): ?>
                                                                <td  style="vertical-align: middle;" rowspan="<?php echo count($value); ?>"><?php echo date('Y-m-d', $row[$header]); ?></td>
                                                            <?php endif; ?>
                                                            <?php $i++; ?>
                                                            <?php if(count($value)>=$i): ?>
                                                                <?php continue; ?>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <?php if($header=='amount_out') $row[$header] = -1*$row[$header]; ?>
                                                            <td><?php echo in_array($header, ['amount', 'amount_in', 'amount_out']) ? th::number_format($row[$header]) : $row[$header]; ?></td>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endforeach ?>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif; ?>
                                <?php endforeach ?>
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
