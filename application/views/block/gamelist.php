<div class="game-block">
    <div class="game-list" data-game="list">
        <div id="game-previews-column">
            <?php foreach($games as $cat=>$games ) :?>
                <?php foreach($games as $name=>$game ) :?>
                    <div data-game="<?php echo $name; ?>"
                         data-name="<?php echo $game['visible_name'] ?>"
                         data-spins="80438"
                         data-payout="1184504"
                         data-mark="<?php echo $game['mark']??''; ?>"
                         data-rating="3"
                         data-developer="19"
                         class="tmb">

                        <i class="sticker"></i>

                        <div class="tmb-img">
                            <img alt="<?php echo $game['image'] ?>" src="<?php echo $game['image'] ?>" width="190" height="110">
                            <div class="tmb-action">
                                <?php if (auth::$user_id): ?>
                                    <?php if(auth::user()->canPlay($name)): ?>
                                <a class="btn btn-red btn-lg "  href="//<?php echo th::main_domain().th::gamelink($cat,$name) ?>">
                                            <span>Играть</span>
                                        </a>
                                    <?php else: ?>
                                        <a class="btn btn-red btn-lg show_popup" href="javascript:void(0);" data-href="/payment/in">
                                            <span>Играть</span>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <a class="btn btn-red btn-lg show_popup" href="javascript:void(0);" data-href="/popup/login">
                                        <span>Играть</span>
                                    </a>
                                <?php endif; ?>
                                <?php if($cat!='live'):?>
                                    <a class="btn btn-blue btn-md" href="<?php echo '//demo.'.th::main_domain().th::gamelink($cat,$name) ?>">
                                            <span><?php echo __('Демо') ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tmb-title"><?php echo $game['visible_name'] ?></div>
                    </div>
                <?php endforeach ?>
            <?php endforeach ?>
        </div>
    </div>
</div>
