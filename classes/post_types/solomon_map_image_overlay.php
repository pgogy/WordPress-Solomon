<?PHP

	class solomon_map_image_overlay{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Map Image Overlay',
				'singular_name' => 'Map Image Overlay',
				'add_new' => 'Add new Map Image Overlay',
				'add_new_item' => 'Add Map Image Overlay',
				'edit_item' => 'Edit Map Image Overlay',
				'new_item' => 'New Map Image Overlay',
				'all_items' => 'All Map Image Overlays',
				'view_item' => 'View Map Image Overlays',
				'search_items' => 'Search Map Image Overlay',
				'not_found' =>  'No Map Image Overlays found',
				'not_found_in_trash' => 'No Map Image Overlays found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Map Image Overlays'
			);
				
			$args = array(
				'labels' => $labels,
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => false,
				'supports' => array('title'),
				'menu_position' => 99,
				'exclude_from_search' => true,
				'publically_queryable' => true,
			);
		
			register_post_type( 'solomonmapioverlay' , $args );

		}	
	
	}
	
	$solomon_map_image_overlay = new solomon_map_image_overlay();
	