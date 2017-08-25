<?PHP

	class solomon_map_display{
	
		function __construct(){
			add_filter('the_content', array($this, 'show_map'));
			add_action("wp_enqueue_scripts", array($this, "scripts"));
		}
		
		function scripts(){
			global $post;
			if($post->post_type=="solomonmap"){
				wp_register_style( 'leaflet_css', plugins_url() . '/solomon/leaflet/leaflet.css', false, '1.0.0' );
				wp_enqueue_style( 'leaflet_css' );
				wp_register_script( 'leaflet_js', plugins_url() . '/solomon/leaflet/leaflet.js', false, '1.0.0' );
				wp_enqueue_script( 'leaflet_js' );
				wp_register_script( 'leaflet_extra_map_js', plugins_url() . '/solomon/leaflet/leaflet.extra.map.js', array("jquery","leaflet_js"), '1.0.0' );
				
				$mapData = array(
					'latLng' =>  get_post_meta($post->ID,"geocenter",true),
					'zoom' => get_post_meta($post->ID,"geozoom",true)
				);
				wp_localize_script( 'leaflet_extra_map_js', 'mapData', $mapData );

				wp_enqueue_script( 'leaflet_extra_map_js' );
				wp_register_script( 'leaflet_add_to_map_js', plugins_url() . '/solomon/leaflet/leaflet.add_to_map.js', array("leaflet_js"), '1.0.0' );
				wp_localize_script( 'leaflet_add_to_map_js', 'leaflet_add_to_map', array( 'ajax_url' => admin_url('admin-ajax.php')) );
				wp_enqueue_script( 'leaflet_add_to_map_js' );
				wp_register_script( 'leaflet_post_display_js', plugins_url() . '/solomon/leaflet/leaflet.post.display.js', array("leaflet_js"), '1.0.0' );
				wp_enqueue_script( 'leaflet_post_display_js' );
			}
		}
		
		function show_map($the_content){
		
			global $post;
		
			if($post->post_type=="solomonmap"){
				
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
				
				?><style>
					#mapInfo{
						<?PHP
							if(get_post_meta($post->ID,"popup_css",true)==""){
								?>
									background:#fff;
									padding:5px;
									margin-left:5px;
								<?PHP
							}else{
								echo get_post_meta($post->ID,"popup_css",true);
							}
						?>
					}
				</style><?PHP
				
				?><div id="map" style="height:600px; width:100%"></div><?PHP
				
				?><script type="text/javascript">
				
					jQuery(document)
						.ready(
							function(){
								<?PHP
									$entries = get_post_meta($post->ID, "mapItems", true);
									$parts = explode(" ", $entries);
								
									foreach($parts as $part){ ?>
										display_item("<?PHP echo $part; ?>"); 
								<?PHP 
									} 
								?> 
							}
						);
				
				</script><?PHP
				
				return do_shortcode($post->post_content);
				
			}else{
				return $the_content;
			}
		}
	
	}
	
	$solomon_map_display = new solomon_map_display();
	