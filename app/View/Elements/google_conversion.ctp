<?php if($domain == 'italy'): ?>

    <!-- Google Code for Reach Payment Conversion Page -->
    <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 1017871724;
        var google_conversion_language = "en";
        var google_conversion_format = "2";
        var google_conversion_color = "ffffff";
        var google_conversion_label = "AXg8CPyDwgIQ7Pqt5QM";
        var google_conversion_value = <?php echo $item_price ?>;
        var google_conversion_currency = "<?php echo ExchangeRate::getCurrency() ?>";
        /* ]]> */
    </script>
    <script type="text/javascript"
            src="https://www.googleadservices.com/pagead/conversion.js">
    </script>


    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/1017871724/?label=AXg8CPyDwgIQ7Pqt5QM&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>
<?php elseif($domain == 'turkey'): ?>
    <!-- Google Code for PX - Conversion Conversion Page -->
    <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 953458821;
        var google_conversion_language = "en";
        var google_conversion_format = "3";
        var google_conversion_color = "ffffff";
        var google_conversion_label = "wxgYCLmC5loQhcHSxgM";
        var google_conversion_value = <?php echo $item_price ?>;
        var google_conversion_currency = "<?php echo ExchangeRate::getCurrency() ?>";
        var google_remarketing_only = false;
        /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/953458821/?value=1.00&amp;currency_code=USD&amp;label=wxgYCLmC5loQhcHSxgM&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>

<?php elseif($domain == 'nyc'): ?>
    <!-- Google Code for Google Tag Manager Purchase qf Conversion Page -->
    <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 989020814;
        var google_conversion_language = "en";
        var google_conversion_format = "3";
        var google_conversion_color = "ffffff";
        var google_conversion_label = "qFcdCPKahAkQjoXN1wM";
        var google_conversion_value = <?php echo $item_price ?>;
        var google_conversion_currency = "<?php echo ExchangeRate::getCurrency() ?>";
        var google_remarketing_only = false;
        /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/989020814/?value=0.00&amp;currency_code=USD&amp;label=qFcdCPKahAkQjoXN1wM&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>
<?php endif ?>
