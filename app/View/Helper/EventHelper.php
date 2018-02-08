<?php

class EventHelper extends AppHelper {

    public function formatDuration($minutes) {
        $duration = '';
        $hours = floor($minutes / 60);
        if ($hours > 0) {
            $duration .= $hours;
            $duration .= ' Hour';
            if ($hours > 1) {
                $duration .= 's';
            }
            $duration .= ' ';
        }
        $minutes = $minutes - $hours * 60;
        if ($minutes > 0) {
            $duration .= $minutes . ' Minutes';
        }
        return trim($duration);
    }

    public function formatCurrency($amount, $currency = 'USD', $precision = 2) {
        if($amount == 0) {
            return 'FREE';
        }

        // Determine euro or dollars based on exchangepair, default to euro
        return $currency == 'USD' ? '$' . number_format($amount, $precision, '.', ',') : '&euro;' . number_format($amount, $precision, '.', ',');
    }

}
