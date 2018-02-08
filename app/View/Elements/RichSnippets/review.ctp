<?php
$reviewSnippet = array(
    "@context"=> "https://schema.org",
    "@type"=> "Review",
    "itemReviewed"=>array(
        "@type"=> "Event",
        "name"=> $name,
        "sameAs"=> FULL_BASE_URL . $this->here,
        "startDate" => date('Y-m-d\TH:i', strtotime($startDate)) . '+01:00',
        "location" => [
            '@type' => 'Place',
            'name' => $name,
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => $country ?: 'USA',
                "addressLocality" => $city,
                'postalCode' => $zip,
                'streetAddress' => $address,
                'email' => 'info@walksofitaly.com',
                'telephone' => '1-888-683-8670'
            ]
        ],
        "offers" => [
            "@type" => 'Offer',
            "price" => $price,
            "priceCurrency" => $currency,
            "availability" => "In stock",
            "url" => $url
        ],
    ),
    "reviewRating"=>array(
        "@type"=> "Rating",
        "ratingValue"=> round($rating,2)

    ),
);

if(isset($review)){
    $reviewSnippet['reviewBody'] = $review;
}
if(isset($author) && $author != ' '){
    $reviewSnippet['author'] = array(
        "@type"=> "Person",
        "name"=> "$author"
    );
}



?>
<script type="application/ld+json">
    <?php echo json_encode($reviewSnippet); ?>
</script>