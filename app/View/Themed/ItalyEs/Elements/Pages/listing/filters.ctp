<form method="get" class="form-b" target="_top">
    <?php if(isset($query['sort'])) : ?>
        <input type="hidden" value="<?php echo $query['sort'] ?>">
    <?php endif ?>
    <fieldset>
        <h2>Search tours</h2>
        <h3>Dates</h3>
        <p>
                        <span>
                            <label for="fba">Start date</label>
                            <input type="text" id="fba" name="min_date" <?php if(!empty($filters['min_date'])) echo 'value="'.$filters['min_date'].'"'?>>
                        </span>
                        <span>
                            <label for="fbb">End date</label>
                            <input type="text" id="fbb" name="max_date" <?php if(!empty($filters['max_date'])) echo 'value="'.$filters['max_date'].'"'?>>
                        </span>
        </p>
        <h3>Location</h3>
        <div>
            <select id="faa" name="city" style="width: 100%">
                <?php foreach($locations as $loc): ?>
                    <option value="<?php echo $loc['DomainsGroup']['url_name'] ?>" <?php if($city['slug'] == $loc['DomainsGroup']['url_name']) echo 'selected'?>>
                        <?php echo $loc['DomainsGroup']['name'] ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <h3>Tour type</h3>
        <ul class="checklist-a custom-radio">
            <li>
                <input type="radio" id="fball" name="group_private[]" value="All" <?php if(in_array('All', $filters['group_private'])) echo 'checked' ?>>
                <label for="fball" class="nts"><span></span>All</label>
            </li>
            <li>
                <input type="radio" id="fbc" name="group_private[]" value="Group" <?php if(in_array('Group', $filters['group_private'])) echo 'checked' ?>>
                <label for="fbc" class="nts"><span></span>Group</label>
            </li>
            <li>
                <input type="radio" id="fbd" name="group_private[]" value="Private" <?php if(in_array('Private', $filters['group_private'])) echo 'checked' ?>>
                <label for="fbd" class="nts"><span></span>Private</label>
            </li>
        </ul>
        <h3>Category</h3>
        <ul class="checklist-a" id="filters_tags">
            <?php foreach($tags as $tag) : ?>
                <li class="custom-checkboxes nts <?php if($tag['Tag']['tag_type'] == 2) echo 'hidden'?>">
                    <input type="checkbox" id="tag_<?php echo $tag['Tag']['id'] ?>" name="type[]" value="<?php echo $tag['Tag']['id'] ?>" <?php if(!empty($filters['type']) && in_array($tag['Tag']['id'], $filters['type'])) echo 'checked' ?>>
                    <label for="tag_<?php echo $tag['Tag']['id'] ?>"><?php echo $tag['Tag']['name'] ?></label>
                </li>
            <?php endforeach; ?>

            <li class="show-more-wrapper">
                <a id="show_more_tags">Show More</a>
            </li>

        </ul>

        <p><button type="submit"><i class="fa fa-check-square-o"></i> Apply filters</button></p>
        <p class="reset"><a>Clear</a></p>
    </fieldset>
</form>