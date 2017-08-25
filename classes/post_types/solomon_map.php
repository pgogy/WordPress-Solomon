<?PHP

	class solomon_map{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Solomon Map',
				'singular_name' => 'Solomon Map',
				'add_new' => 'Add new Solomon Map',
				'add_new_item' => 'Add Solomon Map',
				'edit_item' => 'Edit Solomon Map',
				'new_item' => 'New Solomon Map',
				'all_items' => 'All Solomon Maps',
				'view_item' => 'View Solomon Maps',
				'search_items' => 'Search Solomon Map',
				'not_found' =>  'No Solomon Mapzes found',
				'not_found_in_trash' => 'No Solomon Mapzes found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Solomon Map'
			);
				
			$args = array(
				'labels' => $labels,
				'public' => true,
				'show_ui' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => false,
				'supports' => array('title','editor'),
				'menu_position' => 99,
				'exclude_from_search' => true,
				'publically_queryable' => true,
				'taxonomies' => array('category','tag'),
			);
		
			register_post_type( 'solomonmap' , $args );

		}	
	
	}
	
	$solomon_map = new solomon_map();
	