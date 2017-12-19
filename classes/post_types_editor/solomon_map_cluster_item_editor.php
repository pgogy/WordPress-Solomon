<?PHP

	class solomon_map_cluster_item_editor{
	
		function __construct(){

			$action = false;
			
			add_action("save_post", array($this, "save_post"));
			add_action("update_post", array($this, "save_post"));
			add_action("publish_post", array($this, "save_post"));
			
			if(isset($_GET['action'])){
			
				if(isset($_GET['post'])){
			
					$post = get_post($_GET['post']);
	
					if($_GET['action']=="edit" && $post->post_type=="solomonmapcitem"){
						$action = true;
					}
					
				}

			}else{
			
				if(isset($_GET['post_type'])){
					if($_GET['post_type']=="solomonmapcitem"){
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
			wp_register_style( 'leaflet_css', plugins_url() . '/solomon/leaflet/leaflet.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_css' );
			wp_register_style( 'leaflet_draw_css', plugins_url() . '/solomon/leaflet/leaflet.draw.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_draw_css' );
			wp_register_style( 'leaflet_geosearch_css', plugins_url() . '/solomon/leaflet/l.geosearch.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_geosearch_css' );
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
			wp_register_script( 'leaflet_geocustommarker_js', plugins_url() . '/solomon/leaflet/l.control.geocustommarker.js', false, '1.0.0' );
			
			$query_images_args = array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_status'    => 'inherit',
				'posts_per_page' => 20,
			);

			$query_images = new WP_Query( $query_images_args );

			$images = array();
			
			$default = plugin_basename(__FILE__);
			$parts = explode("/", $default); 
			$default = $parts[0] . "/leaflet/images/marker-icon.png";
			$images[0] = array(site_url() . "/wp-content/plugins/" . $default,25,41);
			
			foreach ( $query_images->posts as $image ) {
				$images[$image->ID] = wp_get_attachment_image_src( $image->ID, "geomarker" );
			}
			
			$customMarkers = array(
				'customMarkers' =>  $images
			);
			wp_localize_script( 'leaflet_geocustommarker_js', 'customMarkers', $customMarkers );
			
			wp_enqueue_script( 'leaflet_geocustommarker_js' );
			
			wp_register_script( 'leaflet_geosearch_openstreetmap_js', plugins_url() . '/solomon/leaflet/l.geosearch.provider.openstreetmap.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_geosearch_openstreetmap_js' );
			wp_register_script( 'leaflet_extra_cluster_js', plugins_url() . '/solomon/leaflet/leaflet.extra.cluster.js', "leaflet_geocustommarker_js", '1.0.0' );
			
			global $post;
			
			if(isset($post)){	

				$mapData = array(
					'latLng' =>  get_post_meta($post->ID,"geocenter",true),
					'zoom' => get_post_meta($post->ID,"geozoom",true)
				);
				wp_localize_script( 'leaflet_extra_cluster_js', 'mapData', $mapData );
			
			}
			
			wp_enqueue_script( 'leaflet_extra_cluster_js' );

		}
	
		function metabox(){
	
			add_meta_box("solomonmapcitemmeta",__("Edit Map"),array($this,"editor"), "solomonmapcitem", "advanced", "high");

		}	

		function move_deck() {

		    	global $post, $wp_meta_boxes;

		    	do_meta_boxes(get_current_screen(), 'advanced', $post);
		    	unset($wp_meta_boxes['solomonmapcitem']['advanced']);
	
		}
		
		function editor(){	
			global $post;
			?> 
				<p><?PHP echo __("Use the map below to add an item. Choose a picture from the top right button, then click the button below it to place the picture."); ?></p>
				<div id="map" style="height:600px; width:100%"></div>
				<?PHP
					
					$geodata = get_post_meta($post->ID, "geodata", true);
					$geomarkers = explode(" ", get_post_meta($post->ID, "geomarkers", true));
					$geomarkersizes = explode(" ", str_replace(", ",",", get_post_meta($post->ID, "geomarkersizes", true)));
					$geomarkerurls = $geomarkers;
					$geomarkerurlsizes = $geomarkersizes;
					$geozoom = explode(" ", get_post_meta($post->ID, "geozoom", true));
					$geocenter = explode(" ", get_post_meta($post->ID, "geocenter", true));
					
					$geo_array = explode( 'geo', str_replace(" ", "", str_replace("LatLng", "", str_replace(")", "", str_replace( "(", "",  $geodata ) ) ) ) );

					$counter = 0;	

					$query_images_args = array(
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'post_status'    => 'inherit',
						//'posts_per_page' => -1,
						'posts_per_page' => 20
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
						<div id="custom_icon_<?PHP echo $image->ID ?>" iconurl="<?PHP echo $date . "/" . $data['sizes']['geomarker']['file']; ?>" size="<?PHP echo $data['sizes']['geomarker']['width']; ?>, <?PHP echo $data['sizes']['geomarker']['height']; ?>" ></div> 
						<?PHP
					}
					
					?><div id="custom_icon_0" iconurl="<?PHP echo site_url() . "/wp-content/plugins/" . $default; ?>" size="<?PHP echo "25, 41"; ?>" ></div><?PHP
					
					echo "<script type='text/javascript' language='javascript'>";
							
					echo '
							jQuery(document).ready(
								function(){
									
									jQuery("#geocustommarkers")
										.children()
										.first()
										.trigger("click");
										
									console.log("click event sent");
					
								}
							);
							
					</script>';
					
					foreach($geo_array as $row){

						$geo = explode("=", $row);	
						
						if(trim($geo[0])!==""){
						
							echo "<script type='text/javascript' language='javascript'>";
							
							echo '
								jQuery(document).ready(
									function(){
									
							';
										
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
									iconAnchor:   [' . ($size_parts[0]/2) . ',' . ($size_parts[0]) . ']
								});	';

								$newpoint = ( substr($geo[1], -1) === ',' ) ? rtrim($geo[1], ",") : $geo[1];

								echo 'var m = new L.marker(new L.LatLng('.$newpoint.'),{icon: customIcon});';
								echo 'drawnItems.addLayer(m);';

								echo 'm.options.icon.options.iconUrl = "' . $url . '"';
							
							}
							
							$counter++;
									
							echo " });</script>";
							
						}
						
					}
	
				?>
				
				<input type="hidden" name="geodata" id="EntryLatlng" value="<?PHP echo $geodata; ?>" />
				<input type="hidden" name="geomarkers" id="EntryMarkers" value="<?PHP echo implode(" ", $geomarkers); ?>" />
				<input type="hidden" name="geomarkersizes" id="EntryMarkerSizes" value="<?PHP echo implode(" ", $geomarkersizes); ?>" />
				<input type="hidden" name="geozoom" id="EntryMapZoom" value="<?PHP echo implode(" ",  $geozoom); ?>" />
				<input type="hidden" name="geocenter" id="EntryMapCenter" value="<?PHP echo implode(" ",  $geocenter); ?>" />
				
				<?PHP
				
					$posts = get_posts(array("post_status" => "publish", "posts_per_page" => -1));
				
					$set = get_post_meta($post->ID, "post_solomonresource", true);
				
					echo "<label>" . __("Post related to his content") . "</label> <br />";
				
					echo "<select name='post_solomonresource'>";
					
					foreach($posts as $postdata){
						echo "<option value='" . $postdata->ID . "' ";
						if($set==$postdata->ID){
							echo " selected ";
						}
						echo ">" . $postdata->post_type . " : " . $postdata->post_title . "</option>";
					}
					
					echo "</select>";
		
		}
		
		function save_post($post_id){
			$post = get_post($post_id);
			if($post->post_type=="solomonmapcitem"){
				if(count($_POST)!=0){
					if(isset($_POST['action'])){
						update_post_meta($post_id, "geodata", $_POST['geodata']); 
						update_post_meta($post_id, "geozoom", $_POST['geozoom']); 
						update_post_meta($post_id, "geocenter", $_POST['geocenter']); 
						update_post_meta($post_id, "geomarkers", $_POST['geomarkers']); 
						update_post_meta($post_id, "geomarkersizes", $_POST['geomarkersizes']); 
										
						if(isset($_POST['post_solomonresource'])){
							update_post_meta($post->ID, "post_solomonresource",  $_POST["post_solomonresource"]);
						}
					}
				}
			}
		}
	
	}
	
	$solomon_map_cluster_item_editor = new solomon_map_cluster_item_editor();
	