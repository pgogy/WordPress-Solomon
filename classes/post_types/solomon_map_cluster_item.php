<?PHP

	class solomon_map_cluster_item{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$labels = array(
				'name' => 'Cluster Item',
				'singular_name' => 'Cluster Item',
				'add_new' => 'Add new Cluster Item',
				'add_new_item' => 'Add Cluster Item',
				'edit_item' => 'Edit Cluster Item',
				'new_item' => 'New Cluster Item',
				'all_items' => 'All Cluster Items',
				'view_item' => 'View Cluster Items',
				'search_items' => 'Search Cluster Item',
				'not_found' =>  'No Cluster Items found',
				'not_found_in_trash' => 'No Cluster Items found in trash', 
				'parent_item_colon' => '',
				'menu_name' => 'Cluster Items'
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
		
			register_post_type( 'solomonmapcitem' , $args );

		}	
	
	}
	
	$solomon_map_cluster_item = new solomon_map_cluster_item();
	