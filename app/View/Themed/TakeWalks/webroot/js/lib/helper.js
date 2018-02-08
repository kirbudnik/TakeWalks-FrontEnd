window.Helper = {
    template: {},
    templatesLoaded: false,
    init: function(){
        window.Helper.loadTemplates();

        // notification defaults
        $.notify.defaults({
            globalPosition: 'bottom right',
            // clickToHide: false,
            // whether to auto-hide the notification
            // autoHide: false,
        });

    },
    getCookie: function(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length === 2) return parts.pop().split(";").shift();
    },

    loadTemplates: function(){
        //only load once
        if(window.Helper.templatesLoaded) return;

        $('.htmlTemplate').each(function(){
            window.Helper.template[$(this).data('name')] = _.template($(this).html());
        });
        window.Helper.templatesLoaded = true;
    },
    showModal: function($modal){
        //disable body scrolling
        $('body').css('overflow', 'hidden');
        window.scrollTo(0,0);


        //$('.modal, .modal-inner').addClass('active');
        $modal.addClass('active');
    },
    hideModal: function($modal){
        //enable body scrolling
        $('body').css('overflow', 'auto');

        $modal.removeClass('active');
    },
    //all modals must pass through here
    initModal: function($modal){
        $modal.find('>div').click(function(e){
            e.stopPropagation();
        });
        $modal.click(function(){ window.Helper.hideModal($modal); });
    },
    formatDateTime: function(date){
        return moment(date).format('MM/DD/YYYY h:mm:ss a');
    },
    formatDate: function(date){
        return moment(date).format('MM/DD/YYYY');
    },
    /**
     @param $form jquery reference to a form
     @param options post() options

     Extracts the url and data
     */
    postFromForm: function($form, options){
        options = $.extend(options, {
            url: $form.attr('action'),
            data: $form.serialize(),
        });
        return window.Helper.post(options);
    },
    //create a post ajax
    /**
     *
     * @param options
     * url: url to submit to
     * data: post data
     * success(results): success function (results.status == 'success')
     * error(results): fail function  (results.status == false)
     * ajaxError(event): ajax request failed
     */
    post: function(options){
        options = $.extend({
            data: '',
            success: $.noop,
            error: $.noop,
            ajaxError: function(e){ console.log('Ajax error', e) }
        },options);

        return $.ajax({
            type: 'post',
            url: options.url,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', Helper.getCookie('csrfToken'));
            },
            data: options.data,
            success: function (result) {
                options.success(result);
            },
            errors: options.ajaxError
        });
    },
    //extract cake error messages
    errorsToArr: function(errors){
        var messages = [];
        for(var field in errors){
            for(var error in errors[field]){
                messages.push(errors[field][error]);
            }
        }
        return messages;
    },
    tabs: function($tabs, $tabTargetContainer, switchCallBack){
        $tabs.find('[data-tab-target]').click(function(){
            $tabs.find('>.active').removeClass('active');
            $tabTargetContainer.find('>.active').removeClass('active');
            $(this).addClass('active');
            $tabTargetContainer.find('[data-tab-container="' + $(this).attr('data-tab-target') + '"]').addClass('active');
            switchCallBack($(this));
        });

    },
    fillForm: function($form,values){
        Object.keys(values).forEach(function(field){
            $form.find('[name="' + field + '"]').val(values[field]);
        });
        return $form;
    },
    loadImage: function(imgSrc){
        var curImg = new Image();
        curImg.src = imgSrc;
        curImg.onload = function(){

        }
    },isMobile: function() {
        var check = false;
        (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
        return check;
    }
};
$(window.Helper.init);

