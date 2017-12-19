<?PHP

	class solomon_map_editor{
	
		function __construct(){

			$action = false;
			
			add_action("save_post", array($this, "save_post"));
			add_action("update_post", array($this, "save_post"));
			add_action("publish_post", array($this, "save_post"));
			
			if(!isset($_GET['page'])){
			
				if(isset($_GET['action'])){
				
					if(isset($_GET['post'])){
			
						$post = get_post($_GET['post']);
		
						if($_GET['action']=="edit" && $post->post_type=="solomonmap"){
							$action = true;
						}
						
					}

				}else{
				
					if(isset($_GET['post_type'])){
						if($_GET['post_type']=="solomonmap"){
							$action = true;
						}
					}
					
				}
				
			}
			
			if($action){
			
				add_action("admin_enqueue_scripts", array($this, "scripts"));
				add_action("admin_head", array($this, "metabox"));
				add_action('edit_form_after_title', array($this, 'move_deck'));
				
			}
			
		}
		
		function scripts(){

			global $post;

			wp_register_style( 'leaflet_css', plugins_url() . '/solomon/leaflet/leaflet.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_css' );
			wp_register_script( 'leaflet_js', plugins_url() . '/solomon/leaflet/leaflet.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_js' );
			wp_register_style( 'leaflet_toolbar_css', plugins_url() . '/solomon/leaflet/leaflet.toolbar.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_toolbar_css' );
			wp_register_style( 'leaflet_label_css', plugins_url() . '/solomon/leaflet/leaflet.label.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_label_css' );
			wp_register_style( 'leaflet_distort_css', plugins_url() . '/solomon/leaflet/leaflet.distortableimage.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_distort_css' );
			wp_register_style( 'leaflet_distorteditor_css', plugins_url() . '/solomon/css/leaflet.distortededitor.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_distorteditor_css' );
			wp_register_script( 'leaflet_toolbar_js', plugins_url() . '/solomon/leaflet/leaflet.toolbar.js', array("leaflet_js"), '1.0.0' );
			wp_enqueue_script( 'leaflet_toolbar_js' );			
			wp_register_script( 'leaflet_image_transform_js', plugins_url() . '/solomon/leaflet/leaflet.imagetransform.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_image_transform_js' );
			wp_register_script( 'leaflet_distort_js', plugins_url() . '/solomon/leaflet/leaflet.distortableimage.js', array("leaflet_js"), '1.0.0' );
			wp_enqueue_script( 'leaflet_distort_js' );
			wp_register_script( 'leaflet_label_js', plugins_url() . '/solomon/leaflet/leaflet.label.js', array("leaflet_js"), '1.0.0' );
			wp_enqueue_script( 'leaflet_label_js' );
			
			
			wp_register_script( 'leaflet_extra_map_js', plugins_url() . '/solomon/leaflet/leaflet.extra.map.js', array("leaflet_js"), '1.0.0' );
			
			$mapData = array(
					'latLng' =>  get_post_meta($post->ID,"geocenter",true),
					'zoom' => get_post_meta($post->ID,"geozoom",true)
				);
			wp_localize_script( 'leaflet_extra_map_js', 'mapData', $mapData );

			wp_enqueue_script( 'leaflet_extra_map_js' );
			wp_register_script( 'leaflet_add_to_map_js', plugins_url() . '/solomon/leaflet/leaflet.add_to_map.js', array("leaflet_js"), '1.0.0' );
			wp_localize_script( 'leaflet_add_to_map_js', 'leaflet_add_to_map', array( 'ajax_url' => admin_url('admin-ajax.php')) );
			wp_enqueue_script( 'leaflet_add_to_map_js' );
		}
	
		function metabox(){

			add_meta_box("solomonmapmeta",__("Edit Map"),array($this,"editor"),"solomonmap","advanced","high");
			add_meta_box("solomonmapcss",__("Popup CSS"),array($this,"css_editor"),"solomonmap","advanced","high");

		}	

		function move_deck() {

		    	global $post, $wp_meta_boxes;

		    	do_meta_boxes(get_current_screen(), 'advanced', $post);
		    	unset($wp_meta_boxes["solomonmap"]['advanced']);
	
		}
		
		function css_editor(){
			?>
				<textarea name="popup_css" style="width:100%; height:200px"><?PHP
					if(isset($_GET['post'])){
						echo get_post_meta($_GET['post'],"popup_css",true);
					}
				?></textarea>
			<?PHP
		}
		
		function editor(){	

			global $post;
			
			$query_images_args = array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_status'    => 'inherit',
				'posts_per_page' => - 1,
			);

			$query_images = new WP_Query( $query_images_args );

			$images = array();
			
			$default = plugin_basename(__FILE__);
			$parts = explode("/", $default); 
			$default = $parts[0] . "/leaflet/images/marker-icon.png";
			$images[] = site_url() . "/wp-content/plugins/" . $default;
			
			?><div id="customIcons"><?PHP
			
			foreach ( $query_images->posts as $image ) {
			
				$data = wp_get_attachment_metadata($image->ID);
				
				$parts = explode("/", $image->guid);
				
				array_pop($parts);
				
				$date = implode("/", $parts);
				
				?>
				<div id="custom_icon_<?PHP echo $image->ID ?>" iconurl="<?PHP echo $date . "/" . $data['sizes']['medium']['file']; ?>" size="<?PHP echo $data['sizes']['medium']['width']; ?>, <?PHP echo $data['sizes']['medium']['height']; ?>" ></div> 
				<?PHP
			}
			
			?><div id="custom_icon_0" iconurl="<?PHP echo site_url() . "/wp-content/plugins/" . $default; ?>" size="<?PHP echo "25, 41"; ?>" ></div></div><?PHP
			
			wp_reset_postdata();
			
			?> 
			<script type="text/javascript">
				
				jQuery(document).ready(
					function(){
					
						selected = Array();
						
						jQuery("a.mapitemaddimage")
							.each(
								function(index,value){
									jQuery(value).click(
										
										function(){
										
											fontweight = jQuery(value).css("font-weight");
											
											if(fontweight!="700"){
											
												jQuery(value).css("font-weight", "700");
											
												className = jQuery(this).attr("id");
										
													jQuery.ajax({
														url : leaflet_add_to_map.ajax_url,
														type : 'post',
														data : {
															action : 'get_post_geo_image',
															post_id : jQuery(this).attr("id"),
														},
														success : function( response ) {
															
																data = JSON.parse(response);
														
																items = jQuery("#mapItems").val();
													
																console.log(items);
													
																items += " " + data['id'];
														
																jQuery("#mapItems").val(items);
														
																anchors = data['anchors'].split(",");	
																markers = data['markers'].split(",");	
																
																anchorPoints = Array();
																markerPoints = Array();
																
																for(x=0;x<=anchors.length;x+=2){
																	if(anchors[x]!=undefined&&anchors[x+1]!=undefined){
																		if(!isNaN(anchors[x])){
																			anchorPoints.push(Array(parseFloat(anchors[x]),parseFloat(anchors[x+1])));
																		}
																	}
																}

																for(x=0;x<=markers.length;x+=2){
																	if(markers[x]!=undefined&&markers[x+1]!=undefined){
																		if(!isNaN(markers[x])){
																			markerPoints.push(Array(parseFloat(markers[x]),parseFloat(markers[x+1])));
																		}
																	}
																}
																
																var anchors = anchorPoints;
																var clipCoords = markerPoints;
																
																var image = L.imageTransform(data['src'], anchorPoints, { className: "mapitem_" + data['id'], opacity: 1, clip: clipCoords, disableSetClip: false });//.addTo(map);
															
																image.addTo(map);
																
															}
														}
													);
					
											}else{
											
												jQuery(value).css("font-weight", "400");
												
												items = jQuery("#mapItems").val();
									
												items = items.split(jQuery(value).attr("id").split("mapitem_").join("") + " ").join("");
												
												jQuery("#mapItems").val(items);
												
												jQuery("#map ." + jQuery(value).attr("id"))
													.each(
														function(index,value){
															jQuery(value)
																.remove();
														}
													)
											
											}
											
										}
										
									)
									
								}
							);
						
						jQuery("a.mapitemaddimagedistortoverlay")
							.each(
								function(index,value){
									jQuery(value).click(
										
										function(){
										
											fontweight = jQuery(value).css("font-weight");
											
											if(fontweight!="700"){
											
												jQuery(value).css("font-weight", "700");
											
												className = jQuery(this).attr("id");
										
												if(jQuery("#map img[src='" + jQuery(this).attr("img") + "']").length==0){
										
													jQuery.ajax({
														url : leaflet_add_to_map.ajax_url,
														type : 'post',
														data : {
															action : 'get_post_geo_imagedistortedoverlay',
															post_id : jQuery(this).attr("id"),
														},
														success : function( response ) {
															
																data = JSON.parse(response);
														
																items = jQuery("#mapItems").val();
													
																console.log(items);
													
																items += " " + data['id'];
												
																jQuery("#mapItems").val(items);
														
																TL = data['tl'].split(",");	
																TR = data['tr'].split(",");	
																BR = data['br'].split(",");	
																BL = data['bl'].split(",");	
																	
																img = new L.DistortableImageOverlay(
																			data['src'], {
																			corners: [	
																							new L.latLng(parseFloat(TL[0]),parseFloat(TL[1])),
																							new L.latLng(parseFloat(TR[0]),parseFloat(TR[1])),
																							new L.latLng(parseFloat(BL[0]),parseFloat(BL[1])),
																							new L.latLng(parseFloat(BR[0]),parseFloat(BR[1])),
																					   ]
																			}
																		  ).addTo(map);
																	
															}
														}
													);
			
												}else{
												
													jQuery("#map img[src='" + jQuery(this).attr("img") + "']")
													.each(
														function(index,value){
															jQuery(value)
																.css("display","block");
														}
													)
												
												}
												
											}else{
											
												jQuery(value).css("font-weight", "400");
												
												
												items = jQuery("#mapItems").val();
									
												items = items.split(jQuery(this).attr("id").split("mapitem_").join("") + " ").join("");
													
												jQuery("#mapItems").val(items);
												
												
												jQuery("#map img[src='" + jQuery(this).attr("img") + "']")
													.each(
														function(index,value){
															jQuery(value)
																.css("display","none");
														}
													);
												
											}
											
										}
									)	
								}
							);
							
							
						map.on('move', function (e) {
				
							latLng = map.getCenter();
							if(latLng['lat']!=0){
								jQuery("#EntryMapCenter").val(latLng['lat'] + " " + latLng['lng']);
							}
							jQuery("#EntryMapZoom").val(e.target['_zoom']);
			
						});
						
						
					
						jQuery("a.mapitemadd")
							.each(
								function(index,value){
									jQuery(value).click(
										
										function(){
										
											fontweight = jQuery(value).css("font-weight");
											
											if(fontweight!="700"){
											
												jQuery(value).css("font-weight", "700");
											
												jQuery.ajax({
													url : leaflet_add_to_map.ajax_url,
													type : 'post',
													data : {
														action : 'get_post_geo',
														classes : jQuery(value).attr("class"),
														post_id : jQuery(this).attr("id"),
													},
													success : function( response ) {
														
														data = JSON.parse(response);
														
														items = jQuery("#mapItems").val();
														
														console.log(items);
											
														items += " " + data['id'];
												
														jQuery("#mapItems").val(items);
												
														for(x in data["geo"]){
														
															if(data["geo"][x].split(" ").join("").length!=0){
															
																parts = data["geo"][x].split("=");
															
																if(parts[0]=="POLYGON"){
																	
																	co_ords = parts[1].split(",");
																							
																	co_ords_holder = Array();
																	
																	for(y=0;y<=co_ords.length;y+=2){
																		if(co_ords[y+1]!=undefined){
																			pointX = co_ords[y];
																			pointY = co_ords[y+1];
																			co_ords_holder.push(new L.LatLng(pointX,pointY));
																		}
																	}																	
																	
																	if(data['classes']!=undefined){
																		if(data['classes'].indexOf("mapitemlabel")!=-1){

																			var m = new L.Polygon(co_ords_holder).bindLabel(data['label'], { noHide: true });
																	
																		}
																	}else{
																	
																		var m = new L.Polygon(co_ords_holder);
																	
																	}
																						
																	className = "mapitem_" + data["id"];
																	
																	m.options['color'] = (data['strokecolors']) ? data['strokecolors'][x-1] : ""; 
																	m.options['stroke-opacity'] = (data["strokeopacity"]) ? data["strokeopacity"][x-1] : "";
																	m.options['fillColor'] = (data['colors']) ? data['colors'][x-1] : "";
																	m.options['fillOpacity'] = (data["opacity"]) ? data["opacity"][x-1] : "";
																	m.options['dashArray'] = (data["strokes"]) ? data["strokes"][x-1].split("--").join(" ") : "";
																	m.options['className'] = className;
													
																	map.addLayer(m);
																
																}
																
																if(parts[0] == "POLYLINE")
																{
																
																	co_ords = parts[1].split(",");
																	
																	co_ords_holder = Array();
																	
																	for(y=0;y<=co_ords.length;y+=2){
																		if(co_ords[y+1]!=undefined){
																			pointX = co_ords[y];
																			pointY = co_ords[y+1];
																			co_ords_holder.push(new L.LatLng(pointX,pointY));
																		}
																	}
																		
																	if(data['classes']!=undefined){
																		if(data['classes'].indexOf("mapitemlabel")!=-1){

																			var m = new L.Polyline(co_ords_holder).bindLabel(data['label'], { noHide: true });
																	
																		}
																	}else{
																	
																		var m = new L.Polyline(co_ords_holder);
																	
																	}
																	
																	className = "mapitem_" + data["id"];
																	
																	m.options['color'] = (data['strokecolors']) ? data['strokecolors'][x-1] : ""; 
																	m.options['stroke-opacity'] = (data["strokeopacity"]) ? data["strokeopacity"][x-1] : "";
																	m.options['fillColor'] = (data['colors']) ? data['colors'][x-1] : "";
																	m.options['fillOpacity'] = (data["opacity"]) ? data["opacity"][x-1] : "";
																	m.options['dashArray'] = (data["strokes"]) ? data["strokes"][x-1].split("--").join(" ") : "";
																	m.options['className'] = className;
													
																	map.addLayer(m);
																	
																}
																
																if(parts[0] == "CIRCLE")
																{

																	co_ords = parts[1].split(",");
																	
																	className = "mapitem_" + data["id"];
																	
																	if(data['classes']!=undefined){
																		if(data['classes'].indexOf("mapitemlabel")!=-1){

																			var m = new L.circle(new L.LatLng(co_ords[0],co_ords[1]),parts[2]).bindLabel(data['label'], { noHide: true });
																	
																		}
																	}else{
																	
																		var m = new L.circle(new L.LatLng(co_ords[0],co_ords[1]),parts[2]);
																	
																	}
																	
																	m.options['color'] = (data['strokecolors']) ? data['strokecolors'][x-1] : ""; 
																	m.options['stroke-opacity'] = (data["strokeopacity"]) ? data["strokeopacity"][x-1] : "";
																	m.options['fillColor'] = (data['colors']) ? data['colors'][x-1] : "";
																	m.options['fillOpacity'] = (data["opacity"]) ? data["opacity"][x-1] : "";
																	m.options['dashArray'] = (data["strokes"]) ? data["strokes"][x-1].split("--").join(" ") : "";
																	m.options['className'] = className;
																	
																	map.addLayer(m);
															
																}
																
																if(parts[0]=="POINT"){
																	
																	size = Array(0,0);
																	anchor = data['markersizes'][x-1].split(",");
																	
																	anchor[0] = anchor[0] / 2; 
																	anchor[1] = anchor[1] / 2; 
																
																	customIcon = L.icon({
																			iconUrl: data['markers'][x-1],
																			shadowUrl: "",
																			iconSize: size, 
																			iconAnchor: anchor
																		});
																	
																	co_ords = parts[1].split(",");
																	
																	if(data['classes']!=undefined){
																	
																		if(data['classes'].indexOf("mapitemlabel")!=-1){
																			if(data['classes'].indexOf("mapitemtextonly")!=-1){

																				<?PHP
																				
																					$default = plugin_basename(__FILE__);
																					$parts = explode("/", $default); 
																					$default = $parts[0] . "/leaflet/images/marker-icon.png";
																				
																				?>
																			
																				customIcon = L.icon({
																					iconUrl: "<?PHP echo $default; ?>",
																					shadowUrl: "",
																					iconSize: size, 
																					iconAnchor: anchor
																				});
																																							
																				var m = new L.marker(new L.LatLng(co_ords[0],co_ords[1]),{icon: customIcon}).bindLabel(data['label'], { noHide: true });
																		
																			}else{
																				var m = new L.marker(new L.LatLng(co_ords[0],co_ords[1]),{icon: customIcon}).bindLabel(data['label'], { noHide: true });
																			}
																		}
																		
																	}else{
																	
																		var m = new L.marker(new L.LatLng(co_ords[0],co_ords[1]),{icon: customIcon});
																	
																	}
																	
																	className = "mapitem_" + data["id"];
																	
																	m.options.icon.options.className = className;
																	
																	map.addLayer(m);
																
																}
																
															}
															
														}
														
													}
												});
											
											}else{
											
												jQuery(value).css("font-weight", "400");
												jQuery("." + jQuery(this).attr("id"))
													.each(
														function(index,innervalue){
														
															node = jQuery(innervalue).get(0);															
															
															items = jQuery("#mapItems").val();
												
															console.log(jQuery(value));
															console.log(jQuery(value).attr("id"));
												
															console.log(jQuery(value).attr("id").split("mapitem_").join("") + " ");
												
															items = items.split(jQuery(value).attr("id").split("mapitem_").join("") + " ").join("");
													
															jQuery("#mapItems").val(items);
															
															
															if(node.nodeName=="IMG"){
															
																zindex = jQuery(innervalue).css("z-index");
															
																jQuery(innervalue)
																	.remove();

																																	
																if(jQuery(value).hasClass("mapitemlabel")){
																	jQuery(".leaflet-label")
																		.each(
																			function(index,value){
																				if(jQuery(value).css("z-index")==zindex){
																					jQuery(value)
																						.remove();
																				}
																			}
																		);
																}

															
															}else{
															
																jQuery(innervalue)
																	.parent()
																	.remove();
																		
															}
														}
													)
													
											}
									
										}
									)
										
								}
							);
					
					}
				);

		
				</script>
				<?PHP
					$geozoom = explode(" ", get_post_meta($post->ID, "geozoom", true));
					$geocenter = explode(" ", get_post_meta($post->ID, "geocenter", true));
					$geoitems = explode(" ", get_post_meta($post->ID, "mapItems", true));
				?>
				<input type="hidden" name="geozoom" id="EntryMapZoom" value="<?PHP echo implode(" ",  $geozoom); ?>" />
				<input type="hidden" name="geocenter" id="EntryMapCenter" value="<?PHP echo implode(" ",  $geocenter); ?>" />
				<div id="map" style="height:600px; width:100%"></div>
				<input id="mapItems" name="mapItems" type="hidden" />
				<?PHP
					$entries = get_post_meta($post->ID, "mapItems", true);
					$parts = explode(" ", $entries);
				?>
				<script type="text/javascript">
					jQuery(document).ready(
						function(){
							<?PHP
								foreach($parts as $part){
								?>
									console.log("<?PHP echo $part; ?>");
									jQuery("#mapitem_<?PHP echo $part; ?>").trigger("click");
								<?PHP		
								}
							?>
						}
					);
				</script>
			<?PHP
			
				$args = array("order" => "title", "post_type" => "solomonmapitem", "posts_per_page" => -1, 'post_status' => array('publish', 'pending', 'draft', 'future', 'private'));
			
				$posts = get_posts($args);
				
				?><h2>Shapes</h2><?PHP
				
				foreach($posts as $post){
					?><a style="margin:5px" class="mapitemadd" id="mapitem_<?PHP echo $post->ID ?>"><?PHP 
						if(trim($post->post_title)==""){
							echo "No title";
						}else{
							echo $post->post_title;
						}
					?></a><?PHP
				}
				
				wp_reset_postdata();
				
				$args = array("order" => "title", "post_type" => "solomonmapioverlay", "posts_per_page" => -1, 'post_status' => array('publish', 'pending', 'draft', 'future', 'private'));
			
				$posts = get_posts($args);
				
				?><h2>Overlays</h2><?PHP
				
				foreach($posts as $post){
					?><a img="<?PHP echo get_post_meta($post->ID, "imageoverlay", true); ?>" style="margin:5px" class="mapitemaddimagedistortoverlay" id="mapitem_<?PHP echo $post->ID ?>"><?PHP 
						if(trim($post->post_title)==""){
							echo "No title";
						}else{
							echo $post->post_title;
						}
					?></a><?PHP
				}
				
				wp_reset_postdata();
				
				?><h2>Images</h2><?PHP
				
				$args = array("order" => "title", "post_type" => "solomonmapimage", "posts_per_page" => -1, 'post_status' => array('publish', 'pending', 'draft', 'future', 'private'));
			
				$posts = get_posts($args);
				
				foreach($posts as $post){
					?><a img="<?PHP echo get_post_meta($post->ID, "imageoverlay", true); ?>" style="margin:5px" class="mapitemaddimage" id="mapitem_<?PHP echo $post->ID ?>"><?PHP 
						if(trim($post->post_title)==""){
							echo "No title";
						}else{
							echo $post->post_title;
						}
					?></a><?PHP
				}
				
				wp_reset_postdata();
				
				?><h2>Labels</h2><?PHP
				
				$args = array("order" => "title", "post_type" => "solomonmaplabel", "posts_per_page" => -1, 'post_status' => array('publish', 'pending', 'draft', 'future', 'private'));
			
				$posts = get_posts($args);
				
				foreach($posts as $post){
					?><a style="margin:5px" class="mapitemadd mapitemlabel" id="mapitem_<?PHP echo $post->ID ?>"><?PHP 
						if(trim($post->post_title)==""){
							echo "No title";
						}else{
							echo $post->post_title;
						}
					?></a><?PHP
				}
				
				wp_reset_postdata();
				
				?><h2>Text</h2><?PHP
				
				$args = array("order" => "title", "post_type" => "solomonmaptlabel", "posts_per_page" => -1, 'post_status' => array('publish', 'pending', 'draft', 'future', 'private'));
			
				$posts = get_posts($args);
				
				foreach($posts as $post){
					?><a style="margin:5px" class="mapitemadd mapitemlabel mapitemtextonly" id="mapitem_<?PHP echo $post->ID ?>"><?PHP 
						if(trim($post->post_title)==""){
							echo "No title";
						}else{
							echo $post->post_title;
						}
					?></a><?PHP
				}
				
				wp_reset_postdata();
		}
		
		function save_post($post_id){	
			$post = get_post($post_id);
			if($post->post_type=="solomonmap"){
				if(count($_POST)!=0){
					if(isset($_POST['action'])){
						update_post_meta($post->ID, "mapItems", trim($_POST['mapItems'])); 
						update_post_meta($post->ID, "popup_css", $_POST['popup_css']);
						update_post_meta($post_id, "geozoom", $_POST['geozoom']); 
						update_post_meta($post_id, "geocenter", $_POST['geocenter']); 
					}
				}
			}
		}	
	
	}
	
	$solomon_map_editor = new solomon_map_editor();
	