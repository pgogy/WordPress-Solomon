<?PHP

	class solomon_map_image_editor{
	
		function __construct(){

			$action = false;
			
			add_action("save_post", array($this, "save_post"));
			add_action("update_post", array($this, "save_post"));
			add_action("publish_post", array($this, "save_post"));
			
			if(isset($_GET['action'])){
			
				if(isset($_GET['post'])){
			
					$post = get_post($_GET['post']);
	
					if($_GET['action']=="edit" && $post->post_type=="solomonmapimage"){
						$action = true;
					}

				}

			}else{
			
				if(isset($_GET['post_type'])){
					if($_GET['post_type']=="solomonmapimage"){
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

		function move_deck() {

		    	global $post, $wp_meta_boxes;

		    	do_meta_boxes(get_current_screen(), 'advanced', $post);
		    	unset($wp_meta_boxes["solomonmapimage"]['advanced']);
	
		}
		
		function scripts(){
			
			global $post;
			
			wp_register_style( 'leaflet_css', plugins_url() . '/solomon/leaflet/leaflet.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_css' );
			wp_register_style( 'leaflet_distorteditor_css', plugins_url() . '/solomon/css/leaflet.distortededitor.css', false, '1.0.0' );
			wp_enqueue_style( 'leaflet_distorteditor_css' );
			wp_register_script( 'leaflet_js', plugins_url() . '/solomon/leaflet/leaflet.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_js' );
			wp_register_script( 'leaflet_draw_js', plugins_url() . '/solomon/leaflet/leaflet.draw.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_draw_js' );
			wp_register_script( 'leaflet_setup_js', plugins_url() . '/solomon/leaflet/leaflet.setup.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_setup_js' );
			wp_register_script( 'leaflet_image_transform_js', plugins_url() . '/solomon/leaflet/leaflet.imagetransform.js', false, '1.0.0' );
			wp_enqueue_script( 'leaflet_image_transform_js' );
			wp_register_script( 'leaflet_extra_map_js', plugins_url() . '/solomon/leaflet/leaflet.extra.map.js', array("leaflet_js"), '1.0.0' );
			wp_register_script( 'leaflet_image_editor_js', plugins_url() . '/solomon/leaflet/leaflet.image.editor.js', array("leaflet_js"), '1.0.0' );
			
			if(isset($post)){
			
				$mapData = array(
						'latLng' =>  get_post_meta($post->ID,"geocenter",true),
						'zoom' => get_post_meta($post->ID,"geozoom",true),
						'src' => get_post_meta($post->ID,"imageoverlay",true)
					);
				wp_localize_script( 'leaflet_image_editor_js', 'mapData', $mapData );
				
			}else{
			
				$mapData = array(
						'latLng' =>  "",
						'zoom' => "",
						'src' => ""
					);
				wp_localize_script( 'leaflet_image_editor_js', 'mapData', $mapData );
			
			}
			
			wp_enqueue_script( 'leaflet_image_editor_js' );
			
		}
	
		function metabox(){
	
			add_meta_box("solomonmapimage_overlaymeta",__("Edit Map"),array($this,"editor"), "solomonmapimage", 'advanced', 'high');

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
				<p><?PHP echo __("To create an image, click once for the top left picture. Then click on the 'bottom right' text box. Then a second time for the bottom right. Then choose a picture"); ?></p>
				<div id="map" style="height:600px; width:100%"></div>
			<?PHP
			
			$query_images_args = array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_status'    => 'inherit',
				'posts_per_page' => 5,
			);

			$query_images = new WP_Query( $query_images_args );
			
			?>
				<label for="overlaytl">Top Left</label>
				<input class='overlayholder' id="overlaytl" name="overlaytl" type="text" value="<?PHP echo get_post_meta($post->ID, "overlaytl", true); ?>" /><br />
				<input class='overlayholder' id="overlaymarkers" name="overlaymarkers" type="hidden" value="<?PHP echo get_post_meta($post->ID, "overlaymarkers", true); ?>" /><br />
				<label for="overlaybr">Bottom Right</label>
				<input class='overlayholder' id="overlaybr" name="overlaybr" type="text" value="<?PHP echo get_post_meta($post->ID, "overlaybr", true); ?>" /><br />
				<input class='overlayholder' id="overlayanchors" name="overlayanchors" type="hidden" value="<?PHP echo get_post_meta($post->ID, "overlayanchors", true); ?>" />
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
			if($post->post_type=="solomonmapimage"){
				if(count($_POST)!=0){
					if(isset($_POST['action'])){
						update_post_meta($post_id, "imageoverlay", $_POST['imageoverlay']); 
						update_post_meta($post_id, "geozoom", $_POST['geozoom']); 
						update_post_meta($post_id, "geocenter", $_POST['geocenter']); 
						update_post_meta($post_id, "overlaytl", $_POST['overlaytl']); 
						update_post_meta($post_id, "overlaymarkers", str_replace(" ",",",$_POST['overlaymarkers'])); 
						update_post_meta($post_id, "overlaybr", $_POST['overlaybr']); 
						update_post_meta($post_id, "overlayanchors", str_replace(" ",",",$_POST['overlayanchors'])); 
					}
				}
			}
		}
	
	}
	
	$solomon_map_image_editor = new solomon_map_image_editor();
	