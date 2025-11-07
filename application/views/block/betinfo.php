<div class="wrapper">
    <div class="watermark"></div>
    <div class="information_container">
        <div class="title_p row-devider">
            <label id="trackingTitle" class="title_text">GAME ROUND INFORMATION</label>
        </div>
        <div>
            <table>
                <tbody>
                <tr class="row-devider2">
                    <td id="playerReferenceTitle" class="general_text cell-devider half">Player reference:</td>
                    <td id="gameRoundIdTitle" class="general_text half">Game round ID:</td>
                </tr>
                <tr class="row-devider">
                    <td id="h_externalPlayerId" class="general_data cell-devider"><?php echo $bet_model->user_id; ?></td>
                    <td id="h_externalGameRoundId" class="general_data"><?php echo $bet_model->roundNum(); ?></td>
                </tr>
                <tr class="row-devider2">
                    <td id="gameNameTitle" class="general_text cell-devider half">Game name:</td>
                    <td id="gameVersionTitle" class="general_text half">Game round status:</td>
                </tr>
                <tr class="row-devider">
                    <td class="general_data"><label id="h_gameRoundFinished"><?php echo $bet_model->gamem->visible_name; ?></label></td>
                    <td class="general_data"><label><?php echo $bet_model->isComplete()?'Complete':'Not complete'; ?></label></td>
                </tr>
                <tr class="row-devider2">
                    <td id="startTimeTitle" colspan="2" class="general_text cell-devider">Bet time:</td>
                </tr>
                <tr>
                    <td id="h_startTime" colspan="2" class="general_data cell-devider no-devider"><?php echo date('Y-m-d H:i:s',$bet_model->created); ?> UTC</td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php if($bet_model->is_freespin>0): ?>
        <div style="" id="h_freeRoundInfo">
            <div class="subtitle_p">
                <label id="freeRoundInformationTitle" class="subtitle_text">FREE ROUND INFORMATION</label>
            </div>
            <table>
                <tbody>
                <tr class="row-devider2">
                    <td id="playerFreeRoundReferenceTitle" class="general_text cell-devider half">Player free round reference:</td>
                    <td id="freeRoundTemplateNameTitle" class="general_text">Template Name:</td>
                </tr>
                <tr>
                    <td class="general_data cell-devider" id="h_freeRoundId">?</td>
                    <td class="general_data" id="h_freeRoundTemplate">Not available</td>
                </tr>
                <tr>
                    <td id="freeRoundsLeftTitle" class="general_text " colspan="2">Free rounds left:</td>
                </tr>
                <tr>
                    <td class="general_data no-devider" colspan="2" id="h_freeRoundsLeft"></td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <div class="subtitle_p">
            <label id="gamePlaySummaryTitle" class="subtitle_text">GAMEPLAY SUMMARY</label>
        </div>
        <div>
            <table>
                <tbody>
                <tr class="row-devider2">
                    <td id="accountBeforeTitle" class="general_text cell-devider third"><label>Before:</label></td>
                    <td id="accountAfterTitle" class="general_text cell-devider third">After:</td>
                    <td id="accountChangeTitle" class="general_text third">Change:</td>
                </tr>
                <tr class="row-devider">
                    <td id="h_balanceAtRoundStart" class="general_data cell-devider"><?php echo th::float_format($bet_model->balance-$bet_model->win+$bet_model->amount,$currency->mult); ?></td>
                    <td id="h_balanceAtRoundEnd" class="general_data cell-devider"><?php echo th::float_format($bet_model->balance,$currency->mult); ?></td>
                    <td id="h_balanceChanged" class="general_data"><?php echo th::float_format($bet_model->win-$bet_model->amount,$currency->mult); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div>
            <table>
                <tbody>
                <tr class="row-devider2">
                    <td id="totalBetTitle" class="general_text cell-devider"><label>Total bet:</label></td>
                    <td id="totalWinTitle" class="general_text"><label>Total win:</label></td>
                </tr>
                <tr class="row-devider">
                    <td class="general_data cell-devider"><label id="h_totalBet"><?php echo th::float_format($bet_model->amount,$currency->mult); ?></label></td>
                    <td class="general_data"><label id="h_totalWin"><?php echo th::float_format($bet_model->win,$currency->mult); ?></label></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="subtitle_p">
            <label id="gamePlayResultTitle" class="subtitle_text">GAMEPLAY RESULT</label>
        </div>
        <div class="slotresult">
            <table>
                <tbody>
                <tr class="row-devider2">
                    <td id="totalBetTitle" class="general_text cell-devider"><label>User's choice:</label></td>
                    <td id="totalWinTitle" class="general_text"><label>Result:</label></td>
                </tr>
                <tr class="row-devider">
                    <td class="general_data cell-devider"><label id="h_totalBet"><?php echo $betcome; ?></label></td>
                    <td class="general_data"><label id="h_totalWin"><?php echo $slotresult; ?></label></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    .wrapper
    {
        width: 960px;
        max-width:100%;
        margin-left: auto;
        margin-right: auto;
        padding: 10px;
        float: center;
        overflow: auto;
    }

    .information_container
    {
        background: #fff;
        border-radius: 0px;
        width: auto;
        border: 0px solid #000;
        padding-bottom: 0px;
    }

    .information_container::before {
        background: url(/theme/interactive1/img/logo.png) center center no-repeat;
        content: ' ';
    position: absolute;
    width: 100%;
    height: 100%;
    left: -15%;
    opacity: 0.6;
    bottom: -11%;
    }
    .general_data
    {
        /*background-color: #eeeeee;*/
        font-size: 15pt;
        line-height: 15pt;
        color: #7746b7;
        font-weight: lighter;
        text-align: left;
        height: 20px;
        padding: 5px;
        padding-left: 10px;
        padding-right: 10px;
        vertical-align: top;
        border-bottom: 1px solid #c0c0c0;
    }
    .general_text
    {
        font-size: 13pt;
        line-height: 13pt;
        color: #000;
        font-weight: normal;
        text-align: left;
        vertical-align: bottom;
        height: 12px;
        padding-top: 10px;
        padding-left: 10px;
        padding-right: 10px;
        border-bottom: 1px dashed #e6e6e6;
    }
    .title_p, .dropdown-title_p
    {
        /*background-color: #f1f1f1;*/
        height: 30px;
        width: auto;
        padding: 5px;
        padding-bottom: 5px;
        text-align: center;
        border-radius: 0px 0px 0px 0px;
        border-bottom: 0px solid #c0c0c0;
    }

    .title_text, .dropdown-title_text
    {
        font-family: helvetica;
        font-size: 17pt;
        line-height: 25pt;
        color: #999;
        font-weight: lighter;
        text-transform: uppercase;
        padding: 10px;
    }

    .subtitle_p, .dropdown-subtitle_p, .subtitle-2_p, .dropdown-subtitle-2_p
    {
        background-color: #f3f3f3;
        height: 30px;
        width: auto;
        padding: 5px;
        padding-bottom: 0px;
        text-align: center;
        border-top: 1px solid #c0c0c0;
        border-bottom: 1px solid #c0c0c0;
    }

    .subtitle_text, .dropdown-subtitle_text, .subtitle-2_text, .dropdown-subtitle-2_text
    {
        font-family: helvetica;
        font-size: 14pt;
        line-height: 20pt;
        color: #999;
        font-weight: normal;
        text-transform: uppercase;
    }

    .half {
        width: 50%;
    }

    .cell-devider {
        border-right: 1px solid #c0c0c0;
    }

    div:not(.slotresult) > table {
        width: 100%;
    }

    .slotresult {
        text-align: center;
    }

    .slotresult > table {
        margin: 0 auto;
    }

</style>
