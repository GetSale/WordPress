(function (d, w, c) {
    w[c] = {
        projectId: parseInt(getsale_vars.getsale_id)
    };

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () {
            n.parentNode.insertBefore(s, n);
        };
    s.type = "text/javascript";
    s.async = true;
    s.src = "//rt.edge.getsale.io/loader.js"; //http://edge.getsale.io/
    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else {
        f();
    }

})(document, window, "getSaleInit");
