<?PHP

	class solomon_streetview_map_editor{
	
		function __construct(){

			$action = false;
			
			add_action("save_post", array($this, "save_post"));
			add_action("update_post", array($this, "save_post"));
			add_action("publish_post", array($this, "save_post"));
			
			if(isset($_GET['action'])){
			
				if(isset($_GET['post'])){
			
					$post = get_post($_GET['post']);
		
					if($_GET['action']=="edit" && $post->post_type=="solomonmapwalk"){
						$action = true;
					}
					
				}

			}else{
			
				if(isset($_GET['post_type'])){
					if($_GET['post_type']=="solomonmapwalk"){
						$action = true;
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
			wp_register_script( 'leaflet_label_js', plugins_url() . '/solomon/leaflet/leaflet.label.js', array("leaflet_js"), '1.0.0' );
			wp_enqueue_script( 'leaflet_label_js' );
			wp_register_script( 'leaflet_geosearch_js', plugins_url() . '/solomon/leaflet/l.control.geosearch.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_geosearch_js' );
			wp_register_script( 'leaflet_geosearch_openstreetmap_js', plugins_url() . '/solomon/leaflet/l.geosearch.provider.openstreetmap.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_geosearch_openstreetmap_js' );
			wp_register_style( 'leaflet_geosearch_css', plugins_url() . '/solomon/leaflet/l.geosearch.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_geosearch_css' );
			wp_register_script( 'leaflet_extra_map_js', plugins_url() . '/solomon/leaflet/leaflet.extra.map.js', array("leaflet_js"), '1.0.0' );
			
			if(isset($post->ID)){
			$mapData = array(
					'latLng' =>  get_post_meta($post->ID,"geocenter",true),
					'zoom' => get_post_meta($post->ID,"geozoom",true)
				);
			}else{
				$mapData = array();
			}
			wp_localize_script( 'leaflet_extra_map_js', 'mapData', $mapData );

			wp_enqueue_script( 'leaflet_extra_map_js' );
			wp_register_script( 'leaflet_add_to_map_js', plugins_url() . '/solomon/leaflet/leaflet.add_to_map.js', array("leaflet_js"), '1.0.0' );
			wp_localize_script( 'leaflet_add_to_map_js', 'leaflet_add_to_map', array( 'ajax_url' => admin_url('admin-ajax.php')) );
			wp_enqueue_script( 'leaflet_add_to_map_js' );
		}
	
		function metabox(){

			add_meta_box("solomonmapmeta",__("Edit Map"),array($this,"editor"),"solomonmapwalk","advanced","high");

		}	

		function move_deck() {

		    	global $post, $wp_meta_boxes;

		    	do_meta_boxes(get_current_screen(), 'advanced', $post);
		    	unset($wp_meta_boxes["solomonmapwalk"]['advanced']);
	
		}
		
		function editor(){	

			global $post;
			
			?> 
			<script type="text/javascript">
			
				jQuery(document).ready(
					function(){
					
						// Add in a crosshair for the map
						var crosshairIcon = L.icon({
							iconUrl: '<?PHP echo plugins_url(); ?>/solomon/leaflet/images/crosshair.png',
							iconSize:     [20, 20], // size of the icon
							iconAnchor:   [10, 10], // point of the icon which will correspond to marker's location
						});
						crosshair = new L.marker(map.getCenter(), {icon: crosshairIcon, clickable:false});
						crosshair.addTo(map);

						// Move the crosshair to the center of the map when the user pans
						map.on('move', function(e) {
							crosshair.setLatLng(map.getCenter());
						});		

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
											
											if(fontweight!="bold"){
											
												jQuery(value).css("font-weight", "bold");
											
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
													
														for(x in data["geo"]){
														
															if(data["geo"][x].split(" ").join("").length!=0){
															
																parts = data["geo"][x].split("=");
											
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
																		
																	var m = new L.Polyline(co_ords_holder);
																	
																	className = "mapitem_" + data["id"];
																	
																	m.options['className'] = className;
													
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
											
											selected = Array();
									
											jQuery("a.mapitemadd")
												.each(
													function(index,value){
														fontweight = jQuery(value).css("font-weight");
														
														if(fontweight=="bold"){
												
															className = jQuery(this).attr("id");
															selected.push(className);
															
														}
													}
												);
											
											jQuery("#mapItems")
												.val(selected.join(" "));
													
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
									jQuery("#<?PHP echo $part; ?>").trigger("click");
								<?PHP		
								}
							?>
						}
					);
				</script>
			<?PHP
			
				$args = array("order" => "title", "post_type" => "solomonmaplay", "posts_per_page" => -1, 'post_status' => array('publish', 'pending', 'draft', 'future', 'private'));
			
				$posts = get_posts($args);
				
				?><h2>Streetview Overlays</h2><?PHP
				
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
				
		}
		
		function save_post($post_id){	
			$post = get_post($post_id);
			if($post->post_type=="solomonmapwalk"){
				if(count($_POST)!=0){
					if(isset($_POST['action'])){
						update_post_meta($post->ID, "mapItems", $_POST['mapItems']); 
						update_post_meta($post->ID, "popup_css", $_POST['popup_css']);
						update_post_meta($post_id, "geozoom", $_POST['geozoom']); 
						update_post_meta($post_id, "geocenter", $_POST['geocenter']); 
					}
				}
			}
		}	
	
	}
	
	$solomon_streetview_map_editor = new solomon_streetview_map_editor();
	