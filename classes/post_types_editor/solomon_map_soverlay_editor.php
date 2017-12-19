<?PHP

	class solomon_map_soverlay_editor{
	
		function __construct(){

			$action = false;
			
			add_action("save_post", array($this, "save_post"));
			add_action("update_post", array($this, "save_post"));
			add_action("publish_post", array($this, "save_post"));
			
			if(isset($_GET['action'])){
			
				if(isset($_GET['post'])){
			
					$post = get_post($_GET['post']);
		
					if($_GET['action']=="edit" && $post->post_type=="solomonmaplay"){
						$action = true;
					}

				}

			}else{
			
				if(isset($_GET['post_type'])){
					if($_GET['post_type']=="solomonmaplay"){
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
			wp_register_script( 'leaflet_geosearch_openstreetmap_js', plugins_url() . '/solomon/leaflet/l.geosearch.provider.openstreetmap.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_geosearch_openstreetmap_js' );
			wp_register_script( 'leaflet_streetview_image', plugins_url() . '/solomon/leaflet/leaflet.streetview.image.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_streetview_image' );
			wp_register_script( 'leaflet_streetview_js', plugins_url() . '/solomon/leaflet/leaflet.streetview.js', false, '1.0.0' );
			
			global $post;
			
			if(isset($post)){	

				$mapData = array(
					'latLng' =>  get_post_meta($post->ID,"geocenter",true),
					'zoom' => get_post_meta($post->ID,"geozoom",true)
				);
				wp_localize_script( 'leaflet_streetview_js', 'mapData', $mapData );
			
			}
			
			wp_enqueue_script( 'leaflet_streetview_js' );

		}
	
		function metabox(){
	
			add_meta_box("solomonmapitemmeta",__("Edit Map"),array($this,"editor"), "solomonmaplay", "advanced", "high");

		}	

		function move_deck() {

		    	global $post, $wp_meta_boxes;

		    	do_meta_boxes(get_current_screen(), 'advanced', $post);
		    	unset($wp_meta_boxes['solomonmaplay']['advanced']);
	
		}
		
		function editor(){	
			global $post;
			?> 
				<p><?PHP echo __("Use the map below to add an item. Options for the item are on the right hand side."); ?></p>
				<div id="map" style="height:600px; width:100%"></div>
				<?PHP
					
					$geodata = get_post_meta($post->ID, "geodata", true);
					$geozoom = get_post_meta($post->ID, "geozoom", true);
					$geocenter = get_post_meta($post->ID, "geocenter", true);
					
					$geo_array = explode( 'geo', str_replace(" ", "", str_replace("LatLng", "", str_replace(")", "", str_replace( "(", "",  $geodata ) ) ) ) );

					$counter = 0;	

					$query_images_args = array(
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'post_status'    => 'inherit',
						'posts_per_page' => 5,
					);

					$query_images = new WP_Query( $query_images_args );
					
					$chosen = get_post_meta($post->ID, "geoimage", true);

					foreach ( $query_images->posts as $image ) {
					
						$data = wp_get_attachment_metadata($image->ID);

						if(isset($data['sizes'])){

						$parts = explode("/", $image->guid);
						
						array_pop($parts);
						
						$url = implode("/", $parts);

						$thumb_url = $url . "/" . $data['sizes']['thumbnail']['file'];
						$real_url = $url . "/" . $data['sizes']['medium']['file'];
	
						if($real_url == $chosen){
							$style = " style='border:2px solid #00F' ";
						}else{
							$style = "";
						}	
	
						?>
						<img class='overlayimage' <?PHP echo $style; ?> id="image_<?PHP echo $image->ID ?>" fullsrc="<?PHP echo $real_url; ?>" src="<?PHP echo $thumb_url; ?>" /> 
						<?PHP

						}
					}
					
					foreach($geo_array as $row){

						$geo = explode("=", $row);	
						
						if(trim($geo[0])!==""){
						
							echo "<script type='text/javascript' language='javascript'>";
							
							echo '
								jQuery(document).ready(
									function(){
							';
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
								
								echo 'var m = new L.Polyline(co_ords); console.log(co_ords);'; 
								echo 'drawnItems.addLayer(m);';
							    
							}
							
							$counter++;
									
							echo " });</script>";
							
						}
						
					}
					
				?>
				<input type="hidden" name="geodata" id="EntryLatlng" value="<?PHP echo $geodata; ?>" />
				<input type="hidden" name="geozoom" id="EntryMapZoom" value="<?PHP echo $geozoom; ?>" />
				<input type="hidden" name="geocenter" id="EntryMapCenter" value="<?PHP echo $geocenter; ?>" />
				<input type="hidden" name="geoimage" id="EntryMapImage" value="<?PHP echo $chosen; ?>" />
			<?PHP
		}
		
		function save_post($post_id){
			$post = get_post($post_id);
			if($post->post_type=="solomonmaplay"){
				if(count($_POST)!=0){
					if(isset($_POST['action'])){					
						update_post_meta($post_id, "geodata", $_POST['geodata']); 
						update_post_meta($post_id, "geozoom", $_POST['geozoom']); 
						update_post_meta($post_id, "geocenter", $_POST['geocenter']); 
						update_post_meta($post_id, "geoimage", $_POST['geoimage']); 
					}
				}
			}
		}
	
	}
	
	$solomon_map_soverlay_editor = new solomon_map_soverlay_editor();
	