
var svo = null;

// the main application object
function SVO(panWidth, panHeight)
{
	this.markerCount = 0;
    this.panWidth = panWidth;
    this.panHeight = panHeight;
}

SVO.prototype.setLocation = function(lat,lng,zoom){
	// Trafalgar Square
    this.lat = lat; //51.507768;
    this.lng = lng; //-0.127957;
    this.zoom = zoom; //16;
	this.createPoint("streetPt", lat, lng);
}

SVO.prototype.setViewpoint = function(heading,pitch,zoom){
	this.sheading = heading;// 69.58;
    this.spitch = pitch; //0;
    this.szoom = zoom;//1;
}

SVO.prototype.addImage = function(lat, lng, width, height, img, className, id){
	this.slat = lat; //51.507527;
    this.slng = lng; //-0.128652;
    this.image = img; //"boobies.png";
	this.createPoint("pt", lat, lng);
	this.m_initMarker(className, id, lat, lng, width, height);
}

SVO.prototype.settings = function(){
    this.distance = 0;  
    this.maximumDistance = 200;
	this.markerWidth = 120;
    this.markerHeight = 80;
}

SVO.prototype.createPoint = function(label, lat, lng){
	this[label] = new google.maps.LatLng(lat, lng);
}

// create street view
SVO.prototype.m_initPanorama = function ()
{
    var visible = false;
    var l_panDiv = jQuery("#panDiv")[0];

    // controls can be hidden here to prevent the position being changed by the user
    var l_panOptions =
    {
        zoomControl: false,
        linksControl: false
    };

    l_panOptions.position = this.streetPt;
    l_panOptions.pov =
    {
        heading: this.sheading,
        pitch: this.spitch,
        zoom: this.szoom
    };

    pan = new google.maps.StreetViewPanorama(l_panDiv, l_panOptions);
	
	console.log(pan);

    // event handlers    
    google.maps.event.addListener(pan, 'pov_changed', function ()
    {
		console.log("POV CHANGED");
        svo.m_updateMarker("imgMarker");
    });

	google.maps.event.addListener(pan, 'position_changed', function ()
    {
		console.log("POSITION CHANGED");
        svo.streetPt = pan.getPosition();
        svo.m_updateMarker("imgMarker");
    });

}

function Marker(p_name, p_icon, p)
{
    this.m_icon = "";

    this.pt = null;
    this.m_pov = null;

    this.m_pixelpt = null;
}

// convert the current heading and pitch (degrees) into pixel coordinates
SVO.prototype.m_convertPointProjection = function (p_pov, p_zoom)
{
    var l_fovAngleHorizontal = 90 / p_zoom;
    var l_fovAngleVertical = 90 / p_zoom;

    var l_midX = this.panWidth / 2;
    var l_midY = this.panHeight / 2;

	console.log("!!!!" + this.sheading);

    var l_diffHeading = this.sheading - p_pov.heading;
    l_diffHeading = normalizeAngle(l_diffHeading);
    l_diffHeading /= l_fovAngleHorizontal;

    var l_diffPitch = (p_pov.pitch - this.spitch) / l_fovAngleVertical;

    var x = l_midX + l_diffHeading * this.panWidth;
    var y = l_midY + l_diffPitch * this.panHeight;

    var l_point = new google.maps.Point(x, y);

    return l_point;
}

// create the 'marker' (a div containing an image which can be clicked)
SVO.prototype.m_initMarker = function (className, id, lat, lng, width, height)
{

	jQuery("#mapHolder").append("<div style='position:absolute; padding:0px; margin:0px; top:0px; left:0px; width:800px; height:400px; overflow:hidden; border:1px solid #f00; '><div class='" + className + "' width='" + width + "' height='" + height + "'  id='" + id + "' lat='" + lat + "' lng='" + lng + "'  ></div></div>");

    var l_markerDiv = jQuery("#" + id)[0];
	
    l_markerDiv.style.width = width + "px";
    l_markerDiv.style.height = height + "px";
    l_markerDiv.style.position = "relative";
    l_markerDiv.style.top = "0px";
    l_markerDiv.style.left = "0px";
    l_markerDiv.style.zIndex = "1000";
    l_markerDiv.style.display = "none";

    var l_iconDiv = jQuery("#" + id)[0];
    l_iconDiv.innerHTML = "<img src='" + this.image + "' width='100%' height='100%' alt='' />";

	console.log("image added");

    this.m_updateMarker(className);
	
}

SVO.prototype.m_updateMarker = function (className)
{
    
	var l_pov = pan.getPov();
    
	if (l_pov)
    {
		this.list = Array();
	
		jQuery("." + className)
			.each(
				function(index,value){
					svo.list.push(Array(jQuery(value).attr("id"),jQuery(value).attr("lat"),jQuery(value).attr("lng"),jQuery(value).attr("width"),jQuery(value).attr("height")));
				}
		);
		
		for(x in this.list){
	
			if(x!=3){
			
			console.log(x);
	
			var l_zoom = pan.getZoom();

			var l_adjustedZoom = Math.pow(2, l_zoom) / 2;
				
			this.createPoint("tempPoint", this.list[x][1], this.list[x][2]);

			this.sheading = google.maps.geometry.spherical.computeHeading(this.streetPt, this.tempPoint);
			
			var l_pixelPoint = this.m_convertPointProjection(l_pov, l_adjustedZoom);
			
			distance = google.maps.geometry.spherical.computeDistanceBetween(this.streetPt, this.tempPoint);

			id = ("#" + this.list[x][0]);
			var l_markerDiv = jQuery(id)[0];
			
			var l_distanceScale = 50 / distance;
			
			l_adjustedZoom = l_distanceScale;

			var wd = this.list[x][3] * l_adjustedZoom;
			var ht = this.list[x][4] * l_adjustedZoom;

			var x_coord = l_pixelPoint.x - Math.floor(wd / 2);
			var y_coord = l_pixelPoint.y - Math.floor(ht / 2);

			l_markerDiv.style.display = "block";
			l_markerDiv.style.position = "absolute";
			l_markerDiv.style.left = x_coord + "px";
			l_markerDiv.style.top = y_coord + "px";
			l_markerDiv.style.width = wd + "px";
			l_markerDiv.style.height = ht + "px";

			l_markerDiv.style.display = distance < this.maximumDistance ? "block" : "none";

			}

		}
    }
}

function formatFloat(n, d)
{
    var m = Math.pow(10, d);
    return Math.round(n * m, 10) / m;
}

function normalizeAngle(a)
{
    while (a > 180)
    {
        a -= 360;
    }

    while (a < -180)
    {
        a += 360;
    }

    return a;
}


