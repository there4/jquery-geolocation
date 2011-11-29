<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title>Geolocation Testing</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.0/jquery.min.js" type="text/javascript"></script>
<script src="jquery.cookie.js" type="text/javascript"></script>
<script src="jquery.json.js" type="text/javascript"></script>
</head>

<body>

<h1>Where are you?</h1>

<p><span class="city">&nbsp;</span>, <span class="state">&nbsp;</span>, <span class="zip">&nbsp;</span></p>


<p><a id="showcookie" href="#">show cookie</a> | <a id="deletecookie" href="#">delete cookie</a></p>

<code><pre id="coookiedebug">&nbsp;</pre></code>

<script type="text/javascript">
var ip            = "<?php print $_SERVER['REMOTE_ADDR'];?>";
var geolocation   = "./ip_proxy.php";
var cookiename    = 'geo_location';
var cookieoptions = { path: '/', expires: 365 };

$(function(){
  
  fetchlocation();

  $('#showcookie').click(function(){
    $('#coookiedebug').text($.cookie(cookiename));
    return false;
  });

  $('#deletecookie').click(function(){
    $.cookie(cookiename, null, cookieoptions);
    return false;
  });
  
});

displaylocation = function(location) {
  if (location.Status == 'OK') {
    $('.city').text(location.City);
    $('.zip').text(location.ZipPostalCode);
    if(location.State != '--') {
      $('.state').text(location.State);
    } else {
      $('.state').text(location.RegionName);
    }
  }
}

fetchlocation = function() {/*
  // look in the cookie for the location data
  cookiedata = $.cookie(cookiename);
  if ('' != cookiedata) {
    locationinfo = $.evalJSON(cookiedata);  
    if ((locationinfo != null) && (locationinfo.Ip == ip)) {
      displaylocation(locationinfo);
      $.cookie(cookiename, cookiedata, cookieoptions);
      return;
    }
  }
  */
  // it's not in the cookie, so fetch from the server
  $.getJSON(
    geolocation, {
      'timezone' : 'false', // set this to false to save the service 2 queries
      'ip'       : ip
    },
    function(data) {
      data.IP = ip;
      displaylocation(data);
      cookiedata = $.toJSON(data);
      $.cookie(cookiename, cookiedata, cookieoptions);
      console.log(data);
    }
  );
}

</script>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-8504376-1");
pageTracker._trackPageview();
} catch(err) {}
</script>
</body>
</html>