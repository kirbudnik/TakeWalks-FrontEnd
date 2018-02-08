<?php
$full_price = 0;
$full_price_usd = 0;
foreach($cart as  $n => $item) {

    if(isset($item['promo_local'])) {
        //$cart[$n]['total_price'] = $item['promo_local'];
        $cart[$n]['charged_usd_amount'] = $item['promo_local'] * $item['exchange_rate'];
    }

    $full_price += isset($item['promo_local']) ? $item['promo_local'] : $item['total_price'];
    $full_price_usd += $cart[$n]['charged_usd_amount'];
}
?>

<p id="title"><i class="fa fa-shield"></i> Su información está asegurada por el software de criptografía "SSL" conforme con el PCI</p>
<section id="content" class="columns-c payment-form" data-full-price-euro="<?php echo $full_price ?>" data-full-price-usd="<?php echo $full_price_usd ?>">
    <form method="post" class="form-c" novalidate id="travellerInfo" onsubmit="return ecCheckout();">
        <input type="hidden" name="currency">
        <fieldset>

            <?php if(isset($booking_id)): ?>
                <input type="hidden" name="booking_id" value="<?php echo $booking_id ?>" />
            <?php endif ?>
            <input type="hidden" name="price" value="<?php echo $full_price ?>" />
            <h2>Comprar</h2>
            <h3>Información del viajero</h3>
            <p>
                <span class="fname">
                    <label for="fca">Nombre</label>
                    <input type="text" id="fca" name="first_name" value="<?php if(isset($_POST['first_name'])) echo $_POST['first_name'] ?>" required>
                </span>
                <span class="lname">
                    <label for="fcb">Apellido</label>
                    <input type="text" id="fcb" name="last_name" value="<?php if(isset($_POST['last_name'])) echo $_POST['last_name'] ?>" required>
                </span>
            </p>
            <p class="thirds">
                <span>
                    <label for="fcc">Email</label>
                    <input type="email" id="fcc" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email'] ?>" maxlength="50" required>
                </span>
                <span>
                    <label for="fccc">Confirmar Email</label>
                    <input type="email" id="fccc" name="confirm_email" value="<?php if(isset($_POST['confirm_email'])) echo $_POST['confirm_email'] ?>" maxlength="50" required>
                </span>
                <span>
                    <label for="fcd">Teléfono</label>
                    <input type="tel" id="fcd" name="phone_number" value="<?php if(isset($_POST['phone_number'])) echo $_POST['phone_number'] ?>" maxlength="14" required>
                </span>
            </p>
            <p>
                <label for="fce">Dirección</label>
                <input type="text" id="fce" name="street_address" value="<?php if(isset($_POST['street_address'])) echo $_POST['street_address'] ?>" maxlength="30" required>
            </p>
            <p>
                <label for="fci" class="hidden">País</label>
                <select id="fci" name="country" required class="noDefaultSelect2" data-placeholder="País">
                    <option></option>
                    <option value="AF" <?php if(isset($_POST['country']) && $_POST['country'] == 'AF') echo 'selected' ?>>Afghanistan</option>
                    <option value="AL" <?php if(isset($_POST['country']) && $_POST['country'] == 'AL') echo 'selected' ?>>Albania</option>
                    <option value="DZ" <?php if(isset($_POST['country']) && $_POST['country'] == 'DZ') echo 'selected' ?>>Algeria</option>
                    <option value="AS" <?php if(isset($_POST['country']) && $_POST['country'] == 'AS') echo 'selected' ?>>American Samoa</option>
                    <option value="AD" <?php if(isset($_POST['country']) && $_POST['country'] == 'AD') echo 'selected' ?>>Andorra</option>
                    <option value="AO" <?php if(isset($_POST['country']) && $_POST['country'] == 'AO') echo 'selected' ?>>Angola</option>
                    <option value="AL" <?php if(isset($_POST['country']) && $_POST['country'] == 'AL') echo 'selected' ?>>Anguilla</option>
                    <option value="AQ" <?php if(isset($_POST['country']) && $_POST['country'] == 'AQ') echo 'selected' ?>>Antarctica</option>
                    <option value="AG" <?php if(isset($_POST['country']) && $_POST['country'] == 'AG') echo 'selected' ?>>Antigua</option>
                    <option value="AR" <?php if(isset($_POST['country']) && $_POST['country'] == 'AR') echo 'selected' ?>>Argentina</option>
                    <option value="AM" <?php if(isset($_POST['country']) && $_POST['country'] == 'AM') echo 'selected' ?>>Armenia</option>
                    <option value="AW" <?php if(isset($_POST['country']) && $_POST['country'] == 'AW') echo 'selected' ?>>Aruba</option>
                    <option value="AU" <?php if(isset($_POST['country']) && $_POST['country'] == 'AU') echo 'selected' ?>>Australia</option>
                    <option value="AT" <?php if(isset($_POST['country']) && $_POST['country'] == 'AT') echo 'selected' ?>>Austria</option>
                    <option value="AZ" <?php if(isset($_POST['country']) && $_POST['country'] == 'AZ') echo 'selected' ?>>Azerbaijan</option>
                    <option value="BS" <?php if(isset($_POST['country']) && $_POST['country'] == 'BS') echo 'selected' ?>>Bahamas</option>
                    <option value="BH" <?php if(isset($_POST['country']) && $_POST['country'] == 'BH') echo 'selected' ?>>Bahrain</option>
                    <option value="BD" <?php if(isset($_POST['country']) && $_POST['country'] == 'BD') echo 'selected' ?>>Bangladesh</option>
                    <option value="BB" <?php if(isset($_POST['country']) && $_POST['country'] == 'BB') echo 'selected' ?>>Barbados</option>
                    <option value="AG" <?php if(isset($_POST['country']) && $_POST['country'] == 'AG') echo 'selected' ?>>Barbuda</option>
                    <option value="BY" <?php if(isset($_POST['country']) && $_POST['country'] == 'BY') echo 'selected' ?>>Belarus</option>
                    <option value="BE" <?php if(isset($_POST['country']) && $_POST['country'] == 'BE') echo 'selected' ?>>Belgium</option>
                    <option value="BZ" <?php if(isset($_POST['country']) && $_POST['country'] == 'BZ') echo 'selected' ?>>Belize</option>
                    <option value="BJ" <?php if(isset($_POST['country']) && $_POST['country'] == 'BJ') echo 'selected' ?>>Benin</option>
                    <option value="BM" <?php if(isset($_POST['country']) && $_POST['country'] == 'BM') echo 'selected' ?>>Bermuda</option>
                    <option value="BT" <?php if(isset($_POST['country']) && $_POST['country'] == 'BT') echo 'selected' ?>>Bhutan</option>
                    <option value="BO" <?php if(isset($_POST['country']) && $_POST['country'] == 'BO') echo 'selected' ?>>Bolivia</option>
                    <option value="AN" <?php if(isset($_POST['country']) && $_POST['country'] == 'AN') echo 'selected' ?>>Bonaire</option>
                    <option value="BA" <?php if(isset($_POST['country']) && $_POST['country'] == 'BA') echo 'selected' ?>>Bosnia-Herzegovina</option>
                    <option value="BW" <?php if(isset($_POST['country']) && $_POST['country'] == 'BW') echo 'selected' ?>>Botswana</option>
                    <option value="BV" <?php if(isset($_POST['country']) && $_POST['country'] == 'BV') echo 'selected' ?>>Bouvet Island</option>
                    <option value="BR" <?php if(isset($_POST['country']) && $_POST['country'] == 'BR') echo 'selected' ?>>Brazil</option>
                    <option value="IO" <?php if(isset($_POST['country']) && $_POST['country'] == 'IO') echo 'selected' ?>>British Indian Ocean</option>
                    <option value="VG" <?php if(isset($_POST['country']) && $_POST['country'] == 'VG') echo 'selected' ?>>British Virgin Islan</option>
                    <option value="BN" <?php if(isset($_POST['country']) && $_POST['country'] == 'BN') echo 'selected' ?>>Brunei</option>
                    <option value="BG" <?php if(isset($_POST['country']) && $_POST['country'] == 'BG') echo 'selected' ?>>Bulgaria</option>
                    <option value="BF" <?php if(isset($_POST['country']) && $_POST['country'] == 'BF') echo 'selected' ?>>Burkina Faso</option>
                    <option value="BI" <?php if(isset($_POST['country']) && $_POST['country'] == 'BI') echo 'selected' ?>>Burundi</option>
                    <option value="KH" <?php if(isset($_POST['country']) && $_POST['country'] == 'KH') echo 'selected' ?>>Cambodia</option>
                    <option value="CM" <?php if(isset($_POST['country']) && $_POST['country'] == 'CM') echo 'selected' ?>>Cameroon</option>
                    <option value="CA" <?php if(isset($_POST['country']) && $_POST['country'] == 'CA') echo 'selected' ?>>Canada</option>
                    <option value="ES" <?php if(isset($_POST['country']) && $_POST['country'] == 'ES') echo 'selected' ?>>Canary Islands</option>
                    <option value="CV" <?php if(isset($_POST['country']) && $_POST['country'] == 'CV') echo 'selected' ?>>Cape Verde</option>
                    <option value="KY" <?php if(isset($_POST['country']) && $_POST['country'] == 'KY') echo 'selected' ?>>Cayman Islands</option>
                    <option value="CF" <?php if(isset($_POST['country']) && $_POST['country'] == 'CF') echo 'selected' ?>>Central African Repu</option>
                    <option value="TD" <?php if(isset($_POST['country']) && $_POST['country'] == 'TD') echo 'selected' ?>>Chad</option>
                    <option value="GB" <?php if(isset($_POST['country']) && $_POST['country'] == 'GB') echo 'selected' ?>>Channel Islands</option>
                    <option value="CL" <?php if(isset($_POST['country']) && $_POST['country'] == 'CL') echo 'selected' ?>>Chile</option>
                    <option value="CN" <?php if(isset($_POST['country']) && $_POST['country'] == 'CN') echo 'selected' ?>>China</option>
                    <option value="CX" <?php if(isset($_POST['country']) && $_POST['country'] == 'CX') echo 'selected' ?>>Christmas Island</option>
                    <option value="CC" <?php if(isset($_POST['country']) && $_POST['country'] == 'CC') echo 'selected' ?>>Cocos (Keeling) Isla</option>
                    <option value="CO" <?php if(isset($_POST['country']) && $_POST['country'] == 'CO') echo 'selected' ?>>Colombia</option>
                    <option value="KM" <?php if(isset($_POST['country']) && $_POST['country'] == 'KM') echo 'selected' ?>>Comoros</option>
                    <option value="CG" <?php if(isset($_POST['country']) && $_POST['country'] == 'CG') echo 'selected' ?>>Congo</option>
                    <option value="CD" <?php if(isset($_POST['country']) && $_POST['country'] == 'CD') echo 'selected' ?>>Congo Dem. Rep. Of</option>
                    <option value="CK" <?php if(isset($_POST['country']) && $_POST['country'] == 'CK') echo 'selected' ?>>Cook Islands</option>
                    <option value="CR" <?php if(isset($_POST['country']) && $_POST['country'] == 'CR') echo 'selected' ?>>Costa Rica</option>
                    <option value="HR" <?php if(isset($_POST['country']) && $_POST['country'] == 'HR') echo 'selected' ?>>Croatia</option>
                    <option value="CU" <?php if(isset($_POST['country']) && $_POST['country'] == 'CU') echo 'selected' ?>>Cuba</option>
                    <option value="AN" <?php if(isset($_POST['country']) && $_POST['country'] == 'AN') echo 'selected' ?>>Curacao</option>
                    <option value="CY" <?php if(isset($_POST['country']) && $_POST['country'] == 'CY') echo 'selected' ?>>Cyprus</option>
                    <option value="CZ" <?php if(isset($_POST['country']) && $_POST['country'] == 'CZ') echo 'selected' ?>>Czech Republic</option>
                    <option value="DK" <?php if(isset($_POST['country']) && $_POST['country'] == 'DK') echo 'selected' ?>>Denmark</option>
                    <option value="DJ" <?php if(isset($_POST['country']) && $_POST['country'] == 'DJ') echo 'selected' ?>>Djibouti</option>
                    <option value="DM" <?php if(isset($_POST['country']) && $_POST['country'] == 'DM') echo 'selected' ?>>Dominica</option>
                    <option value="DO" <?php if(isset($_POST['country']) && $_POST['country'] == 'DO') echo 'selected' ?>>Dominican Republic</option>
                    <option value="TL" <?php if(isset($_POST['country']) && $_POST['country'] == 'TL') echo 'selected' ?>>East Timor</option>
                    <option value="EC" <?php if(isset($_POST['country']) && $_POST['country'] == 'EC') echo 'selected' ?>>Ecuador</option>
                    <option value="EG" <?php if(isset($_POST['country']) && $_POST['country'] == 'EG') echo 'selected' ?>>Egypt</option>
                    <option value="SV" <?php if(isset($_POST['country']) && $_POST['country'] == 'SV') echo 'selected' ?>>El Salvador</option>
                    <option value="GB" <?php if(isset($_POST['country']) && $_POST['country'] == 'GB') echo 'selected' ?>>England</option>
                    <option value="GQ" <?php if(isset($_POST['country']) && $_POST['country'] == 'GQ') echo 'selected' ?>>Equatorial Guinea</option>
                    <option value="ER" <?php if(isset($_POST['country']) && $_POST['country'] == 'ER') echo 'selected' ?>>Eritrea</option>
                    <option value="EE" <?php if(isset($_POST['country']) && $_POST['country'] == 'EE') echo 'selected' ?>>Estonia</option>
                    <option value="ET" <?php if(isset($_POST['country']) && $_POST['country'] == 'ET') echo 'selected' ?>>Ethiopia</option>
                    <option value="FO" <?php if(isset($_POST['country']) && $_POST['country'] == 'FO') echo 'selected' ?>>Faeroe Islands</option>
                    <option value="FK" <?php if(isset($_POST['country']) && $_POST['country'] == 'FK') echo 'selected' ?>>Falkland Islands</option>
                    <option value="FJ" <?php if(isset($_POST['country']) && $_POST['country'] == 'FJ') echo 'selected' ?>>Fiji</option>
                    <option value="FI" <?php if(isset($_POST['country']) && $_POST['country'] == 'FI') echo 'selected' ?>>Finland</option>
                    <option value="FR" <?php if(isset($_POST['country']) && $_POST['country'] == 'FR') echo 'selected' ?>>France</option>
                    <option value="GF" <?php if(isset($_POST['country']) && $_POST['country'] == 'GF') echo 'selected' ?>>French Guiana</option>
                    <option value="PF" <?php if(isset($_POST['country']) && $_POST['country'] == 'PF') echo 'selected' ?>>French Polynesia</option>
                    <option value="TF" <?php if(isset($_POST['country']) && $_POST['country'] == 'TF') echo 'selected' ?>>French Southern Terr</option>
                    <option value="GA" <?php if(isset($_POST['country']) && $_POST['country'] == 'GA') echo 'selected' ?>>Gabon</option>
                    <option value="GM" <?php if(isset($_POST['country']) && $_POST['country'] == 'GM') echo 'selected' ?>>Gambia</option>
                    <option value="GE" <?php if(isset($_POST['country']) && $_POST['country'] == 'GE') echo 'selected' ?>>Georgia</option>
                    <option value="DE" <?php if(isset($_POST['country']) && $_POST['country'] == 'DE') echo 'selected' ?>>Germany</option>
                    <option value="GH" <?php if(isset($_POST['country']) && $_POST['country'] == 'GH') echo 'selected' ?>>Ghana</option>
                    <option value="GI" <?php if(isset($_POST['country']) && $_POST['country'] == 'GI') echo 'selected' ?>>Gibraltar</option>
                    <option value="KY" <?php if(isset($_POST['country']) && $_POST['country'] == 'KY') echo 'selected' ?>>Grand Cayman</option>
                    <option value="GB" <?php if(isset($_POST['country']) && $_POST['country'] == 'GB') echo 'selected' ?>>Great Britain</option>
                    <option value="VG" <?php if(isset($_POST['country']) && $_POST['country'] == 'VG') echo 'selected' ?>>Great Thatch Island</option>
                    <option value="VG" <?php if(isset($_POST['country']) && $_POST['country'] == 'VG') echo 'selected' ?>>Great Tobago Islands</option>
                    <option value="GR" <?php if(isset($_POST['country']) && $_POST['country'] == 'GR') echo 'selected' ?>>Greece</option>
                    <option value="GL" <?php if(isset($_POST['country']) && $_POST['country'] == 'GL') echo 'selected' ?>>Greenland</option>
                    <option value="GD" <?php if(isset($_POST['country']) && $_POST['country'] == 'GD') echo 'selected' ?>>Grenada</option>
                    <option value="GP" <?php if(isset($_POST['country']) && $_POST['country'] == 'GP') echo 'selected' ?>>Guadeloupe</option>
                    <option value="GU" <?php if(isset($_POST['country']) && $_POST['country'] == 'GU') echo 'selected' ?>>Guam</option>
                    <option value="GT" <?php if(isset($_POST['country']) && $_POST['country'] == 'GT') echo 'selected' ?>>Guatemala</option>
                    <option value="GN" <?php if(isset($_POST['country']) && $_POST['country'] == 'GN') echo 'selected' ?>>Guinea</option>
                    <option value="GW" <?php if(isset($_POST['country']) && $_POST['country'] == 'GW') echo 'selected' ?>>Guinea Bissau</option>
                    <option value="GY" <?php if(isset($_POST['country']) && $_POST['country'] == 'GY') echo 'selected' ?>>Guyana</option>
                    <option value="HT" <?php if(isset($_POST['country']) && $_POST['country'] == 'HT') echo 'selected' ?>>Haiti</option>
                    <option value="HM" <?php if(isset($_POST['country']) && $_POST['country'] == 'HM') echo 'selected' ?>>Heard & McDonald Isl</option>
                    <option value="NL" <?php if(isset($_POST['country']) && $_POST['country'] == 'NL') echo 'selected' ?>>Holland</option>
                    <option value="HN" <?php if(isset($_POST['country']) && $_POST['country'] == 'HN') echo 'selected' ?>>Honduras</option>
                    <option value="HK" <?php if(isset($_POST['country']) && $_POST['country'] == 'HK') echo 'selected' ?>>Hong Kong</option>
                    <option value="HU" <?php if(isset($_POST['country']) && $_POST['country'] == 'HU') echo 'selected' ?>>Hungary</option>
                    <option value="IS" <?php if(isset($_POST['country']) && $_POST['country'] == 'IS') echo 'selected' ?>>Iceland</option>
                    <option value="IN" <?php if(isset($_POST['country']) && $_POST['country'] == 'IN') echo 'selected' ?>>India</option>
                    <option value="ID" <?php if(isset($_POST['country']) && $_POST['country'] == 'ID') echo 'selected' ?>>Indonesia</option>
                    <option value="IR" <?php if(isset($_POST['country']) && $_POST['country'] == 'IR') echo 'selected' ?>>Iran</option>
                    <option value="IQ" <?php if(isset($_POST['country']) && $_POST['country'] == 'IQ') echo 'selected' ?>>Iraq</option>
                    <option value="IE" <?php if(isset($_POST['country']) && $_POST['country'] == 'IE') echo 'selected' ?>>Ireland</option>
                    <option value="IL" <?php if(isset($_POST['country']) && $_POST['country'] == 'IL') echo 'selected' ?>>Israel</option>
                    <option value="IT" <?php if(isset($_POST['country']) && $_POST['country'] == 'IT') echo 'selected' ?>>Italy</option>
                    <option value="CI" <?php if(isset($_POST['country']) && $_POST['country'] == 'CI') echo 'selected' ?>>Ivory Coast</option>
                    <option value="JM" <?php if(isset($_POST['country']) && $_POST['country'] == 'JM') echo 'selected' ?>>Jamaica</option>
                    <option value="JP" <?php if(isset($_POST['country']) && $_POST['country'] == 'JP') echo 'selected' ?>>Japan</option>
                    <option value="JO" <?php if(isset($_POST['country']) && $_POST['country'] == 'JO') echo 'selected' ?>>Jordan</option>
                    <option value="VG" <?php if(isset($_POST['country']) && $_POST['country'] == 'VG') echo 'selected' ?>>Jost Van Dyke Island</option>
                    <option value="KZ" <?php if(isset($_POST['country']) && $_POST['country'] == 'KZ') echo 'selected' ?>>Kazakhstan</option>
                    <option value="KE" <?php if(isset($_POST['country']) && $_POST['country'] == 'KE') echo 'selected' ?>>Kenya</option>
                    <option value="KI" <?php if(isset($_POST['country']) && $_POST['country'] == 'KI') echo 'selected' ?>>Kiribati</option>
                    <option value="KI" <?php if(isset($_POST['country']) && $_POST['country'] == 'KI') echo 'selected' ?>>Kiribati</option>
                    <option value="KW" <?php if(isset($_POST['country']) && $_POST['country'] == 'KW') echo 'selected' ?>>Kuwait</option>
                    <option value="KG" <?php if(isset($_POST['country']) && $_POST['country'] == 'KG') echo 'selected' ?>>Kyrgyzstan</option>
                    <option value="LA" <?php if(isset($_POST['country']) && $_POST['country'] == 'LA') echo 'selected' ?>>Laos</option>
                    <option value="LV" <?php if(isset($_POST['country']) && $_POST['country'] == 'LV') echo 'selected' ?>>Latvia</option>
                    <option value="LB" <?php if(isset($_POST['country']) && $_POST['country'] == 'LB') echo 'selected' ?>>Lebanon</option>
                    <option value="LS" <?php if(isset($_POST['country']) && $_POST['country'] == 'LS') echo 'selected' ?>>Lesotho</option>
                    <option value="LR" <?php if(isset($_POST['country']) && $_POST['country'] == 'LR') echo 'selected' ?>>Liberia</option>
                    <option value="LY" <?php if(isset($_POST['country']) && $_POST['country'] == 'LY') echo 'selected' ?>>Libya</option>
                    <option value="LI" <?php if(isset($_POST['country']) && $_POST['country'] == 'LI') echo 'selected' ?>>Liechtenstein</option>
                    <option value="LT" <?php if(isset($_POST['country']) && $_POST['country'] == 'LT') echo 'selected' ?>>Lithuania</option>
                    <option value="LU" <?php if(isset($_POST['country']) && $_POST['country'] == 'LU') echo 'selected' ?>>Luxembourg</option>
                    <option value="MO" <?php if(isset($_POST['country']) && $_POST['country'] == 'MO') echo 'selected' ?>>Macau</option>
                    <option value="MK" <?php if(isset($_POST['country']) && $_POST['country'] == 'MK') echo 'selected' ?>>Macedonia</option>
                    <option value="MG" <?php if(isset($_POST['country']) && $_POST['country'] == 'MG') echo 'selected' ?>>Madagascar</option>
                    <option value="MW" <?php if(isset($_POST['country']) && $_POST['country'] == 'MW') echo 'selected' ?>>Malawi</option>
                    <option value="MY" <?php if(isset($_POST['country']) && $_POST['country'] == 'MY') echo 'selected' ?>>Malaysia</option>
                    <option value="MV" <?php if(isset($_POST['country']) && $_POST['country'] == 'MV') echo 'selected' ?>>Maldives</option>
                    <option value="ML" <?php if(isset($_POST['country']) && $_POST['country'] == 'ML') echo 'selected' ?>>Mali</option>
                    <option value="MT" <?php if(isset($_POST['country']) && $_POST['country'] == 'MT') echo 'selected' ?>>Malta</option>
                    <option value="MH" <?php if(isset($_POST['country']) && $_POST['country'] == 'MH') echo 'selected' ?>>Marshall Islands</option>
                    <option value="MQ" <?php if(isset($_POST['country']) && $_POST['country'] == 'MQ') echo 'selected' ?>>Martinique</option>
                    <option value="MR" <?php if(isset($_POST['country']) && $_POST['country'] == 'MR') echo 'selected' ?>>Mauritania</option>
                    <option value="MU" <?php if(isset($_POST['country']) && $_POST['country'] == 'MU') echo 'selected' ?>>Mauritius</option>
                    <option value="YT" <?php if(isset($_POST['country']) && $_POST['country'] == 'YT') echo 'selected' ?>>Mayotte</option>
                    <option value="MX" <?php if(isset($_POST['country']) && $_POST['country'] == 'MX') echo 'selected' ?>>Mexico</option>
                    <option value="FM" <?php if(isset($_POST['country']) && $_POST['country'] == 'FM') echo 'selected' ?>>Micronesia</option>
                    <option value="MD" <?php if(isset($_POST['country']) && $_POST['country'] == 'MD') echo 'selected' ?>>Moldova</option>
                    <option value="MC" <?php if(isset($_POST['country']) && $_POST['country'] == 'MC') echo 'selected' ?>>Monaco</option>
                    <option value="MN" <?php if(isset($_POST['country']) && $_POST['country'] == 'MN') echo 'selected' ?>>Mongolia</option>
                    <option value="MS" <?php if(isset($_POST['country']) && $_POST['country'] == 'MS') echo 'selected' ?>>Montserrat</option>
                    <option value="MA" <?php if(isset($_POST['country']) && $_POST['country'] == 'MA') echo 'selected' ?>>Morocco</option>
                    <option value="MZ" <?php if(isset($_POST['country']) && $_POST['country'] == 'MZ') echo 'selected' ?>>Mozambique</option>
                    <option value="MM" <?php if(isset($_POST['country']) && $_POST['country'] == 'MM') echo 'selected' ?>>Myanmar / Burma</option>
                    <option value="NA" <?php if(isset($_POST['country']) && $_POST['country'] == 'NA') echo 'selected' ?>>Namibia</option>
                    <option value="NR" <?php if(isset($_POST['country']) && $_POST['country'] == 'NR') echo 'selected' ?>>Nauru</option>
                    <option value="NR" <?php if(isset($_POST['country']) && $_POST['country'] == 'NR') echo 'selected' ?>>Nauru</option>
                    <option value="NP" <?php if(isset($_POST['country']) && $_POST['country'] == 'NP') echo 'selected' ?>>Nepal</option>
                    <option value="NL" <?php if(isset($_POST['country']) && $_POST['country'] == 'NL') echo 'selected' ?>>Netherlands</option>
                    <option value="AN" <?php if(isset($_POST['country']) && $_POST['country'] == 'AN') echo 'selected' ?>>Netherlands Antilles</option>
                    <option value="NC" <?php if(isset($_POST['country']) && $_POST['country'] == 'NC') echo 'selected' ?>>New Caledonia</option>
                    <option value="NZ" <?php if(isset($_POST['country']) && $_POST['country'] == 'NZ') echo 'selected' ?>>New Zealand</option>
                    <option value="NI" <?php if(isset($_POST['country']) && $_POST['country'] == 'NI') echo 'selected' ?>>Nicaragua</option>
                    <option value="NE" <?php if(isset($_POST['country']) && $_POST['country'] == 'NE') echo 'selected' ?>>Niger</option>
                    <option value="NG" <?php if(isset($_POST['country']) && $_POST['country'] == 'NG') echo 'selected' ?>>Nigeria</option>
                    <option value="NU" <?php if(isset($_POST['country']) && $_POST['country'] == 'NU') echo 'selected' ?>>Niue</option>
                    <option value="NF" <?php if(isset($_POST['country']) && $_POST['country'] == 'NF') echo 'selected' ?>>Norfolk Island</option>
                    <option value="VG" <?php if(isset($_POST['country']) && $_POST['country'] == 'VG') echo 'selected' ?>>Norman Island</option>
                    <option value="KP" <?php if(isset($_POST['country']) && $_POST['country'] == 'KP') echo 'selected' ?>>North Korea</option>
                    <option value="GB" <?php if(isset($_POST['country']) && $_POST['country'] == 'GB') echo 'selected' ?>>Northern Ireland</option>
                    <option value="MP" <?php if(isset($_POST['country']) && $_POST['country'] == 'MP') echo 'selected' ?>>Northern Mariana Isl</option>
                    <option value="NO" <?php if(isset($_POST['country']) && $_POST['country'] == 'NO') echo 'selected' ?>>Norway</option>
                    <option value="OM" <?php if(isset($_POST['country']) && $_POST['country'] == 'OM') echo 'selected' ?>>Oman</option>
                    <option value="PK" <?php if(isset($_POST['country']) && $_POST['country'] == 'PK') echo 'selected' ?>>Pakistan</option>
                    <option value="PW" <?php if(isset($_POST['country']) && $_POST['country'] == 'PW') echo 'selected' ?>>Palau</option>
                    <option value="PS" <?php if(isset($_POST['country']) && $_POST['country'] == 'PS') echo 'selected' ?>>Palestine</option>
                    <option value="PA" <?php if(isset($_POST['country']) && $_POST['country'] == 'PA') echo 'selected' ?>>Panama</option>
                    <option value="PG" <?php if(isset($_POST['country']) && $_POST['country'] == 'PG') echo 'selected' ?>>Papua New Guinea</option>
                    <option value="PY" <?php if(isset($_POST['country']) && $_POST['country'] == 'PY') echo 'selected' ?>>Paraguay</option>
                    <option value="PE" <?php if(isset($_POST['country']) && $_POST['country'] == 'PE') echo 'selected' ?>>Peru</option>
                    <option value="PH" <?php if(isset($_POST['country']) && $_POST['country'] == 'PH') echo 'selected' ?>>Philippines</option>
                    <option value="PN" <?php if(isset($_POST['country']) && $_POST['country'] == 'PN') echo 'selected' ?>>Pitcairn</option>
                    <option value="PL" <?php if(isset($_POST['country']) && $_POST['country'] == 'PL') echo 'selected' ?>>Poland</option>
                    <option value="PT" <?php if(isset($_POST['country']) && $_POST['country'] == 'PT') echo 'selected' ?>>Portugal</option>
                    <option value="PR" <?php if(isset($_POST['country']) && $_POST['country'] == 'PR') echo 'selected' ?>>Puerto Rico</option>
                    <option value="QA" <?php if(isset($_POST['country']) && $_POST['country'] == 'QA') echo 'selected' ?>>Qatar</option>
                    <option value="RE" <?php if(isset($_POST['country']) && $_POST['country'] == 'RE') echo 'selected' ?>>Reunion</option>
                    <option value="RO" <?php if(isset($_POST['country']) && $_POST['country'] == 'RO') echo 'selected' ?>>Romania</option>
                    <option value="MP" <?php if(isset($_POST['country']) && $_POST['country'] == 'MP') echo 'selected' ?>>Rota</option>
                    <option value="RU" <?php if(isset($_POST['country']) && $_POST['country'] == 'RU') echo 'selected' ?>>Russia</option>
                    <option value="RW" <?php if(isset($_POST['country']) && $_POST['country'] == 'RW') echo 'selected' ?>>Rwanda</option>
                    <option value="AN" <?php if(isset($_POST['country']) && $_POST['country'] == 'AN') echo 'selected' ?>>Saba</option>
                    <option value="MP" <?php if(isset($_POST['country']) && $_POST['country'] == 'MP') echo 'selected' ?>>Saipan</option>
                    <option value="WS" <?php if(isset($_POST['country']) && $_POST['country'] == 'WS') echo 'selected' ?>>Samoa</option>
                    <option value="IT" <?php if(isset($_POST['country']) && $_POST['country'] == 'IT') echo 'selected' ?>>San Marino</option>
                    <option value="ST" <?php if(isset($_POST['country']) && $_POST['country'] == 'ST') echo 'selected' ?>>Sao Tome & Principe</option>
                    <option value="SA" <?php if(isset($_POST['country']) && $_POST['country'] == 'SA') echo 'selected' ?>>Saudi Arabia</option>
                    <!--                        <option value="GB" --><?php //if(isset($_POST['country']) && $_POST['country'] == 'GB') echo 'selected' ?><!-->Scotland</option>-->
                    <option value="SN" <?php if(isset($_POST['country']) && $_POST['country'] == 'SN') echo 'selected' ?>>Senegal</option>
                    <option value="CS" <?php if(isset($_POST['country']) && $_POST['country'] == 'CS') echo 'selected' ?>>Serbia & Montenegro</option>
                    <option value="SC" <?php if(isset($_POST['country']) && $_POST['country'] == 'SC') echo 'selected' ?>>Seychelles</option>
                    <option value="SL" <?php if(isset($_POST['country']) && $_POST['country'] == 'SL') echo 'selected' ?>>Sierra Leone</option>
                    <option value="SG" <?php if(isset($_POST['country']) && $_POST['country'] == 'SG') echo 'selected' ?>>Singapore</option>
                    <option value="SK" <?php if(isset($_POST['country']) && $_POST['country'] == 'SK') echo 'selected' ?>>Slovak Republic</option>
                    <option value="SI" <?php if(isset($_POST['country']) && $_POST['country'] == 'SI') echo 'selected' ?>>Slovenia</option>
                    <option value="SB" <?php if(isset($_POST['country']) && $_POST['country'] == 'SB') echo 'selected' ?>>Solomon Islands</option>
                    <option value="SO" <?php if(isset($_POST['country']) && $_POST['country'] == 'SO') echo 'selected' ?>>Somalia</option>
                    <option value="ZA" <?php if(isset($_POST['country']) && $_POST['country'] == 'ZA') echo 'selected' ?>>South Africa</option>
                    <option value="GS" <?php if(isset($_POST['country']) && $_POST['country'] == 'GS') echo 'selected' ?>>South Georgia & Sout</option>
                    <option value="KR" <?php if(isset($_POST['country']) && $_POST['country'] == 'KR') echo 'selected' ?>>South Korea</option>
                    <option value="ES" <?php if(isset($_POST['country']) && $_POST['country'] == 'ES') echo 'selected' ?>>Spain</option>
                    <option value="LK" <?php if(isset($_POST['country']) && $_POST['country'] == 'LK') echo 'selected' ?>>Sri Lanka</option>
                    <option value="GP" <?php if(isset($_POST['country']) && $_POST['country'] == 'GP') echo 'selected' ?>>St. Barthelemy</option>
                    <option value="KN" <?php if(isset($_POST['country']) && $_POST['country'] == 'KN') echo 'selected' ?>>St. Christopher</option>
                    <option value="VI" <?php if(isset($_POST['country']) && $_POST['country'] == 'VI') echo 'selected' ?>>St. Croix Island</option>
                    <option value="AN" <?php if(isset($_POST['country']) && $_POST['country'] == 'AN') echo 'selected' ?>>St. Eustatius</option>
                    <option value="SH" <?php if(isset($_POST['country']) && $_POST['country'] == 'SH') echo 'selected' ?>>St. Helena</option>
                    <option value="VI" <?php if(isset($_POST['country']) && $_POST['country'] == 'VI') echo 'selected' ?>>St. John</option>
                    <option value="KN" <?php if(isset($_POST['country']) && $_POST['country'] == 'KN') echo 'selected' ?>>St. Kitts and Nevis</option>
                    <option value="LC" <?php if(isset($_POST['country']) && $_POST['country'] == 'LC') echo 'selected' ?>>St. Lucia</option>
                    <option value="AN" <?php if(isset($_POST['country']) && $_POST['country'] == 'AN') echo 'selected' ?>>St. Maarten</option>
                    <option value="PM" <?php if(isset($_POST['country']) && $_POST['country'] == 'PM') echo 'selected' ?>>St. Pierre</option>
                    <option value="VI" <?php if(isset($_POST['country']) && $_POST['country'] == 'VI') echo 'selected' ?>>St. Thomas</option>
                    <option value="VC" <?php if(isset($_POST['country']) && $_POST['country'] == 'VC') echo 'selected' ?>>St. Vincent</option>
                    <option value="SD" <?php if(isset($_POST['country']) && $_POST['country'] == 'SD') echo 'selected' ?>>Sudan</option>
                    <option value="SR" <?php if(isset($_POST['country']) && $_POST['country'] == 'SR') echo 'selected' ?>>Suriname</option>
                    <option value="SJ" <?php if(isset($_POST['country']) && $_POST['country'] == 'SJ') echo 'selected' ?>>Svalbard & Jan Mayen Is</option>
                    <option value="SZ" <?php if(isset($_POST['country']) && $_POST['country'] == 'SZ') echo 'selected' ?>>Swaziland</option>
                    <option value="SE" <?php if(isset($_POST['country']) && $_POST['country'] == 'SE') echo 'selected' ?>>Sweden</option>
                    <option value="CH" <?php if(isset($_POST['country']) && $_POST['country'] == 'CH') echo 'selected' ?>>Switzerland</option>
                    <option value="SY" <?php if(isset($_POST['country']) && $_POST['country'] == 'SY') echo 'selected' ?>>Syria</option>
                    <option value="PF" <?php if(isset($_POST['country']) && $_POST['country'] == 'PF') echo 'selected' ?>>Tahiti</option>
                    <option value="TW" <?php if(isset($_POST['country']) && $_POST['country'] == 'TW') echo 'selected' ?>>Taiwan</option>
                    <option value="TJ" <?php if(isset($_POST['country']) && $_POST['country'] == 'TJ') echo 'selected' ?>>Tajikistan</option>
                    <option value="TZ" <?php if(isset($_POST['country']) && $_POST['country'] == 'TZ') echo 'selected' ?>>Tanzania</option>
                    <option value="TH" <?php if(isset($_POST['country']) && $_POST['country'] == 'TH') echo 'selected' ?>>Thailand</option>
                    <option value="MP" <?php if(isset($_POST['country']) && $_POST['country'] == 'MP') echo 'selected' ?>>Tinian</option>
                    <option value="TG" <?php if(isset($_POST['country']) && $_POST['country'] == 'TG') echo 'selected' ?>>Togo</option>
                    <option value="TK" <?php if(isset($_POST['country']) && $_POST['country'] == 'TK') echo 'selected' ?>>Tokelau</option>
                    <option value="TO" <?php if(isset($_POST['country']) && $_POST['country'] == 'TO') echo 'selected' ?>>Tonga</option>
                    <option value="VG" <?php if(isset($_POST['country']) && $_POST['country'] == 'VG') echo 'selected' ?>>Tortola Island</option>
                    <option value="TT" <?php if(isset($_POST['country']) && $_POST['country'] == 'TT') echo 'selected' ?>>Trinidad & Tobago</option>
                    <option value="TN" <?php if(isset($_POST['country']) && $_POST['country'] == 'TN') echo 'selected' ?>>Tunisia</option>
                    <option value="TR" <?php if(isset($_POST['country']) && $_POST['country'] == 'TR') echo 'selected' ?>>Turkey</option>
                    <option value="TM" <?php if(isset($_POST['country']) && $_POST['country'] == 'TM') echo 'selected' ?>>Turkmenistan</option>
                    <option value="TC" <?php if(isset($_POST['country']) && $_POST['country'] == 'TC') echo 'selected' ?>>Turks & Caicos Islands</option>
                    <option value="TV" <?php if(isset($_POST['country']) && $_POST['country'] == 'TV') echo 'selected' ?>>Tuvalu</option>
                    <option value="UM" <?php if(isset($_POST['country']) && $_POST['country'] == 'UM') echo 'selected' ?>>U.S. Minor Outlying Islands</option>
                    <option value="VI" <?php if(isset($_POST['country']) && $_POST['country'] == 'VI') echo 'selected' ?>>U.S. Virgin Islands</option>
                    <option value="UG" <?php if(isset($_POST['country']) && $_POST['country'] == 'UG') echo 'selected' ?>>Uganda</option>
                    <option value="UA" <?php if(isset($_POST['country']) && $_POST['country'] == 'UA') echo 'selected' ?>>Ukraine</option>
                    <option value="VC" <?php if(isset($_POST['country']) && $_POST['country'] == 'VC') echo 'selected' ?>>Union Island</option>
                    <option value="AE" <?php if(isset($_POST['country']) && $_POST['country'] == 'AE') echo 'selected' ?>>United Arab Emirates</option>
                    <option value="GB" <?php if(isset($_POST['country']) && $_POST['country'] == 'GB') echo 'selected' ?>>United Kingdom</option>
                    <option value="US" <?php if(!isset($_POST['country']) || isset($_POST['country']) && $_POST['country'] == 'US') echo 'selected' ?> name="US">United States</option>
                    <option value="UY" <?php if(isset($_POST['country']) && $_POST['country'] == 'UY') echo 'selected' ?>>Uruguay</option>
                    <option value="UZ" <?php if(isset($_POST['country']) && $_POST['country'] == 'UZ') echo 'selected' ?>>Uzbekistan</option>
                    <option value="VU" <?php if(isset($_POST['country']) && $_POST['country'] == 'VU') echo 'selected' ?>>Vanuatu</option>
                    <option value="IT" <?php if(isset($_POST['country']) && $_POST['country'] == 'IT') echo 'selected' ?>>Vatican City</option>
                    <option value="VE" <?php if(isset($_POST['country']) && $_POST['country'] == 'VE') echo 'selected' ?>>Venezuela</option>
                    <option value="VN" <?php if(isset($_POST['country']) && $_POST['country'] == 'VN') echo 'selected' ?>>Vietnam</option>
                    <!--                        <option value="GB" --><?php //if(isset($_POST['country']) && $_POST['country'] == 'GB') echo 'selected' ?><!-->Wales</option>-->
                    <option value="WF" <?php if(isset($_POST['country']) && $_POST['country'] == 'WF') echo 'selected' ?>>Wallis & Futuna Islands</option>
                    <option value="EH" <?php if(isset($_POST['country']) && $_POST['country'] == 'EH') echo 'selected' ?>>Western Sahara</option>
                    <option value="YE" <?php if(isset($_POST['country']) && $_POST['country'] == 'YE') echo 'selected' ?>>Yemen</option>
                    <option value="ZM" <?php if(isset($_POST['country']) && $_POST['country'] == 'ZM') echo 'selected' ?>>Zambia</option>
                </select>
            </p>
            <p class="a">
                <span>
                    <label for="fch">Código Postal</label>
                    <input type="text" id="fch" name="zip" value="<?php if(isset($_POST['zip'])) echo $_POST['zip'] ?>" maxlength="10" required>
                </span>
                <span>
                    <label for="fcf">Ciudad</label>
                    <input type="text" id="fcf" name="city" value="<?php if(isset($_POST['city'])) echo $_POST['city'] ?>" maxlength="20" required>
                </span>
                <span>
<!--                    <label for="fcg">Estado / Provincia</label>-->
                    <label for="fcg"></label>
                    <input type="text" id="fcg" name="state_text" value="<?php if(isset($_POST['state'])) echo $_POST['state'] ?>" <?php if(isset($_POST['country']) && !in_array($_POST['country'], ['US', 'CA', 'GB']) )  {echo ' name="state" ';} else {echo ' name="state_textaa" style="display:none;" ';}?> >
                    <select class="state_dropdown" id="state_dropdown_us" <?php if(!isset($_POST['country']) || (isset($_POST['country']) && $_POST['country'] == 'US')) {echo ' name="state" ';} else {echo ' name="state_dropdown_us" style="display:none;" ';} ?> >
                        <option value="__">- Estado/Provincia -</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'AL') echo 'selected' ?> value='AL'>Alabama</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'AK') echo 'selected' ?> value='AK'>Alaska</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'AZ') echo 'selected' ?> value='AZ'>Arizona</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'AR') echo 'selected' ?> value='AR'>Arkansas</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'CA') echo 'selected' ?> value='CA'>California</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'CO') echo 'selected' ?> value='CO'>Colorado</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'CT') echo 'selected' ?> value='CT'>Connecticut</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'DE') echo 'selected' ?> value='DE'>Delaware</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'FL') echo 'selected' ?> value='FL'>Florida</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'GA') echo 'selected' ?> value='GA'>Georgia</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'HI') echo 'selected' ?> value='HI'>Hawaii</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'ID') echo 'selected' ?> value='ID'>Idaho</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IL') echo 'selected' ?> value='IL'>Illinois</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IN') echo 'selected' ?> value='IN'>Indiana</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IA') echo 'selected' ?> value='IA'>Iowa</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KS') echo 'selected' ?> value='KS'>Kansas</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KY') echo 'selected' ?> value='KY'>Kentucky</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LA') echo 'selected' ?> value='LA'>Louisiana</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'ME') echo 'selected' ?> value='ME'>Maine</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'MD') echo 'selected' ?> value='MD'>Maryland</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'MA') echo 'selected' ?> value='MA'>Massachusetts</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'MI') echo 'selected' ?> value='MI'>Michigan</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'MN') echo 'selected' ?> value='MN'>Minnesota</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'MS') echo 'selected' ?> value='MS'>Mississippi</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'MO') echo 'selected' ?> value='MO'>Missouri</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'MT') echo 'selected' ?> value='MT'>Montana</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NE') echo 'selected' ?> value='NE'>Nebraska</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NV') echo 'selected' ?> value='NV'>Nevada</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NH') echo 'selected' ?> value='NH'>New Hampshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NJ') echo 'selected' ?> value='NJ'>New Jersey</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NM') echo 'selected' ?> value='NM'>New Mexico</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NY') echo 'selected' ?> value='NY'>New York</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NC') echo 'selected' ?> value='NC'>North Carolina</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'ND') echo 'selected' ?> value='ND'>North Dakota</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'OH') echo 'selected' ?> value='OH'>Ohio</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'OK') echo 'selected' ?> value='OK'>Oklahoma</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'OR') echo 'selected' ?> value='OR'>Oregon</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'PA') echo 'selected' ?> value='PA'>Pennsylvania</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'RI') echo 'selected' ?> value='RI'>Rhode Island</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'SC') echo 'selected' ?> value='SC'>South Carolina</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'SD') echo 'selected' ?> value='SD'>South Dakota</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'TN') echo 'selected' ?> value='TN'>Tennessee</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'TX') echo 'selected' ?> value='TX'>Texas</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'UT') echo 'selected' ?> value='UT'>Utah</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'VT') echo 'selected' ?> value='VT'>Vermont</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'VA') echo 'selected' ?> value='VA'>Virginia</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'WA') echo 'selected' ?> value='WA'>Washington</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'WV') echo 'selected' ?> value='WV'>West Virginia</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'WI') echo 'selected' ?> value='WI'>Wisconsin</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'WY') echo 'selected' ?> value='WY'>Wyoming</option>
                    </select>
                    <select class="state_dropdown" id="state_dropdown_ca" <?php if(isset($_POST['country']) && $_POST['country'] == 'CA') {echo ' name="state" ';} else {echo ' name="state_dropdown_ca" style="display:none;" ';} ?> >
                        <option value="__">- Estado/Provincia -</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'AB') echo 'selected' ?> value='AB'>Alberta</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'BC') echo 'selected' ?> value='BC'>British Columbia</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'MB') echo 'selected' ?> value='MB'>Manitoba</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NB') echo 'selected' ?> value='NB'>New Brunswick</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NL') echo 'selected' ?> value='NL'>Newfoundland</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NT') echo 'selected' ?> value='NT'>Northwest Terr.</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NS') echo 'selected' ?> value='NS'>Nova Scotia</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'NU') echo 'selected' ?> value='NU'>Nunavut</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'ON') echo 'selected' ?> value='ON'>Ontario</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'PE') echo 'selected' ?> value='PE'>Prince Edward Isl.</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'QC') echo 'selected' ?> value='QC'>Quebec</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'SK') echo 'selected' ?> value='SK'>Saskatchewan</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'YT') echo 'selected' ?> value='YT'>Yukon</option>
                    </select>
                    <select class="state_dropdown" id="state_dropdown_gb" <?php if(isset($_POST['country']) && $_POST['country'] == 'GB') {echo ' name="state" ';} else {echo ' name="state_dropdown_gb" style="display:none;" ';} ?> >
                        <option value="__">- Estado/Provincia -</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'I0') echo 'selected' ?> value='I0'>Aberconwy and Colwyn</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'I1') echo 'selected' ?> value='I1'>Aberdeen City</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'I2') echo 'selected' ?> value='I2'>Aberdeenshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'I3') echo 'selected' ?> value='I3'>Anglesey</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'I4') echo 'selected' ?> value='I4'>Angus</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'I5') echo 'selected' ?> value='I5'>Antrim</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'I6') echo 'selected' ?> value='I6'>Argyll and Bute</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'I7') echo 'selected' ?> value='I7'>Armagh</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'I8') echo 'selected' ?> value='I8'>Avon</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'I9') echo 'selected' ?> value='I9'>Ayrshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IB') echo 'selected' ?> value='IB'>Bath and NE Somerset</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IC') echo 'selected' ?> value='IC'>Bedfordshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IE') echo 'selected' ?> value='IE'>Belfast</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IF') echo 'selected' ?> value='IF'>Berkshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IG') echo 'selected' ?> value='IG'>Berwickshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IH') echo 'selected' ?> value='IH'>BFPO</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'II') echo 'selected' ?> value='II'>Blaenau Gwent</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IJ') echo 'selected' ?> value='IJ'>Buckinghamshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IK') echo 'selected' ?> value='IK'>Caernarfonshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IM') echo 'selected' ?> value='IM'>Caerphilly</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IO') echo 'selected' ?> value='IO'>Caithness</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IP') echo 'selected' ?> value='IP'>Cambridgeshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IQ') echo 'selected' ?> value='IQ'>Cardiff</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IR') echo 'selected' ?> value='IR'>Cardiganshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IS') echo 'selected' ?> value='IS'>Carmarthenshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IT') echo 'selected' ?> value='IT'>Ceredigion</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IU') echo 'selected' ?> value='IU'>Channel Islands</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IV') echo 'selected' ?> value='IV'>Cheshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IW') echo 'selected' ?> value='IW'>City of Bristol</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IX') echo 'selected' ?> value='IX'>Clackmannanshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IY') echo 'selected' ?> value='IY'>Clwyd</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'IZ') echo 'selected' ?> value='IZ'>Conwy</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'J0') echo 'selected' ?> value='J0'>Cornwall/Scilly</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'J1') echo 'selected' ?> value='J1'>Cumbria</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'J2') echo 'selected' ?> value='J2'>Denbighshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'J3') echo 'selected' ?> value='J3'>Derbyshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'J4') echo 'selected' ?> value='J4'>Derry/Londonderry</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'J5') echo 'selected' ?> value='J5'>Devon</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'J6') echo 'selected' ?> value='J6'>Dorset</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'J7') echo 'selected' ?> value='J7'>Down</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'J8') echo 'selected' ?> value='J8'>Dumfries and Galloway</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'J9') echo 'selected' ?> value='J9'>Dunbartonshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JA') echo 'selected' ?> value='JA'>Dundee</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JB') echo 'selected' ?> value='JB'>Durham</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JC') echo 'selected' ?> value='JC'>Dyfed</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JD') echo 'selected' ?> value='JD'>East Ayrshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JE') echo 'selected' ?> value='JE'>East Dunbartonshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JF') echo 'selected' ?> value='JF'>East Lothian</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JG') echo 'selected' ?> value='JG'>East Renfrewshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JH') echo 'selected' ?> value='JH'>East Riding Yorkshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JI') echo 'selected' ?> value='JI'>East Sussex</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JJ') echo 'selected' ?> value='JJ'>Edinburgh</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JK') echo 'selected' ?> value='JK'>England</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JL') echo 'selected' ?> value='JL'>Essex</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JM') echo 'selected' ?> value='JM'>Falkirk</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JN') echo 'selected' ?> value='JN'>Fermanagh</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JO') echo 'selected' ?> value='JO'>Fife</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JP') echo 'selected' ?> value='JP'>Flintshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JQ') echo 'selected' ?> value='JQ'>Glasgow</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JR') echo 'selected' ?> value='JR'>Gloucestershire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JS') echo 'selected' ?> value='JS'>Greater London</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JT') echo 'selected' ?> value='JT'>Greater Manchester</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JU') echo 'selected' ?> value='JU'>Gwent</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JV') echo 'selected' ?> value='JV'>Gwynedd</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JW') echo 'selected' ?> value='JW'>Hampshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JX') echo 'selected' ?> value='JX'>Hartlepool</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'HA') echo 'selected' ?> value='HA'>Hereford and Worcester</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JY') echo 'selected' ?> value='JY'>Hertfordshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'JZ') echo 'selected' ?> value='JZ'>Highlands</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'K0') echo 'selected' ?> value='K0'>Inverclyde</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'K1') echo 'selected' ?> value='K1'>Inverness-Shire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'K2') echo 'selected' ?> value='K2'>Isle of Man</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'K3') echo 'selected' ?> value='K3'>Isle of Wight</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'K4') echo 'selected' ?> value='K4'>Kent</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'K5') echo 'selected' ?> value='K5'>Kincardinshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'K6') echo 'selected' ?> value='K6'>Kingston Upon Hull</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'K7') echo 'selected' ?> value='K7'>Kinross-Shire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'K8') echo 'selected' ?> value='K8'>Kirklees</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'K9') echo 'selected' ?> value='K9'>Lanarkshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KA') echo 'selected' ?> value='KA'>Lancashire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KB') echo 'selected' ?> value='KB'>Leicestershire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KC') echo 'selected' ?> value='KC'>Lincolnshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KD') echo 'selected' ?> value='KD'>Londonderry</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KE') echo 'selected' ?> value='KE'>Merseyside</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KF') echo 'selected' ?> value='KF'>Merthyr Tydfil</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KG') echo 'selected' ?> value='KG'>Mid Glamorgan</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KI') echo 'selected' ?> value='KI'>Mid Lothian</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KH') echo 'selected' ?> value='KH'>Middlesex</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KJ') echo 'selected' ?> value='KJ'>Monmouthshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KK') echo 'selected' ?> value='KK'>Moray</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KL') echo 'selected' ?> value='KL'>Neath & Port Talbot</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KM') echo 'selected' ?> value='KM'>Newport</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KN') echo 'selected' ?> value='KN'>Norfolk</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KP') echo 'selected' ?> value='KP'>North Ayrshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KQ') echo 'selected' ?> value='KQ'>North East Lincolnshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KR') echo 'selected' ?> value='KR'>North Lanarkshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KT') echo 'selected' ?> value='KT'>North Lincolnshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KU') echo 'selected' ?> value='KU'>North Somerset</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KV') echo 'selected' ?> value='KV'>North Yorkshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KO') echo 'selected' ?> value='KO'>Northamptonshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KW') echo 'selected' ?> value='KW'>Northern Ireland</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KX') echo 'selected' ?> value='KX'>Northumberland</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'KZ') echo 'selected' ?> value='KZ'>Nottinghamshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'L0') echo 'selected' ?> value='L0'>Orkney and Shetland Isles</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'L1') echo 'selected' ?> value='L1'>Oxfordshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'L2') echo 'selected' ?> value='L2'>Pembrokeshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'L3') echo 'selected' ?> value='L3'>Perth and Kinross</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'L4') echo 'selected' ?> value='L4'>Powys</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'L5') echo 'selected' ?> value='L5'>Redcar and Cleveland</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'L6') echo 'selected' ?> value='L6'>Renfrewshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'L7') echo 'selected' ?> value='L7'>Rhonda Cynon Taff</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'L8') echo 'selected' ?> value='L8'>Rutland</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'L9') echo 'selected' ?> value='L9'>Scottish Borders</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LB') echo 'selected' ?> value='LB'>Shetland</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LC') echo 'selected' ?> value='LC'>Shropshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LD') echo 'selected' ?> value='LD'>Somerset</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LE') echo 'selected' ?> value='LE'>South Ayrshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LF') echo 'selected' ?> value='LF'>South Glamorgan</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LG') echo 'selected' ?> value='LG'>South Gloucesteshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LH') echo 'selected' ?> value='LH'>South Lanarkshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LI') echo 'selected' ?> value='LI'>South Yorkshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LJ') echo 'selected' ?> value='LJ'>Staffordshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LK') echo 'selected' ?> value='LK'>Stirling</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LL') echo 'selected' ?> value='LL'>Stockton On Tees</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LM') echo 'selected' ?> value='LM'>Suffolk</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LN') echo 'selected' ?> value='LN'>Surrey</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LO') echo 'selected' ?> value='LO'>Swansea</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LP') echo 'selected' ?> value='LP'>Torfaen</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LQ') echo 'selected' ?> value='LQ'>Tyne and Wear</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LR') echo 'selected' ?> value='LR'>Tyrone</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LS') echo 'selected' ?> value='LS'>Vale Of Glamorgan</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LT') echo 'selected' ?> value='LT'>Wales</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LU') echo 'selected' ?> value='LU'>Warwickshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LV') echo 'selected' ?> value='LV'>West Berkshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LW') echo 'selected' ?> value='LW'>West Dunbartonshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LX') echo 'selected' ?> value='LX'>West Glamorgan</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LY') echo 'selected' ?> value='LY'>West Lothian</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'LZ') echo 'selected' ?> value='LZ'>West Midlands</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'M0') echo 'selected' ?> value='M0'>West Sussex</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'M1') echo 'selected' ?> value='M1'>West Yorkshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'M2') echo 'selected' ?> value='M2'>Western Isles</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'M3') echo 'selected' ?> value='M3'>Wiltshire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'M4') echo 'selected' ?> value='M4'>Wirral</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'M5') echo 'selected' ?> value='M5'>Worcestershire</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'M6') echo 'selected' ?> value='M6'>Wrexham</option>
                        <option <?php if(isset($_POST['state']) && $_POST['state'] == 'M7') echo 'selected' ?> value='M7'>York</option>
                    </select>
                </span>
            </p>
            <?php if(isset($charities) && $charities): ?>
                <div id="charities">
                    <h3>Charities</h3>
                    <div class="charity-info">
                        A portion of this sale will go to help the local charity we support but if you would like to go <br/>
                        above and beyond that, you can add an additional donation to your cart. <br/><br/>
                        We'll match any donation you make - so €2 from you, means €4 for charity.
                    </div>

                    <?php foreach($charities as $charity): ?>
                        <h4><?php echo $charity['Charity']['charity_name'] ?></h4>
                        <input type="hidden" name="charity_id[]" value="<?php echo $charity['Charity']['id'] ?>">
                        <?php if($charity['Charity']['description']): ?>
                            <div class="charity-description">
                                <?php echo $charity['Charity']['description'] ?>
                            </div>
                        <?php endif ?>
                        <select name="donation_amount[]" data-charity-id="<?php echo $charity['Charity']['id'] ?>" data-charity-name="<?php echo $charity['Charity']['charity_name'] ?>">
                            <?php $amounts = array(2,4,5,6,8,10); ?>
                            <option value="0" data-usd="0">No, thank you</option>
                            <?php foreach($amounts as $amount): ?>
                                <option value="<?php echo $amount ?>">
                                    &euro; <?php echo $amount ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    <?php endforeach ?>
                </div>
            <?php endif ?>


            <h4>Información de facturación</h4>
            <p class="b">
							<span class="a">
								<label for="fck">Número tarjeta de crédito</label>
								<input type="text" id="fck" name="ccNo" maxlength="16" required>
							</span>
							<span>
								<label for="fcl" class="hidden">Tipo de tarjeta</label>
								<select id="fcl" name="ccType" required data-placeholder="Tipo de Tarjeta">
                                    <option></option>
                                    <option <?php if(isset($_POST['ccType']) && $_POST['ccType'] == 'VI') echo 'selected' ?> value="VI">Visa</option>
                                    <option <?php if(isset($_POST['ccType']) && $_POST['ccType'] == 'MC') echo 'selected' ?> value="MC">MasterCard</option>
                                    <option <?php if(isset($_POST['ccType']) && $_POST['ccType'] == 'DI') echo 'selected' ?> value="DI">Discover</option>
                                    <option <?php if(isset($_POST['ccType']) && $_POST['ccType'] == 'AX') echo 'selected' ?> value="AX">American Express</option>
                                    <?php /*
				    <option value="DI">Discover</option>
                                    <option value="AX">American Express</option>
				    */ ?>
                                </select>
							</span>
            </p>
            <p class="c">
                    <span class="b">
                        <span class="label">Fecha de caducidad</span>
                        <span>
                            <label for="fcm" class="hidden">MM</label>
                            <select id="fcm" name="ccMonth" required data-placeholder="MM">
                                <option></option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '01') echo 'selected' ?> value="01">01</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '02') echo 'selected' ?> value="02">02</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '03') echo 'selected' ?> value="03">03</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '04') echo 'selected' ?> value="04">04</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '05') echo 'selected' ?> value="05">05</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '06') echo 'selected' ?> value="06">06</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '07') echo 'selected' ?> value="07">07</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '08') echo 'selected' ?> value="08">08</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '09') echo 'selected' ?> value="09">09</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '10') echo 'selected' ?> value="10">10</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '11') echo 'selected' ?> value="11">11</option>
                                <option <?php if(isset($_POST['ccMonth']) && $_POST['ccMonth'] == '12') echo 'selected' ?> value="12">12</option>
                            </select>
                        </span>
                        <span>
                            <label for="fcn" class="hidden">YYYY</label>
                            <select id="fcn" name="ccYear" required data-placeholder="AAAA">
                                <option></option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  17) echo 'selected' ?> value="17">2017</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  18) echo 'selected' ?> value="18">2018</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  19) echo 'selected' ?> value="19">2019</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  20) echo 'selected' ?> value="20">2020</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  21) echo 'selected' ?> value="21">2021</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  22) echo 'selected' ?> value="22">2022</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  23) echo 'selected' ?> value="23">2023</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  24) echo 'selected' ?> value="24">2024</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  25) echo 'selected' ?> value="25">2025</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  26) echo 'selected' ?> value="26">2026</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  27) echo 'selected' ?> value="27">2027</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  28) echo 'selected' ?> value="28">2028</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  29) echo 'selected' ?> value="29">2029</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  30) echo 'selected' ?> value="30">2030</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  31) echo 'selected' ?> value="31">2031</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  32) echo 'selected' ?> value="32">2032</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  33) echo 'selected' ?> value="33">2033</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  34) echo 'selected' ?> value="34">2034</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  35) echo 'selected' ?> value="35">2035</option>
                                <option <?php if(isset($_POST['ccYear']) && $_POST['ccYear'] ==  36) echo 'selected' ?> value="36">2036</option>
                            </select>
                        </span>
                    </span>
                    <span class="c">
                        <span class="label">Código trasero</span>
                        <span>
                            <label for="fco" class="hidden">Código trasero</label>
                            <input type="text" id="fco" name="ccCCV" required maxlength="4">
                        </span>
                    </span>
                    <span class="d ccName">
                        <span class="label">Nombre en la tarjeta</span>
                        <span>
                            <label for="fcp">Nombre</label>
                            <input type="text" id="fcp" name="ccFirstName" value="<?php if(isset($_POST['ccFirstName'])) echo $_POST['ccFirstName'] ?>" required>
                        </span>
                        <span>
                            <label for="fcq">Apellido</label>
                            <input type="text" id="fcq" name="ccLastName" value="<?php if(isset($_POST['ccLastName'])) echo $_POST['ccLastName'] ?>" required>
                        </span>
                    </span>
            </p>
            <div class="custom-checkboxes nts check-a">
                <input type="checkbox" id="conditions" name="conditions">
                <label for="conditions">He leído y acepto todas las <a href="./terms">condiciones de reserva</a>.</label>
                <span id="conditionError" style="color: red"></span>
            </div>
            <p class="scheme-c"><button type="submit"><i class="fa fa-check-square-o"></i>Reservar</button></p>
        </fieldset>
    </form>
    <aside>
        <div class="cart-a">
            <div id="currencyPicker">
                <h3>Moneda de Pago</h3>
                <div data-currency='USD' class="currency <?php echo ExchangeRate::getCurrency() == 'USD' ? 'selected' : '' ?>">USD</div>
                <div data-currency='EUR' class="currency <?php echo ExchangeRate::getCurrency() == 'EUR' ? 'selected' : '' ?>">EUR</div>
                <div data-currency='GBP' class="currency <?php echo ExchangeRate::getCurrency() == 'GBP' ? 'selected' : '' ?>">GBP</div>
                <div data-currency='CAD' class="currency <?php echo ExchangeRate::getCurrency() == 'CAD' ? 'selected' : '' ?>">CAD</div>
                <div data-currency='AUD' class="currency <?php echo ExchangeRate::getCurrency() == 'AUD' ? 'selected' : '' ?>">AUD</div>
            </div>
            <!--
            <?php foreach($cart as $n => $item): ?>
                <article>
                    <h3><?php echo $item['name'] ?></h3>
                    <p><?php echo date('F j, Y - H.ia', strtotime($item['datetime'])) ?></p>
                    <ul>
                        <?php foreach(array('Adultos', 'seniors', 'Estudiantes', 'Niños', 'Infantes') as $type): ?>
                            <?php if($item[$type]) : ?>
                                <li>
                                    <?php echo $item[$type] ?> <?php echo ucfirst($type) ?>
                                    <span><?php echo $item["{$type}_price"] == 0 ? 'GRATIS' : ExchangeRate::convert($item["{$type}_price"],1,1,'EUR') ?> (<?php echo $item["{$type}_price"] == 0 ? 'GRATIS' : ExchangeRate::convert($item["{$type}_price"],1,1,'USD') ?>)</span>
                                </li>
                            <?php endif ?>
                        <?php endforeach; ?>
                        <li class="strong">Subtotal <span><?php echo $item['total_price'] == 0 ? 'GRATIS' : ExchangeRate::convert($item['total_price'],1,1,'EUR') ?> (<?php echo ExchangeRate::convert($item['total_price'],1,1,'USD');?>)</span></li>
                        <?php if(isset($item['promo_local'])): ?>
                            <li>Discount <span>- <?php echo $item['promo_discount'] == 0 ? 'GRATIS' : ExchangeRate::convert($item['promo_discount'],1,1,'EUR') ?> (<?php echo ExchangeRate::convert($item['promo_discount'],1,1,'USD');?>)</span></li>
                        <?php endif ?>
                    </ul>
                    <a class="close" href="/pages/remove_from_cart/<?php echo $n ?>"><i class="fa fa-remove"></i></a>
                </article>
            <?php endforeach ?>
            -->
            <?php if(isset($charities) && $charities): ?>
                <?php foreach($charities as $charity): ?>
                    <article data-charity-id="<?php echo $charity['Charity']['id']; ?>" data-euro-price="0" data-usd-price="0" class="donation-cart-item">
                        <h3><?php echo $charity['Charity']['charity_name'] ?></h3>
                        <p>
                            Subtotal &euro;<span class="charity_subtotal_euro">0</span> ($<span class="charity_subtotal_usd">0</span>)
                        </p>
                    </article>
                <?php endforeach ?>
            <?php endif ?>

            <?php if(isset($promo_discount_fixed_total)): ?>
                <p style="font-size: 1.7em; display:none;">
                    <i>Subtotal</i>
                    <span><span class="full-euro-price subtotalPrice"></span> <br/></span>
                </p>
                <p style="font-size: 1.7em; display:none;">
                    <i>Discount</i>
                    <span>- <span class="full-euro-price discountFixed"></span> <br/></span>
                </p>
            <?php endif ?>
            <p style="font-weight: bold">
                Total
                <span>
                    <span class="full-euro-price fullPrice"></span> <br/>

                </span>

            </p>
        </div>
        <form method="post" action="/pages/apply_promo" class="form-d" id="apply_promo_form">
            <fieldset>
                <h3><label for="fda"><a id="add_promo">Añadir código promocional</a></label></h3>
                <p style="display: none" id="promo_code">
                    <input type="text" id="fda" name="promo">
                    <button type="submit"><i class="fa fa-check-square-o"></i> Apply</button>
                </p>
            </fieldset>
        </form>
    </aside>
</section>

<script type="template" class="cartTour">
    <article class="tour">
        <h3><%- name %></h3>
        <p><%- dateTime %></p>
        <ul>
            <% for(var type in tickets){ %>
            <li>
                <%-tickets[type].amount + ' ' + type %>
                <span><%=tickets[type].formattedPrice %></span>
            </li>
            <% } %>

            <% if(discount > 0 ){ %>
            <li style="text-decoration: line-through"><strong>Precio original</strong> <span style="text-decoration: line-through"><%= originalPrice %></span></li>
            <li><strong>Descuento</strong> <span>- <%= discountAmount %></span></li>
            <% } %>
            <li class="strong"><strong>Subtotal</strong> <span><%= priceDiscount %></span></li>

        </ul>
        <a
            data-position="<%- position %>"
            data-price="<%- totalPrice %>"
            data-name="<%- name %>"
            data-id="<%- event_id %>"
            data-href="/pages/remove_from_cart/<%-position %>"
            onclick="ecRemoveFromCart(this);"
            href="#" class="close"><i class="fa fa-remove"></i></a>
    </article>
</script>

<script type="template" class="cartDonation">
    <article data-charity-id="0" class="donationCartItem">
        <h3><%- name %></h3>
        <ul>
            <li>

                Donation
                <span class="strong"><%= formattedPrice %> (&#8364;<%- formattedEuroPrice %>)</span>
            </li>
        </ul>
    </article>
</script>
