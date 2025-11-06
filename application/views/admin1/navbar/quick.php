<div class="dropdown-menu pc-h-dropdown pc-mega-dmenu">
        <div class="row no-gutters">

            <?php foreach($navBar as $nameGroup=>$group) :?>
                <?php if (count($group)>0) : ?>
                    <div class="col">
                    <h6 class="mega-title"><?php echo $nameGroup ?></h6>
                    <ul class="pc-mega-list">

                        <?php foreach($group as $link=>$name):?>
                            <li><a href="<?php echo $dir.'/'.$link; ?>" class="dropdown-item"><i data-feather="<?php echo $icons[$nameGroup]??'menu' ?>"></i><span><?php echo $name ?></span></a></li>

                        <?php endforeach; ?>
                    </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="col">
                <h6 class="mega-title">Profile</h6>
                <ul class="pc-mega-list">
                    <li><a href="<?php echo $dir.'/profile' ?>" class="dropdown-item"><i data-feather="user"></i><span>Profile</span></a></li>
                    <li><a href="<?php echo $dir.'/login/logout' ?>" class="dropdown-item"><i data-feather="power"></i><span>Logout</span></a></li>
                </ul>
            </div>
        </div>
</div>
