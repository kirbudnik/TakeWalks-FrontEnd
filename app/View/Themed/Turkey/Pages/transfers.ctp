<div id="root">
	<article id="featured" class="transfers-hero none-home-hero">
		<div class="header-inner">
            <header>
                <h2>Transfers</h2>

                <p>
                    Prices Starting From <?php echo ExchangeRate::convert(64); ?>
                </p>

                <div class="top-right-border-outer"></div>
                <div class="top-right-border-inner"></div>
            </header>
        </div>
<!-- 
		<header>
			<h2>Transfers</h2>

			<p>Prices Starting From <?php echo ExchangeRate::convert(64); ?></p>
		</header> -->
	</article>

	<nav id="breadcrumbs" class="transfer-bc">
		<div>
			<h2>Transfers</h2>
			<ol>
				<li><a href="/">Home</a></li>
				<li>Transfers</li>
			</ol>
		</div>
	</nav>

	<section id="content">
		<div class="transfers-intro">
			Walks of Italy is affiliated with a select group of safe, fully-insured and reliable transport agencies that offer a range of itineraries. These services include, but are not limited to, the following:
		</div>


		<ul class="transfers-intro-list">
			<li>
				<div class="bullet"><i class="fa fa-check-circle-o"></i></div>
				<span class="text">Airport transfers</span>
			</li>
			<li>
				<div class="bullet"><i class="fa fa-check-circle-o"></i></div>
				<span class="text">Day trips departing from major cities</span>
			</li>
			<li>
				<div class="bullet"><i class="fa fa-check-circle-o"></i></div>
				<span class="text">Train station transfers</span>
			</li>
			<li>
				<div class="bullet"><i class="fa fa-check-circle-o"></i></div>
				<span class="text">Cruise port transfers</span>
			</li>
			<li>
				<div class="bullet"><i class="fa fa-check-circle-o"></i></div>
				<span class="text">Meeting point pick-up/drop-off</span>
			</li>
			<li>
				<div class="bullet"><i class="fa fa-check-circle-o"></i></div>
				<span class="text">City transfers</span>
			</li>
		</ul>
		<section id="content">
			<div class="contact-us-intro">
				If you're interested in reserving a private transfer, <strong>please email our team at <a href="mailto:info@walksofitaly.com">info@walksofitaly.com</a></strong> or call the numbers below: 
			</div>

			<div class="contact-details">
				<div>
					<h3 class="header-a">Outside of italy</h3>

					<div class="detail-content">
						<span>From the US (toll-free):</span> +1-888-683-8670<br>
						<span>International:</span> +1-202-684-6916 <br>

						<span>Our U.S. office hours</span> are <br>
						Monday through Friday <br>
						from 8:30 a.m. to 5:00 p.m. (CDT)<br>
					</div>
				</div>

				<div>
					<h3 class="header-a">Within italy</h3>

					<div class="detail-content">
						<span>From Italy:</span> +39-069-480-4888<br>

						<span>Our Rome office hours</span> are<br>
						Monday through Friday <br>
						from 7:30 a.m. to 8:00 p.m. <br>
						and on Saturdays and Sundays <br>
						from 7:30 a.m. to 3:30 p.m., Central European Time, <br>
						Rome local time (GMT+1). <br>

						<span>If dialing Italy from a U.S. phone</span>, dial 011-39.<br>

					</div>
				</div>
			</div> <!-- //.contact-details -->


		</section>

		<section class="transfer-routes">
			<?php foreach($transfers as $location=>$locationEvents): ?>

				<div class=" transfers-list list-with-images <?php echo strtolower($location) ?>">
					<div>
						<div class="img-wrapper">
							<?php
							//temporary fix for Tuscany
								$imageUrl = $location == 'Tuscany' ? 'florence' : $location;
							?>
							<img src="https://app.resrc.it/O=20(40)/s=W478/https://images.walks.org/italy/cities/homepage/<?php echo strtolower($imageUrl) ?>.jpg" alt="">
							<div class="text"><?php echo $location ?><br> Transfers</div>
						</div>
					</div>

					<?php foreach($locationEvents as $event): ?>
						<div class="route">
							<div class="city-transfer"><?php echo $event['name'] ?></div>
							<!--Do not show more info

							<a href="/transfers-tours/<?php echo $event['url_name'] ?>" class="link-a">
								<i class="fa fa-info-circle"></i>
								More Info
							</a>
							-->
							<span class="price-a">
								<span>from</span>
								 <?php echo ExchangeRate::convert($event['private_base_price']) ?>
							</span>
						</div>
					<?php endforeach ?>


				</div>

			<?php endforeach ?>

		</section>
		</div>
