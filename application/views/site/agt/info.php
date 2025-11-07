<style>
    body {
        background: grey;
        color: white;
        text-align: center;

        margin: 0 auto;
        position: relative;
        font-size: 16pt;

        font-family: 'Montserrat', sans-serif;
    }
    h2 b {
        color: #00AC1C;
    }

    .container {
        max-width: 1100px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 10px;
        padding-right: 10px;
        padding-top: 40px;
    }
    h2 {
        font-size: 7vw;
        margin: 20px 0;
        text-align: center;
    }
    h2 small {
        font-size: 0.5em;
    }
    .responsive-table {
        color: black;
        padding: 0;
    }
    .responsive-table li {
        border-radius: 3px;
        padding: 25px 30px;
        display: flex;
        justify-content: space-between;
        margin-bottom: 25px;
    }
    .responsive-table .table-header {
        background-color: #95a5a6;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .responsive-table .table-row {
        background-color: #fff;
        box-shadow: 0px 0px 9px 0px rgba(0, 0, 0, 0.1);
    }
    .responsive-table .col-1 {
        flex-basis: 35%;
    }
    .responsive-table .col-2 {
        flex-basis: 40%;
    }
    .responsive-table .col-3 {
        flex-basis: 25%;
    }
    @media all and (max-width: 767px) {

        .responsive-table .table-header {
            display: none;
        }
        .responsive-table li {
            display: block;
        }
        .responsive-table .col {
            flex-basis: 100%;
        }
        .responsive-table .col {
            display: flex;
            padding: 10px 0;
        }
        .responsive-table .col:before {
            color: #6c7a89;
            padding-right: 10px;
            content: attr(data-label);
            flex-basis: 45%;
            text-align: right;
        }
    }


    * {
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

body {
  background: #ffffff;
    color: #494949;
}

/*table {
  border-collapse: separate;
  background: #fff;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
  margin: 20px auto;
  -moz-box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
  -webkit-box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
  box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
}

thead {
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
}

thead th {
  font-size: 16px;
  font-weight: 400;
  color: #fff;
  text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.5);
  text-align: left;
  padding: 20px;
  background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzY0NmY3ZiIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzRhNTU2NCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');
  background-size: 100%;
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #646f7f), color-stop(100%, #4a5564));
  background-image: -moz-linear-gradient(#646f7f, #4a5564);
  background-image: -webkit-linear-gradient(#646f7f, #4a5564);
  background-image: linear-gradient(#646f7f, #4a5564);
  border-top: 1px solid #858d99;
}
thead th:first-child {
  -moz-border-radius-topleft: 5px;
  -webkit-border-top-left-radius: 5px;
  border-top-left-radius: 5px;
}
thead th:last-child {
  -moz-border-radius-topright: 5px;
  -webkit-border-top-right-radius: 5px;
  border-top-right-radius: 5px;
}

tbody tr td {
  font-family: 'Open Sans', sans-serif;
  font-weight: 400;
  color: #5f6062;
  font-size: 13px;
  padding: 20px 20px 20px 20px;
  border-bottom: 1px solid #e0e0e0;
}

tbody tr:nth-child(2n) {
  background: #f0f3f5;
}

tbody tr:last-child td {
  border-bottom: none;
}
tbody tr:last-child td:first-child {
  -moz-border-radius-bottomleft: 5px;
  -webkit-border-bottom-left-radius: 5px;
  border-bottom-left-radius: 5px;
}
tbody tr:last-child td:last-child {
  -moz-border-radius-bottomright: 5px;
  -webkit-border-bottom-right-radius: 5px;
  border-bottom-right-radius: 5px;
}*/
</style>

<style>
    h2 {
        color: #0E0B93;
    }
    h4 {
        font-size: 11pt;
    }
    h4 b{
        background: #00AC1C;
        color: #ffffff;
        padding: 3pt 6pt;
        border-radius: 25px;
    }
    .acloud {
        width: calc(100% - 20px);
        margin: 10px;
        border: 1px #454545 solid;
        border-radius: 30px;
        padding: 1.4em;
        font-size: 0.9em;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin: 20px auto;
        font-size: 10pt;
    }
    table th {
        background-color: #5CB1FF;
        color: #fff;
        padding: 10pt;
        text-align: left;
    }
    thead th:first-child {
        -moz-border-radius-topleft: 10px;
        -webkit-border-top-left-radius: 10px;
        border-top-left-radius: 10px;
    }
    thead th:last-child {
        -moz-border-radius-topright: 10px;
        -webkit-border-top-right-radius: 10px;
        border-top-right-radius: 10px;
    }
    table td {
        background-color: #fff;
        color: #777777;
        padding: 7.6pt;
        white-space: nowrap;
    }
    .coefhist tbody tr:last-child td:last-child inn {
        background: #FFCC00;
        color: #000;
    }
    .coefhist tbody td:last-child inn {
        padding: 3pt 6pt;
        border-radius: 6pt;
    }
    .row {
        overflow-x: overlay;
        width: 100%;
        margin: 0 auto;
    }
    .small {
        font-size: 0.5em;
    }

    .closebtn-block a {
        text-decoration: none;
        color: #0E0B93;
    }

    .closebtn-block {
        position: absolute;
        color: #0E0B93;
        font-size: 7vw;
        top: 10px;
        right: 10px;
        border: 2px solid;
        border-radius: 43px;
        padding: 10px 24px;
        font-weight: bold;
        background: rgba(14, 11, 147,0.4);
        font-family: sans-serif;
    }
</style>
<script>
    //не работает, если вызвано через window.open. но работает, если запущено через айфрейм
    function closeAGTPopup() {
        console.log(window,window.parent,document);

        var someIframe = window.parent.document.getElementById('iframe-agt-dspopup');
        someIframe.parentNode.removeChild(someIframe);

        // parent.closeIFrame();
    }
</script>
<div class="closebtn-block">
    <a href="javascript:typeof closeAGTPopup=='function'?closeAGTPopup():window.close()">X</a>
</div>
<div class="container">
    <h2><?php echo __('Play every day and get Daily Spins (DS)'); ?>*</h2>
    <h4><?php echo __('Your current Daily Spins cashback coefficient is'); ?> <b><?php echo $bonus_coeff * 100; ?>%</b></h4>
    <div class="acloud">
    <?php echo __('Play and get +0.5% for every day up to 10%'); ?>!
    <?php if(!empty($u->last_bet_time)): ?>
        <?php if($sum_all > 0): ?>
            <?php echo sprintf(__('You made bets for %d credits'),$sum_all); ?>.
            <br />
        <?php endif; ?>
        <?php echo __('To recieve DS cashback you should go to any game'); ?>.
        <br />
        <?php if($time > time()): ?>
            &nbsp;&nbsp;&nbsp;Next Daily Spins: <?php echo date('y/m/d H:i:s',$time + $u->office->zone_time * 60 * 60) ?>
            <br />
        <?php else: ?>

        <?php endif; ?>
        &nbsp;&nbsp;&nbsp;
        <?php echo __('Last bet time'); ?>: <?php echo date('y/m/d H:i:s',$u->last_bet_time + $u->office->zone_time * 60 * 60); ?>
        &nbsp;&nbsp;&nbsp;
        ID: <?php echo auth::$user_id; ?>
        <br />
    <?php else: ?>
        <?php echo __('Make your first bet to start receive Daily Spins cashback!'); ?>
        <br />
    <?php endif; ?>
    </div>
    <div class="acloud">
    <?php echo __('Last update'); ?>: <?php echo date('y/m/d H:i:s',time() + $u->office->zone_time * 60 * 60); ?>
    <?php echo __('Next update'); ?>: <?php echo date('y/m/d H:i:s',$timenext + $u->office->zone_time * 60 * 60); ?>
    </div>
    <?php if(count($history)): ?>
    <table class="coefhist">
        <thead>
            <tr>
                <th><?php echo __('No.'); ?></th>
                <th><?php echo __('Date'); ?></th>
                <th><?php echo __('Change'); ?></th>
                <th><?php echo __('Value'); ?></th>
            <tr>
        </thead>
        <tbody>
        <?php foreach($history as $i=>$h): ?>
            <tr class="table-row">
                <td><?php echo ($i+1);?></td>
                <td><?php echo date('y/m/d',$h->created + $u->office->zone_time * 60 * 60); ?></td>
                <td style="font-weight: bold; color: <?php echo $h->change>0?'green':'red'; ?>"><?php echo $h->change>0?'+':''; ?><?php echo $h->change * 100; ?>%</td>
                <td><inn><?php echo $h->coef * 100; ?>%</inn></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

        <br />

        <h2>
            <?php echo __('AWARDED / DECLINED DAILY SPINS HISTORY'); ?>
        </h2>
        <div class="row">
        <table id="fshistory">
            <thead>
                <tr>
                    <th><?php echo __('Time left'); ?></th>
                    <th><?php echo __('Status'); ?></th>
                    <th><?php echo __('Type'); ?></th>
                    <th><?php echo __('In'); ?></th>
                    <th><?php echo __('Out'); ?></th>
                    <th><?php echo __('Coef.'); ?></th>
                    <th><?php echo __('Balance'); ?></th>
                    <th><?php echo __('Game'); ?></th>
                    <th>DS</th>
                    <th><?php echo __('Bet'); ?></th>
                    <th><?php echo __('Lines'); ?></th>
                    <th><?php echo __('Expiration'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($current_fs as $cur_fs_one): ?>
                    <?php if($cur_fs_one->src=='cashback' && $cur_fs_one->fs_count>$cur_fs_one->fs_played): ?>

                        <?php $infolog=$cur_fs_one->log[2]; ?>

                        <?php if(isset($infolog->ds_info)): ?>
                            <?php $infolog=reset($infolog->ds_info); ?>
                        <?php endif; ?>

                        <tr>
                            <td><?php echo date('y/m/d H:i:s',$cur_fs_one->created + $u->office->zone_time * 60 * 60); ?></td>
                            <td><?php echo ucfirst($cur_fs_one->status()); ?></td>
                            <td>DS</td>
                            <td><?php echo th::float_format($infolog->in,$mult); ?></td>
                            <td><?php echo th::float_format($infolog->out,$mult); ?></td>
                            <td><?php echo $cur_fs_one->log[1]*100; ?>%</td>
                            <td><?php echo th::float_format($cur_fs_one->log[1]*$cur_fs_one->log[0],$mult); ?></td>
                            <td><?php echo $cur_fs_one->game->visible_name; ?></td>
                            <td><?php echo $cur_fs_one->fs_played; ?>/<?php echo $cur_fs_one->fs_count; ?></td>
                            <td><?php echo th::float_format($cur_fs_one->amount,$mult); ?></td>
                            <td><?php echo $cur_fs_one->lines; ?></td>
                            <?php $elapsed=(10*60+$cur_fs_one->created-time())/60; ?>
                            <?php if($cur_fs_one->active==0 && $elapsed>0): ?>
                                <td>
                                    <?php echo date('y/m/d H:i:s',$cur_fs_one->created+10*60 + $u->office->zone_time * 60 * 60); ?>
                                </td>
                            <?php else: ?>
                                <td><?php echo date('y/m/d H:i:s',$cur_fs_one->expirtime + $u->office->zone_time * 60 * 60); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td><?php echo date('y/m/d H:i:s',$cur_fs_one->created + $u->office->zone_time * 60 * 60); ?></td>
                            <td><?php echo ucfirst($cur_fs_one->status()); ?></td>
                            <td><?php echo !empty($cur_fs_one->type)?$cur_fs_one->type:($cur_fs_one->src=='cashback'?'DS':'Promo'); ?></td>
                            <?php if($cur_fs_one->src=='cashback' && $cur_fs_one->active==1): ?>

                                <?php $infolog=$cur_fs_one->log[2]; ?>

                                <?php if(isset($infolog->ds_info)): ?>
                                    <?php $infolog=reset($infolog->ds_info); ?>
                                <?php endif; ?>

                                <td><?php echo th::float_format($infolog->in,$mult); ?></td>
                                <td><?php echo th::float_format($infolog->out,$mult); ?></td>
                                <td><?php echo $cur_fs_one->log[1]*100; ?>%</td>
                                <td><?php echo th::float_format($cur_fs_one->log[1]*$cur_fs_one->log[0],$mult); ?></td>
                            <?php else: ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php endif; ?>
                            <td><?php echo $cur_fs_one->game->visible_name; ?></td>
                            <td><?php echo $cur_fs_one->fs_played; ?>/<?php echo $cur_fs_one->fs_count; ?></td>
                            <td><?php echo th::float_format($cur_fs_one->amount,$mult); ?></td>
                            <td><?php echo $cur_fs_one->lines; ?></td>
                            <td><?php echo date('y/m/d H:i:s',$cur_fs_one->expirtime + $u->office->zone_time * 60 * 60); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                <tr>
                    <td><?php echo date('y/m/d H:i:s',$next_fs->created + $u->office->zone_time * 60 * 60); ?></td>
                    <td>Forecast</td>
                    <td>DS</td>
                    <td><?php echo $next_fs->in??0; ?></td>
                    <td><?php echo $next_fs->out??0; ?></td>
                    <td><?php echo 100*($next_fs->coeff??0); ?>%</td>
                    <td><?php echo $next_fs->sumfsback??0; ?></td>
                    <td><?php echo $next_fs->visible_name??'-'; ?></td>
                    <td><?php echo $next_fs->fs_count??'-'; ?></td>
                    <td><?php echo $next_fs->amount??'-'; ?></td>
                    <td><?php echo $next_fs->lines??'-'; ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        </div>
        <div class="acloud small">
            *<?php echo __('fs_rules'); ?>
        </div>
</div>