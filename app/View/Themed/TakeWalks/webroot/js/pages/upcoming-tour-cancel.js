var UpcomingTourCancel = {
    cancelTour: $('#cancelTour'),
    init: function(){
        UpcomingTourCancel.cancelTour.click(UpcomingTourCancel.bookingCancel);
    },
    bookingCancel: function(event) {
        var selectedOption = $('input:radio:checked').val();
        var otherOption = $('#cancelTourOtherInput').val();

        event.preventDefault();
        $('body').css({cursor:'wait'});
        $.ajax({
            data: { 
                bookingDetailsId: this.dataset.bookingDetailsId,
                selectedOption: selectedOption,
                otherOption: otherOption,
            },
            url: '/user/booking-cancel',
            method: 'post'
        }).done(function(response) {
            window.location.href = '/account';
        }).fail(function(response) {
            window.location.href = '/account';
        });
        return false;

    }
};

(function(){
    UpcomingTourCancel.init();
})();

