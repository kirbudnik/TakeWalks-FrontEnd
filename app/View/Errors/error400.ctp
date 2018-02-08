<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');

endif;
?>

<style>
	.locations{
		display: flex;
		flex-wrap: wrap;
	}
	.locations .location{
		position: relative;
		flex-grow: 1;
		background-size: cover;
		width: 48%;
		min-height: 260px;
		background-position: center center;
		margin: 0 1% 1% 0;
		display: flex;
		align-items: flex-end;
	}

	.locations .name{
		position: absolute;
		bottom: 20px;
		right: 20px;
		color: #FFF;
		text-align: right;
		text-shadow: 0 0 18px #000;
	}
	.locations .name h2{
		color: #FFF;

		margin-bottom: 10px;
	}
	.locations .moreInfo, #blog a{
		display: inline-block;
		height: 35px;
		padding: 0 18px;
		border: 1px solid #fff;
		line-height: 35px;
		text-transform: uppercase;
		text-decoration: none;
		font-size: 14px;
		color: #fff;
	}

	.locations .moreInfo:hover{
		background: white;
		color: #611906;
		text-shadow: none;
	}

    #blog{
        background: #e4e4e1;
        padding: 20px;
        font-size: 20px;
        margin: 25px 0;
        text-align: right;
    }
    #blog a{
        border: 1px solid #67220F;
        color: #611906;
    }
    #blog a:hover{
        background: white;
    }
    #blog a:active{
        background: none;
    }

	@media (max-width: 750px) {
		.locations .location{
			width: 100%;
			margin-right: 0;
		}



	}
</style>

<div id="root">
	<article id="featured" class="transfers-hero contact-us-hero">
		<header>
			<h2>Page Not Found</h2>

			<p>Have any further questions about tour or trip?<br>
				Our Customer Service Team is here to help.</p>
		</header>
	</article>

	<nav id="breadcrumbs" class="transfer-bc">
		<div>
			<h2>Page not found</h2>
			<ol>
				<a href="/">Home</a></li>
			</ol>
		</div>
	</nav>

	<section id="content">
		<?php if($config->domain == 'italy'): ?>

            <div class="locations">
                <?php foreach(array_slice($locations,0,4) as $location): ?>
                    <div class="location" style="background-image: url(<?php echo $location['DomainsGroup']['hero'] ?>)">
                        <div class="name">
                            <h2><?php echo $location['DomainsGroup']['name'] ?></h2>
                            <a href="/<?php echo $location['DomainsGroup']['url_name'] ?>-tours" class="moreInfo"><i class="fa fa-info-circle"></i> MORE INFO</a>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <div id="blog">
                <a href="/blog">Visit our blog</a>
            </div>

        <?php endif ?>

		<div class="contact-us-intro">
			The best way to reach us for non-urgent matters us is by email at info@walksof<?php echo $config->domain ?>.com. Our team will try to get back to you within 24 hours with a full response. <br><br>

			Depending on where you’re calling us from, you can get us on any of the numbers below:
		</div>

		<div class="contact-details">
            <?php if($config->domain != 'turkey'): ?>
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
            <?php else: ?>
                <div>
                    <h3 class="header-a">Contact information</h3>

                    <div class="detail-content">
                        <span>Phone (US):</span> 1-866-671-1430<br>


                        <span>Email:</span> info@walksofturkey.com<br />
                        <span>Our office hours</span> are<br>
                        Monday through Sunday <br/>
                        from 11:00am to 7:00pm <br/>
                        (UTC +2, Istanbul time) Global



                    </div>
                </div>

            <?php endif ?>
		</div> <!-- //.contact-details -->

		<article class="important-info">
			<h2><i class="fa fa-info-circle"></i>Urgent Problems</h2>

			<p class="disclaimer"><strong>Running late</strong> or <strong>can’t find your</strong> tour meeting point? Check your confirmation email for our <strong>emergency phone number.</strong></p>
		</article>

		<section class="feeling-social">
			<h2 class="header-with-border"><i class="fa fa-twitter"></i>Feeling Social?</h2>

			<div class="social-text">Feel free to Tweet us <span class="red">@walksof<?php echo $config->domain ?></span> write to us on <span class="red">Facebook</span> or <span class="red">Google+</span> or catch us on <span class="red">GTalk</span> as walksof<?php echo $config->domain ?>.</div>

			<div class="social-icons">
                <a href="https://www.facebook.com/walkingtours"><i class="fa fa-facebook"></i></a>
                <a href="https://plus.google.com/+WalksofitalyTours/posts"><i class="fa fa-google-plus"></i></a>
                <a href="https://twitter.com/WalksofItaly"><i class="fa fa-twitter"></i></a>
                <a href="http://instagram.com/walksofitaly""><img src="/img/social/instagram.png" alt=""></a>
                <a href="https://www.youtube.com/user/walksofitaly"><i class="fa fa-youtube"></i></a>
                <a href="http://vimeo.com/walksofitaly"><img src="/img/social/vimeo.png" alt=""></a>
                <a href="https://www.pinterest.com/walksofitaly/"><i class="fa fa-pinterest-p"></i></a>
			</div>

		</section>

	</section>


</div>