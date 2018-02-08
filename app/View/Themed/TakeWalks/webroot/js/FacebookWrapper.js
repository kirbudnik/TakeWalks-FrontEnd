var FacebookWrapper = {
    btnLogin : null,
    btnSignUp : null,
    loginStatus : null,
    user : null,
    action : null,
    init: function(){
        FacebookWrapper.btnLogin = $('.btnLoginFacebook');
        FacebookWrapper.btnLogin.each(function(i,e){$(e).click(FacebookWrapper.login)});
        FacebookWrapper.btnSignUp = $('#btnSignUp');
    },
    login: function(event){
        event.preventDefault();
        FacebookWrapper.action = event.currentTarget.dataset.socialAction;
        FB.getLoginStatus(function(response) {
            FacebookWrapper.loginStatus = response;
            if (response.status !== 'connected') {
                FB.login(function(loginResponse){
                    FacebookWrapper.loginStatus = loginResponse;
                    if (FacebookWrapper.loginStatus.status === 'connected') {
                        FacebookWrapper.queryFbUser();
                    } else {
                        // user click "cancel" in facebook popup
                    }
                }, {scope: 'email,public_profile'});
            } else if (response.status === 'connected') {
                FacebookWrapper.queryFbUser();
            }
        });
    },
    queryFbUser: function() {
        FB.api( "/me?fields=email,first_name,last_name,picture",
            function (response) {
                FacebookWrapper.user = response;
                if (response && !response.error && response.email !== undefined) {
                    $('#facebook_id').val(FacebookWrapper.user.id);
                    FacebookWrapper.createUserWithFbId();
                } else {
                    FacebookWrapper.btnLogin.hide();
                    FacebookWrapper.btnSignUp.trigger('click');
                    $('[name=first_name]').val(FacebookWrapper.user.first_name).trigger('focus');
                    $('[name=last_name]').val(FacebookWrapper.user.last_name).trigger('focus');
                    $('[name=email]').trigger('focus');
                }
            }
        );
    },
    getUserByFbId: function(facebook_id) {
        var data = {facebook_id: facebook_id};
        $.ajax({ data: data, url: '/user/get', method: 'post' }
        ).done(function(response) {
            $('body').css({cursor:'default'});
            window.location.href = '/account';
        }).fail(function(response){
            $('body').css({cursor:'default'});
            window.location.reload();
        });
    },
    loginUserWithFbId: function() {
        var data = {
            facebook_id: FacebookWrapper.user.id,
            email: FacebookWrapper.user.email,
            password: FacebookWrapper.user.email.split('@')[0]
        };
        $('body').css({cursor:'wait'});
        $.ajax({ data: data, url: '/user/login', method: 'post' }
        ).done(function(response) {
            $('body').css({cursor:'default'});
            if (response.success) {
                window.location.href = '/account';
            } else {
                window.location.reload();
            }
        }).fail(function(response){
            $('body').css({cursor:'default'});
            window.location.reload();
        });
    },
    createUserWithFbId: function() {
        var url = '';
        var data = {};
        var redirect = '';

        if(FacebookWrapper.action === 'register') {
            url = '/user/login/social';
            data = {
                provider: 'facebook',
                socialUserId: FacebookWrapper.user.id,
                first_name: FacebookWrapper.user.first_name,
                last_name: FacebookWrapper.user.last_name,
                email: FacebookWrapper.user.email,
                password: FacebookWrapper.user.email.split('@')[0]
            };
            redirect = '/account';
        } else if(FacebookWrapper.action === 'connect') {
            url = '/user/social';
            data = {
                provider: 'facebook',
                socialUserId: FacebookWrapper.user.id
            };
        }

        $('body').css({cursor:'wait'});
        $.ajax({ data: data, url: url, method: 'post' }
        ).done(function(response) {
            //$('body').css({cursor:'default'});
            if (redirect !== ''){
                window.location.href = redirect;
            } else {
                window.location.reload();
            }
        }).fail(function(response){
            //$('body').css({cursor:'default'});
            window.location.reload();
        });
    }

};