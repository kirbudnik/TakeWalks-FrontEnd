<section class="hero">
	<div class="wrap content">
		<h1 class="larger">My Account</h1>
	</div>
</section>

<div class="content wrap">

	<main>

		<form class="account" method="post">
            <h3 class="large">Update Account: <?php echo $user['email'] ?></h3>
			<fieldset>
                <div class="row">
                    <label class="half">
                        First Name
                        <input type="text" name="firstname" placeholder="(None specified)" value="<?php echo $user['fname'] ?>">
                    </label>
                    <label class="half">
                        Last Name
                        <input type="text" name="lastname" placeholder="(None specified)" value="<?php echo $user['lname'] ?>">
                    </label>
                </div>
			</fieldset>
            <input class="large blue button" type="submit" value="Update Account">

            <h3 class="large">Change Password</h3>
			<fieldset>
				<label>
					Current password
					<input type="password" name="password">
				</label>
				<div class="row">
					<label class="half">
						New password
						<input type="password" name="password_new">
					</label>
					<label class="half">
						Verify new password
						<input type="password" name="password_verify">
					</label>
				</div>
			</fieldset>

			<input class="large blue button" type="submit" value="Update Password">
		</form>

	</main>
	<aside>
    <?php echo $this->element('static') ?>
