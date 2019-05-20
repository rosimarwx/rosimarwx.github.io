/**
 * Get the value of a querystring
 * @param  {String} field The field to get the value of
 * @param  {String} url   The URL to get the value from (optional)
 * @return {String}       The field value
 */
var getQueryString = function ( field, url ) {
    var href = url ? url : window.location.href;
    var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
    var string = reg.exec(href);
    return string ? string[1] : null;
};


function defaultQueryStringParameter(uri, key, value) {
  // Add key=value to the query string if key is not already present.
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  if (uri.match(re)){
    return uri;
  }
  else {
    // if there already is a '?' use '&'. Otherwise use '?'.
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    return uri + separator + key + "=" + value;
  }
}


var ddmenuitem = 0;
var timeout = 250;
var closetimer = 0;
function show_div(divname)
{
        document.getElementById("calendar").style.display= "none";
        document.getElementById("centerpoints").style.display= "none";
        document.getElementById(divname).style.display = "block";
}

function mopen(id)
{
    mcancelclosetime();
    // close old layer 
    if (ddmenuitem) ddmenuitem.style.visibility = 'hidden';
        // get new layer and show it
ddmenuitem = document.getElementById(id);
        ddmenuitem.style.visibility = 'visible';
        ddmenuitem.style.zIndex = 30;
       
}

function mclose()
{
        if (ddmenuitem) ddmenuitem.style.visibility = 'hidden';
}

function mclosetime()
{
        closetimer = window.setTimeout(mclose, timeout);
}

function mcancelclosetime()
{
        if(closetimer)
        {
                window.clearTimeout(closetimer);
                closetimer = null;
        }
}
 
 document.onclick = mclose;

function testurl(url) {
        var http = new XMLHttpRequest();
        http.open('HEAD', url, false);
        http.send();
        hstatus = true;
        if (http.status==404) {
            hstatus = false;
        }
        return hstatus;
}
