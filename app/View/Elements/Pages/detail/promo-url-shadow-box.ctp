<?php
$promoCodeUrlShadowBox = CakeSession::read('promoCodeUrlShadowBox');
CakeSession::delete('promoCodeUrlShadowBox');
?>

<div class="modal-wrap" data-modal-target="congratulationsModal" id="divCongratulationsModal">
    <div class="modal-inner">
        <div class="modal-content">
            <div class="modal-close" data-modal-close  data-description="Closed modal" ></div>
            <input id="congratulationsModal" type="hidden" value="<?php echo $promoCodeUrlShadowBox ?>"/>
            <?php
            if($promoCodeUrlShadowBox == 1){
            ?>
                <div class="modal-header">
                    <h2>Congratulations!</h2>
                </div>

                <div class="modal-summary">
                    <p>
                        You have activated a special promotion. You won't see it on the tour page but your discount will be applied in the checkout process, before you make your payment.
                    </p>
                </div>
            <?php
            } else {
            ?>
                <div class="modal-header">
                    <h2>Invalid Promo Code!</h2>
                </div>

                <div class="modal-summary">
                    <p>
                        Sorry, the promo code is invalid. A discount will not be applied.
                    </p>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
