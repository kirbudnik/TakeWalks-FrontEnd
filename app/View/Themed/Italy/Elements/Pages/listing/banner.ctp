<?php $item = $pageUrl . '-' . $position ?>

<?php if($item == 'rome-tours-1'): ?>
    <div class="banner rome-tour">
        <div class="red-cover">
            <div class="header">Not sure which colosseum tour is for you?</div>
            <div class="text">
                Or which is available on your dates?<br />
                See details of all tours and<br />
                a calendar of events here:
            </div>
            <a href="/colosseum-tours-compare">HELP ME</a>
        </div>
        <div class="img"></div>

    </div>
<?php elseif($item == 'rome-tours-3'): ?>
    <div class="banner rome-tour-sub">
        <div class="red-cover">
            <div class="header">Not sure which vatican tour is for you?</div>
            <div class="sub-header">Or which is available on your dates?</div>
            <div class="text">See details of all tours and a calendar of events here</div>
            <a href="/vatican-tours-compare">HELP ME</a>
        </div>
    </div>
<?php elseif($item == 'vatican-tours-1'): ?>
    <div class="banner vatican-tour">
        <div class="red-cover">
            <div class="header">Not sure which vatican tour is for you?</div>
            <div class="text">
                Or which is available on your dates?<br />
                See details of all tours and<br />
                a calendar of events here:
            </div>
            <a href="/vatican-tours-compare">HELP ME</a>
        </div>
        <div class="img"></div>
    </div>
<?php elseif($item == 'venice-tours-1'): ?>
    <div class="banner venice-tour">
        <div class="red-cover">
            <div class="header">Not sure which st mark's basilica<br> & doge's palace tour is for you?</div>
            <div class="text">
                Or which is available on your dates?<br />
                See details of all tours and<br />
                a calendar of events here:
            </div>
            <a href="/st-marks-doges-palace-tours-compare">HELP ME</a>
        </div>
        <div class="img"></div>
    </div>
<?php endif ?>
