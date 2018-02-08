<?php
class Domain extends AppModel {

    public function formatCurrency($amount, $currency) {
        // Determine euro or dollars based on exchangepair, default to euro
        return $currency == 'USD' ? '$' . number_format($amount, 2, '.', ',') : '€' . number_format($amount, 2, ',', '.');
    }
}
