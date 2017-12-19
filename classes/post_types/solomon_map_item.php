<?PHP

	class solomon_map_item{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Map Item',
				'singular_name' => 'Map Item',
				'add_new' => 'Add new Map Item',
				'add_new_item' => 'Add Map Item',
				'edit_item' => 'Edit Map Item',
				'new_item' => 'New Map Item',
				'all_items' => 'All Map Items',
				'view_item' => 'View Map Items',
				'search_items' => 'Search Map Item',
				'not_found' =>  'No Map Items found',
				'not_found_in_trash' => 'No Map Items found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Map Items'
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
		
			register_post_type( 'solomonmapitem' , $args );

		}	
	
	}
	
	$solomon_map_item = new solomon_map_item();
	