<?= $this->element('header'); ?>

<main class="default">
  <div class="container">
    <h1 class="page-title">Password Reset</h1>
  </div>

  <section class="grey">
    <div class="container">
 
<?php
if ($hashNotFound){
?>

     <div class="guide-description">
        <p class="descr">The reset link from your email is not longer active, please try again.<br /><br /></p>
      </div>
<?php
} else {
?>

     <div class="guide-description">
        <p class="descr"><b><?=$client->fname?></b>, please use the following form to reset your password<br /><br /></p>
            <form method="post" id="formPayment">
                <input type="hidden" name="reset_hash" value="<?=$client->reset_hash?>" >
                <input type="hidden" name="id" value="<?=$client->id?>" >
                <?php// pr($client); ?>

                <div class="input-row auto foo-validate">
                    <div class="input-div input-icon">
                        <input type="password" name="password" placeholder="New Password" value="" required maxlength="100" 
                        required pattern=".{6,}"
                           oninvalid="setCustomValidity('6 or more characters please.')"
                           onchange="try{setCustomValidity('')}catch(e){}">
                    </div>
                </div>
                <div class="input-row auto foo-validate">
                    <div class="input-div input-icon">
                        <input type="password" name="verifypassword" placeholder="Verify Password" value="" required maxlength="100" 
                         required pattern=".{6,}"
                           oninvalid="setCustomValidity('6 or more characters please.')"
                           onchange="try{setCustomValidity('')}catch(e){}">
                    </div>
                </div>
                <div class="center-btn medium">
                    <div class="error-message">Invalid something.</div>
                    <button class="btn primary green">Update Password</button>
                </div>
            </form>
      </div>
<?php
}
?>






    </div>
  </section>
</main>

<?= $this->element('footer'); ?>
<?= $this->element('cart-dropdown'); ?>
