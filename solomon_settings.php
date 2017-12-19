<?PHP

class solomon_settings{

	function __construct(){
		add_action('admin_menu', array($this,'options'));
		add_action('admin_init', array($this,'settings_api_init') );
	}

	function settings_api_init() {
		
		add_settings_section(
			'solomon_setting_section',
			__('solomon settings'),
			array($this,'solomon_intro_function'),
			'solomon-settings'
		);
		
		add_settings_field(
			'solomon_short_code',
			'Turn on short codes',
			array($this,'short_code_function'),
			'solomon-settings',
			'solomon_setting_section'
		);
		
		add_settings_field(
			'solomon_post_item',
			'Add a solomon map item to a post',
			array($this,'post_function'),
			'solomon-settings',
			'solomon_setting_section'
		);
				
		register_setting( 'solomon-settings', 'solomon_short_code' );
		register_setting( 'solomon-settings', 'solomon_post_item' );
	}
 
	function solomon_intro_function() {
		echo '<p>' . __("This page is where you can configure some Solomon settings") . '</p>';
	}
 
	function short_code_function() {
		echo "<p>" . __("Checking this box will show short codes for each map item") . "</p>";
		$checked = "";
		$checked = get_option("solomon_short_code");
		echo '<input name="solomon_short_code" id="solomon_short_code" type="checkbox" value="on" ';
		if($checked!=""){
			echo "checked ";
		}
		echo '/>';
	}
	
	function post_function() {
		echo "<p>" . __("Checking this box will all a solomon map item to be added to a post") . "</p>";
		$checked = "";
		$checked = get_option("solomon_post_item");
		echo '<input name="solomon_post_item" id="solomon_post_item" type="checkbox" value="on" ';
		if($checked!=""){
			echo "checked ";
		}
		echo ' />';
	}
	
	function options_page() {
		?><form method="POST" action="options.php">
		<?php 
			settings_fields("solomon-settings");	
			do_settings_sections("solomon-settings"); 	//pass slug name of page
			submit_button();
		?>
		</form><?PHP
	}
	
	function options() {
		add_submenu_page( "edit.php?post_type=solomonmap", __("Settings"), __("Settings"), "manage_options", 'solomon-settings', array($this,"options_page"));
	}

}

$solomon_settings = new solomon_settings;
