// Sort dropdown
$(function(){
    $('select#fac').on('change', function() {
        document.location.href = $(':checked', this).attr('data-url');
    })

});

//Filter Reset Logic
$(function() {
    $('.reset a').on('click', function() {
        //Date reset
        $('.form-b #fba')
            .removeAttr('value')
            .val('')
            .datepicker('setDate', null);
        $('.form-b #fbb')
            .removeAttr('value')
            .datepicker('setDate', null);

        // Type reset
        $('.form-b #fbc, .form-b #fbd')
            .removeAttr('checked')
            .val('')
            .prop('checked', false);

        //Category reset
        $('.form-b input[type=checkbox], .form-b input[type=radio]')
            .removeAttr('checked')
            .prop('checked', false);

        $(this).closest('form').get(0).submit();
    })
});

// Calendar popup
$(function() {
	$('.form-b #fba').datepicker({
        minDate: 0,
        dateFormat: 'M d, yy',
        onSelect: function(date) {
            $('.form-b #fbb').datepicker('option', 'minDate', new Date(date));
        }
    });
	$('.form-b #fbb').datepicker({
        minDate: 0,
        dateFormat: 'M d, yy',
		pickerClass: 'endDatePick'
	});
});

$(function() {
    $('.nav-a').on('click', '.close', function(e) {
        e.stopPropagation();

        var tag_id = $(this).closest('[data-id]').attr('data-id');
        $('#filters_tags').find('input[type=checkbox][value='+tag_id+']').prop('checked', false);

        $('form.form-b').get(0).submit();
    });

    $('#show_more_tags').on('click', function(e) {
        e.stopPropagation();

        var li = $(this).parent();
        li.siblings().removeClass('hidden');
        li.addClass('hidden');
    });


});
