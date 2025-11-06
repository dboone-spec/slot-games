<div class="navbar-content">
				<ul class="pc-navbar">
					<li class="pc-item pc-caption">
						<label>Navigation</label>

					</li>



                                        <?php foreach($navBar as $nameGroup=>$group) :?>
                                            <?php if (count($group)==1) : $link=reset($group); ?>
                                                <li class="pc-item">
                                                       <a href="<?php echo $dir.'/'.key($group) ?>" class="pc-link ">
                                                           <span class="pc-micon"><i data-feather="<?php echo $icons[$nameGroup]??'menu' ?>"></i></span>
                                                           <span class="pc-mtext"><?php echo $link ?></span></a>
                                               </li>

                                            <?php endif; ?>


                                            <?php if (count($group)>1) : ?>
                                                <li class="pc-item pc-hasmenu <?php echo ($nameGroup==$bigcurrent)?'pc-trigger':''?>">
                                                        <a href="#" class="pc-link ">
                                                            <span class="pc-micon"><i data-feather="<?php echo $icons[$nameGroup]??'menu' ?>"></i></span>
                                                            <span class="pc-mtext"><?php echo $nameGroup ?></span>
                                                            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                                                        </a>
                                                        <ul class="pc-submenu">
                                                            <?php foreach($group as $link=>$name):?>
                                                                <li class="pc-item  <?php echo ($link==$current)?'active':''?>">
                                                                    <a class="pc-link" href="<?php echo $dir.'/'.$link; ?>"><?php echo $name ?></a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                </li>
                                            <?php endif; ?>



                                        <?php endforeach; ?>

				</ul>
			</div>