<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>NCAR MPAS Forecasts</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<meta http-equiv="refresh" content="21600">
<link rel="stylesheet" type="text/css" href="main.css">
<link rel="stylesheet" href="jquery-ui.css">

<script src="jquery.min.js"></script>
<script src="jquery-ui.min.js"></script>
<script src="jquery.hotkeys.js"></script>
<script src="menus.js"></script>

<?php

// GET VARIABLES FROM URL
$ens_member =  ($_GET['e']) ? $_GET['e'] : ""; // string because that is what json_encode($ens_member) does.
$field      =  ($_GET['f']) ? $_GET['f'] : "rain24h";
$region     =  ($_GET['r']) ? $_GET['r'] : "conus";
$project    =  ($_GET['p']) ? $_GET['p'] : "uni";

// Define ensemble member subdirectory.
$ens_dir = "";
if ($ens_member != "") {
    // concatenate string operator is "." in PHP, not "+".
    $ens_dir = "/ens_" . strval($ens_member);
}  

// Guess most recent model run (0 or 12 UTC).
// Estimate most recent model run by subtracting
// $model_runtime_sec from current time
// and subtracting fractional part of 12 hours.
$model_runtime_sec = 3600*3;
$inittime = time() - $model_runtime_sec;
$inittime = $inittime - ($inittime % (3600*12));
//print_r("inittime=".$inittime."<br>");

$yyyymmddhh = gmdate("YmdH", $inittime);
//print_r("yyyymmddhh=".$yyyymmddhh."<br>");
// Starting with likely most recent model run (00 or 12 UTC),
// and looking back 12 hours at a time,
// find first initialization time with plot directory.
$i = 0;
while (! file_exists("/web/htdocs/projects/mpas/plots/$yyyymmddhh") and $i < 10000){
    //print_r("did not find " . $yyyymmddhh . "\n");
    $inittime = $inittime - 3600*12;
    $yyyymmddhh = gmdate('YmdH', $inittime);
    $i++;
}
if ($_GET['d']) {
    $yyyymmddhh = $_GET['d'];
    //print_r("override initialization time with search string $yyyymmddhh\n");
    $hour  = substr($yyyymmddhh, 8, 2);
    $month = substr($yyyymmddhh, 4, 2);
    $day   = substr($yyyymmddhh, 6, 2);
    $year  = substr($yyyymmddhh, 0, 4);
    $inittime = gmmktime($hour, 0, 0, $month, $day, $year);
}

// SET TIME & DATE VARIABLES USED WITHIN THIS PAGE
//$jsdate      = gmdate("Y-m-d", $inittime);
$inithr      = gmdate("H", $inittime);
$date_string = gmdate("H \U\T\C D d M Y", $inittime);

$baseimgdir = "/projects/mpas/plots/$yyyymmddhh" . $ens_dir;

?>


<script type="text/javascript">

$(document).ready(function() {
    // why hard code "00 UTC "?
    $( "#datepicker" ).datepicker({
        minDate: "00 UTC Tue 1 Jan 2017",
        maxDate: "+0D",
        dateFormat: "00 UTC D d M yy", 
        onSelect: changeDate,
        buttonImage: "/imagearchive/mpas/images/calendar_small.png",
        showOn: "button",
        buttonImageOnly: true,
        changeYear: true,
        showOtherMonths: true,
        showButtonPanel: false, //Today and Done buttons
        selectOtherMonths: true,
    });

    // Derive initialization hour from yyyymmddhh.
    var inithr = yyyymmddhh.substr(8);
    $( "input[name='init_hr'][id='"+inithr+"']" ).prop('checked',true);

    window.activehr=0; 
    loadImages();

    $(document).bind('keydown', 'left right . ,', showImage);    

    $("div.menuheader.title a").text("MPAS "+project.toUpperCase()+" Forecasts") ;

    // Add instructions on how to change basin of TC track plots.
    // and change basin when image is clicked. 
    if (field == "gfdl_tracks") {
        $("#rollovercenter").append("<br>mouse click cycles basin");
        $("#mainimage").click(function() { loadNewBasin(); });
    }




    // For each href attribute in div elements with class="dropdown"...
    // if target plot of this field type does not exist, hide the hyperlink.
    // Also add key=value to href query string with values of variables project, region, field, etc. 
    // if they aren't already in the query string.
    $("div.dropdown [href]").each(function(){
        var href = $(this).attr('href');
        var testregion = region;
        if (window['field'] == 'skewt' || window['field'] == 'gfdl_tracks'){
            testregion = 'global';
        }
        // If plot of this field type does not exist, deactivate the hyperlink.
        if (href.indexOf("skewt") == -1 && href.indexOf("gfdl_tracks") == -1) {
            var testfield = getQueryString('f',href);
            if (testfield) {
                var regex = new RegExp("^" + project+"."+testfield + "." + testregion + ".hr\\d\\d\\d\.png$");
                // Used to use testurl() function but I think testing if an 
                // element is in an array is faster than
                // requesting a url and testing for error.
                // if any element of the daily_plots array matches the regular expression
                if (! daily_plots.find(element => regex.test(element))){
                    //$(this).hide();
                    //used to hide, but now just unwrap the text from the hyperlink a href
                    $(this).contents().unwrap();
                }
            }
        }
        // Add date, region, project, field, etc. to the query string of each URL. 
        href = defaultQueryStringParameter(href, 'd', yyyymmddhh)
        href = defaultQueryStringParameter(href, 'e', ens_member)
        href = defaultQueryStringParameter(href, 'f', field)
        href = defaultQueryStringParameter(href, 'p', project)
        href = defaultQueryStringParameter(href, 'r', testregion)
        $(this).attr('href',href);
    });

    if (ens_member == "") {
        $("#rollover-members").hide();
    } 

    // Enhance ensemble member buttons.
    $("div.ensemble_member").each(function(){
        // Grab text of button
        var etext = $(this).text();
        if (etext.substring(0,4) != "Mem ") alert("Unexpected ensemble member: "+etext);
        var j=etext.substring(4);
        // Create ensemble member button, define style, 
        // and add function to execute when clicked.
        var ens_j = "ens_"+String(j);

        $(this).addClass("rollover"); // just for style

        // If ens_j directory exists add "loaded" class.
        // in other words, make it look loaded.
        var url = '/projects/mpas/plots/'+yyyymmddhh+'/'+ens_j;
        if (jQuery.inArray(ens_j, daily_plots) != -1) {
            $(this).addClass("loaded");
        }
        // add "selected" to class if j equals global variable ens_member
        if ( j == ens_member ) {
            $(this).addClass("selected");
        }
        // attach click event 
        $(this).click(function() {
            changeEns(j);
        });
    });

});

function loadNewBasin() {
    var basins = ['wp', 'ep', 'al', 'io', 'global'];
    current_basin = window.region; 
    thisInd = jQuery.inArray(current_basin, basins);
    nextIndex = (thisInd + 1) % basins.length;
    nextbasin = basins[nextIndex];
    // Replace basin in the global array variable 'imagelist'
    window.imagelist = window.imagelist.map(function(img) {
        return img.replace("."+current_basin+".", "."+nextbasin+"."); }
    );
    window.region = nextbasin;
    $('img#mainimage').attr("src", window.imagelist[window.activehr]);
}

function showImage(e) {
    if (e.key == 'ArrowLeft' || e.key == ',') incr = -1;
    else if (e.key == 'ArrowRight' || e.key == '.') incr = 1;
    var nextIndex = window.activehr;
    loaded = -1;
    while( loaded == -1) {
        nextIndex = nextIndex + incr;
	    if (nextIndex > imagelist.length-1) nextIndex = 0;
        if (nextIndex < 0) nextIndex = imagelist.length-1;
        var parts = imagelist[nextIndex].split(".");
        // Grab "hr000" part from image filename.
        // This should match the div id of the rollover button.
        var divid = parts[parts.length-2];
        loaded = jQuery.inArray(divid, imagesLoaded);
    }
    window.activehr = nextIndex;
    // Remove "selected" class from each forecast hour button. 
    $("#rollovercenter").children().each(function() {
        $( this ).removeClass("selected");
    });
    $("div#"+divid+".rollover").addClass("selected");
    $('img#mainimage').attr("src", window.imagelist[nextIndex]);
}

function nextFrame() {
    var e = new Object();
    e.key = 'ArrowRight'
    showImage(e)
}
function prevFrame() {
    var e = new Object();
    e.key = 'ArrowLeft'
    showImage(e)
}


function loadImages() {
    // preload images here
    imagesLoaded = new Array();    
    window.images = new Array();
    for (var i = 0; i < window.imagelist.length; i++) {
        window.images[i]= new Image();               // initialize array of image objects
        window.images[i].onload = function() {
            // figure out which forecast hour this is
            var parts = this.src.split(".")
            // Grab "hr000" part from image filename.
            // This should match the div id of the rollover button.
            var divid = parts[parts.length-2]
            var thisrollover = $("#"+divid+".rollover")
            imagesLoaded.push(divid)
            // change class
            thisrollover.addClass("loaded");

            // Define what happens when forecast hour button is rolled over. 
            thisrollover.mouseover(function() {
                var fcsthr = $( this ).attr('id');
		        for (var activehr=0; activehr < imagelist.length; activehr++) {
		            if (imagelist[activehr].match(fcsthr)) {break;}
  	            }
                // Remove "selected" class from all forecast hour buttons. 
                $("#rollovercenter").children().each(function() {
                    $( this ).removeClass("selected");
                });
                // Add "selected" class to this forecast hour button. 
                $( this ).addClass("selected");
                $('img#mainimage').attr("src", window.imagelist[activehr])
	            window.activehr = activehr
            });
        };
        window.images[i].src = window.imagelist[i];    // src of image
    }
}

<?php

$start = 0;

// Create array of all files in base image directory.
$daily_plots = array();
if($handle = opendir("/web/htdocs$baseimgdir")) {
    while (false !== ($file = readdir($handle))) {
        array_push($daily_plots, $file);
    }
    closedir($handle);
}
$daily_plots = array_slice($daily_plots, 2); // skip '.' and '..'


// Parse array of files for available regions.
// Eventually populate "Plot Domain" menu with 
// available regions. 
//
// Find last forecast hour $last_fhr to use for forecast hour bar. 

$regions = array();
$last_fhr = 0;
foreach($daily_plots as $png) {
    // Don't parse region from Skew-T filename.
    if(strpos($png, "skewt")) continue;

    //    $project.$field.$region.hr$fhr.png
    $pattern = '/(.*)\..*\.(.*)\.hr([0-9]+).*\.png$/';
    preg_match($pattern, $png, $matches);
    $imgproject = $matches[1];
    // Skip image if its project differs from global variable 'project'. 
    // Different projects may share the same directory on the web server.
    if($imgproject != $project) continue;

    $imgregion = $matches[2]; // don't use global variable window.region.
    $fhr    = $matches[3];
    if (intval($fhr) > $last_fhr) {
        $last_fhr = intval($fhr);
    }
    // Don't add unmatched subpatterns reported as empty string
    // to region list.
    if($imgregion && ! in_array($imgregion,$regions) ) {
        array_push($regions, $imgregion);
    }
}


// PRODUCE array OF IMAGES AND ROLLOVER LINKS, FOR USE IN JAVASCRIPT ARRAYs

$image_hrs = range($start, $last_fhr);

// Produce different image_hrs bar if field=TC track
// Instead of an array forecast hours, create array of models/meshes.
if ($field == "gfdl_tracks") {
    $a=array();
    // Loop thru possible TC track plot models
	$possible_meshes = array("uni_model", "us_model", "gfs_model", "hwt_model", "mpas_model", "mpas15_3_model","wp_model", "ep_model", "al_model");
    foreach ( $possible_meshes as $mesh ) {
        // Add this mesh to image_hrs bar if global TC track plot exists
        // $daily_plots is an array of available plots created earlier. 
		$tmp = "hur15.${field}.global.${mesh}.png";
		if (in_array($tmp, $daily_plots)) {
			array_push($a, $mesh);
		}
	}
	$image_hrs = $a;
}

$rollover_links = "";
$imagelist = array();
foreach ($image_hrs as $i) {
	$fhr_string = $i;
	if (is_numeric($i)) {
       // CONSTRUCT FORECAST HOUR STRING FOR GRAPHIC NAME
       $fhr3 = sprintf("%03d", $i); // three digits
       $fhr_string = "hr$fhr3";
	}

    // CONSTRUCT GRAPHIC NAME
	$thisimg = "$baseimgdir/${project}.${field}.${region}.${fhr_string}.png";
	if ($field == "gfdl_tracks") $thisimg = "$baseimgdir/hur15.${field}.${region}.${fhr_string}.png";

    // If the image doesn't exist don't add it to the imagelist array or make rollover link.
    if (! in_array(basename($thisimg), $daily_plots)) continue;
    
    array_push($imagelist, $thisimg);

    // CONSTRUCT ROLLOVER LINKS
    if (isset($firstimage)) { $class = 'rollover';} 
	else { $firstimage = $thisimg; $class = 'rollover selected'; }
    $rollover_links = $rollover_links . "<div class=\"${class}\" id=\"$fhr_string\">$i</div>\n";
}

?>

// Encode PHP scalars and arrays so Javascript can use them. 
var baseimgdir  = <?php echo json_encode($baseimgdir); ?>;
var project     = <?php echo json_encode($project);    ?>;
var yyyymmddhh  = <?php echo json_encode($yyyymmddhh); ?>;
var field       = <?php echo json_encode($field);      ?>;
var ens_member  = <?php echo json_encode($ens_member); ?>;
var region      = <?php echo json_encode($region);     ?>;
// Even arrays work
var daily_plots = <?php echo json_encode($daily_plots);?>;
var imagelist   = <?php echo json_encode($imagelist);  ?>;

</script>

</head>
<body>

<div id="maincontainer">

<?php require('header.php'); ?>

<div id="bodycontainer" style="clear: both;">

<div id="rolloverdiv">
<div id="rollovercenter">
<?php echo $rollover_links; ?>
<br style="clear:both;"/>
</div>

<div id="rollover-members" style="float:left; width:50px; padding-top: 20px;">
    <div class="ensemble_member">Mem 1</div>
    <div class="ensemble_member">Mem 2</div>
    <div class="ensemble_member">Mem 3</div>
    <div class="ensemble_member">Mem 4</div>
    <div class="ensemble_member">Mem 5</div>
    <div class="ensemble_member">Mem 6</div>
    <div class="ensemble_member">Mem 7</div>
    <div class="ensemble_member">Mem 8</div>
    <div class="ensemble_member">Mem 9</div>
    <div class="ensemble_member">Mem 10</div>
</div>

<img id="mainimage" src="<?php echo $firstimage; ?>" style="display: block; margin: 0 auto;" alt='main image'/>

<?php require('/web/htdocs/imagearchive/mpas/footer.php'); ?>

</div> <!-- end bodycontainer -->
</div> <!-- end maincontainer -->

</body>
</html>

