<?= $this->element('header'); ?>
<?= $this->element('account-header',['selectedPage' => 'wishlist']); ?>

<?php $this->start('scripts'); ?>
    <script>

        var WishList = {
            init: function() {
                $('[data-event-id]').click(WishList.remove).css({cursor:'pointer'});
            },
            remove: function(event) {
                var eventId = event.currentTarget.dataset.eventId;
                $('[data-tour-id=' + eventId + ']').remove();
                $('body').css({cursor:'wait'});
                $.ajax({ data: {event_id: eventId}, url: '/user/wishlist/remove', method: 'post' }
                ).done(function(response) {
                    $('body').css({cursor:'default'});
                }).fail(function(response){
                    $('body').css({cursor:'default'});
                    window.location.reload();
                });
            }
        };

        $(document).ready(function(){
            WishList.init();
        });
    </script>
<?php $this->end(); ?>

    <section class="grey bordered">
        <?php if (empty($wishlistTours)): ?>
            <div class="account-no-content">
                <p class="descr">You don't have tours in your Wishlist yet!</p>
                <div class="center-btn small">
                    <a href="/" class="btn secondary green">Find A Tour</a>
                </div>
            </div>
        <?php else: ?>
            <div class="upcoming-tours">
                <div class="container">
                    <div class="wishlist-tours">

                        <?php foreach ($wishlistTours as $wishlistTour): ?>
                            <div class="wishlist-tour" data-tour-id="<?= $wishlistTour['event_id']; ?>">
                                <div class="tour-img" style="background-image: url(<?= $wishlistTour['image']; ?>)">
                                </div>
                                <div class="tour-details">
                                    <div class="tour-price"><span class="default-price">$<?= $wishlistTour['price']; ?></span></div>
                                    <h3 class="tour-title"><?= $wishlistTour['title']; ?></h3>
                                    <p class="descr"><?= $wishlistTour['description']; ?></p>
                                    <div class="tour-footer">
                                        <a data-event-id="<?= $wishlistTour['event_id']; ?>" class="remove-underlined"><i class="icon icon-remove_tour"></i> Remove from wishlist</a>
                                        <a href="<?= DS . $wishlistTour['citySlug']. DS . $wishlistTour['slug']; ?>" class="btn secondary purple">View Tour</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!--
                                <div class="wishlist-tour">
                                  <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour1.jpg)">
                                  </div>
                                  <div class="tour-details">
                                    <div class="tour-price"><span class="default-price">$35</span></div>
                                    <h3 class="tour-title">The Complete Vatican Tour with Vatican Museums</h3>
                                    <p class="descr">
                                      Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec.
                                    </p>
                                    <div class="tour-footer">
                                      <a href="#" class="remove-underlined"><i class="icon icon-remove_tour"></i> Remove from wishlist</a>
                                      <a href="#" class="btn secondary purple">View Tour</a>
                                    </div>
                                  </div>
                                </div>
                                <div class="wishlist-tour">
                                  <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour2.jpg)">
                                  </div>
                                  <div class="tour-details">
                                    <div class="tour-price"><span class="default-price">$49</span></div>
                                    <h3 class="tour-title">Rome Catacombs at Night</h3>
                                    <p class="descr">
                                      Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                                    </p>
                                    <div class="tour-footer">
                                      <a href="#" class="remove-underlined"><i class="icon icon-remove_tour"></i> Remove from wishlist</a>
                                      <a href="#" class="btn secondary purple">View Tour</a>
                                    </div>

                                  </div>
                                </div>
                                <div class="wishlist-tour">
                                  <div class="tour-img" style="background-image: url(/theme/TakeWalks/img/tours/tour3.jpg)">
                                  </div>
                                  <div class="tour-details">
                                    <div class="tour-price"><span class="default-price">$24</span></div>
                                    <h3 class="tour-title">Caesar's Forum at Night</h3>
                                    <p class="descr">
                                      Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec mass soccis natoque.
                                    </p>
                                    <div class="tour-footer">
                                      <a href="#" class="remove-underlined"><i class="icon icon-remove_tour"></i> Remove from wishlist</a>
                                      <a href="#" class="btn secondary purple">View Tour</a>
                                    </div>
                                  </div>
                                </div>
                                  -->
                    </div>
                </div>
            </div>
        <?php endif ?>

    </section>
<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
