<?PHP

	class solomon_map_text_label{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Map Text Label',
				'singular_name' => 'Map Text Label',
				'add_new' => 'Add new Map Text Label',
				'add_new_item' => 'Add Map Text Label',
				'edit_item' => 'Edit Map Text Label',
				'new_item' => 'New Map Text Label',
				'all_items' => 'All Map Text Labels',
				'view_item' => 'View Map Text Labels',
				'search_items' => 'Search Map Text Label',
				'not_found' =>  'No Map Text Labels found',
				'not_found_in_trash' => 'No Map Text Labels found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Map Text Labels'
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
		
			register_post_type( 'solomonmaptlabel' , $args );

		}	
	
	}
	
	$solomon_map_text_label = new solomon_map_text_label();
	