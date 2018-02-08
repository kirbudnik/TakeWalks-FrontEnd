
var GoogleSignInWrapper = {
    btnLogin : null,
    googleUser : null,
    action : null,
    init: function(){
        GoogleSignInWrapper.btnLogin = $('.btnLoginGoogle');
        GoogleSignInWrapper.btnLogin.each(function(i,e){$(e).click(GoogleSignInWrapper.login)});

        gapi.load('auth2', function(){
            // Retrieve the singleton for the GoogleAuth library and set up the client.
            auth2 = gapi.auth2.init({
//                client_id: '290459239257-1gj6ohesqbsmm34utecvhr8bvla9cmcm.apps.googleusercontent.com',
                client_id: '1025766043026-6bdblevi4rt494m9v9ssrgp20r07bb3n.apps.googleusercontent.com',
                cookiepolicy: 'single_host_origin',
                // Request scopes in addition to 'profile' and 'email'
                //scope: 'additional_scope'
            });
            GoogleSignInWrapper.btnLogin.each(function(i,e){
                GoogleSignInWrapper.attachSignin(document.getElementById( $(e).attr('id') ));
            });
        });
    },
    login: function(event){
        event.preventDefault();
        GoogleSignInWrapper.action = event.currentTarget.dataset.socialAction;
    },
    attachSignin: function(element){
        auth2.attachClickHandler(element, {},
            function(googleUser) {
                GoogleSignInWrapper.onSignIn(googleUser);
            }, function(error) {
                // user click "cancel" in google popup
                //alert(JSON.stringify(error, undefined, 2));
            });
    },
    onSignIn: function(googleUser){
        GoogleSignInWrapper.googleUser = googleUser;
        var profile = googleUser.getBasicProfile();
        var url = '';
        var data = {};
        var redirect = '';

        if(GoogleSignInWrapper.action === 'register') {
            url = '/user/login/social';
            data = {
                provider: 'google',
                socialUserId: profile.getId(),
                first_name: profile.getGivenName(),
                last_name: profile.getFamilyName(),
                email: profile.getEmail(),
                password: profile.getEmail().split('@')[0]
            };
            redirect = '/account';
        } else if(GoogleSignInWrapper.action === 'connect') {
            url = '/user/social';
            data = {
                provider: 'google',
                socialUserId: profile.getId()
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