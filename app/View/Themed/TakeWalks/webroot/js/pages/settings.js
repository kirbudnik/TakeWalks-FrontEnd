var Settings = {
    addDestination : $('#addDestination'),
    addTraveler: $('#addTraveler'),
    init: function(){
        $('#form-edit').find('input').each(function(i,e){ $(e).trigger('focus'); });
        window.scrollTo(0, 0);
        $('.formSubmit').submit(Settings.formSubmit);
        Settings.addDestination.click(Settings.addUpcomingTrip);
        Settings.addTraveler.click(Settings.addAdditionalTraveler);
    },
    formSubmit: function(event) {
        event.preventDefault();
        var form = $(event.currentTarget);
        var url = event.currentTarget.action;
        var data = form.serialize();

        $('body').css({cursor:'wait'});
//            form.find('[type=submit]').attr('disabled', 'disabled');
        $.ajax({ data: data, url: url, method: 'post' }
        ).done(function(response) {
            $('body').css({cursor:'default'});
            if (response.success) {
                form.find('[type=submit]').html('DONE!');
                setTimeout(function(){window.location.reload();}, 1000);
            } else {
                if (response.results !== undefined && response.results.message !== undefined) {
                    var errorMessage = response.results.message;
                    /*
                     for (var i in response.results.data ){
                     var e = response.results.data[i];
                     if (e[0] !== undefined && e[0] !== errorMessage) errorMessage += '<br>' + e[0];
                     }
                     */
                    form.find('.error-message').html(errorMessage).show();
                } else {
                    window.location.reload();
                }
            }
        }).fail(function(response){
            //$('body').css({cursor:'default'});
            window.location.reload();
        });
        return false;
    },
    addForm: function(selector, template) {
        var container = $(selector);
        container.append('<div></div>');
        container.find('div:last').append(_.template($(template).html()));
        applyDatePicker('.foo-datepick');
        $('.remove-form').click(Settings.removeForm);
        $('body').on('blur', '.foo-validate input',function(){
            $(this).parents('.input-div').toggleClass('valid', $(this).val() !== '');
        });

    },
    removeForm: function(event) {
        event.preventDefault();
        $(this).parent().remove();
    },
    addUpcomingTrip: function(event) {
        event.preventDefault();
        Settings.addForm('#upcoming-trip-information', 'script.divUpcomingTripInfo');
    },
    addAdditionalTraveler: function(event) {
        event.preventDefault();
        Settings.addForm('#additional-travelers', 'script.divAdditionalTraveler');
    }
};

(function(){
    setTimeout(function(){
        Settings.init();
    }, 100);
})();

