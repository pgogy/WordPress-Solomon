<?PHP

class solomon_post_meta{

	function __construct(){
		add_action( 'add_meta_boxes', array($this,'options'));
		add_action( 'save_post', array($this,'update'));
	}
	
	function options() {

		$screens = array(
			'post'
		);
			
		foreach ( $screens as $screen ) {

			add_meta_box(
				'testdiv',
				__( 'Solomon Map for this post', 'solomon' ),
				array($this,'map'),
				$screen
			);
		}
		
    }
	
	function map(){
		
		global $wpdb, $post;
		
		$querystr = "
			SELECT $wpdb->posts.* 
			FROM $wpdb->posts
			WHERE $wpdb->posts.post_type like '%solomon%' 
			AND $wpdb->posts.post_status = 'publish' 
			ORDER BY $wpdb->posts.post_title ASC";

		$pageposts = $wpdb->get_results($querystr, OBJECT);
		
		$set = get_post_meta($post->ID, "post_solomonresource", true);
		
		echo "<select name='post_solomonresource'>";
		
		foreach($pageposts as $post){
			echo "<option value='" . $post->ID . "' ";
			if($set==$post->ID){
				echo " selected ";
			}
			echo ">" . $post->post_type . " : " . $post->post_title . "</option>";
		}
		
		echo "</select>";
		
	}
	
	function update($id){
		global $post;
		if(isset($_POST['post_solomonresource'])){
			update_post_meta($post->ID, "post_solomonresource",  $_POST["post_solomonresource"]);
		}
	}

}

$solomon_post_meta = new solomon_post_meta;
