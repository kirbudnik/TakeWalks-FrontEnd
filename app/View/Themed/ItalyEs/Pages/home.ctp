<?php
$this->start('headBottom');

//-----WebSite rich snippet-------
$WebSiteRs = $this->RichSnippets->create('WebSite');
$WebSiteRs->setVal('name','Walks of Italy');
$WebSiteRs->setVal('url','https://es.walksofitaly.com/');
    $searchActionRS = $WebSiteRs->addChild('potentialAction','SearchAction');
    $searchActionRS->setVal('target',"https://es.walksofitaly.com/blog/?s={search_term_string}");
    $searchActionRS->setVal('query-input',"required name=search_term_string");
echo $WebSiteRs->getJSON();


//--------local business rich snippet----------
$localBusinessRS = $this->RichSnippets->create('LocalBusiness');
$localBusinessRS->setVal('additionalType','http://www.productontology.org/id/Tour_operator');
$localBusinessRS->setVal('name','Walks of Italy');
$localBusinessRS->setVal('url','https://www.walksofitaly.com/');
$localBusinessRS->setVal('logo','https://www.walksofitaly.com/blog/wp-content/uploads/2015/02/walks-of-italy-logo.png');
$localBusinessRS->setVal('sameAs', array("https://en.wikipedia.org/wiki/Walks_of_Italy", "http://www.freebase.com/m/0g9t3zv", "https://www.facebook.com/walkingtours", "https://plus.google.com/+WalksofitalyTours", "https://twitter.com/WalksofItaly", "http://instagram.com/walksofitaly", "https://www.youtube.com/user/walksofitaly", "http://vimeo.com/walksofitaly", "https://www.pinterest.com/walksofitaly/"));
$localBusinessRS->setVal('currenciesAccepted','EUR');
    $founderRS = $localBusinessRS->addChild('founder','Person');
    $founderRS->setVal('name','Jason Spiehler');
    $founderRS->setVal('sameAs', array("https://plus.google.com/116930771859616754446", "https://www.linkedin.com/pub/jason-spiehler/35/a8b/937"));
    $addressRs = $localBusinessRS->addChild('Address','PostalAddress');
    $addressRs->setVal('streetAddress','Via di Santa Maria dell\'Anima 48');
    $addressRs->setVal('addressLocality','Roma');
    $addressRs->setVal('addressRegion', 'Italy');
    $addressRs->setVal('postalCode','00186');
    $offerRs = $localBusinessRS->addChild('makesOffer', 'Offer');
        $paymentMethodRs = $offerRs->addChild('acceptedPaymentMethod','PaymentMethod');
        $paymentMethodRs->setVal('name',array("http://purl.org/goodrelations/v1#AmericanExpress", "http://purl.org/goodrelations/v1#MasterCard", "http://purl.org/goodrelations/v1#VISA", "http://purl.org/goodrelations/v1#Discover"));

//get all of the featured tours
$toursRS = array();
foreach($featured as $event) {
    $imageUrl = null;

    //get the featured image
    foreach($event['EventsImage'] as $image) {
        if($image['feature']) {
            $imageUrl = $image['images_name'];
            break;
        }
    }

    $productRs = $this->RichSnippets->create('Product');
    $productRs->setVal('name',$event['Event']['name_short']);
    $productRs->setVal('url',FULL_BASE_URL . "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}");
    $productRs->setVal('image',$imageUrl);
        $productOfferRs = $productRs->addChild('offers','Offer');
        $productOfferRs->setVal('price', $event['Event']['adults_price']);
        $productOfferRs->setVal('priceCurrency','EUR');
        $productOfferRs->setVal('availability','InStock');

    $toursRS[] = $productRs->getArray();
}

$offerRs->setVal('itemOffered',$toursRS);



echo $localBusinessRS->getJSON();
$this->end();
?>
<section class="as-featured-on">
    <div class="container">
        <h4>Recomendados por</h4>
        <div class="images">
            <a style="cursor:default">
                <img src="theme/ItalyEs/img/featured-tripadvisor.png" alt="TripAdvisor">
            </a>
            <a style="cursor:default">
                <img src="theme/ItalyEs/img/featured-rick.png" alt="Rick Steves">
            </a>
            <a style="cursor:default">
                <img src="theme/ItalyEs/img/featured-fodors.png" alt="Fodors">
            </a>
            <a style="cursor:default">
                <img src="theme/ItalyEs/img/featured-huffington.png" alt="Huffington Post">
            </a>
        </div>
    </div>
</section>
<section class="small-groups">
    <div class="container">
        <h2>GRUPOS REDUCIDOS, TOURS SOLO EN ESPAÑOL</h2>
        <p>Walks of Italy lleva desde 2007 ofreciendo tours por toda Italia para grupos reducidos. Seguimos creciendo y ahora incluimos tours solo en español; para viajeros de habla hispana con guías de habla hispana. Tu tour será completamente en español con la misma idea de grupos reducidos y acceso especial que lleva proporcionando Walks of Italy desde 2007.</p>
    </div>
</section>
<section id="content" class="home-activities">
    <?php for ($i = 0; $i < min(count($featured), 6); $i++): ?>
    <?php
        $event = $featured[$i];
        foreach($event['EventsImage'] as $image) {
            if($image['feature']) {
                $imageUrl = $image['images_name'];
                break;
            }
        }
    ?>

    <a class="activity" 
        data-position="<?php echo ($i + 1) ?>" 
        data-name="<?php echo $event['Event']['name_long'] ?>" 
        data-id="<?php echo $event['Event']['id'] ?>" 
        data-href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>" 
        onclick="ecOnProductClick(event, this); return !ga.loaded;"
        href="<?php echo "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}" ?>">
        <div class="image-wrapper">
            <img src="<?php echo $this->ReSrc->resrcUrl($imageUrl, 480) ?>" alt="" width="480">
            <p class="overlay"></p>
            <div class="image-text">
                <div class="reviews">
                    <div class="stars">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>z
                    </div>
                    <div class="text">
                        Basado en <div onclick="window.location='<?php echo isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['amount'] : ''?>'"> opiniones</div>
                    </div>
                </div>
                <div class="price">
                    <?php echo ExchangeRate::convert($event['Event']['adults_price']) ?>
                </div>
            </div>
        </div> <!-- image-wrapper -->
        <div class="activity-info">
            <div class="activity-title"><p><?php echo $event['Event']['name_listing'] ?></p></div>
            <div class="activity-key-info">
                <div class="key-info-item place">
                    <i class="fa fa-map-marker"></i>
                    Ciudad: <?php echo $event['EventsDomainsGroup'][0]['DomainsGroup']['name'] ?>
                </div>
                <div class="key-info-item time">
                    <i class="fa fa-clock-o"></i>
                    <?php echo $event['Event']['display_duration'] ?>
                </div>
                <div class="key-info-item">
                    <i class="fa fa-arrow-right"></i>
                    Hora de inicio: <?php echo $event['Event']['display_time'] ?>
                </div>
            </div>
            <div class="activity-description">
                <?php echo str_replace("\n",'<br />',$event['Event']['description_listing']); ?>
            </div>
        </div>
        <div class="activity-link">
            <div class="button green">
                <i class="fa fa-info-circle"></i>
                más información
            </div>
        </div>
    </a>
    <?php endfor ?>
</section>
<section class="new-testimonial slick-dots-fix">
    <div class="container">
        <div class="outer-wrapper">
            <div class="testimonial-slider" id="new-testimonial-slider">
                <div>
                    <div class="testimonial-text">
                        ¡Paseos increíbles con guías informativos, divertidos y amables! Las 2 visitas guiadas fueron el punto culminante de nuestro viaje a Roma.
                    </div>
                    <div class="testimonial-footer">
                        <div class="author">Jacqueline Chen</div>
                        <div class="link">Coliseo Premium</div>
                    </div>
                </div>
                <div>
                    <div class="testimonial-text">
                        ¡Hicimos tres visitas guiadas y todas eran geniales! Los guías eran todos muy agradables, simpáticos y bien informados. Nunca esperamos en colas y las pocas veces que si, duraron pocos minutos. También los guías compartieron datos y información divertida y/o útil. Realmente vale el dinero que pagas.
                    </div>
                    <div class="testimonial-footer">
                        <div class="author">Judith Niekel</div>
                        <div class="link">Vaticano con Entrada Temprana</div>
                    </div>
                </div>
                <div>
                    <div class="testimonial-text">
                        Maravillosa visita. Muy recomendable, especialmente si sólo tienes unos días en Roma, pero deseas ver todos los sitios más destacados.
                    </div>
                    <div class="testimonial-footer">
                        <div class="author">Miriana Hart</div>
                        <div class="link">Roma en un Día</div>
                    </div>
                </div>
            </div>
            <div class="quote-image quote-left"><img src="theme/ItalyEs/img/quote-left.png" alt=""></div>
            <div class="quote-image quote-right"><img src="theme/ItalyEs/img/quote-right.png" alt=""></div>
        </div>
    </div>
</section>

<section class="home-tripadvisor-section slick-dots-fix">
    <div class="container">
        <div class="image-wrap">
            <div class="tripadvisor-es-text">Certificado de Excelencia</div>
            <img src="theme/ItalyEs/img/tripadvisor-es.png" alt="">
        </div>
        <div class="slider-wrap">
            <h2 style="font-size:2em">Más de <b style="font-size:1.5em">2,000</b> opiniones con 5 <i class="fa fa-star"></i> y sumando</h2>

            <div id="home-tripadvisor-slider" class="tripadvisor-slider">
                <div>
                    <div class="testimonial-text">
                        ¡Que tours increíbles! Los guías eran increíbles y el hecho de que nos dejaron entrar en la Capilla Sixtina antes del público general era alucinante.
                    </div>
                    <div class="testimonial-author">
                        <div class="userpic">
                            <img src="theme/ItalyEs/img/person-head-icon-7.jpg" alt="">
                        </div>
                        <div class="author-info">
                            <div class="name">Richard W.</div>
                            <div class="date">Añadido en: Dec 10, 2015</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="testimonial-text">
                        Una empresa genial con guías muy competentes!
                    </div>
                    <div class="testimonial-author">
                        <div class="userpic">
                            <img src="theme/ItalyEs/img/person-head-icon-7.jpg" alt="">
                        </div>
                        <div class="author-info">
                            <div class="name">Nicholas M. Añadido</div>
                            <div class="date">Añadido en: Dec 3, 2015</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="testimonial-text">
                        Recomendamos Walks of Italy y especialmente nuestra guía, Camilla, sin ninguna duda.
                    </div>
                    <div class="testimonial-author">
                        <div class="userpic">
                            <img src="theme/ItalyEs/img/person-head-icon-7.jpg" alt="">
                        </div>
                        <div class="author-info">
                            <div class="name">nfall2010</div>
                            <div class="date">Añadido en: Nov 25, 2015</div>
                        </div>
                    </div>
                </div>
                <a href="#" class="button green">VER TODAS LAS OPINIONES</a>
            </div>
        </div>
    </div>
</section>

<section class="bottom-section">
    <div class="container">
        <div class="questions">
            <h2>¿POR QUÉ ELEGIRNOS?</h2>

            <div class="question">
                <div class="question-image">
                    <img src="theme/ItalyEs/img/question-1-icon.png" alt="">
                </div>
                <div class="question-text">
                    <h4>Guías Expertos</h4>
                    <div class="question-entry">
                        Solo trabajamos con los mejores guías locales (expertos en arte, historia y arqueología) con un español perfecto.
                    </div>
                </div>
            </div>
            <div class="question">
                <div class="question-image">
                    <img src="theme/ItalyEs/img/question-2-icon.png" alt="">
                </div>
                <div class="question-text">
                    <h4>¡Evita Las Colas!</h4>
                    <div class="question-entry">
                        Con nuestra entrada rápida podrás evitar las colas en los sitios principales de Roma, incluso el Coliseo y El Vaticano, sin perder un solo segundo.
                    </div>
                </div>
            </div>
            <div class="question">
                <div class="question-image">
                    <img src="theme/ItalyEs/img/question-3-icon.png" alt="">
                </div>
                <div class="question-text">
                    <h4>Entrada temprana</h4>
                    <div class="question-entry">
                        Nuestra visita guiada al Vaticano con entrada temprana te permitirá acceder al Vaticano antes que el público general - e incluso más temprano que ningun otro operador turístico.
                    </div>
                </div>
            </div>
        </div>

        <div class="blog">
            <h2>Blog</h2>
            <?php for($n = 0; $n < min(3, count($blog_posts)); $n++): ?>
            <div class="blog-entry">
                <div class="blog-image"><img src="<?php echo str_replace('http:','',$theme->blogUrl) . $blog_posts[$n]['thumbnail'] ?>" alt="<?php echo $blog_posts[$n]['wp']['post_title'] ?>"></div>
                <div class="blog-text">
                    <h4><?php echo $blog_posts[$n]['wp']['post_title'] ?></h4>
                    <p><?php echo isset($blog_posts[$n]['wp']['summary']) ? $blog_posts[$n]['wp']['summary'] : ''; ?></p>
                    <a href="<?php echo $theme->blogUrl . $blog_posts[$n]['wpt2']['slug'] . '/' . $blog_posts[$n]['wp']['post_name'] ?>" class="button brown">
                        <i class="fa fa-bars"></i>
                        Lee Más
                    </a>
                </div>
            </div>
            <?php endfor ?>
        </div>
    </div>
</section>

<footer class="new-footer">
    <div class="container">
        <div>
            <a href="https://www.facebook.com/walkingtours/" class="text-left gold"><i class="fa fa-facebook"></i> Síguenos en Facebook</a>
            <span class="text-right black">© Derecho de Autor por Walks of Italy 2015 Via di Santa Maria dell'Anima 48, 00186 Roma, Italy</span>
        </div>
    </div>
</footer>
