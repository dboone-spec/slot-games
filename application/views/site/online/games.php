<div class="container" style="height: 60px">

</div>


<div class="container" >

	<div class="sixteen columns">

		<!-- Page Title -->
		<div id="page-title">

			<!-- Filters -->
			<div id="filters">
				<ul class="option-set" data-option-key="filter">
					<li><a href="#filter" class="selected" data-option-value="*">All</a></li>
                    <li><a href="#filter" data-option-value=".hot">Hot</a></li>
					<li><a href="#filter" data-option-value=".classic">Classic</a></li>
                    <li><a href="#filter" data-option-value=".table">Table</a></li>
					<li><a href="#filter" data-option-value=".novomatic">Novomatic</a></li>
					<li><a href="#filter" data-option-value=".egt">EGT</a></li>
					<li><a href="#filter" data-option-value=".igrosoft">Igrosoft</a></li>
				</ul>
			</div>
			<div class="clear"></div>


		</div>
		<!-- Page Title / End -->

	</div>
</div>



<div class="container">

	<!-- Portfolio Content -->
	<div id="portfolio-wrapper" style="position: relative; height: 873.153px;">


                <?php foreach ($games as $game): ?>
                    <!-- 1/4 Column -->
                    <div class="four columns portfolio-item <?php echo $game['brand']; ?> <?php echo $game['category']; ?>">
                            <div class="picture">
                                <a
                                    <?php if(!auth::$user_id): ?>
                                    class="fancybox.ajax auth_popup"
                                    href="/login"
                                    <?php else: ?>
                                    href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; echo in_array($game['brand'],['agt','egt']) ? '' : '/';?>"
                                    <?php endif; ?>
                                    >
                                    <img src="<?php echo $game['image']; ?>" style="width: 100%; height: 150px;">
                                </a>
                            </div>
                            <div class="item-description alt">
                                    <h5>
                                        <a class="<?php if(!auth::$user_id): ?>auth_popup<?php endif; ?>" href="/games/<?php echo $game['brand'] ?? 'agt'; ?>/<?php echo $game['name']; echo in_array($game['brand'],['agt','egt']) ? '' : '/';?>">
                                            <?php echo $game['visible_name'] ?>
                                        </a>
                                    </h5>

                            </div>
                    </div>

                <?php endforeach; ?>


	</div>
	<!-- End Portfolio Content -->

</div>