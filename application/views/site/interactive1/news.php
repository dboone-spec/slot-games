<section class="s-we-offer">
		<div class="container">
			<h2>News</h2>

			<div class="row we-offer-cover">


                            <?php foreach ($news as $new):?>
				<div class="col-12 col-sm-6 we-offer-item" style=" padding-top: 40px;" >
					<div class="offer-item-img">
						<img style="object-fit: contain;" src="/theme/interactive1/news/<?php echo $new->id ?>.jpg" alt="img">
					</div>
					<div class="offer-item-content">
						<h4 class="title-line-left"><?php echo $new->title ?></h4>
						<p><?php echo $new->text ?></p>
						<h6><?php echo date('Y-m-d',$new->created); ?></h6>
					</div>
				</div>
                            <?php endforeach; ?>
			</div>
			
			<div>
            <?php echo $page; ?>
        </div>
		

		</div>
	</section>