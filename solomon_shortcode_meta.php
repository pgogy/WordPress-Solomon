<?PHP

class solomon_shortcode_meta{

	function __construct(){
		add_action( 'add_meta_boxes', array($this,'options'), 1);
	}
	
	function options() {

		if(isset($_GET['post'])){

			$screens = array(
				'solomonmaptlabel',
				'solomonmcluster',
				'solomonmap',
				'solomonmapcitem',
				'solomonmapimage',
				'solomonmaplabel',
				'solomonmapioverlay',
				'solomonmapitem',
				'solomonmaplay',
				'solomonmapwalk',
				'solomonmaptlabel'
			);
			
			foreach ( $screens as $screen ) {

				add_meta_box(
					'testdiv',
					__( 'Solomon Shortcode', 'solomon' ),
					array($this,'shortcode'),
					$screen,
					'side'
				);
			}
			
		}
		
    }
	
	function shortcode(){
		$post = get_post($_GET['post']);
		?><textarea style="width:100%">[solomon type="<?PHP echo $post->post_type; ?>" id="<?PHP echo $post->ID; ?>"]</textarea><?PHP
	}

}

$solomon_shortcode_meta = new solomon_shortcode_meta;
