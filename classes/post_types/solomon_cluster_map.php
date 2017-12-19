<?PHP

	class solomon_cluster_map{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Cluster Map',
				'singular_name' => 'Cluster Map',
				'add_new' => 'Add new Cluster Map',
				'add_new_item' => 'Add Cluster Map',
				'edit_item' => 'Edit Cluster Map',
				'new_item' => 'New Cluster Map',
				'all_items' => 'All Cluster Maps',
				'view_item' => 'View Cluster Maps',
				'search_items' => 'Search Cluster Map',
				'not_found' =>  'No Cluster Maps found',
				'not_found_in_trash' => 'No Cluster Maps found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Cluster Map'
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
		
			register_post_type( 'solomonmcluster' , $args );

		}	
	
	}
	
	$solomon_cluster_map = new solomon_cluster_map();
	