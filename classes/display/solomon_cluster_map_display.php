<?PHP

	class solomon_cluster_map_display{
	
		function __construct(){
			add_filter('the_content', array($this, 'show_map'));
			add_action("wp_enqueue_scripts", array($this, "scripts"));
		}
		
		function scripts(){
			global $post;
			
			
			if(isset($post->post_type)){
			
				if($post->post_type=="solomonmcluster"){
					wp_register_style( 'leaflet_css', plugins_url() . '/solomon/leaflet/leaflet.css', false, '1.0.0' );
					wp_enqueue_style( 'leaflet_css' );
					wp_register_script( 'leaflet_103_js', plugins_url() . '/solomon/leaflet/leaflet-src.1.0.3.js', false, '1.0.0' );
					wp_enqueue_script( 'leaflet_103_js' );
					wp_register_script( 'leaflet_extra_map_js', plugins_url() . '/solomon/leaflet/leaflet.extra.cluster.map.display.js', array("jquery","leaflet_103_js"), '1.0.0' );
					
					$mapData = array(
						'latLng' =>  get_post_meta($post->ID,"geocenter",true),
						'zoom' => get_post_meta($post->ID,"geozoom",true)
					);
					wp_localize_script( 'leaflet_extra_map_js', 'mapData', $mapData );
					wp_enqueue_script( 'leaflet_extra_map_js' );
					
					wp_register_script( 'leaflet_add_to_map_js', plugins_url() . '/solomon/leaflet/leaflet.add_to_map.js', array("leaflet_103_js"), '1.0.0' );
					wp_localize_script( 'leaflet_add_to_map_js', 'leaflet_add_to_map', array( 'ajax_url' => admin_url('admin-ajax.php')) );
					wp_enqueue_script( 'leaflet_add_to_map_js' );
					
					wp_register_script( 'leaflet_cluster_display_js', plugins_url() . '/solomon/leaflet/leaflet.cluster.display.js', array("leaflet_103_js"), '1.0.0' );
					wp_enqueue_script( 'leaflet_cluster_display_js' );
					wp_register_script( 'leaflet_marker_cluster', plugins_url() . '/solomon/leaflet/MarkerCluster.js', array("leaflet_103_js"), '1.0.0' );
					wp_enqueue_script( 'leaflet_marker_cluster' );
					wp_register_script( 'leaflet_marker_cluster_g', plugins_url() . '/solomon/leaflet/MarkerClusterGroup.js', array("leaflet_103_js"), '1.0.0' );
					wp_enqueue_script( 'leaflet_marker_cluster_g' );
					wp_register_script( 'leaflet_distance', plugins_url() . '/solomon/leaflet/DistanceGrid.js', array("leaflet_103_js"), '1.0.0' );
					wp_enqueue_script( 'leaflet_distance' );
					wp_register_script( 'leaflet_marker_cluster_o', plugins_url() . '/solomon/leaflet/MarkerOpacity.js', array("leaflet_103_js"), '1.0.0' );
					wp_enqueue_script( 'leaflet_marker_cluster_o' );
					wp_register_script( 'leaflet_marker_cluster_qh', plugins_url() . '/solomon/leaflet/MarkerCluster.QuickHull.js', array("leaflet_103_js"), '1.0.0' );
					wp_enqueue_script( 'leaflet_marker_cluster_qh' );
					wp_register_script( 'leaflet_marker_cluster_sp', plugins_url() . '/solomon/leaflet/MarkerCluster.Spiderfier.js', array("leaflet_103_js"), '1.0.0' );
					wp_enqueue_script( 'leaflet_marker_cluster_sp' );
					wp_register_script( 'leaflet_marker_cluster_gr', plugins_url() . '/solomon/leaflet/MarkerClusterGroup.Refresh.js', array("leaflet_103_js"), '1.0.0' );
					wp_enqueue_script( 'leaflet_marker_cluster_gr' );
					
				}
			}
		}
		
		function show_map($the_content){
		
			global $post;
		
			if($post->post_type=="solomonmcluster"){
		
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
				
				?><div id="customicons"><?PHP
				
				foreach ( $query_images->posts as $image ) {
				
					$data = wp_get_attachment_metadata($image->ID);
					
					$parts = explode("/", $image->guid);
					
					array_pop($parts);
					
					$date = implode("/", $parts);
					
					?>
					<div id="custom_icon_<?PHP echo $image->ID ?>" iconurl="<?PHP echo $date . "/" . $data['sizes']['medium']['file']; ?>" size="<?PHP echo $data['sizes']['medium']['width']; ?>, <?PHP echo $data['sizes']['medium']['height']; ?>" ></div> 
					<?PHP
				}
				
				?><div id="custom_icon_0" iconurl="<?PHP echo site_url() . "/wp-content/plugins/" . $default; ?>" size="<?PHP echo "25, 41"; ?>" ></div></div><?PHP
				
				?><style>
					.mycluster{
						background:#000000;
						text-align:center;
						width:200px;
						color:#ffffff;
					}	
					
					.picturePrev,
					.pictureDisplay,
					.pictureNext{
						display:inline-block;
						vertical-align:middle;
						z-index:9999999999999;
						margin:1px;
					}
					
					.picturePrev:hover,
					.pictureNext:hover{
						background: #ff0000;
					}
					
					.pictureDisplay img{
						max-height:100px;
					}
				</style><?PHP
				
				?><div id="map" style="height:600px; width:100%"></div><?PHP
						
				?><script type="text/javascript">
				
					var markers = L.markerClusterGroup({
									maxClusterRadius: 120,
									iconCreateFunction: function (cluster) {
										
										var markers = cluster.getAllChildMarkers();
										var n = 0;
										src = Array();
										for (var i = 0; i < markers.length; i++) {
											n += markers[i].number;
											src.push(markers[i].options.icon.options.iconUrl);
										}
										
										pictures = "";
										
										for(x in src){
											pictures += src[x] + " ";
										}
										
										html = "<div class='pictureHolder' count='0' pictures='" + pictures + "'><div class='picturePrev'>Prev</div><div class='pictureDisplay'><img class='pictureThumb' width=100 height=100 src='" + markers[0].options.icon.options.iconUrl + "' /></div><div class='pictureNext'>Next</div></div>";
										
										holder = L.divIcon({ html: html, className: 'mycluster', iconSize: L.point(160, 105) });
										
										return holder;
									},
									//Disable all of the defaults:
									spiderfyOnMaxZoom: false, showCoverageOnHover: false, zoomToBoundsOnClick: false
								});
				
					jQuery(document)
						.ready(
							function(){	
							
								jQuery("body")
									.on("click", function(ev){
											console.log("body click");
											target = jQuery(ev.target).attr("class");
											if(target=="picturePrev"){
												pictures = jQuery(ev.target).parent().attr("pictures").split(" ");
												pictures.pop();
												pos = jQuery(ev.target).parent().attr("count");
												total = pictures.length;
												if(pos==0){
													pos = total-1;
												}else{
													pos = pos-1;
												}
												jQuery(ev.target).parent().attr("count", pos);
												jQuery(ev.target).parent().children().first().next().children().first().attr("src", pictures[pos]);
											}
											
											if(target=="pictureNext"){
												pictures = jQuery(ev.target).parent().attr("pictures").split(" ");
												pictures.pop();
												pos = jQuery(ev.target).parent().attr("count");
												total = pictures.length;
												if(pos==total-1){
													pos = 0;
												}else{
													pos = parseInt(pos)+1;
												}
												jQuery(ev.target).parent().attr("count", pos);
												jQuery(ev.target).parent().children().first().next().children().first().attr("src", pictures[pos]);
											}
										}
									);
							
								<?PHP
									$entries = get_post_meta($post->ID, "mapItems", true);
									$parts = explode(" ", $entries);
								
									foreach($parts as $part){ 
									
										$data = explode("_",$part);
										$mapitem = get_post(array_pop($data));
										
										if($mapitem->post_type=="solomonmapcitem"){ ?>
											display_item("<?PHP echo $part; ?>", markers);
										<?PHP 
										} 
										
									}
									
								?>
								
								map.addLayer(markers);
							
							}
						
					);
				
				</script><?PHP
				
				return do_shortcode($post->post_content);
				
			}else{
				return $the_content;
			}

		}

	}

	$solomon_cluster_map_display = new solomon_cluster_map_display();
	