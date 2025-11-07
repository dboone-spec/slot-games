<link href="https://fonts.googleapis.com/css?family=Pacifico|Varela+Round" rel="stylesheet">
<style>

.jp-win {
	-webkit-justify-content: center;
	-moz-justify-content: center;
	-ms-justify-content: center;
	-o-justify-content: center;
	justify-content: center;
	-ms-align-items: center;
	align-items: center;
	display: none;
	width: 0;
	height: 0;
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	top: 0;
	-webkit-transform: scale(0);
	-ms-transform: scale(0);
	-o-transform: scale(0);
	transform: scale(1);
	background-color: rgba(0,0,0,.1);
}

.jp-win.active {
	animation-name: jp-win-appear;
	animation-duration: 1s;
	animation-iteration-count: 1;
	animation-fill-mode: forwards;
	animation-timing-function: ease-in-out;
	opacity: 0;
	display: -webkit-flex;
	display: -moz-flex;
	display: -ms-flex;
	display: -o-flex;
	-webkit-display: flex;
	-moz-display: flex;
	-ms-display: flex;
	-o-display: flex;
	display: flex;
	width: 100%;
	height: 100%;
    z-index: 9999;
}

@keyframes jp-win-appear {
	0% {
		-webkit-transform: scale(0);
		-ms-transform: scale(0);
		-o-transform: scale(0);
		transform: scale(0);
	}
	15% {
		-webkit-transform: scale(0);
		-ms-transform: scale(0);
		-o-transform: scale(0);
		transform: scale(0);
		opacity: 0;
	}
	50% {
		opacity: 0.1;
	}
	100% {
		-webkit-transform: scale(1);
		-ms-transform: scale(1);
		-o-transform: scale(1);
		transform: scale(1);
		opacity: 1;
	}
}


.jackpot-message_cover:after {
	width: 90%;
	height: 150%;
	position: absolute;
	content: '';
	background: url('/jp/img/bg-jp.png') no-repeat 50% 50%;
        background-position-y: 50%;
	-webkit-background-size: 100% 100%;
	background-size: 100% 100%;
	z-index: -2;
	left: 50%;
	/*top: 34%;*/
        top: 86%;
	-webkit-transform: translate(-50%,-50%);
	-moz-transform: translate(-50%,-50%);
	-ms-transform: translate(-50%,-50%);
	-o-transform: translate(-50%,-50%);
	transform: translate(-50%,-50%);
}

.jp-message_header {
	position: absolute;
	left: 50%;
	top: 20%;
	background-image: url('/jp/img/jp_header_ru.png');
	background-repeat: no-repeat;
	background-position: 50% 0;
	-webkit-transform: translateX(-50%);
	-ms-transform: translateX(-50%);
	-o-transform: translateX(-50%);
	transform: translateX(-50%);
	-webkit-background-size: contain;
	background-size: contain;
	width: 30%;
	height: 70%;
}

.jackpot-message_amount {
	display: block;
	color: #fff;
    text-align:center;
	font-family: 'Pacifico', cursive;
	font-size: 6.5vw;
	line-height: 1;
	text-shadow: 2px 2px 30px rgba(103,0,255,.55), -2px -2px 30px rgba(103,0,255,.55), -2px 2px 30px rgba(103,0,255,.55), 2px -2px 30px rgba(103,0,255,.55);
	position: absolute;
	left: 50%;
	bottom: 50%;
	width: 100%;
	-webkit-transform: translateX(-50%);
	-ms-transform: translateX(-50%);
	-o-transform: translateX(-50%);
	transform: translateX(-50%);
}
.jp-message_congradulations {
	position: absolute;
	bottom: 30%;
	left: 50%;
	display: -webkit-flex;
	display: -moz-flex;
	display: -ms-flex;
	display: -o-flex;
	display: flex;
	-ms-align-items: center;
	align-items: center;
	-webkit-justify-content: center;
	-moz-justify-content: center;
	-ms-justify-content: center;
	-o-justify-content: center;
	justify-content: center;
	background-image: url('/jp/img/jp-congrats.png');
	background-repeat: no-repeat;
	background-position: 50% 0;
	background-size: 64%;
	width: 50%;
	height: 18%;
	-webkit-transform: translateX(-50%);
	-ms-transform: translateX(-50%);
	-o-transform: translateX(-50%);
	transform: translateX(-50%);
	text-transform: uppercase;
}

.jp-message_congradulations span {
	color: #fff;
	font-size: 1.5vw;
	line-height: 1.3;
	text-shadow: 1px 1px 1px rgba(255,0,0,.7), -1px 1px 1px rgba(255,0,0,.7), 1px -1px 1px rgba(255,0,0,.7), -1px -1px 1px rgba(255,0,0,.7), 0 0 15px rgba(255,0,0,.7);
	font-family: 'Varela Round', sans-serif;
	letter-spacing: 2px;
}

.jp-win-anim {
	position: absolute;
	left: 50%;
	top: 50%;
	-webkit-transform: translate(-50%, -50%);
	-ms-transform: translate(-50%, -50%);
	-o-transform: translate(-50%, -50%);
	transform: translate(-50%, -50%);
	width: 100%;
	height: 100%;
	z-index: -1;
}



/*==========  Desktop First  ==========*/
/* Large Devices, Wide Screens */
@media only screen and (max-width: 1200px) {
  /**/
}

/* Medium Devices, Desktops */
@media only screen and (max-width: 992px) {
  /**/
}

/* Small Devices, Tablets */
@media only screen and (max-width: 768px) {
  /**/
}

/* Extra Small Devices, Phones */
@media only screen and (max-width: 480px) {
  /**/
}

/* Custom, iPhone Retina */
@media only screen and (max-width: 320px) {
  /**/
}

/* Height media queries*/
@media only screen and (max-height: 650px) {
  .jp-small {
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flex;
    display: -o-flex;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-justify-content: space-between;
    -moz-justify-content: space-between;
    -ms-justify-content: space-between;
    -o-justify-content: space-between;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -webkit-flex-direction: column;
    -moz-flex-direction: column;
    -ms-flex-direction: column;
    -o-flex-direction: column;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    flex-direction: column;
  }
  .jp-small .jp-content {
    max-width: 600px;
  }
  .jp-small .jp-title .jp-title_text {
    font-size: 1.3vw;
  }
  .jp-small .jp-title .jp-title_element {
    font-size: 0.7vw;
  }
  .jp-small .jp-counter_list li {
    font-size: 4.82vw;
  }
  .jp-small .jp-wager_title {
    font-size: 0.42vw;
  }
}
</style>

<div class="jp-win" id="jp-win">
    <div class="jackpot-message_cover">
        <div class="jp-message_header"></div>
        <div class="jackpot-message_amount" id="jackpot-message_amount">10 000 000</div>
        <div class="jp-message_congradulations">
            <span><?php echo __('Поздравляем, счастливчик!'); ?></span>
        </div>
        <div class="jp-win-anim border-animation">
            <div style="width: 100%; height: 100%;" class="border-animation__inner">
                <canvas width="1340" height="450" style="width: 100%; height: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>
<div id="music"></div>