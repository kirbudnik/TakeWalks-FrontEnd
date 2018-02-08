<?php
$richSnippet = [
    "@context"=> "https://schema.org",
    "@type" => "Product",
    "name" => $name,
    "description" => $description,
    "brand" => $brand,

    "offers" => [
        "@type" => 'Offer',
        "price" => $price,
        "priceCurrency" => $currency,
        "availability" => "In stock",
        "url" => $url
    ],
    "sku" => ''
];

//create event > image
if(isset($image) && $image != null){
    //todo add url prefix if http missing
    $richSnippet['image'] = $image;
}

if(isset($averageRating) && $averageRating != null && $averageRating != 0){
    $richSnippet['offers']['aggregateRating'] = $averageRating;
    $richSnippet["aggregateRating"] = [
        "@type" => "AggregateRating",
        "ratingValue" => $averageRating,
        "reviewCount" => $reviewCount,
        "url" => $url
    ];
}

?>
<script type="application/ld+json">
    <?php echo json_encode($richSnippet); ?>
</script>
