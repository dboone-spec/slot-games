<div id="noselect" style="text-decoration: none; font-size: larger;">
<a href="/enter">enter</a>
<br/>
<a target="_blank" href="/manuals/gameapi.doc">gameapi.doc</a>
<br/>
<a target="_blank" href="/manuals/gamelist.pdf">game list (pdf)</a>
</div>
<div class="lang-box">
    <?php if (Cookie::get('lang') == 'en' || I18n::$lang == 'en'): ?>
        <a href="/lang/set/ru">
            <img width="22px" src="/assets/img/ru_RU.png" alt="ru">
        </a>
    <?php elseif (Cookie::get('lang') == 'ru' || I18n::$lang == 'ru'): ?>
        <a href="/lang/set/en">
            <img width="22px" src="/assets/img/en_EN.png" alt="en">
        </a>
    <?php endif; ?>
</div>
<div style="background: #000; margin:0;padding:0;display: flex;flex: 1;width: 100%;flex-flow: wrap;">
    <div id="filter_brands" style="width: 100%"></div>
    <div id="filter_tech" style="width: 100%"></div>
<?php foreach($gamelist as $i=>$game): ?>
    <a tech="<?php echo $game['tech']=='h'?'HTML5':'FLASH'; ?>" brand="<?php echo UTF8::strtoupper($game['brand']); ?>"
       style="text-decoration-line: none;padding: 0.5%;border: 1px solid #fff;color: #fff;width: 10vw; margin: 0.5%;<?php echo $game['can_demo']==0?'opacity: 0.5;':''; ?>"
       href="<?php echo $game['can_demo']==1?'/demo/play/'.$game['game_id']:'javascript:void(0)'; ?>">
        <img style="width: 10vw; height: 10vh;" src="<?php echo $game['thumb']; ?>" alt="<?php echo $game['name']; ?>" />
        <span style="float: left;"><?php echo $game['name']; ?></span>
        <span style="    float: left;clear: both;color:cornflowerblue;"><?php echo UTF8::strtoupper($game['brand']); ?></span>
        <span style=" float: right; color: crimson;
              /*display: block;position: absolute;top: 1.8%;margin-left: 6.4%;background: crimson;padding: 5px;font-weight: bold;*/
              "><?php echo $game['tech']=='h'?'HTML5':'FLASH'; ?></span>
    </a>
<?php endforeach; ?>
</div>
<script>
    var brands = ['ALL'];
    var tech = ['ALL','HTML5','FLASH'];

    function load_tech() {
        var div = document.getElementById('filter_tech');

        tech.forEach(function(e) {
            var b = document.createElement('button');
            b.innerHTML=e;
            div.appendChild(b);

            b.addEventListener('click',function(s) {
                var links = document.getElementsByTagName('a');
                for(var i=0;i<links.length;i++) {
                    if(links[i].parentNode.getAttribute('id')!='noselect') {
                        if(s.target.innerHTML=='ALL') {
                            links[i].style.display='block';
                        }
                        else {
                            if(links[i].getAttribute('tech')==s.target.innerHTML) {
                                links[i].style.display='block';
                            }
                            else {
                                links[i].style.display='none';
                            }
                        }
                    }
                }
            });
        });
    }

    function load_brands() {
        var div = document.getElementById('filter_brands');
        if(div.innerHTML == null || div.innerHTML == "") {
            var nextSibling = div.nextSibling;
            while(nextSibling) {
//                if(brands.indexOf(nextSibling.getAttribute('brand')))
                nextSibling = nextSibling.nextSibling;
                if(nextSibling && nextSibling.nodeType == 1 && nextSibling.getAttribute('brand')!=null && brands.indexOf(nextSibling.getAttribute('brand'))+1==0) {
                    brands.push(nextSibling.getAttribute('brand'));
                }
            }
        }
        brands.forEach(function(e) {
            var b = document.createElement('button');
            b.innerHTML=e;
            div.appendChild(b);

            b.addEventListener('click',function(s) {
                var links = document.getElementsByTagName('a');
                for(var i=0;i<links.length;i++) {
                    if(links[i].parentNode.getAttribute('id')!='noselect') {
                        if(s.target.innerHTML=='ALL') {
                            links[i].style.display='block';
                        }
                        else {
                            if(links[i].getAttribute('brand')==s.target.innerHTML) {
                                links[i].style.display='block';
                            }
                            else {
                                links[i].style.display='none';
                            }
                        }
                    }
                }
            });
        });

    }

    window.onload = function() {
        load_brands();
        load_tech();
    };
</script>
