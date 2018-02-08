
// Filters dropdown (mobile)
$(function(){
	$('.controls .filters').click(function(){
		$('.listingFilters').toggleClass('expanded');
	});
});
$(document).on('click', function(e){ // Hide when click outside
    if (! $(e.target).closest('.listingFilters').length && ! $(e.target).closest('.filters').length )
        $('.listingFilters').removeClass('expanded');
});

// Sort dropdown (mobile)
$(function(){
	$('.sort').click(function(){
		$('.listingSort').toggleClass('expanded');
		$('.sort').toggleClass('expanded');
	});
    $('input[name=sort]').on('change', function() {
        $('a.sort span').html($(this).next().html());
        document.location.href = $(this).attr('data-url');
    })
});
$(document).on('click', function(e){ // Hide when click outside
    if (! $(e.target).closest('.listingSort').length && ! $(e.target).closest('.sort').length )
        $('.listingSort').removeClass('expanded');
});

//Filter Reset Logic
$(function() {
    $('input#filter_reset').on('click', function() {
        //Date reset
        $('.listingFilters .dates .startDate')
            .removeAttr('value')
            .datepick('clear');
        $('.listingFilters .dates .endDate')
            .removeAttr('value')
            .datepick('clear');

        //Guests reset
        $('.listingFilters .guests').val(1);

        // Type reset
        $('.listingFilters .type input')
            .removeAttr('checked')
            .prop('checked', false);

        //Price reset
        $(".priceSlider")
            .slider('values', [0, 200]);
        $(".priceMin")
            .removeAttr('value')
            .val('');
        $(".priceMax")
            .removeAttr('value')
            .val('');

        //Category reset
        $('.listingFilters .category input')
            .removeAttr('checked')
            .prop('checked', false);
    })
});

// Price slider
$(function() {
    var min = $('.priceMin').val() || 0;
    var max = $('.priceMax').val() || 200;
	$( ".priceSlider" ).slider({
		range: true,
		min: 0,
		max: 200,
		values: [min, max],
		slide: function(event, ui){
			$( ".priceMin" ).val(ui.values[0]);
			$( ".priceMax" ).val(ui.values[1]);
		}
	});
});

// Calendar popup
$(function() {
	$('.listingFilters .dates .startDate').datepick({
        minDate: 0,
        dateFormat: 'M d, yyyy',
        onSelect: function(dates) {
            $('.listingFilters .dates .endDate').datepick('option', 'minDate', dates[0]);
        }
    });
	$('.listingFilters .dates .endDate').datepick({
        minDate: 0,
        dateFormat: 'M d, yyyy',
		alignment: 'bottomRight',
		pickerClass: 'endDatePick'
	});
});
