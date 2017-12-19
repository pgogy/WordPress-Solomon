jQuery(document).ready(
	function(){

		jQuery(".overlayimage")
			.each(
				function(index,value){
					jQuery(value)
						.on("click", function(){
								jQuery(".overlayimage")
									.each(
										function(index,value){
											jQuery(value)
												.css("border","none");
										}
									);
								jQuery("#EntryMapImage").val(jQuery(this).attr("fullsrc"));
								jQuery(this)
									.css("border","2px solid #00F");
							}
						)
				}
			);
	
	}
);