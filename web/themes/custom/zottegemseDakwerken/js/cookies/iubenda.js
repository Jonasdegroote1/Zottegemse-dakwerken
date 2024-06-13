var _iub = _iub || [];
_iub.csConfiguration = {
  "askConsentAtCookiePolicyUpdate": true,
  "enableFadp": true,
  "enableLgpd": true,
  "enableUspr": true,
  "fadpApplies": true,
  "floatingPreferencesButtonDisplay": "bottom-right",
  "lang": "nl",
  "perPurposeConsent": true,
  "siteId": 3670242,
  "usprApplies": true,
  "whitelabel": false,
  "cookiePolicyId": 92515636,
  "banner": {
    "acceptButtonCaptionColor": "#FFFFFF",
    "acceptButtonColor": "#0073CE",
    "acceptButtonDisplay": true,
    "backgroundColor": "#FFFFFF",
    "brandBackgroundColor": "#FFFFFF",
    "brandTextColor": "#000000",
    "closeButtonDisplay": false,
    "customizeButtonCaptionColor": "#4D4D4D",
    "customizeButtonColor": "#DADADA",
    "customizeButtonDisplay": true,
    "explicitWithdrawal": true,
    "listPurposes": true,
    "ownerName": "zottegemsedakwerken.be",
    "position": "float-bottom-center",
    "rejectButtonCaptionColor": "#FFFFFF",
    "rejectButtonColor": "#0073CE",
    "rejectButtonDisplay": true,
    "showTitle": false,
    "showTotalNumberOfProviders": true,
    "textColor": "#000000"
  }
};
(function (w, d) {
  var loader = function () {
    var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0];
    s.src = "https://cs.iubenda.com/autoblocking/3670242.js";
    tag.parentNode.insertBefore(s, tag);
  };
  if (w.addEventListener) {
    w.addEventListener("load", loader, false);
  } else if (w.attachEvent) {
    w.attachEvent("onload", loader);
  } else {
    w.onload = loader;
  }
})(window, document);
(function (w, d) {
  var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0];
  s.src = "//cdn.iubenda.com/cs/gpp/stub.js";
  tag.parentNode.insertBefore(s, tag);
})(window, document);
(function (w, d) {
  var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0];
  s.src = "//cdn.iubenda.com/cs/iubenda_cs.js";
  s.setAttribute("charset", "UTF-8");
  s.setAttribute("async", "true");
  tag.parentNode.insertBefore(s, tag);
})(window, document);
