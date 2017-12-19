<?PHP

	class solomon_map_streetview_overlay_map{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Streetview Walkthrough',
				'singular_name' => 'Streetview Walkthrough',
				'add_new' => 'Add new Streetview Walkthrough',
				'add_new_item' => 'Add Streetview Walkthrough',
				'edit_item' => 'Edit Streetview Walkthrough',
				'new_item' => 'New Streetview Walkthrough',
				'all_items' => 'All Streetview Walkthroughs',
				'view_item' => 'View Streetview Walkthroughs',
				'search_items' => 'Search Streetview Walkthrough',
				'not_found' =>  'No Streetview Walkthroughs found',
				'not_found_in_trash' => 'No Streetview Walkthroughs found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Streetview Walkthroughs'
			);
				
			$args = array(
				'labels' => $labels,
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => false,
				'supports' => array('title', 'editor'),
				'menu_position' => 99,
				'exclude_from_search' => true,
				'publically_queryable' => true,
			);
		
			register_post_type( 'solomonmapwalk' , $args );

		}	
	
	}
	
	$solomon_map_streetview_overlay_map = new solomon_map_streetview_overlay_map();
	