<div id="header">

<script type="text/javascript">
  function changeDate(datetext, inst) {
      var jsdate = $.datepicker.parseDate("00 UTC D d M yy", datetext);
      var yyyymmddhh = $.datepicker.formatDate("yymmdd00", jsdate);
      var path = window.location.pathname;
      var pagename = path.split('/').pop();
      if (pagename == 'images.php') window.location.href = pagename+"?p="+project+"&d="+yyyymmddhh+"&r="+region+"&f="+field+"&e="+ens_member;
      else window.location.href = "index.php?d="+yyyymmddhh;
  }
  function changeHr(init_hr){
      yyyymmddhh = yyyymmddhh.substring(0,8)+init_hr;
      var path = window.location.pathname;
      var pagename = path.split('/').pop();
      window.location.href = pagename+"?p="+project+"&d="+yyyymmddhh+"&r="+region+"&f="+field+"&e="+ens_member;
  }
  function changeEns(ens_member){
      var path = window.location.pathname;
      var pagename = path.split('/').pop();
      window.location.href = pagename+"?p="+project+"&d="+yyyymmddhh+"&r="+region+"&f="+field+"&e="+ens_member;
  }
</script>

<div class="menuheader title">
<a href="http://www2.mmm.ucar.edu/projects/mpas/Projects/MPAS_FORECAST_EXPERIMENTS.html">MPAS Forecasts</a><br>
<span style="font-size: 13px;font-weight:normal;">Initialized:</span>
<input size="25" type="text" id='datepicker' value="<?php echo $date_string; ?>" readonly>
</div>

<div class="menuheader" onmouseover="mopen('dropdown1')" onmouseout="mclosetime()">Surface/Precip</div>
<div class="menuheader" onmouseover="mopen('dropdown2')" onmouseout="mclosetime()">Upper-Air</div>
<div class="menuheader" onmouseover="mopen('dropdown3')" onmouseout="mclosetime()">Severe</div>
<div class="menuheader" onmouseover="mopen('domainMenu')" onmouseout="mclosetime()">Plot Domain</div>
<div class="menuheader" onmouseover="mopen('meshesMenu')" onmouseout="mclosetime()">Meshes</div>

<div id="dropdown1" class="dropdown fields" onmouseover="mcancelclosetime()" onmouseout="mclosetime()" style="margin-left: 347px; width: 210px;">
<dl>
<dt>Surface Conditions</dt>

<dd><a href="images.php?f=t2m">2-m Temperature / 10-m Wind</a></dd>
<dd><a href="images.php?f=dewpoint_surface">2-m Dewpoint / 10-m Wind</a></dd> 
<dd><a href="images.php?f=thetae">2-m Equivalent Potential Temp.</a></dd>
<dd><a href="images.php?f=speed_10m">10-m wind / MSLP</a></dd>
<dd><a href="images.php?f=snow">Snow Water Equiv.</a></dd>

<dt>Precipitation</dt>
<dd>
<a href="images.php?f=rain1h">1-hr</a> |
<a href="images.php?f=rain3h">3-hr</a> |
<a href="images.php?f=rain6h">6-hr</a> |
<a href="images.php?f=rain24h">24-hr</a> |
<a href="images.php?f=rain120h">5-day</a></dd>

<dt>Radar Reflectivity</dt>
<dd>
<a href="images.php?f=refl10cm_max">Comp Refl</a> |
<a href="images.php?f=refl10cm_1km">1 km AGL</a>
</dd>

<dt>Lightning Flash Rate</dt>
<dd>
<a href="images.php?f=cloud_toph_flashrate">Cloud top height</a><br>
<a href="images.php?f=dbz35_flashrate">35 dBZ volume</a><br>
<a href="images.php?f=ice_path_flashrate">Total ice path</a> | 
<a href="images.php?f=max_vvel_flashrate">Max vert vel</a><br>
<a href="images.php?f=npice_massflux_flashrate">Non-precip ice mass flux</a><br>
<a href="images.php?f=precip_ice_flashrate">Precip ice</a> | 
<a href="images.php?f=updraft_vol_flashrate">Updraft volume</a><br> 
<a href="images.php?f=lightning_threat1">Threat 1</a> | 
<a href="images.php?f=lightning_threat2">Threat 2</a> | 
<a href="images.php?f=lightning_threat3">Threat 3</a> 
</dd>

<dt>Fluxes</dt>
<dd>
<a href="images.php?f=lh">Latent Heat</a> | 
<a href="images.php?f=hfx">Sensible Heat</a>
</dd>

</dl>
</div>

<div id="dropdown2" class="dropdown fields" onmouseover="mcancelclosetime()" onmouseout="mclosetime()" style="margin-left: 499px; width: 300px;">
<dl>
<dt>Fields</dt>
<dd>Wind Speed:<br>
<a href="images.php?f=speed_200hPa">200 mb</a> | 
<a href="images.php?f=speed_250hPa">250 mb</a> | 
<a href="images.php?f=speed_500hPa">500 mb</a> | 
<a href="images.php?f=speed_700hPa">700 mb</a> | 
<a href="images.php?f=speed_850hPa">850 mb</a> |
<a href="images.php?f=speed_925hPa">925 mb</a>
</dd>

<dd>Temperature:<br> 
<a href="images.php?f=temperature_200hPa">200 mb</a> | 
<a href="images.php?f=temperature_500hPa">500 mb</a> | 
<a href="images.php?f=temperature_700hPa">700 mb</a> | 
<a href="images.php?f=temperature_850hPa">850 mb</a> 
</dd>

<dd>Potential Temperature / MSLP: <a href="images.php?f=theta_pv">2 PVU</a></dd>
<dd><a href="images.php?f=rain6h">1000-500 mb thickness</a></dd>

<dd>Vorticity: <a href="images.php?f=vorticity_500hPa">500mb</a> |
<a href="images.php?f=vorticity_200hPa">200mb</a></dd>

<dt>Moisture</dt>
<dd>
<a href="images.php?f=precipw">Precipitable Water</a> |
<a href="images.php?f=dewpoint_850hPa">850 mb Td</a> 
</dd>

<dd>Relative humidity:<br> 
<a href="images.php?f=relhum_500hPa">500 mb</a> | 
<a href="images.php?f=relhum_700hPa">700 mb</a> | 
<a href="images.php?f=relhum_850hPa">850 mb</a> |
<a href="images.php?f=relhum_925hPa">925 mb</a>
</dd>

<dt>Outgoing LW Radiation</dt>
<dd><a href="images.php?f=olrtoa">Top of Atmosphere</a></dd> 

<?php
$snd_plots = preg_grep("/.*$project\.skewt\..*\.png/", $daily_plots);
$stids = array("ABQ","ABR","ALY","AMA","APX","BIS","BOI","BUF","CAR","CHH","CHS","DDC","DNR","DTX","FWD","GJT","GRB","ILX","JAN","LBF","LCH","LMN","LZK","MAF","MPX","OAX","OUN","REE","RIW","SGF","SHV","TOP","UNR","S07W112","N15E121","N18E121","N20W155","N22W159","N24E124","N35E125","N35E127","N36E129","N37E127","N38E125","N40E140","N40W105");
if (count($snd_plots) > 0) {
    echo "<dt>Soundings <a href='sounding_map.html?d=${yyyymmddhh}'>[map]</a></dt>\n";
    echo "<dd>\n";
    $a=array();
    foreach ( $stids as $stid) {
        if (in_array("$project.skewt.$stid.hr000.png", $snd_plots)) {
            array_push($a, "<a href='images.php?d=${yyyymmddhh}&f=skewt&r=${stid}'>$stid</a> ");
        }
    }
    echo join(" | ", $a);
    echo "</dd>\n";
}
?>

</dl>
</div>

<div id="dropdown3" class="dropdown fields" onmouseover="mcancelclosetime()" onmouseout="mclosetime()" style="margin-left: 651px; width: 230px;">
<dl>
<dt>Instability</dt>
<dd>
<a href="images.php?f=cape">MUCAPE / 0-6 km shear</a> |
<a href="images.php?f=cin">MUCIN</a>
</dd> 

<dt>Wind shear</dt>
<dd>
<a href="images.php?f=shear_0_1km">0-1 km</a></dd>
<dt>Storm-relative Helicity</dt>
<dd>
<a href="images.php?f=srh_0_1km">0-1 km</a> |
<a href="images.php?f=srh_0_3km">0-3 km</a></dd>

<dl>
<dt>Updraft Helicity</dt>
<dd>
<a href="images.php?f=updraft_helicity_max">1-hour max</a> |
<a href="images.php?f=updraft_helicity_max24h">24-hour max</a></dd>

<dt>Updraft Speed</dt>
<dd>
  <a href="images.php?f=w_velocity_max">Updraft (1-hour max)</a></dd>

<dt>Lowest Model Level Wind Speed</dt>
<dd>
  <a href="images.php?f=wind_speed_level1_max">Wind speed (1-hour max)</a></dd>

</dl>
</div>

<div id="domainMenu" class="dropdown" onmouseover="mcancelclosetime()" onmouseout="mclosetime()" style="margin-left: 803px; width: 140px;">
<?php require('/web/htdocs/imagearchive/mpas/domain.menu.php'); ?>
</div>

<div id="meshesMenu" class="dropdown" onmouseover="mcancelclosetime()" onmouseout="mclosetime()" style="margin-left: 955px; width: 140px;">
<?php require('/web/htdocs/imagearchive/mpas/mesh.menu.php'); ?>
</div>
<input size="25" id="00" type="radio" name="init_hr" onclick="changeHr('00')">00Z
<input size="25" id="12" type="radio" name="init_hr" onclick="changeHr('12')">12Z

</div> <!-- div#header end -->
