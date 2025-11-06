<div id="noselect" style="text-decoration: none; font-size: larger; float: left; margin: 10px;">
    <a href="/enter">enter</a>
    <a target="_blank" href="/manuals/gameapi.doc">gameapi.doc</a>
    <a target="_blank" href="/manuals/gamelist.pdf">game list (pdf)</a>
</div>
<div class="lang-box" style=" float: left; margin: 15px; margin-top: 0px; border: 1px solid blueviolet;">
    <?php if (Cookie::get('lang') == 'en' || I18n::$lang == 'en'): ?>
        <a href="/lang/set/ru">
            <img width="50px" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0NTAgMzAwIj4NCjxwYXRoIGZpbGw9IiNmZmYiIGQ9Im0wLDBoNDUwdjEwMGgtNDUweiIvPg0KPHBhdGggZmlsbD0iIzAwZiIgZD0ibTAsMTAwaDQ1MHYxMDBoLTQ1MHoiLz4NCjxwYXRoIGZpbGw9IiNmMDAiIGQ9Im0wLDIwMGg0NTB2MTAwaC00NTB6Ii8+DQo8L3N2Zz4NCg==" alt="en">
        </a>
    <?php elseif (Cookie::get('lang') == 'ru' || I18n::$lang == 'ru'): ?>
        <a href="/lang/set/en">
            <img width="50px" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNjAgMzAiIGhlaWdodD0iNjAwIj4NCjxkZWZzPg0KPGNsaXBQYXRoIGlkPSJ0Ij4NCjxwYXRoIGQ9Im0zMCwxNWgzMHYxNXp2MTVoLTMwemgtMzB2LTE1enYtMTVoMzB6Ii8+DQo8L2NsaXBQYXRoPg0KPC9kZWZzPg0KPHBhdGggZmlsbD0iIzAwMjQ3ZCIgZD0ibTAsMHYzMGg2MHYtMzB6Ii8+DQo8cGF0aCBzdHJva2U9IiNmZmYiIHN0cm9rZS13aWR0aD0iNiIgZD0ibTAsMGw2MCwzMG0wLTMwbC02MCwzMCIvPg0KPHBhdGggc3Ryb2tlPSIjY2YxNDJiIiBzdHJva2Utd2lkdGg9IjQiIGQ9Im0wLDBsNjAsMzBtMC0zMGwtNjAsMzAiIGNsaXAtcGF0aD0idXJsKCN0KSIvPg0KPHBhdGggc3Ryb2tlPSIjZmZmIiBzdHJva2Utd2lkdGg9IjEwIiBkPSJtMzAsMHYzMG0tMzAtMTVoNjAiLz4NCjxwYXRoIHN0cm9rZT0iI2NmMTQyYiIgc3Ryb2tlLXdpZHRoPSI2IiBkPSJtMzAsMHYzMG0tMzAtMTVoNjAiLz4NCjwvc3ZnPg0K" alt="ru">
        </a>
    <?php endif; ?>
</div>
<div style="float: right;">
    <?php if(!auth::$user_id): ?>
        <a style="

            text-decoration: none;
            font-size: larger;
            text-transform: uppercase;
            font-weight: bold;
            line-height: 200%;
            border: 1px solid;
            padding: 5px 10px;
         " href="/login/testauth"><?php echo __('Войти'); ?></a>
    <?php else: ?>
        <?php echo __('Баланс'); ?>: <?php echo auth::user()->amount(); ?>
        <a href="/login/logout"><?php echo __('Выйти'); ?></a>
    <?php endif; ?>
</div>
<div style="background: #000; margin:0;padding:0;display: flex;flex: 1;width: 100%;flex-flow: wrap;">
    <div id="filter_brands" style="width: 100%"></div>
    <div id="filter_tech" style="width: 100%"></div>
<?php foreach($gamelist as $i=>$game): ?>
    <a tech="<?php echo $game['tech']=='h'?'HTML5':'FLASH'; ?>" brand="<?php echo UTF8::strtoupper($game['brand']); ?>"
       style="text-decoration-line: none;padding: 0.5%;border: 1px solid #fff;color: #fff;width: 10vw; margin: 0.5%;<?php echo $game['can_demo']==0?'opacity: 0.5;':''; ?>"
       href="<?php echo auth::$user_id?'/play/'.$game['game_id']:($game['can_demo']==1?'/demo/play/'.$game['game_id']:'javascript:void(0)'); ?>">
        <img style="width: 10vw; height: 10vh;" src="<?php echo $game['thumb']; ?>" alt="<?php echo $game['name']; ?>" />
        <span style="float: left;"><?php echo $game['name']; ?></span>
        <span style="float: left;clear: both;color:cornflowerblue;"><?php echo UTF8::strtoupper($game['brand']); ?></span>
        <span style="float: right; color: crimson;
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
