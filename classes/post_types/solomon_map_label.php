<?PHP

	class solomon_map_label{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Map Label',
				'singular_name' => 'Map Label',
				'add_new' => 'Add new Map Label',
				'add_new_item' => 'Add Map Label',
				'edit_item' => 'Edit Map Label',
				'new_item' => 'New Map Label',
				'all_items' => 'All Map Labels',
				'view_item' => 'View Map Labels',
				'search_items' => 'Search Map Label',
				'not_found' =>  'No Map Labels found',
				'not_found_in_trash' => 'No Map Labels found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Map Labels'
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
			);
		
			register_post_type( 'solomonmaplabel' , $args );

		}	
	
	}
	
	$solomon_map_label = new solomon_map_label();
	