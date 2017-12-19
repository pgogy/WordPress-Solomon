<?PHP

	class solomon_streetview_map_display{
	
		function __construct(){
			
			add_filter('the_content', array($this, 'show_map'));
			add_action("wp_enqueue_scripts", array($this, "scripts"));	
			
		}
		
		function scripts(){
		
			global $post;
		
			if(isset($post->post_type)){
			
				if($post->post_type=="solomonmapwalk"){
					
					wp_register_script( 'google_streetview_js', 'http://maps.google.com/maps/api/js?libraries=geometry&key=AIzaSyCpTV9X4M2yfHJk94au7wqN8bSnM1RcT2Y', array("jquery"), '1.0.0' );
					wp_enqueue_script( 'google_streetview_js' );
					wp_register_script( 'leaflet_streetview_js', plugins_url() . '/solomon/leaflet/map.js', array("jquery","google_streetview_js"), '1.0.0' );
					wp_enqueue_script( 'leaflet_streetview_js' );
				
				}
				
			}
			
		}
		
		function show_map($the_content){
		
			global $post;
		
			if($post->post_type=="solomonmapwalk"){
		
				?><div id="mapHolder" style="height:600px; width:800px; position:relative; top:0px; left: 0px; display:inline-block;">
					<div id="panDiv" style="height:600px; width:800px; position:absolute; top:0px; left: 0px; display:inline-block;"></div>
				</div>
				<script type="text/javascript">
				
					function loadPage(){	
					
						svo = new SVO(800,600);
						svo.settings();
						
						<?PHP
							$center = explode(" ",get_post_meta($post->ID,"geocenter",true));
						?>
						
						svo.setLocation(<?PHP echo $center[0] . "," . $center[1]; ?>,16);
						svo.setViewpoint(69,0,1);
						svo.m_initPanorama();
						
						<?PHP
							$entries = get_post_meta($post->ID, "mapItems", true);
							$parts = explode(" ", $entries);
						
							foreach($parts as $part){ 
							
								$data = explode("_",$part);
								$mapitem = get_post(array_pop($data));
								
								$geodata = get_post_meta($mapitem->ID, "geodata", true);
								$geoimage = get_post_meta($mapitem->ID, "geoimage", true);
								
								$parts = explode("LatLng(", $geodata);
								$next_parts = explode(" ", $parts[1]);
								$path = explode("/", $geoimage);
								
								echo ' svo.addImage(' . substr($next_parts[0],0,strlen($next_parts[0])-1) . ', ' . substr($next_parts[1],0,strlen($next_parts[1])-2) . ', 150, 150, "' . $geoimage . '", "imgMarker", "pic_' . str_replace(".","",array_pop($path)) . '");
						';
								
							}
							
						?>
						
					}
			
				</script>
				<script type="text/javascript">
					google.maps.event.addDomListener(window, 'load', loadPage);
				</script><?PHP
				
				return do_shortcode($post->post_content);
				
			}else{
				return $the_content;
			}

		}

	}

	$solomon_streetview_map_display = new solomon_streetview_map_display();
	