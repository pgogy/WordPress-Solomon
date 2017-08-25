<?PHP
	
	class solomon_get_post_content{
	
		function __construct(){;
			add_action("wp_ajax_no_priv_get_post_content", array($this, "get_post_content"));
			add_action("wp_ajax_get_post_content", array($this, "get_post_content"));
		}
		
		function get_post_content(){
			$post = get_post($_POST['post_id']);
			echo do_shortcode($post->post_content);
			die();
		}	
	
	}
	
	$solomon_get_post_content = new solomon_get_post_content();
	