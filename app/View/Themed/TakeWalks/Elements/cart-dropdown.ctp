<div class="sidebar shopping-cart hidden">
    <div class="sidebar-heading">
        <h2 class="heading">Cart</h2>
        <div class="close-cart">
            <i class="icon icon-close icon-grey"></i>
        </div>
    </div>

    <div class="cart-item-container">

    </div>

    <div class="right-sidebar-item">
        <div class="sidebar-content cta">
            <h5 class="subtitle grey">Subtotal</h5>
            <h2 class="subtotal-price default">Empty</h2>
            <a href="/payment" class="btn primary purple">Checkout</a>
        </div>
    </div>
</div>

<?= $this->element('templates/cart-item'); ?>
