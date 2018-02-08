<?php


class ReSrcHelper extends AppHelper {

    function resrcUrl($url, $width, $params = "") {
        return 'https://app.resrc.it/O=20(40)/' . $params . str_replace('https:', "s=W$width/https:", $url);
    }
} 