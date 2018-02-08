var VerifyPromo = {
    Books : null,
    init: function(){
        this.Books = initValues;

        //make the books select
        for(var i=0;i<this.Books.length; i++){
            $('#chooseBook select').append(
                $('<option/>',{value: i}).html(this.Books[i]['name'])
            );
        }

        $('#chooseBook select').change(this.showQuestion);
        $('.goback').click(this.goBack);

    },
    showQuestion: function(){
        var book = VerifyPromo.Books[$(this).val()];
        var question = book.questions[Math.round(Math.random() * (book.questions.length-1) )];

        $('.goback.active').hide();
        $('#question input[name="question_id"]').val(question['id']);
        $('#question .bookQuestion').html(question['question']);
        $('#question').slideDown();

    },
    goBack: function(event){
        event.preventDefault();
        $('#goback_form').submit();
    }

};

$(function(){
    VerifyPromo.init();
});