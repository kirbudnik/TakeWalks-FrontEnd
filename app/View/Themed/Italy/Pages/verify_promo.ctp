<div id="root">

    <nav id="breadcrumbs" class="transfer-bc">
        <div>
            <h2>Verify your discount</h2>
        </div>
    </nav>

    <section id="content">
        <div id="chooseBook">
            Which book did you purchase?<br />
            <select placeholder="Choose a book">
                <option></option>
            </select>
            <br/><br/>
            <div id="question">
                <form action="/pages/apply_promo" method="post">
                    <input type="hidden" name="promo" value="<?php echo $promoCode; ?>">
                    <input type="hidden" name="question_id">
                    <div class="bookQuestion">

                    </div>
                    <input type="text" name="answer">
                    <br />
                    <button>Verify</button>
                    <button class="goback">Go Back</button>
                </form>
                <form action="/payment" method="post" id="goback_form">
                    <?php if(isset($postDataPromoCode)){ foreach($postDataPromoCode as $n => $v ){ ?>
                    <input type="hidden" name="<?php echo $n; ?>" value="<?php echo $v; ?>">
                    <?php } }?>
                </form>
            </div>
            <button class="goback active" style="margin-top: 10px;">Go Back</button>
        </div>
    </section>
</div>