<?PHP

	/*
		Plugin Name: Solomon
		Description: Interactive Maps for WordPress
		Author: pgogy
		Version: 0.1
	*/
	
	class solomon{
	
		function __construct(){
			//add_action("admin_menu", array($this, "menu_create"));
		}
	
		
		function menu_create(){
			//add_menu_page( __("Solomon Maps"), __("Solomon Maps"), "edit_linkedcanvascourse", "molie_mgmt", array($this,"mgmt"));
		}
		
		function mgmt(){
			?>
				<h1>M.O.L.I.E</h1>
				<p>
					<?PHP echo __("Start by click on getting your token in the menu"); ?>
				</p>
			<?PHP
		}	
		
	}
	
	$solomon = new solomon();
	
	require_once("classes/post_types/solomon_map_image_overlay.php");
	require_once("classes/post_types/solomon_map_streetview_overlay.php");
	require_once("classes/post_types/solomon_map_streetview_overlay_map.php");
	require_once("classes/post_types/solomon_map_image.php");
	require_once("classes/post_types/solomon_map_item.php");
	require_once("classes/post_types/solomon_map_label.php");
	require_once("classes/post_types/solomon_map_text_label.php");
	require_once("classes/post_types/solomon_cluster_map.php");
	require_once("classes/post_types/solomon_map_cluster_item.php");
	require_once("classes/post_types/solomon_map.php");
	
	require_once("classes/post_types_editor/solomon_map_soverlay_editor.php");
	require_once("classes/post_types_editor/solomon_map_editor.php");
	require_once("classes/post_types_editor/solomon_streetview_map_editor.php");
	require_once("classes/post_types_editor/solomon_map_item_editor.php");
	require_once("classes/post_types_editor/solomon_cluster_map_editor.php");
	require_once("classes/post_types_editor/solomon_map_cluster_item_editor.php");
	require_once("classes/post_types_editor/solomon_map_image_editor.php");
	require_once("classes/post_types_editor/solomon_map_label_editor.php");
	require_once("classes/post_types_editor/solomon_map_text_label_editor.php");
	require_once("classes/post_types_editor/solomon_map_image_overlay_editor.php");
	
	require_once("classes/ajax/solomon_get_post_geo.php");
	require_once("classes/ajax/solomon_get_post_content.php");
	
	require_once("classes/display/solomon_menu.php");
	require_once("classes/display/solomon_image_sizes.php");
	require_once("classes/display/solomon_map_display.php");
	require_once("classes/display/solomon_cluster_map_display.php");
	require_once("classes/display/solomon_streetview_map_display.php");
	require_once("classes/display/solomon_map_item_display.php");
	
	require_once("solomon_settings.php");
	require_once("solomon_post_meta.php");
	require_once("solomon_shortcode_meta.php");
	