function drawBackground() {
  // Get the svg elements
  var svghodo = d3.select("div#hodobox svg g").append("g").attr("class", "hodobg");
  var svg = d3.select("div#mainbox svg g").append("g").attr("class", "skewtbg");

  // Path generators for dry and moist adiabats
  var dryline = d3.svg.line()
      .interpolate("linear")
      .x(function(d,i) { return x( ( 273.15 + d ) / Math.pow( (1000/pp[i]), 0.286) -273.15) + (y(basep)-y(pp[i]))/tan;})
      .y(function(d,i) { return y(pp[i])} );

  var moistline = d3.svg.line().interpolate("linear")
      .x(function(d,i) { return x(d) + (y(basep)-y(p_levels[i]))/tan;})
      .y(function(d,i) { return y(p_levels[i])} );

  // Add clipping path
  svg.append("clipPath")
    .attr("id", "clipper")
    .append("rect")
    .attr("x", 0)
    .attr("y", 0)
    .attr("width", w)
    .attr("height", h);    
 
 // Draw skewed temperature lines
  svg.selectAll("gline")
    .data(d3.range(-100,45,10))
   .enter().append("line")
     .attr("x1", function(d) { return x(d)-0.5 + (y(basep)-y(100))/tan; })
     //.attr("x1", function(d) { return x(d)-0.5; })
     .attr("x2", function(d) { return x(d)-0.5; })
     .attr("y1", 0)
     .attr("y2", h)
     .attr("class", function(d) { if (d == 0) { return "tempzero"; } else { return "gridline"}})
     .attr("clip-path", "url(#clipper)");
     //.attr("transform", "translate(0," + h + ") skewX(-30)");
     
  // Draw logarithmic pressure lines
  svg.selectAll("gline2")
    	.data(plines)
   	.enter().append("line")
     	.attr("x1", 0)
     	.attr("x2", w)
     	.attr("y1", function(d) { return y(d); })
     	.attr("y2", function(d) { return y(d); })
     	.attr("class", "gridline");
     
  // create array to plot dry adiabats
  var pp = d3.range(topp,basep+1,10);
  var dryad = d3.range(-30,240,20);
  var all = [];
  for (i=0; i<dryad.length; i++) { 
      var z = [];
      for (j=0; j<pp.length; j++) { z.push(dryad[i]); }
      all.push(z);
  }

  // Draw dry adiabats
  svg.selectAll(".dryline")
      .data(all)
    .enter().append("path")
      .attr("class", "gridline")
      .attr("clip-path", "url(#clipper)")
      .attr("d", dryline);
 
  // Draw moist adiabats (6,10,14,18,22,26,30C) at these t/p coordinates 
  var p_levels = [1000,988,975,962,950,938,925,900,875,850,825,800,750,700,650,600,550,500,450,400,350,300,275,250,225,200];
  var moist_temps = [ [ 6.0,5.4,4.8,4.2,3.6,2.9,2.3,0.9,-0.5,-2.0,-3.5,-5.1,-8.7,-12.6,-16.9,-21.8,-27.3,-33.5,-40.5,-48.3,-57.1,-67.0,-72.3,-77.9,-84.0,-90.6 ],
                      [ 10.0,9.5,8.9,8.3,7.7,7.1,6.5,5.3,4.0,2.6,1.2,-0.3,-3.5,-7.2,-11.2,-15.8,-21.0,-26.9,-33.7,-41.5,-50.3,-60.5,-65.9,-71.7,-78.0,-84.8 ],
                      [ 14.0,13.5,13.0,12.4,11.9,11.4,10.8,9.7,8.5,7.2,5.9,4.5,1.6,-1.7,-5.4,-9.6,-14.3,-19.9,-26.3,-33.8,-42.6,-52.9,-58.4,-64.4,-70.8,-77.9 ],
                      [ 18.0,17.5,17.0,16.6,16.1,15.6,15.0,14.0,12.9,11.7,10.5,9.3,6.6,3.6,0.3,-3.4,-7.7,-12.6,-18.5,-25.4,-33.8,-44.0,-49.6,-55.7,-62.4,-69.7 ],
                      [ 22.0,21.6,21.1,20.7,20.2,19.7,19.3,18.3,17.3,16.2,15.1,14.0,11.5,8.9,5.9,2.6,-1.2,-5.6,-10.7,-16.8,-24.4,-33.9,-39.4,-45.5,-52.2,-59.8 ],
                      [ 26.0,25.6,25.2,24.8,24.3,23.9,23.5,22.5,21.6,20.6,19.6,18.6,16.4,13.9,11.2,8.3,4.9,1.1,-3.3,-8.6,-15.1,-23.4,-28.3,-33.9,-40.4,-47.9 ],
                      [ 30.0,29.6,29.2,28.8,28.4,28.0,27.6,26.8,25.9,25.0,24.1,23.1,21.0,18.8,16.4,13.7,10.7,7.4,3.5,-1.0,-6.4,-13.3,-17.4,-22.2,-27.8,-34.6 ] ];
  svg.selectAll(".moistline").data(moist_temps).enter().append("path").attr("class", "gridline moist").attr("clip-path", "url(#clipper)").attr("d", moistline);

  svg.selectAll(".moistlabels")
	.data([6,10,14,18,22,26,30]).enter().append("text")
	.attr('x', function (d,i) { return x(moist_temps[i][moist_temps[i].length-2]) + (y(basep)-y(225))/tan; })
        .attr('y', y(225))
        .attr('dy', '0.75em')
    	.attr('class', 'moistlabels')
    	.attr('text-anchor', 'middle')
    	.text(function(d) { return d; });

  // Draw line along right edge of plot
  svg.append("line").attr("x1", w-0.5).attr("x2", w-0.5).attr("y1", 0).attr("y2", h).attr("class", "gridline");
    
    // Draw hodograph background
   svghodo.selectAll(".circles")
       .data(d3.range(10,90,10))
    .enter().append("circle")
       .attr("cx", 0)
       .attr("cy", 0)
       .attr("r", function(d) { return r(d); })
       .attr("class", function(d,i) { if ((d/10)%2 == 0) { return "gridline odd" } else { return "gridline" } });

    // Draw hodograph labels
    svghodo.selectAll("hodolabels")
	  .data(d3.range(20,90,20)).enter().append("text")
	    .attr('x', 0)
        .attr('y', function (d,i) { return r(d); })
        .attr('dy', '0.4em')
    	.attr('class', 'hodolabels')
    	.attr('text-anchor', 'middle')
    	.text(function(d) { return d+'kts'; });
       
    // Draw axes
    svg.append("g").attr("class", "x axis").attr("transform", "translate(0," + (h-0.5) + ")").call(xAxis);
    svg.append("g").attr("class", "y axis").attr("transform", "translate(-0.5,0)").call(yAxis);
    svg.append("g").attr("class", "y axis ticks").attr("transform", "translate(-0.5,0)").call(yAxis2);
    //svg.append("g").attr("class", "y axis hght").attr("transform", "translate(0,0)").call(yAxis2);
    
    // Add NCAR watermark image
    var watermark = svg.selectAll("image").data([0]);
    watermark.enter().append("svg:image").attr("xlink:href", "ncar.png").attr("x", 10).attr("y",y(875)).attr("opacity", 0.3).attr("width", "150").attr("height", "57");
}

function drawMap() {
  var snd_ids = [
    { lon: -97.44, lat: 35.18, id:'OUN' },
    { lon: -97.30, lat: 32.83, id:'FWD' },
    { lon: -95.62, lat: 39.07, id:'TOP' },
    { lon: -104.87, lat: 39.75, id:'DNR' },
    { lon: -99.97, lat: 37.77, id:'DDC' },
    { lon: -100.68, lat: 41.13, id:'LBF' },
    { lon: -108.52, lat: 39.12, id:'GJT' },
    { lon: -108.47, lat: 43.07, id:'RIW' },
    { lon: -103.20, lat: 44.07, id:'UNR' },
    { lon: -98.42, lat: 45.45, id:'ABR' },
    { lon: -100.75, lat: 46.77, id:'BIS' },
    { lon: -93.57, lat: 44.85, id:'MPX' },
    { lon: -96.37, lat: 41.32, id:'OAX' },
    { lon: -90.58, lat: 41.62, id:'DVN' },
    { lon: -89.33, lat: 40.15, id:'ILX' },
    { lon: -83.80, lat: 39.43, id:'ILN' },
    { lon: -93.38, lat: 37.23, id:'SGF' },
    { lon: -86.68, lat: 36.12, id:'BNA' },
    { lon: -101.72, lat: 35.22, id:'AMA' },
    { lon: -106.62, lat: 35.05, id:'ABQ' },
    { lon: -106.70, lat: 31.87, id:'EPZ' },
    { lon: -102.20, lat: 31.95, id:'MAF' },
    { lon: -102.05, lat: 33.61, id:'REE' },
    { lon: -100.92, lat: 29.37, id:'DRT' },
    { lon: -93.83, lat: 32.45, id:'SHV' },
    { lon: -92.27, lat: 34.83, id:'LZK' },
    { lon: -93.22, lat: 30.13, id:'LCH' },
    //{ lon: -89.83, lat: 30.33, id:'STL' },
    { lon: -89.82, lat: 30.33, id:'SIL' },
    { lon: -86.77, lat: 33.17, id:'BMX' },
    { lon: -88.13, lat: 44.48, id:'GRB' },
    { lon: -97.50, lat: 27.77, id:'CRP' },
    { lon: -90.08, lat: 32.32, id:'JAN' },
    //{ lon: -111.38, lat: 47.47, id:'TFX' },
    { lon: -111.97, lat: 40.78, id:'SLC' },
    { lon: -111.82, lat: 35.23, id:'FGZ' },
    //{ lon: -110.95, lat: 32.23, id:'TWC' },
    { lon: -110.96, lat: 32.23, id:'TUS' },
    { lon: -97.41, lat: 25.91, id:'BRO' },
    { lon: -115.18, lat: 36.05, id:'VEF' },
    { lon: -119.80, lat: 39.56, id:'REV' },
    { lon: -115.73, lat: 40.86, id:'LKN' },
    { lon: -106.61, lat: 48.21, id:'GGW' },
    { lon: -117.12, lat: 32.85, id:'NKX' },
    { lon: -120.56, lat: 34.75, id:'VBG' },
    { lon: -122.21, lat: 37.73, id:'OAK' },
    { lon: -122.86, lat: 42.36, id:'MFR' },
    { lon: -123.00, lat: 44.91, id:'SLE' },
    { lon: -124.55, lat: 47.95, id:'UIL' },
    //{ lon: -117.63, lat: 47.68, id:'OTX' },
    { lon: -114.22, lat: 51.06, id:'YBW' },
    { lon: -117.31, lat: 47.37, id:'GEG' },
    { lon: -111.23, lat: 47.28, id:'GTF' },
    { lon: -97.48, lat: 36.62, id:'LMN' },
    { lon: -93.40, lat: 48.56, id:'INL' },
    { lon: -83.46, lat: 42.70, id:'DTX' },
    { lon: -84.71, lat: 44.91, id:'APX' },
    { lon: -84.57, lat: 33.36, id:'FFC' },
    { lon: -86.68, lat: 34.68, id:'QAG' },
    { lon: -86.51, lat: 30.48, id:'VPS' },
    //{ lon: -81.79, lat: 24.55, id:'KEY' },
    { lon: -81.75, lat: 24.55, id:'EYW' },
    { lon: -80.38, lat: 25.75, id:'MFL' },
    { lon: -82.40, lat: 27.70, id:'TBW' },
    { lon: -84.30, lat: 30.45, id:'TLH' },
    { lon: -81.70, lat: 30.50, id:'JAX' },
    { lon: -80.55, lat: 28.48, id:'XMR' },
    { lon: -80.03, lat: 32.90, id:'CHS' },
    { lon: -76.88, lat: 34.78, id:'MHX' },
    { lon: -79.95, lat: 36.08, id:'GSO' },
    { lon: -80.41, lat: 37.20, id:'RNK' },
    { lon: -77.46, lat: 38.98, id:'IAD' },
    { lon: -80.22, lat: 40.53, id:'PIT' },
    //{ lon: -76.07, lat: 39.47, id:'APG' },
    { lon: -75.47, lat: 37.93, id:'WAL' },
    { lon: -72.86, lat: 40.87, id:'OKX' },
    { lon: -78.73, lat: 42.93, id:'BUF' },
    { lon: -73.83, lat: 42.69, id:'ALB' },
    { lon: -69.96, lat: 41.66, id:'CHH' },
    { lon: -70.25, lat: 43.90, id:'GYX' },
    { lon: -68.01, lat: 46.86, id:'CAR' },
    //{ lon: -106.02, lat: 28.67, id:'MCU' },
    //{ lon: -119.40, lat: 49.94, id:'WLW' },
    //{ lon: -76.01, lat: 46.30, id:'WMW' },
    { lon: -116.13, lat: 43.34, id:'BOI' },
    //{ lon: -112.00, lat: 33.43, id:'PHX' },
    { lon: -97.68, lat: 30.18, id:'AUS' },
  ];

  
  var width = 500,
      height = 360,
      radius = 4;

  var projection = d3.geo.albers()
      .scale(675)
      .translate([width / 2, height / 2]);
      
  xycoord = [];

  // add sounding locations to xycoordinate list 
  for (var i=0; i<snd_ids.length; i++) {
      var thiscoord = projection([snd_ids[i].lon, snd_ids[i].lat]);
      xycoord.push(thiscoord);
  }

  // create array of filestrings for each site (site ID for snd pts and lat/lon for grid pts)
  var filestrings = xycoord.map(function(coord,index) {
      if (index < snd_ids.length) { return snd_ids[index].id; } 
  });
  
  var path = d3.geo.path()
      .projection(projection);

  svgmap = d3.select("div#hodobox").append('svg').attr("class", "mapbg")
      .attr("width", width)
      .attr("height", height);

  d3.json("us.json", function(error, us) {
    // add filled land areas
    svgmap.insert("path", ".graticule")
        .datum(topojson.feature(us, us.objects.land))
        .attr("class", "land")
        .attr("d", path);

    // add state boundaries
    svgmap.insert("path", ".graticule")
      .datum(topojson.mesh(us, us.objects.states, function(a, b) { return a !== b; }))
      .attr("class", "state-boundary")
      .attr("d", path);

     // add dots for sounding locations
     svgmap.append("g").attr("class", "bubble").selectAll("circle")
      .data(snd_ids).enter().append("circle")
      .attr("transform", function(d) { return "translate(" + projection([d.lon, d.lat]) + ")"; })
      .attr("class", function(d,i) { 
          //if (d.id == site) { return "stnselected bubble "+d.id+" point-"+i; }
          //if (d.id.substring(0,4) == "GRID") { return "gridpt bubble "+d.id+" point-"+i; }
          //else { return "bubble "+d.id+" point-"+i; }
          return "bubble "+d.id+" point-"+i;
      })
      .attr("r", radius);

      // add hover dot (default to denver)
      var thisstn = snd_ids.filter(function(d) { return d.id == site; })[0]; //get lat/lon of active site (whatever comes in via &s URL param)
      hoverdot = svgmap.selectAll("g.bubble").append("circle")
         .attr("transform", "translate(" + projection([thisstn.lon,thisstn.lat]) + ")")
         .attr("class", "hover").attr("r", radius);
      
      // add selected dot on top of hover dot (also default to denver)
      selecteddot = svgmap.selectAll("g.bubble").append("circle")
         .attr("transform", function(d) { return "translate(" + projection([thisstn.lon,thisstn.lat]) + ")"; })
         .attr("class", "selected").attr("r", radius);
     
      // move hover dot to closest grid point when mouse is moving
      var minidx;
      svgmap.on('mousemove', function() {

          var xy = d3.mouse(this); // get cursor x,y position on map
          var mindiff = 100; // arbitrary large number
          // figure out closest grid point to cursor x,y point
          for (var i=0, len = xycoord.length; i<len; i++) {
              var xdiff = Math.abs(xycoord[i][0] - xy[0]);
              var ydiff = Math.abs(xycoord[i][1] - xy[1]);
              var thisdiff = xdiff + ydiff;
              if (thisdiff <= mindiff) { var mindiff = thisdiff; minidx = i; }
          }
          
          hoverdot.attr("transform", "translate(" + xycoord[minidx] + ")")
                  .attr("class", function(d) {
                       if (minidx < snd_ids.length) {
                             $('#sitetext').text(filestrings[minidx]);
                             return "hoversnd";
                       } else {
                             return "hover";
                       }
                  });
      });

      // change site and move selected dot when clicked
      svgmap.on('click', function() {
          selecteddot.attr("transform", function(d) { return "translate(" + xycoord[minidx] + ")"; })
          loadNewData(filestrings[minidx]);
      });

      // move hover dot off map area when mouse is not over map
      svgmap.on('mouseout', function() {
          hoverdot.attr("transform", "translate(1000,1000)");
      });


//  RS 5/2015: I tried using the d3 voronoi mesh to compute the map area nearest to each station. This worked
//  nicely, but it slowed down the sounding rollover (esp in Safari/FF), since the voronoi mesh adds 
//  a path svg element for each area (so for 1800 sounding locations, it adds 1800 svg elements to the
//  DOM). Because of this performance hit, I decided to manually compute the nearest sounding point in
//  the for loop above as the mouse is moving over the map. This performs nearly as fast without the
//  addition of DOM elements (and associated mouseover/out/click handlers for each element).

//      svgmap.selectAll("path.voronoi")
//        .data(d3.geom.voronoi(xycoord))
//       .enter().append("svg:path")
//        .attr("d", function(d) { return "M" + d.join(",") + "Z"; })
//        .attr("id", function(d,i) { return "path-"+i; })
//        .attr("class", "voronoi")
//        .style('fill-opacity', 0);

//      svgmap.selectAll("path.voronoi")
//        .on('mouseover', function(d,i) {
//           //svgmap.select("circle.point-"+i).classed('stnhover', true );
//           //svgmap.select("circle.hover").attr("transform", function(d) { return "translate(" + projection([ids[i].lon, ids[i].lat]) + ")"; })
//           //svgmap.select("circle.hover").attr("transform", "translate(" + xycoord[i] + ")");
//           hoverdot.attr("transform", "translate(" + xycoord[i] + ")");
//        })
//        //.on('mouseout', function(d,i) {
//           //svgmap.select("circle.point-"+i).classed({'stnhover':false });
//        //})
//        .on("click", function(d,i) {
//           //svgmap.select("circle.selected").attr("transform", function(d) { return "translate(" + projection([ids[i].lon, ids[i].lat]) + ")"; })
//           selecteddot.attr("transform", function(d) { return "translate(" + xycoord[i] + ")"; })
//           //var thisid = ids[i].id; loadNewData(thisid);
//           var filestring = gridpt_lats[i].toString() + gridpt_lons[i].toString();
//           loadNewData(filestring);
//        });
  });

//d3.select(self.frameElement).style("height", height + "px");
}
