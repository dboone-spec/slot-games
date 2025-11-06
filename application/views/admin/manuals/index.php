<div class="row">
<?php if(I18n::$lang=='ru'): ?>
	<div class="col-sm-12">
		<div class="white-box">
			<div class="row">
				<div class="col-sm-12">
					<table class="table table-striped" >
                                            <?php foreach($manuals as $manual_link => $manual_label): ?>
						<tr>
                                                    <td><a href="<?php echo $manual_link; ?>"><?php echo $manual_label; ?></a></td>
						</tr>
                                            <?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
	<?php if(auth::user()->office_id && th::getBit(auth::user()->my_office()->visualization, 2)): ?>
	<div class="col-sm-12">
            <div class="white-box">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-striped" >
                            <tr>
                                <td>
                                    <a href="/manuals/terminal.doc">Настройка терминала</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/robot/robot.zip">TERMINAL</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
	</div>
    <?php endif; ?>
</div>
