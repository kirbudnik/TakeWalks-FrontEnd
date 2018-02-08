var PastTourRefund = {
    refundTour: $('#refundTour'),
    init: function(){
        PastTourRefund.refundTour.click(PastTourRefund.bookingCancel);
    },
    bookingCancel: function(event) {
        var selectedOption = $('input:radio:checked').val();

        event.preventDefault();
        $('body').css({cursor:'wait'});
        $.ajax({
            data: { 
                bookingDetailsId: this.dataset.bookingDetailsId,
                selectedOption: selectedOption,
            },
            url: '/user/booking-refund',
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
    PastTourRefund.init();
})();

