<div class="jackpotsblock" id="jackpotsblock" style="display: none;">
<?php foreach ($jackpots as $jp): ?>
    <div class="deal-item rectangle <?php if ($jp->hot()): ?>hot<?php endif; ?>">
        <div class="jp-item-wrap">
            <div class="jp-item-label">JP<?php echo $jp->type; ?></div>
            <div class="jp-item-val" jptype="<?php echo $jp->type; ?>">
                <?php echo number_format($jp->current, 2, '.', ' ') ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<style>
.deal-item.rectangle .jp-item-label,.deal-item.rectangle .jp-item-val {
    float:left;
}
.jp-item-label {
    border-right: 1px solid;
    width: 35%;
    font-family: sans-serif;
}
.jp-item-val {
    width: 64%;
}
.jackpotsblock {

    text-align: center;
    margin: 0 auto;
}

.deal-item.rectangle > div {
}

.deal-item.rectangle {
    float: left;
    width: 13%;
    background: rgba(0,0,0,0.5);
    border: goldenrod 2px solid;
    border-radius: 10px;
}
.deal-item.rectangle:nth-child(1) {
    margin-left: 7.5%;
}
.oldnovomatic .deal-item.rectangle:nth-child(1) {
    margin-left: 11.5%;
}
.novomatic .deal-item.rectangle:nth-child(1) {
    margin-left: 0%;
}
.deal-item.rectangle:nth-child(2) {
    float: left;
    margin-left: 0%;
}
.deal-item.rectangle:nth-child(3) {
    margin-left: 32%;
}
.oldnovomatic .deal-item.rectangle:nth-child(3) {
    margin-left: 21%;
}
.novomatic .deal-item.rectangle:nth-child(3) {
    margin-left: 46%;
}
.deal-item.rectangle:nth-child(4) {
    float: left;
    margin-left: 0%;
}
.jackpotsblock {
    position: absolute;
    top: 22px;
    width: 100%;
    z-index: 10002;
    flex: 1;
    justify-content: space-between;
    display: block;
    align-items: flex-start;
    flex-wrap: wrap;

    font-size: 30px;
    text-align: center;
    color: #fff;
    text-shadow: -2px -2px 0 #000, 2px -2px 0 #000, -2px 2px 0 #000, 2px 2px 0 #000;
    font-weight: 700;
    font-family: fantasy;

    line-height: 35px;
}

.jackpotsblock.oldnovomatic {
    margin-top: 6.4%;
}

.jackpotsblock.oldnovomatic.threee {
    margin-top: 3.4%;
}

.jackpotsblock.oldnovomatic.alwayshot {
    top: 12%;
}

.jackpotsblock.novomatic {
    margin-top: 3.2%;
}

.jackpotsblock.novomatic.luckyladycharmd {
    margin-top: 5.6%;
}

.jackpotsblock.novomatic.bookofrad {
    margin-top: 4%;
}

.jackpotsblock.novomatic.coldspell {
    margin-top: 5.2%;
}

.jackpotsblock.novomatic.polarfox {
    margin-top: 5.2%;
}

.jackpotsblock.novomatic.pharaohsgold3 {
    margin-top: 5.6%;
}

.jackpotsblock.novomatic.sizzlinghotd {
    margin-top: 5.2%;
}

.jackpotsblock.novomatic.dolphinsd {
    margin-top: 5.6%;
}

.jackpotsblock.igrosoft {

    margin-top: 20%;
    margin-left: 87.1%;
    width: 7%;
}

.jackpotsblock.igrosoft .deal-item.rectangle {
    margin: 0;
    margin-top: 17%;
    width:100%;
}

.jackpotsblock.igrosoft .jp-item-val,.jackpotsblock.igrosoft .jp-item-label {
    width: 100%;
}

@media only screen and (max-width: 1366px) {

   .jackpotsblock.novomatic {
      font-size: 20px;
      line-height: 25px;
   }

}
@media only screen and (max-width: 820px) {

    .jackpotsblock.novomatic {
      font-size: 15px;
      line-height: 20px;
   }

   .jackpotsblock.novomatic .deal-item.rectangle {
       width: 12.7%;
   }
   .jackpotsblock.novomatic .deal-item.rectangle .jp-item-val {
       width: 60%;
   }
}

@keyframes blink {
    0% { color: #ff9900;}
    50% { color: red; }
    100% { color: #ff9900; }
}
@-webkit-keyframes blink {
    0% { color: #ff9900; }
    50% { color: red;}
    100% { color: #ff9900;}
}
.deal-item.rectangle .hot, .deal-item.rectangle.hot {
    -webkit-animation: blink 1s linear infinite;
    -moz-animation: blink 1s linear infinite;
    animation: blink 1s linear infinite;
}
/*for old novomatic*/
#jp {
    width:100% !important;
}
</style>
</div>
<script>
    o_id = '<?php echo auth::user()->office_id ?? OFFICE; ?>';
    u_id =<?php echo auth::$user_id; ?>;
    jp_show_time =<?php echo ORM::factory('jackpot')->jp_show_time(); ?>;

    function hidejp() {
        var a = document.getElementById('jackpotsblock');
        a.style.display = 'none';
    }

    function showjp($class) {

        var a = document.getElementById('jackpotsblock');
        a.style.display = '';

        if(typeof $class == 'string') {
            if($class.length>0) {
                a.classList.add($class);
            }
        }
        if($class && typeof $class.forEach == 'function') {
            $class.forEach(function($c) {
                a.classList.add($c);

                if($c=='oldnovomatic' || $c=='igrosoft') {
                    setTimeout(window.onresize,50);
                }
            });
        }
    }

    window.onresize = function(){
        //egt
        document.getElementById('content') && document.getElementById('content').appendChild(document.getElementById('jackpotsblock'));
        //oldnovomatic and igrosoft
        if(document.getElementById('jp') && document.getElementById('jackpotsblock').parentElement!=document.getElementById('jp')) {
            document.getElementById('jp').parentNode.insertBefore(document.getElementById('jackpotsblock'), document.getElementById('jp').nextSibling);
        }
        //novomatic
        if(document.getElementById('game-box') && document.getElementById('jackpotsblock').parentElement!=document.getElementById('game-box')) {
            document.getElementById('game-box').appendChild(document.getElementById('jackpotsblock'));
        }
    };
    window.onload = window.onresize;
</script>
<script async="false" src="/js/jp.js?ver=<?php echo th::ver(); ?>" type="text/javascript"></script>
<?php echo block::jackpot_win(); ?>