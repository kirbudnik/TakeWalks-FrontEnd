<div class="modal-wrap" data-modal-target="cartModal">
	<div class="modal-inner">
		<div class="modal-content">
			<div class="modal-close" data-modal-close  data-description="Closed bundle modal" ></div>
			<div class="modal-header">
				<h2>Your Cart</h2>
			</div>

				<div class="modal-summary">
				<?php
				$totalTotalPrice = 0;
				foreach($cart as $i => $item): ?>
				<div class="modal-summary-row">
					<div class="modal-summary-col">
						<h4 class="summary-header">Tour Name</h4>
						<p class="summary-content event-title"><?php echo $item['name']; ?></p>
					</div>
					<div class="modal-summary-col">
						<h4 class="summary-header">Date & Time</h4>
						<div class="summary-content">
							<p><?php echo  date("F j, Y",strtotime($item['datetime'])) ?></p>
							<p class="time"><?php echo  date("h:ia",strtotime($item['datetime'])) ?></p>
						</div>
					</div>
					<div class="modal-summary-col guest-col">
						<h4 class="summary-header">Guests</h4>
						<p class="summary-content"><?php echo $item['adults'] + $item['children'] + $item['infants'] + $item['students']; ?></p>
					</div>
					<div class="modal-summary-col last" >
						<h4 class="summary-header">&nbsp;</h4>
						<div class="summary-content">
							<?php if ( $item['discount_bundle_tour'] == 0): ?>
							<p>&nbsp;</p>
							<?php endif; ?>
							<p class="summary-header subtotal">
								<span>Subtotal</span>
								<span data-event-id-price="<?php echo $item['event_id']; ?>" data-related="<?php echo implode(',', $item['related']); ?>">
								<?php if ( $item['discount_bundle_tour'] != 0): ?>
									<span style="text-decoration: line-through;"><?php echo ExchangeRate::convert( $item['total_price']); ?></span><br>
									<span style="margin-left: 0px; "><?php echo ExchangeRate::convert( $item['total_price'] - $item['discount_bundle_tour']); $totalTotalPrice += $item['total_price']; ?></span>
								<?php else : ?>
									<?php echo ExchangeRate::convert( $item['total_price'] - $item['discount_bundle_tour']); $totalTotalPrice += $item['total_price']; ?>
								<?php endif; ?>
								</span>
							</p>
						</div>
					</div>
<!--					<div class="modal-summary-col last">-->
<!--						<h4 class="summary-header subtotal">-->
<!--							<span>Subtotal</span>-->
<!--							<span data-event-id-price="--><?php //echo $item['event_id']; ?><!--" data-related="--><?php //echo implode(',', $item['related']); ?><!--">-->
<!--							--><?php //if ( $item['discount_bundle_tour'] != 0): ?>
<!--								<span style="text-decoration: line-through;">--><?php //echo ExchangeRate::convert( $item['total_price']); ?><!--</span><br>-->
<!--								<span style="margin-left: 0px; ">--><?php //echo ExchangeRate::convert( $item['total_price'] - $item['discount_bundle_tour']); $totalTotalPrice += $item['total_price']; ?><!--</span>-->
<!--							--><?php //else : ?>
<!--								--><?php //echo ExchangeRate::convert( $item['total_price'] - $item['discount_bundle_tour']); $totalTotalPrice += $item['total_price']; ?>
<!--							--><?php //endif; ?>
<!--							</span>-->
<!--						</h4>-->
<!--					</div>-->
				</div>
				<?php endforeach; ?>
				<div class="modal-summary-row" id="actual_event">
					<div class="modal-summary-col">
						<h4 class="summary-header">Tour Name</h4>
						<p class="summary-content event-title"><?php echo $event['name_listing'] != '' ? $event['name_listing'] : $event['name_short'] ?></p>
					</div>
					<div class="modal-summary-col">
						<h4 class="summary-header">Date & Time</h4>
						<div class="summary-content">
							<p id="row_date"></p>
							<p class="time" id="row_time"></p>
						</div>
					</div>
					<div class="modal-summary-col guest-col">
						<h4 class="summary-header">Guests</h4>
						<p class="summary-content" id="row_guests"></p>
					</div>
					<div class="modal-summary-col last">
						<h4 class="summary-header">&nbsp;</h4>
						<div class="summary-content">
							<p class="summary-space">&nbsp;</p>
							<p class="summary-header subtotal">
								<span>Subtotal</span>
								<span id="row_subtotal"></span>
							</p>
						</div>
					</div>
				</div>
				<div class="modal-summary-row">
					<div class="modal-summary-col first">
					</div>
					<div class="modal-summary-col">
						<h4 class="summary-header"></h4>
						<p class="summary-content event-title"></p>
					</div>
					<div class="modal-summary-col">
						<h4 class="summary-header"></h4>
						<div class="summary-content">
							<p></p>
							<p class="time"></p>
						</div>
					</div>
					<div class="modal-summary-col guest-col">
						<h4 class="summary-header"></h4>
						<p class="summary-content"></p>
					</div>
					<div class="modal-summary-col last">
						<h4 class="summary-header subtotal">
						</h4>
						<div class="summary-content total">
							<span>Total</span>
							<span class="total-price price-a" id="cart_total"><?php echo ExchangeRate::convert($totalTotalPrice); ?></span>
						</div>
					</div>
				</div>

				<div class="modal-summary-row">
					<div class="buttons">
						<a href="#" class="link-a inverse" id="modal-checkout"
						   onclick="WrapperGA.ecTrackClickUI(event, this, true);"
						   data-description="Clicked checkout in bundle modal"
						>Checkout</a>
					</div>
				</div>
				<div class="modal-summary-row">
					<div class="buttons" style="margin-top: 0px;">
						<a id="modify-cart" class="modify-cart">Modify Cart</a>
					</div>
				</div>
			</div>

			<?php if ( count($relatedTours) > 0 ): ?>
<!--				<?php /*$relatedTours = str_replace('\\', '\\\\', json_encode(isset($relatedTours) ? $relatedTours : array())); */?>
				<script>var relatedTours = JSON.parse('<?php /*echo $relatedTours;  */?>//');</script>-->
			<div class="modal-header second">
				<h2>Book One Of These Tours Now & Save <?php echo $discountPercentRelatedTour; ?>%</h2>
			</div>

			<div class="tour-suggestions">
				<p class="suggestions-header">
					Book any of the below tours now and save <?php echo $discountPercentRelatedTour; ?>%!<br>
					Offer only valid when purchased with the item currently in your cart & only valid for these tours.
				</p>
				<div class="suggestion-activities-row">
				<?php
				$j = 0;
				foreach($relatedTours as $i => $relatedTour):
					$existInCart = false;
					foreach($cart as $k => $item){
						if ($relatedTour['Event']['id'] == $item['event_id']){
							$existInCart = true;
							break;
						}
					}
					if (intval($relatedTour['Event']['id']) == 0){
						$existInCart = true;
					}
					if ( !$existInCart &&  $j < 3 ) :
						$j++;
						?>
					<div data-eventid="<?php echo $relatedTour['Event']['id']; ?>" class="suggested-activity">
						<div class="image-wrap">
							<?php
							$priceForGuests = "";
							$totalTotalPrice = 0;
							$cartItem = null;
							foreach($cart as $k => $item){
								$priceForGuests .= ($item['adults']) ? $item['adults']." adults, " : "";
								$priceForGuests .= ($item['children']) ? $item['children']." children, " : "";
								$priceForGuests .= ($item['infants']) ? $item['infants']." infants, " : "";
								$priceForGuests .= ($item['students']) ? $item['students']." students, " : "";
								$cartItem = $item;
								break;
							}
							$priceForGuests = substr( $priceForGuests, 0, -2 );
							//apply discount
							$discount = 1 - ($discountPercentRelatedTour / 100);
							$adultsPrice = $relatedTour['Event']['adults_price'];
							$totalTotalPrice += ($relatedTour['Event']['adults_price'] * $cartItem['adults']);
							$totalTotalPrice += ($relatedTour['Event']['students_price'] * $cartItem['students']);
							$totalTotalPrice += ($relatedTour['Event']['children_price'] * $cartItem['children']);
							$totalTotalPrice += ($relatedTour['Event']['infants_price'] * $cartItem['infants']);

							//price to show in corner
							$adultsPriceDiscount = round($adultsPrice * $discount, 2);
							$diffPrice = ExchangeRate::convert($adultsPrice, true, false) - ExchangeRate::convert($adultsPriceDiscount, true, false);
							$diffPrice = ExchangeRate::format($diffPrice);
							//price to show in discount
							$totalTotalPriceDiscount = round($totalTotalPrice * $discount, 2);
							$diffPriceDiscount = ExchangeRate::convert($totalTotalPrice, true, false) - ExchangeRate::convert($totalTotalPriceDiscount, true, false);
							$diffPriceDiscount = ExchangeRate::format($diffPriceDiscount);

							//pick image
							$imageUrl = 'http://placehold.it/450x450';
							$imageAlt = '';
							foreach($relatedTour['Event']['EventsImage'] as $image) {
								if($image['publish']) {
									$imageUrl = $image['images_name'];
									$imageAlt = $image['alt_tag'];
									break;
								}
							}
							?>
							<img src="https://app.resrc.it/O=20(40)/<?php echo str_replace('https:', 's=W500/https:', $imageUrl); ?>" alt="<?php echo $imageAlt; ?>">
							<div class="triangle-price">
								<div class="price price-old"><?php echo ExchangeRate::convert(ceil($adultsPrice), false); ?></div>
								<div class="price price-new"><?php echo ExchangeRate::convert(ceil($adultsPriceDiscount), false); ?></div>
							</div>
						</div>

						<div class="content">
							<div class="activity-header"><?php echo $relatedTour['Event']['name_listing']; ?></div>
							<div class="activity-total">
								<p data-modalfield="price_for">Price for <?php echo $priceForGuests; ?> </p>
								<b class="price-a" data-modalfield="price_total_discount"><?php echo ExchangeRate::convert($totalTotalPriceDiscount); ?></b>
								<p class="gold-highlight" data-modalfield="diff_total_discount">Book now and save <?php echo ($diffPriceDiscount); ?></p>
							</div>
						</div>

						<div class="activity-actions">
							<select name="" id="">
								<option value="" selected hidden>Choose Date</option>
								<option value="">07.06</option>
								<option value="">14.06</option>
								<option value="">21.06</option>
							</select>
							<div class="activity-actions-buttons">
								<a class="link-a inverse add-to-cart"
								   onclick="Tour.ModalCheckout.bookingModalOption(event, this)"
								   data-position="<?php echo $j ?>"
								   data-name="<?php echo $relatedTour['Event']['name_listing'] != '' ? $relatedTour['Event']['name_listing'] : $relatedTour['Event']['name_short'] ?>"
								   data-id="<?php echo $relatedTour['Event']['id'] ?>"
								   data-sku="<?php echo $relatedTour['Event']['sku'] ?>"
								   data-price="<?php echo $totalTotalPriceDiscount ?>"
								   data-description="Clicked add to cart bundle modal product <?php echo $j ?>"
								>
									<i class="fa fa-shopping-cart"></i>
									Add To Cart
								</a>
								<a class="link-a more-info"
								   href="<?php echo "/{$relatedTour['Event']['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$relatedTour['Event']['url_name']}" ?>"
								   onclick="Tour.ModalCheckout.bookingModalOption(event, this)"
								   data-position="<?php echo $j ?>"
								   data-name="<?php echo $relatedTour['Event']['name_listing'] != '' ? $relatedTour['Event']['name_listing'] : $relatedTour['Event']['name_short'] ?>"
								   data-id="<?php echo $relatedTour['Event']['id'] ?>"
								   data-sku="<?php echo $relatedTour['Event']['sku'] ?>"
								   data-description="Clicked bundle modal tour <?php echo $j ?>"
								   data-price="<?php echo $totalTotalPriceDiscount ?>"
								   data-href="<?php echo "/{$relatedTour['Event']['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$relatedTour['Event']['url_name']}" ?>"
								>
									<i class="fa fa-info-circle"></i>
									More Info
								</a>
								<form id="form_<?php echo $relatedTour['Event']['id']; ?>" action="/add_to_cart_modal" method="post" style="display: none;">
									<input type="hidden" name="date">
									<input type="hidden" name="time">
									<input type="hidden" name="event_name" value="<?php echo $event['name_listing'] != '' ? $event['name_listing'] : $event['name_short'] ?>">
									<input type="hidden" name="event_id" value="<?php echo $event['id'] ?>">
									<input type="hidden" name="modal_redirect">
									<input type="hidden" name="modal_adults">
									<input type="hidden" name="modal_seniors">
									<input type="hidden" name="modal_students">
									<input type="hidden" name="modal_children">
									<input type="hidden" name="modal_infants">
									<input type="hidden" name="modal_date_time">
									<input type="hidden" name="modal_event_price">
									<input type="hidden" name="modal_type" value="group">
									<input type="hidden" name="modal_event_id" value="<?php echo $relatedTour['Event']['id']; ?>">
									<input type="hidden" name="modal_event_sku" value="<?php echo $relatedTour['Event']['sku']; ?>">
									<input type="hidden" name="modal_discount" value="<?php echo $discountPercentRelatedTour; ?>">
									<input type="hidden" name="modal_event_name" value="<?php echo $relatedTour['Event']['name_listing'] != '' ? $relatedTour['Event']['name_listing'] : $relatedTour['Event']['name_short'] ?>">
								</form>
							</div>
						</div>
					</div>
					<?php endif; endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
