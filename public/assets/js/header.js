function loadScript( url, callback ) {
    var script = document.createElement( "script" )
    script.type = "text/javascript";
    if(script.readyState) {  // only required for IE <9
        script.onreadystatechange = function() {
        if ( script.readyState === "loaded" || script.readyState === "complete" ) {
            script.onreadystatechange = null;
            callback();
        }
        };
    } else {  //Others
        script.onload = function() {
        callback();
        };
    }

    script.src = url;
    document.getElementsByTagName( "head" )[0].appendChild( script );
}

function loadStyle(url, callback ) {
    var id = window.btoa(url)

    if (document.getElementById(id)) {
        return
    }

    var link = document.createElement( "link" )
    link.type = "text/css";
    link.rel  = 'stylesheet';
    link.id   = id;
    if(link.readyState) {  // only required for IE <9
        link.onreadystatechange = function() {
        if ( link.readyState === "loaded" || link.readyState === "complete" ) {
            link.onreadystatechange = null;
            callback();
        }
        };
    } else {  //Others
        link.onload = function() {
        callback();
        };
    }

    link.src = url;
    document.getElementsByTagName( "head" )[0].appendChild( link );
}

window.addEventListener("popstate", function (event) { 
    window.location.reload();
});