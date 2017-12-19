<?PHP

	class solomon_menu{
	
		function __construct(){
			add_action("admin_menu", array($this, "menu_hide"));
			if(isset($_GET['page'])){
				if($_GET['page']=="solomondashboard"){
					add_action("admin_enqueue_scripts", array($this, "scripts"));
				}
			}
		}
		
		function scripts(){
			wp_register_style( 'solomon_dashboard', plugins_url() . '/solomon/css/dashboard.css', false, '1.0.0' );
			wp_enqueue_style( 'solomon_dashboard' );
		}
	
		function menu_hide(){
			
			global $submenu, $menu;
			
			foreach($menu as $key => $item){
				if($item[0] == "Cluster Map"){
					unset($menu[$key]);
				}
				if($item[0] == "Cluster Items"){
					unset($menu[$key]);
				}
				if($item[0] == "Streetview Walkthroughs"){
					unset($menu[$key]);
				}
				if($item[0] == "Streetview Overlays"){
					unset($menu[$key]);
				}
				if($item[0] == "Map Image Overlays"){
					unset($menu[$key]);
				}
				if($item[0] == "Map Images"){
					unset($menu[$key]);
				}
				if($item[0] == "Map Items"){
					unset($menu[$key]);
				}
				if($item[0] == "Map Labels"){
					unset($menu[$key]);
				}
				if($item[0] == "Map Text Labels"){
					unset($menu[$key]);
				}
			}
			
			$submenu['edit.php?post_type=solomonmap'] = "";
			
			add_submenu_page('edit.php?post_type=solomonmap',__("Dashboard"),__("Solomon Maps Dashboard"),"manage_options","solomondashboard",array($this,"dashboard"));
		
		}
		
		function dashboard(){
			?><h1>Solomon Maps Dashboard</h1>
			<div class="solomonmaps">
				<h3>Standard Maps</h3>
				<p>Standard maps allow you to create a rich set of items and then add those items to a map</p>
			<?PHP

				echo "<div class='map'><a target='_blank' href='" . admin_url("post-new.php?post_type=solomonmap") . "'>" . __("Create a new Map") . "</a>";
				echo "<a target='_blank' href='" . admin_url("edit.php?post_type=solomonmap") . "'>" . __("See maps you've already made") . "</a></div>";
				echo "<div class='notmap'><a target='_blank' href='" . admin_url("post-new.php?post_type=solomonmapitem") . "'>" . __("Create one or more of a variety of items for your map") . "</a>"; 
				echo "<a target='_blank' href='" . admin_url("edit.php?post_type=solomonmapitem") . "'>" . __("See items you've already made") . "</a>";
				echo "<a target='_blank' href='" . admin_url("post-new.php?post_type=solomonmapioverlay") . "'>" . __("Create a rotatable image overlay for your map") . "</a>"; 
				echo "<a target='_blank' href='" . admin_url("edit.php?post_type=solomonmapioverlay") . "'>" . __("See image overlays you've already made") . "</a>"; 
				echo "<a target='_blank' href='" . admin_url("post-new.php?post_type=solomonmapimage") . "'>" . __("Create an deformable image overlay for your map") . "</a>"; 
				echo "<a target='_blank' href='" . admin_url("edit.php?post_type=solomonmapimage") . "'>" . __("See deformable image overlays you've already made") . "</a>"; 
				echo "<a target='_blank' href='" . admin_url("post-new.php?post_type=solomonmaptlabel") . "'>" . __("Create a labelled item your map") . "</a>"; 
				echo "<a target='_blank' href='" . admin_url("edit.php?post_type=solomonmaptlabel") . "'>" . __("See labelled items you've already made") . "</a>"; 
				echo "<a target='_blank' href='" . admin_url("post-new.php?post_type=solomonmaplabel") . "'>" . __("Create a text label for your map") . "</a>"; 
				echo "<a target='_blank' href='" . admin_url("edit.php?post_type=solomonmaplabel") . "'>" . __("See text labels you've already made") . "</a></div>"; 
			?>	
			</div>
			<div class="solomonmaps">
				<h3>Streetview Maps</h3>
				<p>Streetview maps allow you to create Streetview walkthroughs. Create some overlays and then add these to a map</p><?PHP
					echo "<div class='map'><a target='_blank' href='" . admin_url("post-new.php?post_type=solomonmapwalk") . "'>" . __("Create new Streetview Map") . "</a>";
					echo "<a target='_blank' href='" . admin_url("edit.php?post_type=solomonmapwalk") . "'>" . __("See Streetview Maps you've already made") . "</a></div>";
					echo "<div class='notmap'><a target='_blank' href='" . admin_url("post-new.php?post_type=solomonmaplay") . "'>" . __("Create an overlay you can add to a map") . "</a>";
					echo "<a target='_blank' href='" . admin_url("edit.php?post_type=solomonmaplay") . "'>" . __("See overlays you've already made") . "</a></div>";
				?>
			</div>
			<div class="solomonmaps">
				<h3>Thumbnail Maps</h3>
				<p>Thumbnail maps allow you to add images to maps and have them cluster depending on the zoom. Create some markers and then add these to a map</p><?PHP
					echo "<div class='map'><a target='_blank' href='" . admin_url("post-new.php?post_type=solomonmcluster") . "'>" . __("Create new Cluster Map") . "</a>";
					echo "<a target='_blank' href='" . admin_url("edit.php?post_type=solomonmcluster") . "'>" . __("See Cluster Maps you've already made") . "</a></div>";
					echo "<div class='notmap'><a target='_blank' href='" . admin_url("post-new.php?post_type=solomonmapcitem") . "'>" . __("Create a marker you can add to a map") . "</a>";
					echo "<a target='_blank' href='" . admin_url("edit.php?post_type=solomonmapcitem") . "'>" . __("See markers you've already made") . "</a></div>";
				?>
			</div>
			<?PHP

		}
	
	}
	
	$solomon_menu = new solomon_menu();
	