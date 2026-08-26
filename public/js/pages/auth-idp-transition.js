/* Auth IDP Transition Scripts */

setTimeout(function () {
    const targetUrl = window.authIdpConfig?.redirectUrl || '/idp/redirect';
    if (targetUrl) {
        window.location.href = targetUrl;
    }
}, 800);