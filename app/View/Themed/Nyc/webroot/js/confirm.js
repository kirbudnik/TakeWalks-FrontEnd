// Calendar popup
$(function() {
	$('.startDate').datepick({
        minDate: 0,
        dateFormat: 'M d, yyyy',
        onSelect: function(dates) {
            $('.endDate').datepick('option', 'minDate', dates[0]);
            $('.startDate').trigger('change');
        }
    });
	$('.endDate').datepick({
        minDate: 0,
        dateFormat: 'M d, yyyy',
		alignment: 'bottomRight',
		pickerClass: 'endDatePick',
        onSelect: function(dates){
            $('.endDate').trigger('change');
        }
	});

    // Check that form is complete before activating submit button
    $('form').on('change', 'input', function(){
        var ready = true;
        $('div#hotels fieldset input').each(function() {
            if($(this).val().length == 0) {
                ready = false;
                return false;
            }
        });
        if ( ready || $('input[name="noHotel"]').is(':checked') ) {
            $('input[type="submit"]').removeAttr('disabled');
        } else {
            $('input[type="submit"]').attr('disabled','disabled');
        }
    });

    var n = 1;
    $('a#new_hotel').on('click', function() {
        var template = $('<fieldset class="serif">' +
            '<legend>Hotel</legend>' +
            '<label>Hotel name<input type="text" name="data['+n+'][BookingsAddress][hotel_name]"></label>' +
            '<label>Hotel phone<input type="text" name="data['+n+'][BookingsAddress][hotel_telephone]"></label>' +
            '<label>Arrival<input type="text" class="startDate" name="data['+n+'][BookingsAddress][staying_from]"></label>' +
            '<label>Departure<input type="text" class="endDate" name="data['+n+'][BookingsAddress][staying_to]"></label>' +
            '<a class="grey small button" id="remove_hotel">Remove this hotel</a>' +
            '</fieldset>');
        n++;

        $('div#hotels').append(template);
        $('.startDate', template).datepick({
            minDate: 0,
            dateFormat: 'M d, yyyy',
            onSelect: function(dates) {
                $('.endDate', template).datepick('option', 'minDate', dates[0]);
                $('.startDate',template).trigger('change');
            }
        });
        $('.endDate', template).datepick({
            minDate: 0,
            dateFormat: 'M d, yyyy',
            alignment: 'bottomRight',
            pickerClass: 'endDatePick',
            onSelect: function(dates) {
                $('.endDate', template).trigger('change');
            }
        });

        $('input', template).first().trigger('change');
    });

    $('div#hotels').on('click', 'a#remove_hotel', function() {
        $(this).parent().remove();
        $('input', this).first().trigger('change');
    })
});