<?PHP

	class solomon_map_text_label_editor{
	
		function __construct(){

			$action = false;
			
			add_action("save_post", array($this, "save_post"));
			add_action("update_post", array($this, "save_post"));
			add_action("publish_post", array($this, "save_post"));
			
			if(isset($_GET['action'])){
			
				if(isset($_GET['post'])){
			
					$post = get_post($_GET['post']);
		
					if($_GET['action']=="edit" && $post->post_type=="solomonmaptlabel"){
						$action = true;
					}

				}

			}else{
			
				if(isset($_GET['post_type'])){
					if($_GET['post_type']=="solomonmaptlabel"){
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
			
			
			$default = plugin_basename(__FILE__);
			$parts = explode("/", $default); 
			$default = $parts[0] . "/leaflet/images/marker-icon.png";
						
			wp_register_script( 'leaflet_geosearch_openstreetmap_js', plugins_url() . '/solomon/leaflet/l.geosearch.provider.openstreetmap.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_geosearch_openstreetmap_js' );
			wp_register_script( 'leaflet_extra_js', plugins_url() . '/solomon/leaflet/leaflet.labelextra.js', false, '1.0.0' );
			
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

		function move_deck() {

		    global $post, $wp_meta_boxes;

	    	do_meta_boxes(get_current_screen(), 'advanced', $post);
	    	unset($wp_meta_boxes["solomonmaptlabel"]['advanced']);
	
		}
	
		function metabox(){
	
			add_meta_box("solomonmaplabelmeta",__("Edit Map"),array($this,"editor"),"solomonmaptlabel");

		}	
		
		function editor(){
	
			global $post;
			?> 
				<p><?PHP echo __("Use the marker to place the label on the map. The marker will not appear with this option type"); ?></p>
				<div id="map" style="height:600px; width:100%"></div>
				<?PHP
					
					$geodata = get_post_meta($post->ID, "geodata", true);
					$geozoom = explode(" ", get_post_meta($post->ID, "geozoom", true));
					$geocenter = explode(" ", get_post_meta($post->ID, "geocenter", true));
					
					$geo_array = explode( 'geo', str_replace(" ", "", str_replace("LatLng", "", str_replace(")", "", str_replace( "(", "",  $geodata ) ) ) ) );

					$counter = 0;	
			
					$default = plugin_basename(__FILE__);
					$parts = explode("/", $default); 
					$default = $parts[0] . "/leaflet/images/marker-icon.png";
					
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
								$newpoint = ( substr($geo[1], -1) === ',' ) ? rtrim($geo[1], ",") : $geo[1];

								echo 'var m = new L.marker(new L.LatLng('.$newpoint.'));';
								echo 'drawnItems.addLayer(m);';

							}

							$counter++;
									
							echo " });</script>";
							
						}
						
					}
					
				?>
				<p>Put the label for this marker / shape in the normal text editor box</p>
				<input type="hidden" name="geodata" id="EntryLatlng" value="<?PHP echo implode(" ", $geodata); ?>" />
				<input type="hidden" name="geozoom" id="EntryMapZoom" value="<?PHP echo implode(" ",  $geozoom); ?>" />
				<input type="hidden" name="geocenter" id="EntryMapCenter" value="<?PHP echo implode(" ",  $geocenter); ?>" />
			<?PHP
		}
		
		function save_post($post_id){
			$post = get_post($post_id);
			if($post->post_type=="solomonmaptlabel"){
				if(count($_POST)!=0){
					if(isset($_POST['action'])){
						update_post_meta($post_id, "geodata", $_POST['geodata']); 
						update_post_meta($post_id, "geozoom", $_POST['geozoom']); 
						update_post_meta($post_id, "geocenter", $_POST['geocenter']); 
					}
				}
			}
		}
	
	}
	
	$solomon_map_text_label_editor = new solomon_map_text_label_editor();
	