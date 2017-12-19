<?PHP

	class solomon_map_image{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Map Image',
				'singular_name' => 'Map Image',
				'add_new' => 'Add new Map Image',
				'add_new_item' => 'Add Map Image',
				'edit_item' => 'Edit Map Image',
				'new_item' => 'New Map Image',
				'all_items' => 'All Map Images',
				'view_item' => 'View Map Images',
				'search_items' => 'Search Map Image',
				'not_found' =>  'No Map Images found',
				'not_found_in_trash' => 'No Map Images found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Map Images'
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
		
			register_post_type( 'solomonmapimage' , $args );

		}	
	
	}
	
	$solomon_map_image = new solomon_map_image();
	