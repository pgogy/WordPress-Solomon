<?PHP

	class solomon_map_item{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Solomon Map Item',
				'singular_name' => 'Solomon Map Item',
				'add_new' => 'Add new Solomon Map Item',
				'add_new_item' => 'Add Solomon Map Item',
				'edit_item' => 'Edit Solomon Map Item',
				'new_item' => 'New Solomon Map Item',
				'all_items' => 'All Solomon Map Items',
				'view_item' => 'View Solomon Map Items',
				'search_items' => 'Search Solomon Map Item',
				'not_found' =>  'No Solomon Map Items found',
				'not_found_in_trash' => 'No Solomon Map Items found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Solomon Map Items'
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
		
			register_post_type( 'solomonmapitem' , $args );

		}	
	
	}
	
	$solomon_map_item = new solomon_map_item();
	