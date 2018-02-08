<?= $this->element('header'); ?>

<main class="default">
    <div class="container">
        <ul class="breadcrumbs no-offset">
            <li><a href="#">Take Walks</a></li>
            <li class="active"><a href="#"><?= $fullName ?></a></li>
        </ul>

        <h1 class="page-title">Meet Our Local Tour Guides</h1>
    </div>

    <section class="grey">
        <div class="container">
            <div class="guide-detail">
<!--                <div class="col img-col" style="background-image: url(/theme/TakeWalks/img/guides/angelo.jpg)">-->
                <div class="col img-col" style="background-image: url(<?= $tourGuideImage ?>)">
                </div>

                <div class="col">
                    <div class="text">
                        <h2><?= $fullName ?></h2>
                        <h5 class="subtitle green"><?= $city ?>, <?= $country ?></h5>
                        <p class="descr"><?= $description ?></p>

                        <div class="tags">
                            <?php foreach ($tours as $tour): ?>
                            <div class="col"><a href="<?= DS.$tour['citySlug'].DS.$tour['slug'] ?>" class="guide-tag"><?= $tour['titleShort'] ?></a></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="handwriting guide-quote">"<?= $quote ?>"</h2>

            <h2>About <?= explode(' ', $fullName)[0] ?></h2>

            <div class="guide-description">
                <?= str_replace('<p>', '<p class="descr">', $descriptionLong) ?>
            </div>
<!--            <div class="guide-description">-->
<!--                <p class="descr"><b>Some paragraph title</b></p>-->
<!--                <p class="descr">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters</p>-->
<!---->
<!--                <p class="descr"><b>What is Lorem Ipsum?</b></p>-->
<!--                <p class="descr">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</p>-->
<!---->
<!--                <p class="descr"><b>Why do we use it?</b></p>-->
<!--                <p class="descr">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable.</p>-->
<!--            </div>-->
        </div>
    </section>
</main>

<?= $this->element('footer'); ?>
