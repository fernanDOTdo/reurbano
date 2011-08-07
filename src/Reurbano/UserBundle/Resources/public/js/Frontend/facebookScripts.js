    function fblogin(){
        FB.login(function(response) {
            if (response.session) {
                if (response.perms) {
                    // user is logged in and granted some permissions.
                    FacebookConnect.connect(false);
                } else {
                    // user is logged in, but did not grant any permissions
                    window.location.reload();
                }
            } else {
                // user is not logged in
                window.location.reload();
            }
        }, {perms:'user_about_me,email'});
        return false;
    }
    window.fbAsyncInit = function() {
        FB.init({
            appId: facebookAppId,
            status: true,
            cookie: true,
            xfbml: true
    });
                FacebookConnect.init(
                        facebookRedir,
                        true,
                        'Para poder utilizar o login integrado do Facebook, você precisa permitir que nosso site se comunique com seu Facebook.',
                        'Login invalido, Por favor tente mais tarde'
                );
            };
var FacebookConnect = {
    status: 'unknown',
    baseUrl: '',
    loggedIn: false,
    user: '',
    genericErrorText: '',
    permissionErrorText: '',
    init: function (url, isLoggedIn, permissionText, genericText) {
        FacebookConnect.baseUrl = url;
        FacebookConnect.loggedIn = isLoggedIn;
        FacebookConnect.permissionErrorText = permissionText;
        FacebookConnect.genericErrorText = genericText;
        FB.getLoginStatus(function (response) {
            status = response.status;
            if (!FacebookConnect.loggedIn && status == 'connected') {
                var callback = function (json) {
                        if (json.success) {
                            window.location.href=url;
                        }
                    }
                FacebookConnect.loginHandler(callback);
            }
        }, true);
    },
    connect: function () {
        
        FacebookConnect.toggleFacebookError(false);
        var newStatus = 'unknown';
        FB.getLoginStatus(function (response) {
            newStatus = response.status;
            if (newStatus == 'notConnected') {
                FacebookConnect.toggleFacebookError(true, FacebookConnect.permissionErrorText);
            } else if (newStatus == 'connected') {
                var callback = function (json) {
                        if (json.success) {
                            FacebookConnect.forwardUser(json.url);
                        } else {
                            FacebookConnect.toggleFacebookError(true, FacebookConnect.genericErrorText)
                        }
                    }
                FacebookConnect.loginHandler(callback, response.session.access_token);
            }
            status = newStatus;
        }, true);
    },
    loginHandler: function (callback, securityToken) {
        FB.api('/me', function (user) {
            if (user != null) {
                FacebookConnect.user = user;
                /*DEBUG TABAJARA*/
                console.log('user: ', user);
                /* /DEBUG TABAJARA*/
                var params = {
                    'facebookLogin': user.username,
                    'facebookId': user.id,
                    'email': user.email,
                    'firstName': user.first_name,
                    'lastName': user.last_name,
                    'cidade': user.location.name,
                    'returnJson': 'true',
                    'gender': user.gender,
                    'facebookToken': securityToken
                };
                $.ajax({
                    url: facebookUrl,
                    data: params,
                    dataType: "json",
                    contentType: 'application/x-www-form-urlencoded; charset=utf-8',
                    scriptCharset: 'utf-8',
                    success: callback,
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        FacebookConnect.toggleFacebookError(true, FacebookConnect.genericErrorText);
                    }
                });
            }
        });
    },
    toggleFacebookError: function (showError, errorText) {
        if (showError) {
            alert(errorText);
        } 
    },
    forwardUser: function (url) {
         if (url) {
            window.location.href = url;
        } else {
            window.location.href = FacebookConnect.baseUrl;
        }
    }
}