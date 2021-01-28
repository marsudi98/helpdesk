/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.4                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

var lcjakint = (lcjak_popup = null);
var lcjakwidgetid = 1;
var lcj_container = document.getElementById("jaklcp-chat-container");
var isSafari = navigator.vendor && navigator.vendor.indexOf("Apple") > -1 && navigator.userAgent && navigator.userAgent.indexOf("CriOS") == -1 && navigator.userAgent.indexOf("FxiOS") == -1;
function lcjak_chatWidget(id, data, origdomain) {
    if (data.widgethtml) {
        lcj_container.setAttribute("style", "position:fixed;right:0;bottom:0;width:0px;height:0px;");
        lcj_container.innerHTML = data.widgethtml;
        var lcj_resize = function (e) {
            if (lcjak_extractDomain(e.origin) !== origdomain) {
                console.log(e.origin);
                return false;
            }
            if (lcj_container) {
                message = e.data.split("::");
                if (message[0] == "redirecturl") {
                    window.location = message[1];
                } else if (message[0] == "knockknock") {
                    alert(message[1]);
                } else {
                    lcj_container.setAttribute("style", message[1]);
                    lcjak_pageloaded(id, data.url);
                }
            }
        };
        if (window.addEventListener) {
            window.addEventListener("message", lcj_resize, false);
        } else if (window.attachEvent) {
            window.attachEvent("onmessage", lcj_resize);
        }
    }
    return true;
}
function lcjak_pageloaded(id, origurl, effect) {
    var iframeW = document.getElementById("livesupportchat" + id).contentWindow;
    message = "pageloaded";
    if (iframeW.postMessage) iframeW.postMessage(message, origurl);
}
function lcjak_linkOpen(popup, id, a, b, c, d) {
    id = typeof id !== "undefined" ? id : "1";
    a = typeof a !== "undefined" ? a : "";
    b = typeof b !== "undefined" ? b : "";
    c = typeof c !== "undefined" ? c : "";
    d = typeof d !== "undefined" ? d : "";
    var w = { id: id, lang: a, cName: b, cEmail: c, cMessage: d, lcjUrl: chatloc };
    if (popup) {
        lcjak_popup = window.open("", "", "width=780,height=600", "_blank");
    }
    lcjak_loadchat(w, popup, "start");
}
function lcjak_loadchat(w, popup, p) {
    lcjakwidgetid = w.id;
    chatloc = JSON.parse(JSON.stringify(w.lcjUrl));
    if (lcjak_extractDomain(w.lcjUrl) == window.location.hostname) {
        var request = new XMLHttpRequest();
        request.open("GET", chatloc + "include/loadiframe.php?id=" + lcjakwidgetid + "&p=" + p + "&popup=" + popup + "&lang=" + w.lang + "&name=" + w.cName + "&email=" + w.cEmail + "&msg=" + w.cMessage, true);
        request.timeout = 3000;
        request.onload = function () {
            if (request.status >= 200 && request.status < 400) {
                var data = JSON.parse(request.responseText);
                if (data.status) {
                    if (popup) {
                        lcjak_popup.location.href = data.chaturl;
                        lcjak_popup.document.title = data.ctitle;
                    } else {
                        lcjak_chatWidget(lcjakwidgetid, data, lcjak_extractDomain(w.lcjUrl));
                    }
                    return true;
                } else {
                    console.log(data.error);
                }
            } else {
            }
        };
        request.onerror = function () {};
        request.ontimeout = function (e) {};
        request.send();
    } else {
        var url =
            chatloc +
            "include/loadiframe_cross.php?id=" +
            lcjakwidgetid +
            "&p=" +
            p +
            "&popup=" +
            popup +
            "&lang=" +
            w.lang +
            "&name=" +
            w.cName +
            "&email=" +
            w.cEmail +
            "&msg=" +
            w.cMessage +
            "&safari=" +
            isSafari +
            "&crossurl=" +
            window.location +
            "&callback=LiveChatJAK";
        var request = lcjak_createCORSRequest("GET", url);
        if (!request) {
            console.log("CORS not supported");
            return;
        }
        request.onload = function () {
            var data = JSON.parse(request.responseText);
            if (data.status) {
                if (isSafari) {
                    lcj_container.setAttribute("style", data.widgetpos + "width:" + data.btnwidth + "px;height:" + data.btnheight + "px;");
                    lcj_container.innerHTML = data.safaribtn;
                } else {
                    if (popup) {
                        lcjak_popup.location.href = data.chaturl;
                        lcjak_popup.document.title = data.ctitle;
                    } else {
                        lcjak_chatWidget(lcjakwidgetid, data, lcjak_extractDomain(w.lcjUrl));
                    }
                }
                return true;
            } else {
                console.log(data.error);
            }
        };
        request.onerror = function () {
            console.log("Woops, there was an error making the request.");
        };
        request.send();
    }
}
function lcjak_extractDomain(url) {
    var domain;
    if (url.indexOf("://") > -1) {
        domain = url.split("/")[2];
    } else {
        domain = url.split("/")[0];
    }
    domain = domain.split(":")[0];
    return domain;
}
function lcjak_createCORSRequest(method, url) {
    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;
    if ("withCredentials" in xhr) {
        xhr.open(method, url, true);
    } else if (typeof XDomainRequest != "undefined") {
        xhr = new XDomainRequest();
        xhr.open(method, url);
    } else {
        xhr = null;
    }
    return xhr;
}
(function (w) {
    lcjak_loadchat(w, 0, "btn");
})(window);
