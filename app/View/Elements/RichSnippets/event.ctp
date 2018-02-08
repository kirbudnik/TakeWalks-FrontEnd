<?php
$richSnippet = [
    "@context"=> "https://schema.org",
    "@type" => "Event",
    "name" => $name,
    "description" => $description,
    "url" => $url,
    "startDate" => date('Y-m-d\TH:i', strtotime($startDate)) . '+01:00',
    //"endDate" => date('Y-m-d\TH:i', strtotime($endDate)) . '+01:00',
    "location" => [
        '@type' => 'Place',
        'name' => $name,
        'address' => [
            '@type' => 'PostalAddress',
            'addressCountry' => $country ?: 'USA',
            "addressLocality" => $city,
            'postalCode' => $zip,
            'streetAddress' => $address,
            'email' => $email,
            'telephone' => $telephone
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => $latitude,
            'longitude' => $longitude
        ]
    ],
    "offers" => [
        "@type" => 'Offer',
        "price" => $price,
        "priceCurrency" => $currency,
        "availability" => "In stock",
        "url" => $url
    ]
];

//create event > image
if(isset($image) && $image != null){
    //add url prefix if http missing
    $richSnippet['image'] = $image;
}

if(isset($averageRating) && $averageRating != null && $averageRating != 0){
    $richSnippet['offers']['aggregateRating'] = $averageRating;
}

?>
<script type="application/ld+json">
    <?php echo json_encode($richSnippet); ?>
</script>
