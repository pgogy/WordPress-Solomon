<?PHP

	class solomon_image_sizes{
	
		function __construct(){
			add_action("admin_init", array($this, "image_size"));
		}
		
		function image_size(){
		
			add_image_size( 'geomarker', 100, 100, array("center","center") );
			add_image_size( 'archive_image', 500, 500, false );
			add_image_size( 'archive_post_thumbnail_image', 1500, 1500, false );
	
		}
	
	}
	
	$solomon_image_sizes = new solomon_image_sizes();
	