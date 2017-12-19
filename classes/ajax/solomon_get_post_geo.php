<?PHP
	
	class solomon_get_post_geo{
	
		function __construct(){;
			add_action("wp_ajax_no_priv_get_post_geo", array($this, "get_post_geo"));
			add_action("wp_ajax_get_post_geo", array($this, "get_post_geo"));
			add_action("wp_ajax_no_priv_get_post_geo_imagedistortedoverlay", array($this, "get_post_geo_doverlay"));
			add_action("wp_ajax_get_post_geo_imagedistortedoverlay", array($this, "get_post_geo_doverlay"));
			add_action("wp_ajax_no_priv_get_post_geo_image", array($this, "get_post_geo_image"));
			add_action("wp_ajax_get_post_geo_image", array($this, "get_post_geo_image"));
		}
		
		function get_post_geo_image(){
			$id = explode("_", $_POST['post_id']);
			$post_id = array_pop($id);
			$returnData = new StdClass();
			$returnData->id = $post_id;
			$returnData->src = get_post_meta($post_id, "imageoverlay", true);
			$returnData->anchors = get_post_meta($post_id, "overlayanchors", true);
			$returnData->markers = get_post_meta($post_id, "overlaymarkers", true);
			echo json_encode($returnData, JSON_FORCE_OBJECT);
			die();
		}
		
		function get_post_geo_doverlay(){
			$id = explode("_", $_POST['post_id']);
			$post_id = array_pop($id);
			$returnData = new StdClass();
			$returnData->id = $post_id;
			$returnData->src = get_post_meta($post_id, "imageoverlay", true);
			$returnData->tl = get_post_meta($post_id, "overlaytl", true);
			$returnData->tr = get_post_meta($post_id, "overlaytr", true);
			$returnData->br = get_post_meta($post_id, "overlaybr", true);
			$returnData->bl = get_post_meta($post_id, "overlaybl", true);
			echo json_encode($returnData, JSON_FORCE_OBJECT);
			die();
		}
		
		function get_post_geo(){
			$id = explode("_", $_POST['post_id']);
			$post_id = array_pop($id);
			$geodata = get_post_meta($post_id, "geodata", true);
			$geocolors = explode(" ", get_post_meta($post_id, "geofillcolors", true));
			$geostrokecolors = explode(" ", get_post_meta($post_id, "geostrokecolors", true));
			$geostrokeopacity = explode(" ", get_post_meta($post_id, "geostrokeopacity", true));
			$geofillopacity = explode(" ", get_post_meta($post_id, "geofillopacity", true));
			$geomarkers = explode(" ", get_post_meta($post_id, "geomarkers", true));
			$geomarkersizes = explode(" ", str_replace(", ",",", get_post_meta($post_id, "geomarkersizes", true)));
			$geostroke = explode(" ", get_post_meta($post_id, "geostroke", true));
			$geo_array = explode( 'geo', str_replace(" ", "", str_replace("LatLng", "", str_replace(")", "", str_replace( "(", "",  $geodata ) ) ) ) );
			$returnData = new StdClass();
			$returnData->id = $post_id;
			$returnData->colors = $geocolors;
			$returnData->opacity = $geofillopacity;
			$returnData->strokecolors = $geostrokecolors;
			$returnData->strokeopacity = $geostrokeopacity;
			$returnData->markers = $geomarkers;
			$returnData->markersizes = $geomarkersizes;
			$returnData->strokes = $geostroke;
			$returnData->geo = $geo_array;
			if(strpos($_POST['classes'],"mapitemlabel")!==FALSE){
				$post = get_post($post_id);
				$returnData->label = do_shortcode($post->post_content);
				$returnData->classes = $_POST['classes'];
			}
			echo json_encode($returnData, JSON_FORCE_OBJECT);
			die();
		}	
	
	}
	
	$solomon_get_post_geo = new solomon_get_post_geo();
	