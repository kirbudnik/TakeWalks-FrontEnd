<section class="hero">
	<div class="wrap content">
		<h1 class="larger">My Account</h1>
	</div>
</section>

<div class="content wrap">

	<main class="history">
		
		<h2 class="large"><?php echo $user['email']; ?> <a class="small pink button" href="/account">Edit profile</a></h2>

		<h3 class="larger">Booking history</h3>

		<table class="results">
			<tr>
				<th>Tour name</th>
				<th>Date</th>
				<th></th>
			</tr>
            <?php foreach ($tours as $tour) { ?>
                <tr>
                    <td>
                        <h3><a href="<?php echo "/{$theme->city_slug}-tours/{$tour['Event']['url_name']}" ?>"><?php echo $tour['Event']['name_short'] ?></a></h3>
                        <img src="https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W300/https:', $tour['Event']['EventsImage'][0]['images_name']); ?>" alt="">
                    </td>
                    <td>
                        <?php echo date('M d, Y g:ia', strtotime($tour['events_datetimes'])) ?>
                    </td>
                    <td>
                        <?php if (strtotime($tour['events_datetimes']) > strtotime('now')) { ?>
                            <a href="/clients/resend_confirmation/<?php echo $tour['bookings_id'] ?>">Resend confirmation</a>
                        <?php } else { ?>
                            <a href="<?php echo "/{$theme->city_slug}-tours/{$tour['Event']['url_name']}" ?>">Book again from $<?php echo number_format($tour['Event']['adults_price'], 2) ?></a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if (empty($tours)) { ?>
              <tr><td colspan="3">You have not yet made a booking.  <a href="/<?php echo "{$theme->city_slug}-tours" ?>">Start here to find a tour you will love!</a></td></tr>
            <?php } ?>

		</table>

	</main>
	<aside>
    <?php echo $this->element('static') ?>
