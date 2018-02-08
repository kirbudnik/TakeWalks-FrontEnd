(function(){
    $('.reset-calendar').on('click', function(event) {
        $('input[name=start_date]').datepick('setDate', new Date());
        $('input[name=end_date]').val('');
    });
})();