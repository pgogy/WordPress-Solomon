<?PHP

	class solomon_map_image_overlay_editor{
	
		function __construct(){

			$action = false;
			
			add_action("save_post", array($this, "save_post"));
			add_action("update_post", array($this, "save_post"));
			add_action("publish_post", array($this, "save_post"));
			
			if(isset($_GET['action'])){
			
				if(isset($_GET['post'])){
			
					$post = get_post($_GET['post']);
	
					if($_GET['action']=="edit" && $post->post_type=="solomonmapioverlay"){
						$action = true;
					}
					
				}

			}else{
			
				if(isset($_GET['post_type'])){
					if($_GET['post_type']=="solomonmapioverlay"){
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
			wp_register_style( 'leaflet_toolbar_css', plugins_url() . '/solomon/leaflet/leaflet.toolbar.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_toolbar_css' );
			wp_register_style( 'leaflet_distort_css', plugins_url() . '/solomon/leaflet/leaflet.distortableimage.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_distort_css' );
			wp_register_style( 'leaflet_distorteditor_css', plugins_url() . '/solomon/css/leaflet.distortededitor.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_distorteditor_css' );
			wp_register_script( 'leaflet_js', plugins_url() . '/solomon/leaflet/leaflet.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_js' );
			wp_register_script( 'leaflet_setup_js', plugins_url() . '/solomon/leaflet/leaflet.setup.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_setup_js' );
			wp_register_script( 'leaflet_toolbar_js', plugins_url() . '/solomon/leaflet/leaflet.toolbar.js', array("leaflet_js"), '1.0.0' );
			wp_enqueue_script( 'leaflet_toolbar_js' );
			wp_register_script( 'leaflet_distort_js', plugins_url() . '/solomon/leaflet/leaflet.distortableimage.js', array("leaflet_js"), '1.0.0' );
			wp_enqueue_script( 'leaflet_distort_js' );
			wp_register_script( 'leaflet_extra_map_js', plugins_url() . '/solomon/leaflet/leaflet.extra.map.js', array("leaflet_js"), '1.0.0' );
			wp_register_script( 'leaflet_overlay_editor_js', plugins_url() . '/solomon/leaflet/leaflet.image.overlay.editor.js', array("leaflet_js"), '1.0.0' );
			
			if(isset($post)){

				$mapData = array(
					'latLng' =>  get_post_meta($post->ID,"geocenter",true),
					'zoom' => get_post_meta($post->ID,"geozoom",true),
					'src' => get_post_meta($post->ID,"imageoverlay",true)
				);

			}else{
	
				$mapData = array();
			
			}

			wp_localize_script( 'leaflet_overlay_editor_js', 'mapData', $mapData );
			
			wp_enqueue_script( 'leaflet_overlay_editor_js' );
			
		}

		function move_deck() {

		    	global $post, $wp_meta_boxes;

		    	do_meta_boxes(get_current_screen(), 'advanced', $post);
		    	unset($wp_meta_boxes['solomonmapioverlay']['advanced']);
	
		}
	
		function metabox(){
	
			add_meta_box("solomonmapimage_overlaymeta",__("Edit Map"),array($this,"editor"), "solomonmapioverlay", "advanced", 'high' );

		}	
		
		function editor(){	
			global $post;
			
			$geozoom = explode(" ", get_post_meta($post->ID, "geozoom", true));
			$geocenter = explode(" ", get_post_meta($post->ID, "geocenter", true));
			$geocenter = explode(" ", get_post_meta($post->ID, "geocenter", true));
			$src = get_post_meta($post->ID, "imageoverlay", true);
						
			?> 
				<input type="hidden" name="geozoom" id="EntryMapZoom" value="<?PHP echo implode(" ",  $geozoom); ?>" />
				<input type="hidden" name="geocenter" id="EntryMapCenter" value="<?PHP echo implode(" ",  $geocenter); ?>" />
				<input type="hidden" name="imageoverlay" id="imageoverlay" value="<?PHP echo $src; ?>" />
				<p>Click on where you'd like the top left corner to be, then click the top right box. Move through each corner and then click the image you'd like.</p>
				<div id="map" style="height:600px; width:100%"></div>
			<?PHP
			
			$query_images_args = array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_status'    => 'inherit',
				'posts_per_page' => -1,
			);

			$query_images = new WP_Query( $query_images_args );
			
			?>
				<label for="overlaytl">Top Left</label>
				<input class='overlayholder' id="overlaytl" name="overlaytl" type="text" value="<?PHP echo get_post_meta($post->ID, "overlaytl", true); ?>" /><br />
				<label for="overlaytr">Top Right</label>
				<input class='overlayholder' id="overlaytr" name="overlaytr" type="text" value="<?PHP echo get_post_meta($post->ID, "overlaytr", true); ?>" /><br />
				<label for="overlaybr">Bottom Right</label>
				<input class='overlayholder' id="overlaybr" name="overlaybr" type="text" value="<?PHP echo get_post_meta($post->ID, "overlaybr", true); ?>" /><br />
				<label for="overlaybr">Bottom Left</label>
				<input class='overlayholder' id="overlaybl" name="overlaybl" type="text" value="<?PHP echo get_post_meta($post->ID, "overlaybl", true); ?>" />
			<?PHP
			
			?><div id='overlayimages'><?PHP
			
			foreach($query_images->posts as $image){
				echo "<img src='" . $image->guid . "' />";
			}
			
			?></div><?PHP
			
			wp_reset_postdata();
			
		}
		
		function save_post($post_id){
			$post = get_post($post_id);
			if($post->post_type=="solomonmapioverlay"){
				if(count($_POST)!=0){
					if(isset($_POST['action'])){
						update_post_meta($post_id, "imageoverlay", $_POST['imageoverlay']); 
						update_post_meta($post_id, "geozoom", $_POST['geozoom']); 
						update_post_meta($post_id, "geocenter", $_POST['geocenter']); 
						update_post_meta($post_id, "overlaytl", $_POST['overlaytl']); 
						update_post_meta($post_id, "overlaytr", $_POST['overlaytr']); 
						update_post_meta($post_id, "overlaybr", $_POST['overlaybr']); 
						update_post_meta($post_id, "overlaybl", $_POST['overlaybl']); 
					}
				}
			}
		}
	
	}
	
	$solomon_map_image_overlay_editor = new solomon_map_image_overlay_editor();
	