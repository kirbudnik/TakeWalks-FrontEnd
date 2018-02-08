<?php
$this->start('headBottom');
foreach($events as $event){

	$imageUrl = null;

	//get the featured image
	foreach($event['EventsImage'] as $image) {
		if($image['feature']) {
			$imageUrl = $image['images_name'];
			break;
		}
	}

	if(isset($event['EventsStagePaxRemaining'][0]) && $event['Event']['street'] != null){
		echo $this->element('RichSnippets/event', array(
			'name' => $event['Event']['name_short'],
			'description' => $event['Event']['description_short'],
			'url' => FULL_BASE_URL . "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}",
			'startDate' => $event['EventsStagePaxRemaining'][0]['datetime'],
			//'endDate' => $event['EventsSchedule'][0]['date_end'] != '0000-00-00' ? $event['EventsSchedule'][0]['date_end'] : $event['EventsSchedule'][0]['date_start'],
			'country' => $event['Event']['country'],
			'city' => $event['Event']['city'],
			'zip' => $event['Event']['zip_code'],
			'address' => ($event['Event']['street_number'] != null ? $event['Event']['street_number'] . ' ' : '') . $event['Event']['street'],
			'latitude' => $event['Event']['latitude'],
			'longitude' => $event['Event']['longitude'],
			'price' => $event['EventsStagePaxRemaining'][0]['adults_price'],
			'currency' => 'USD',
			'email' => 'info@walksofnewyork.com',
			'telephone' => '1-888-683-8671',
			'averageRating' => isset($ratings[$event['Event']['id']]['average']) ? $ratings[$event['Event']['id']]['average'] : null,
			'image' => $imageUrl
		));


		echo $this->element('RichSnippets/product',array(
			'name' => $event['Event']['name_short'],
			'description' => $event['Event']['description_short'],
			'brand' => 'Walks of Italy',
			'price' => $event['EventsStagePaxRemaining'][0]['adults_price'],
			'currency' => 'USD',
			'url' => FULL_BASE_URL . "/{$event['EventsDomainsGroup'][0]['DomainsGroup']['url_name']}-tours/{$event['Event']['url_name']}",
			'image' => $imageUrl,
			'averageRating' => isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['average'] : null,
			'reviewCount' => isset($ratings[$event['Event']['id']]) ? $ratings[$event['Event']['id']]['amount'] : null,
		));
	}



}

$this->end();
?>
<div class="controls">
	<a class="filters"><img src="/img/filter.png" alt="Filter results"></a>
	<a class="sort"><img src="/img/sort.png" alt="Sort results"></a>
	<h1>All Tours</h1>
</div>

<section class="hero">
	<div class="wrap content">
		<h1 class="larger"><span><?php echo $num_results ?></span> New York Walking Tours</h1>
		<p class="serif">Prices starting from $<?php echo $min_price ?>.00</p>
		<a class="small sort">
			Sort Results
			<span><?php echo $sort_name ?></span>
		</a>
	</div>
</section>

<form action="" class="listingSort">
	<fieldset>
		<label>
			<input name="sort" type="radio" value="popular" <?php if($sort == 'popular') echo 'checked'?> data-url="<?php echo Router::url(array('?' => array('sort' => 'popular') + $query)) ?>">
			<span>Most Popular</span>
		</label>
		<label>
			<input name="sort" type="radio" value="best" <?php if($sort == 'best') echo 'checked'?> data-url="<?php echo Router::url(array('?' => array('sort' => 'best') + $query)) ?>">
			<span>Best Rated</span>
		</label>
		<label>
			<input name="sort" type="radio" value="priceLow" <?php if($sort == 'priceLow') echo 'checked'?> data-url="<?php echo Router::url(array('?' => array('sort' => 'priceLow') + $query)) ?>">
			<span>Lowest Price</span>
		</label>
		<label>
			<input name="sort" type="radio" value="priceHigh" <?php if($sort == 'priceHigh') echo 'checked'?> data-url="<?php echo Router::url(array('?' => array('sort' => 'priceHigh') + $query)) ?>">
			<span>Highest Price</span>
		</label>
	</fieldset>
</form>

<div class="wrap content">

	<form class="listingFilters" method="get">
		<input id="filter_reset" type="reset" value="clear">
		<h3>Filter Results</h3>

		<fieldset class="dates dark">
			<legend>Dates</legend>
			<label>
				<span>Start date</span>
				<input type="text" readonly name="min_date" class="startDate" <?php if(!empty($filters['min_date'])) echo 'value="'.$filters['min_date'].'"'?>>
			</label>
			<label>
				<span>End date</span>
				<input type="text" readonly name="max_date" class="endDate" <?php if(!empty($filters['max_date'])) echo 'value="'.$filters['max_date'].'"'?>>
			</label>
		</fieldset>

		<fieldset>
			<legend>Guests</legend>
			<select id="guests" name="guests">
                <?php for ($n = 1; $n <= 12; $n++) { ?>
                    <option value="<?php echo $n ?>" <?php if(!empty($filters['guests']) && $filters['guests'] == $n) echo 'selected' ?>>
                        <?php echo $n ?> Guest<?php echo $n > 1 ? 's' : '' ?>
                    </option>
                <?php } ?>
			</select>
		</fieldset>

		<fieldset id="type" class="type dark">
			<legend>Tour Type</legend>
			<label>
				<input type="checkbox" name="group_private[]" value="Group" <?php if(!empty($filters['group_private']) && in_array('Group', $filters['group_private'])) echo 'checked' ?>>
				<span><img src="../img/group-darkgrey.png" alt=""> Group</span>
			</label>
			<label>
				<input type="checkbox" name="group_private[]" value="Private" <?php if(!empty($filters['group_private']) && in_array('Private', $filters['group_private'])) echo 'checked' ?>>
				<span><img src="../img/private-darkgrey.png" alt=""> Private</span>
			</label>
		</fieldset>

		<fieldset class="price">
			<legend>Price</legend>
			<label class="large">
				<span>Min</span>
				<input type="numeric" name="min_price" class="priceMin" placeholder="Min" <?php if(!empty($filters['min_price'])) echo 'value="'.$filters['min_price'].'"'?>>
			</label>
			<label class="large">
				<span>Max</span>
				<input type="numeric" name="max_price" class="priceMax" placeholder="Max" <?php if(!empty($filters['max_price'])) echo 'value="'.$filters['max_price'].'"'?>>
			</label>
			<div class="priceSlider"></div>
		</fieldset>

		<fieldset class="category dark">
			<legend>Category</legend>
            <?php foreach($tags as $tag) : ?>
                <label>
                    <input type="checkbox" name="type[]" value="<?php echo $tag['Tag']['id'] ?>" <?php if(!empty($filters['type']) && in_array($tag['Tag']['id'], $filters['type'])) echo 'checked' ?>>
				<span>
					<img src="/img/<?php echo $tag['Tag']['url_name'] ?>-darkgrey.png" alt="">
					<?php echo $tag['Tag']['name']; ?>
				</span>
                </label>
            <?php endforeach; ?>
		</fieldset>
		<input type="submit" class="pink button" value="Apply Filters">
	</form>

	<ul class="listingResults">
        <?php if(empty($events)) : ?>
            <p>Your search did not match any tours.  Please broaden your search criteria.  In the meantime, here are some featured tours:</p>
            <?php $events = $featured; ?>
        <?php endif; ?>
        <?php $itemPosition = 0; foreach($events as $event) : ?>
            <?php $itemPosition++; echo $this->element('Pages/listing/result', compact('event', 'itemPosition')); ?>
        <?php endforeach; ?>
	</ul>

</div>
