<?PHP

	class solomon_map_item_editor{
	
		function __construct(){

			$action = false;
			
			add_action("save_post", array($this, "save_post"));
			add_action("update_post", array($this, "save_post"));
			add_action("publish_post", array($this, "save_post"));
			
			if(isset($_GET['action'])){
			
				$post = get_post($_GET['post']);
	
				if($_GET['action']=="edit" && $post->post_type=="solomonmapitem"){
					$action = true;
				}

			}else{
			
				if(isset($_GET['post_type'])){
					if($_GET['post_type']=="solomonmapitem"){
						$action = true;
					}
				}
				
			}
			
			if($action){
			
				add_action("admin_enqueue_scripts", array($this, "scripts"));
				add_action("admin_head", array($this, "metabox"));
				
			}
			
		}
		
		function scripts(){
			wp_register_style( 'spectrum_css', plugins_url() . '/solomon/spectrum/spectrum.css', false, '1.0.0' );
			wp_enqueue_style( 'spectrum_css' );
			wp_register_script( 'spectrum_js', plugins_url() . '/solomon/spectrum/spectrum.js', false, '1.0.0' );
			wp_enqueue_script( 'spectrum_js' );
			wp_register_style( 'leaflet_css', plugins_url() . '/solomon/leaflet/leaflet.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_css' );
			wp_register_style( 'leaflet_draw_css', plugins_url() . '/solomon/leaflet/leaflet.draw.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_draw_css' );
			wp_register_style( 'leaflet_geosearch_css', plugins_url() . '/solomon/leaflet/l.geosearch.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_geosearch_css' );
			wp_register_style( 'leaflet_geostroketype_css', plugins_url() . '/solomon/leaflet/l.geostroketype.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_geostroketype_css' );
			wp_register_style( 'leaflet_geostroketype_css', plugins_url() . '/solomon/leaflet/l.geostroketype.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_geostroketype_css' );
			wp_register_style( 'leaflet_geocustommarker_css', plugins_url() . '/solomon/leaflet/l.geocustommarker.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_geocustommarker_css' );
			wp_register_style( 'leaflet_extra_css', plugins_url() . '/solomon/leaflet/leaflet.extra.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_extra_css' );
			wp_register_script( 'leaflet_js', plugins_url() . '/solomon/leaflet/leaflet.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_js' );
			wp_register_script( 'leaflet_draw_js', plugins_url() . '/solomon/leaflet/leaflet.draw.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_draw_js' );
			wp_register_script( 'leaflet_setup_js', plugins_url() . '/solomon/leaflet/leaflet.setup.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_setup_js' );
			wp_register_script( 'leaflet_geosearch_js', plugins_url() . '/solomon/leaflet/l.control.geosearch.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_geosearch_js' );
			wp_register_script( 'leaflet_geolayercolor_js', plugins_url() . '/solomon/leaflet/l.control.geolayercolor.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_geolayercolor_js' );
			wp_register_script( 'leaflet_geostrokecolor_js', plugins_url() . '/solomon/leaflet/l.control.geostrokecolor.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_geostrokecolor_js' );
			wp_register_script( 'leaflet_geostroketype_js', plugins_url() . '/solomon/leaflet/l.control.geostroketype.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_geostroketype_js' );
			wp_register_script( 'leaflet_geocustommarker_js', plugins_url() . '/solomon/leaflet/l.control.geocustommarker.js', false, '1.0.0' );
			
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
			
			foreach ( $query_images->posts as $image ) {
				$images[$image->ID] = wp_get_attachment_url( $image->ID );
			}
			
			$customMarkers = array(
				'customMarkers' =>  $images
			);
			wp_localize_script( 'leaflet_geocustommarker_js', 'customMarkers', $customMarkers );
			
			wp_enqueue_script( 'leaflet_geocustommarker_js' );
			
			wp_register_script( 'leaflet_geosearch_openstreetmap_js', plugins_url() . '/solomon/leaflet/l.geosearch.provider.openstreetmap.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_geosearch_openstreetmap_js' );
			wp_register_script( 'leaflet_imageoverlay_js', plugins_url() . '/solomon/leaflet/leaflet.imageOverlay.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_imageoverlay_js' );
			wp_register_script( 'leaflet_extra_js', plugins_url() . '/solomon/leaflet/leaflet.extra.js', false, '1.0.0' );
			
			global $post;
			
			if(isset($post)){	

				$mapData = array(
					'latLng' =>  get_post_meta($post->ID,"geocenter",true),
					'zoom' => get_post_meta($post->ID,"geozoom",true)
				);
				wp_localize_script( 'leaflet_extra_js', 'mapData', $mapData );
			
			}
			
			wp_enqueue_script( 'leaflet_extra_js' );

		}
	
		function metabox(){
	
			add_meta_box("solomonmapitemmeta",__("Edit Map"),array($this,"editor"));

		}	
		
		function layer_options($counter,$geocolors,$geostrokecolors,$geostrokeopacity,$geofillopacity,$geostroke){
			?>
				m.options['color'] = "<?PHP echo $geostrokecolors[$counter]; ?>";
				m.options['opacity'] = <?PHP if($geostrokeopacity[$counter]==""){ echo 1; }else{ echo $geostrokeopacity[$counter]; } ?>;
				m.options['fillColor'] = "<?PHP echo $geocolors[$counter]; ?>";
				m.options['fillOpacity'] = <?PHP if($geofillopacity[$counter]==""){ echo 1; }else{ echo $geofillopacity[$counter]; } ?>;
				m.options['dashArray'] = "<?PHP echo str_replace("--"," ",$geostroke[$counter]); ?>";
			<?PHP
		}
		
		function editor(){	
			global $post;
			?> 
				<div id="map" style="height:600px; width:100%"></div>
				<?PHP
					
					$geodata = get_post_meta($post->ID, "geodata", true);
					$geocolors = explode(" ", get_post_meta($post->ID, "geofillcolors", true));
					$geostrokecolors = explode(" ", get_post_meta($post->ID, "geostrokecolors", true));
					$geostrokeopacity = explode(" ", get_post_meta($post->ID, "geostrokeopacity", true));
					$geofillopacity = explode(" ", get_post_meta($post->ID, "geofillopacity", true));
					$geomarkers = explode(" ", get_post_meta($post->ID, "geomarkers", true));
					$geomarkersizes = explode(" ", str_replace(", ",",", get_post_meta($post->ID, "geomarkersizes", true)));
					$geomarkerurls = $geomarkers;
					$geomarkerurlsizes = $geomarkersizes;
					$geostroke = explode(" ", get_post_meta($post->ID, "geostroke", true));
					$geozoom = explode(" ", get_post_meta($post->ID, "geozoom", true));
					$geocenter = explode(" ", get_post_meta($post->ID, "geocenter", true));
					
					$geo_array = explode( 'geo', str_replace(" ", "", str_replace("LatLng", "", str_replace(")", "", str_replace( "(", "",  $geodata ) ) ) ) );

					$counter = 0;	

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
					
					foreach ( $query_images->posts as $image ) {
					
						$data = wp_get_attachment_metadata($image->ID);
						
						$parts = explode("/", $image->guid);
						
						array_pop($parts);
						
						$date = implode("/", $parts);
						
						?>
						<div id="custom_icon_<?PHP echo $image->ID ?>" iconurl="<?PHP echo $date . "/" . $data['sizes']['medium']['file']; ?>" size="<?PHP echo $data['sizes']['medium']['width']; ?>, <?PHP echo $data['sizes']['medium']['height']; ?>" ></div> 
						<?PHP
					}
					
					?><div id="custom_icon_0" iconurl="<?PHP echo site_url() . "/wp-content/plugins/" . $default; ?>" size="<?PHP echo "25, 41"; ?>" ></div><?PHP
					
					foreach($geo_array as $row){

						$geo = explode("=", $row);	
						
						if(trim($geo[0])!==""){
						
							echo "<script type='text/javascript' language='javascript'>";
							
							echo '
								jQuery(document).ready(
									function(){
							';
										
							if($geo[0] == "CIRCLE")
							{

								$newpoint = ( substr($geo[1], -1) === ',' ) ? rtrim($geo[1], ",") : $geo[1];
								
								echo 'var m = new L.circle(new L.LatLng('.$newpoint.'),'.substr($geo[2],0,strlen($geo[2])-1).');';
								$this->layer_options($counter,$geocolors,$geostrokecolors,$geostrokeopacity,$geofillopacity,$geostroke);
								echo 'drawnItems.addLayer(m);';

							}
							if($geo[0] == "POINT")
							{

								$url = array_shift($geomarkerurls);
								$size = array_shift($geomarkersizes);
								
								echo "// " . $size . ";\n";
								
								$size_parts = explode(",",$size); 
								
								echo 'customIcon = L.icon({
									iconUrl: "' . $url . '",
									shadowUrl: "",
									iconSize: [' . $size . '], 
									iconAnchor:   [' . ($size_parts[0]/2) . ',' . ($size_parts[0]/2) . ']
								});	';

								$newpoint = ( substr($geo[1], -1) === ',' ) ? rtrim($geo[1], ",") : $geo[1];

								echo 'var m = new L.marker(new L.LatLng('.$newpoint.'),{icon: customIcon});';
								$this->layer_options($counter,$geocolors,$geostrokecolors,$geostrokeopacity,$geofillopacity,$geostroke);
								echo 'drawnItems.addLayer(m);';

								echo 'm.options.icon.options.iconUrl = "' . $url . '"';
							
							}
							if($geo[0] == "POLYGON")
							{

								echo 'var co_ords = [];';
								$co_ords = explode(",", $geo[1]);
								$co_ords = array_filter($co_ords);
								
								$co_ords_holder = array();

								for($x=0;$x!=count($co_ords);$x+=2){
									$pointX = str_replace(",","",$co_ords[($x)]);
									$pointY = str_replace(",","",$co_ords[($x+1)]);
									array_push($co_ords_holder, array($pointX, $pointY));
									echo "co_ords.push(new L.LatLng(".$pointX.",".$pointY."));";
								}
								
								echo 'var m = new L.Polygon(co_ords);'; 
								$this->layer_options($counter,$geocolors,$geostrokecolors,$geostrokeopacity,$geofillopacity,$geostroke);
								echo 'drawnItems.addLayer(m);';
							}
							if($geo[0] == "POLYLINE")
							{

								echo 'var co_ords = [];';
								$co_ords = explode(",", $geo[1]);
								$co_ords = array_filter($co_ords);
								
								$co_ords_holder = array();

								for($x=0;$x!=count($co_ords);$x+=2){
									$pointX = str_replace(",","",$co_ords[($x)]);
									$pointY = str_replace(",","",$co_ords[($x+1)]);
									array_push($co_ords_holder, array($pointX, $pointY));
									echo "co_ords.push(new L.LatLng(".$pointX.",".$pointY."));";
								}
								
								echo 'var m = new L.Polyline(co_ords);'; 
								$this->layer_options($counter,$geocolors,$geostrokecolors,$geostrokeopacity,$geofillopacity,$geostroke);
								echo 'drawnItems.addLayer(m);';
							}
							
							$counter++;
									
							echo " });</script>";
							
						}
						
					}
					
				?>
				<input type="hidden" name="geodata" id="EntryLatlng" value="<?PHP echo implode(" ", $geodata); ?>" />
				<input type="hidden" name="geofillcolors" id="EntryColors" value="<?PHP echo implode(" ", $geocolors); ?>" />
				<input type="hidden" name="geomarkers" id="EntryMarkers" value="<?PHP echo implode(" ", $geomarkers); ?>" />
				<input type="hidden" name="geomarkersizes" id="EntryMarkerSizes" value="<?PHP echo implode(" ", $geomarkersizes); ?>" />
				<input type="hidden" name="geostrokecolors" id="EntryStrokeColors" value="<?PHP echo implode(" ",  $geostrokecolors); ?>" />
				<input type="hidden" name="geofillopacity" id="EntryFillOpacity" value="<?PHP echo implode(" ",  $geofillopacity); ?>" />
				<input type="hidden" name="geostrokeopacity" id="EntryStrokeOpacity" value="<?PHP echo implode(" ",  $geostrokeopacity); ?>" />
				<input type="hidden" name="geostroke" id="EntryStrokeType" value="<?PHP echo implode(" ",  $geostroke); ?>" />
				<input type="hidden" name="geozoom" id="EntryMapZoom" value="<?PHP echo implode(" ",  $geozoom); ?>" />
				<input type="hidden" name="geocenter" id="EntryMapCenter" value="<?PHP echo implode(" ",  $geocenter); ?>" />
			<?PHP
		}
		
		function save_post($post_id){
			$post = get_post($post_id);
			if($post->post_type=="solomonmapitem"){
				if(count($_POST)!=0){
					if(isset($_POST['action'])){
					
						update_post_meta($post_id, "geodata", $_POST['geodata']); 
						update_post_meta($post_id, "geostrokecolors", $_POST['geostrokecolors']); 
						update_post_meta($post_id, "geofillcolors", $_POST['geofillcolors']); 
						update_post_meta($post_id, "geofillopacity", $_POST['geofillopacity']); 
						update_post_meta($post_id, "geostrokeopacity", $_POST['geostrokeopacity']); 
						update_post_meta($post_id, "geostroke", $_POST['geostroke']); 
						update_post_meta($post_id, "geozoom", $_POST['geozoom']); 
						update_post_meta($post_id, "geocenter", $_POST['geocenter']); 
						update_post_meta($post_id, "geomarkers", $_POST['geomarkers']); 
						update_post_meta($post_id, "geomarkersizes", $_POST['geomarkersizes']); 
					}
				}
			}
		}
	
	}
	
	$solomon_map_item_editor = new solomon_map_item_editor();
	