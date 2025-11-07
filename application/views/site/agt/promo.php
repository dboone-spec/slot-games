<script>
    //не работает, если вызвано через window.open. но работает, если запущено через айфрейм
    function closeAGTPopup() {
        var someIframe = window.parent.document.getElementById('iframe-agt-promopopup');
        someIframe.parentNode.removeChild(someIframe);
    }
</script>
<div class="closebtn-block">
    <a href="javascript:typeof closeAGTPopup=='function'?closeAGTPopup():window.close()">X</a>
</div>
<?php if(empty($events)): ?>
    Sorry, no promo available now
    <?php exit; ?>
<?php endif; ?>
<div class="container" style="background: #fff;">
    <div class="row">
        <div class="header" data-text="Lucky spins">
            <?php if($events[0]->dow=='5'): ?>
            <?php echo __('promo_black_friday'); ?>
            <?php else: ?>
            <?php echo __('promo_title'); ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <img style="max-width: 100vw;max-height: 50vh;" src="/games/agt/images/banners/promo2.png" data-caption="" />
        <span class="over">
            <h3>
			<?php if($events[0]->dow=='5'): ?>
				<?php echo __('promo_black_friday'); ?>
			<?php else: ?>
				<?php echo __('promo_title'); ?>
			<?php endif; ?>
			</h3>
            <?php echo __('promo_maxrefund'); ?>: <?php echo th::number_format($events[0]->max_payout); ?> <?php echo $events[0]->office->currency->code; ?>
            <br />
            <?php echo __('promo_start'); ?>
            <?php echo date('H:i',$events[0]->startTime()+$events[0]->office->currency->getTimeZoneSeconds()); ?>,
            <br />
            <?php echo strtolower(__('promo_end')); ?>
            <?php echo date('H:i',$events[0]->startTime()+$events[0]->duration+$events[0]->office->currency->getTimeZoneSeconds()); ?>.
            <br />
            (<?php echo __('timezone'); ?> <?php echo $events[0]->office->currency->timezone; ?>)
            <br />
            <span style="font-weight:bold;">
            <?php echo __('Every '.$events[0]->getDowName()); ?>
            </span>
            <br />
            <?php echo date('d.m.Y',$events[0]->starts); ?>-<?php echo date('d.m.Y',$events[0]->ends); ?>.
			<div id="promocountdown" class="timerblock" data-starttime="<?php echo $events[0]->startTime(); ?>" data-duration="<?php echo $events[0]->duration; ?>" data-collecttime="<?php echo $events[0]->time_to_collect; ?>">
                12:12
            </div>
        </span>
    </div>
    <div class="row">
        <div class="rect">
            <div class="rules">
                <!--<?php echo __('promo_maxrefund'); ?>: <?php echo th::number_format($events[0]->max_payout); ?> <?php echo $events[0]->office->currency->code; ?>
                <br />
                <?php echo __('promo_start'); ?>
                <?php echo date('H:i',$events[0]->startTime()+$events[0]->office->currency->getTimeZoneSeconds()); ?>,
                <?php echo strtolower(__('promo_end')); ?>
                <?php echo date('H:i',$events[0]->startTime()+$events[0]->duration+$events[0]->office->currency->getTimeZoneSeconds()); ?>.
                (UTC+3)
                <br />
                <?php echo __('promo_everyday'); ?>
                <?php echo date('d.m.Y',$events[0]->starts); ?>
                -
                <?php echo date('d.m.Y',$events[0]->ends); ?>.
                <br />-->
                <?php echo str_replace([':date1',':date2'],[date('H:i',$events[0]->startTime()+$events[0]->duration+10*60+$events[0]->office->currency->getTimeZoneSeconds()),date('H:i',$events[0]->startTime()+$events[0]->duration+$events[0]->time_to_collect-1+$events[0]->office->currency->getTimeZoneSeconds())],__('promo_rule')); ?>
            </div>
        </div>
    </div>
</div>
<style>
    :root {
        --shadow-color: white;
        --shadow-color-light: white;
    }

    .closebtn-block a {
        text-decoration: none;
        color: #fff;
    }

    span.red {
        width: 100%;
        color: #e12828;
        font-size: 8vw;
    }

    .over {
        color: maroon;
        position: absolute;
        margin-top: 54px;
        text-align: center;
        font-size: min(2vw,100%);
    }
	
	.timerblock {
        font-weight: bold;
    }

    .closebtn-block {
        position: absolute;
        color: #fff;
        font-size: 50px;
        top: 10px;
        right: 10px;
        border: 2px solid;
        border-radius: 43px;
        padding: 10px 24px;
        font-weight: bold;
        background: rgba(200,200,200,0.4);
        font-family: sans-serif;
    }

    .container {
        display: flex;
        width: 100%;
        margin: 0 auto;
        /*max-width: 525px;*/
        align-content: center;
        flex-wrap: wrap;
        min-height: 100vh;
        flex-direction: column;

        font-family: 'Montserrat', sans-serif;
        /*justify-content: space-around;*/
    }

    .row {
        width: 100%;
        text-align: center;
        display: flex;
        place-content: center;
    }

    .rect {
        color: #494949;
        border: 2px solid #0E0B93;
        border-radius: 25px;
        padding: 4% 3%;
        margin: 1% 4%;
    }

    .rect:last-child {
        overflow: hidden;
    }

    .header {
        font-size: 2em;
        color: #0E0B93;
        font-weight: bold;
        margin-top: 5%;
    }

    .lighttext {
        background: -webkit-linear-gradient(-176deg, #f83600,#facc22);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;

        font-size: 72pt;
        -webkit-text-stroke-width: 1px;
        -webkit-text-stroke-color: white;


        font-size: 9vw;
        width: fit-content;

        font-weight: 800;
        filter: drop-shadow(0px 0px 1px #52fef4) drop-shadow(-1px -1px 10px #52fef4) drop-shadow(1px 1px 4px #52fef4);
    }

    .rules,.gamesblock-title {
        text-align: center;
        font-size: 2vw;
        display: flex;
        place-content: center;
    }

    .gamesblock-title {
        color: #0E0B93;
        white-space: nowrap;
        font-size: 4vw;
        margin-bottom: 3%;
    }

    .gain-block {
        background: #64d09f;
        border-radius: 4vw;
        font-size: 2vw;
        width: 9.6vw;
        display: flex;
        flex-direction: column;
        margin: 1vw;
        color: #0d0e38;
    }

    .gamethumb {
        width: 25vw;
    }

    .gain-block-header {
        padding: 3vw 0;
        text-align: center;
    }

    .gain-block.gain-now {
        background: #fffb00;
    }


    .gain-block.gain-next {
        background: #9dfb38;
    }

    .gain-block-footer {
        padding: 3vw 2vw;
        text-align: center;
    }

    .gain-block-treasure-container {
        background: #0d0e38;
        width: 8vw;
        height: 8vw;
        align-self: center;
        border-radius: 5vw;
    }

    .gain-block span {
        display: none;
    }

    .gain-block.gained span {
        display: block;
    }

    .gain-block span {
        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) -958px -335px;
        width: 24px;
        height: 24px;
        margin: 0 auto;
        margin-top: -2.2vw;
    }

    .treasure {
        width: 100%;
        height: 100%;
    }

    .gain-container > .row {
        padding-top: 2%;
        display: flex;
        align-items: center;
    }

    .bg-tresure1 {
        /*width: 50px; height: 50px;*/
        /*background: url('/games/agt/images/common/ui/lssprite.png') -678px -335px;*/

        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) 70% 56.5%;
        background-size: 1787%;
    }

    .bg-tresure3 {
        /*width: 50px; height: 50px;*/
        /*background: url('/games/agt/images/common/ui/lssprite.png') -818px -335px;*/

        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) 84.5% 56.5%;
        background-size: 1787%;
    }

    .bg-tresure2 {
        /*width: 50px; height: 50px;*/
        /*background: url('/games/agt/images/common/ui/lssprite.png') -748px -335px;*/

        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) 77.3% 56.5%;
        background-size: 1787%;
    }

    .bg-tresure4 {
        /*width: 50px; height: 50px;*/
        /*background: url('/games/agt/images/common/ui/lssprite.png') -888px -335px;*/

        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) 91.8% 56.5%;
        background-size: 1787%;
    }

    .bg-tresure5 {
        /*width: 59px; height: 57px;*/
        /*background: url('/games/agt/images/common/ui/lssprite.png') -520px -335px;*/

        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) 54.1% 57.6%;
        background-size: 1787%;
    }

    .bg-tresure6 {
        /*width: 59px; height: 57px;*/
        /*background: url('/games/agt/images/common/ui/lssprite.png') -599px -335px;*/

        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) 62.5% 57.6%;
        background-size: 1787%;
    }

    .bg-tresure7 {
        /*width: 57px; height: 59px;*/
        /*background: url('/games/agt/images/common/ui/lssprite.png') -506px -473px;*/

        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) 91.9% 56.6%;
        background-size: 1787%;
    }

    .bg-bigrect {
        /*width: 490px; height: 443px;*/
        /*background: url('/games/agt/images/common/ui/lssprite.png') -10px -10px;*/

        width: 100%;
        height: 0;
        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) 1.4% 3%;
        background-size: 207%;
        padding-bottom: 92%;
    }

    .bg-middlerect {
        /*width: 490px; height: 305px;*/
        /*background: url('/games/agt/images/common/ui/lssprite.png') -520px -10px;*/

        width: 100%;
        height: 0;
        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) 98% 3%;
        background-size: 207%;
        padding-bottom: 62%;
    }

    .bg-smallrect {
        /*width: 476px; height: 158px;*/
        /*background: url('/games/agt/images/common/ui/lssprite.png') -10px -473px;*/

        width: 100%;
        height: 0;
        background: url(https:<?php echo URL::site('/games/agt/images/common/ui/lssprite.png','https'); ?>) 0 100%;
        background-size: 207%;
        padding-bottom: 34%;
        /*margin-top: 15%;*/
    }

    .slidewrapper {
        overflow-x: scroll;
        overflow-y: hidden;
        width: 100%;
        display: flex;
    }

    .onegame-block {
        width: 33%;
        margin: 0 1%;
        margin-bottom: 3%;
        cursor: pointer;
    }

    .countdown {
        color: #fff;
        font-size: 3.6vw;
        background: #0E0B93;
        width: 86%;
        margin: 0 auto;
        margin-top: 5%;
        border-radius: 3vw;
        position: relative;
        height: 4.4vw;
    }
    .countdown-current {
        width: attr(data-width);
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        z-index: 1;
    }
    .countdown-current-text {
        position: absolute;
        height: 50px;
        top: 0;
        width: 100%;
        z-index: 2;
        left: 0;
    }

    body,html {
        margin: 0;
        padding: 0;
    }

    /* Shine */
    /*.gain-now .treasure {
        position: relative;
    }
    .gain-now .treasure:after {
        content:'';
        top:0;
        transform:translateX(100%);
        width:25%;
        height:100%;
        position: absolute;
        z-index:1;
        animation: slide 1s infinite;
        background: -moz-linear-gradient(left, rgba(255,255,255,0) 0%, rgba(255,255,255,0.8) 50%, rgba(128,186,232,0) 99%, rgba(125,185,232,0) 100%);
        background: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(255,255,255,0)), color-stop(50%,rgba(255,255,255,0.8)), color-stop(99%,rgba(128,186,232,0)), color-stop(100%,rgba(125,185,232,0)));
        background: -webkit-linear-gradient(left, rgba(255,255,255,0) 0%,rgba(255,255,255,0.8) 50%,rgba(128,186,232,0) 99%,rgba(125,185,232,0) 100%);
        background: -o-linear-gradient(left, rgba(255,255,255,0) 0%,rgba(255,255,255,0.8) 50%,rgba(128,186,232,0) 99%,rgba(125,185,232,0) 100%);
        background: -ms-linear-gradient(left, rgba(255,255,255,0) 0%,rgba(255,255,255,0.8) 50%,rgba(128,186,232,0) 99%,rgba(125,185,232,0) 100%);
        background: linear-gradient(to right, rgba(255,255,255,0) 0%,rgba(255,255,255,0.8) 50%,rgba(128,186,232,0) 99%,rgba(125,185,232,0) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00ffffff', endColorstr='#007db9e8',GradientType=1 );
    }

    @keyframes slide {
        0% {transform:translateX(-200%);}
        100% {transform:translateX(100%);}
    }*/

</style>
