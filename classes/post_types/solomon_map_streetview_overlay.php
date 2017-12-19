<?PHP

	class solomon_map_streetview_overlay{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Streetview Overlay',
				'singular_name' => 'Streetview Overlay',
				'add_new' => 'Add new Streetview Overlay',
				'add_new_item' => 'Add Streetview Overlay',
				'edit_item' => 'Edit Streetview Overlay',
				'new_item' => 'New Streetview Overlay',
				'all_items' => 'All Streetview Overlays',
				'view_item' => 'View Streetview Overlays',
				'search_items' => 'Search Streetview Overlay',
				'not_found' =>  'No Streetview Overlays found',
				'not_found_in_trash' => 'No Streetview Overlays found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Streetview Overlays'
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
		
			register_post_type( 'solomonmaplay' , $args );

		}	
	
	}
	
	$solomon_map_streetview_overlay = new solomon_map_streetview_overlay();
	