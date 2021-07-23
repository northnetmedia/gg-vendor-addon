jQuery(document).ready(function(){
	
	jQuery('#vendor_list').change(function(){

	    var vendor = jQuery(this).val();
	    var post_id = location.search.slice(1).split("&")[0].split("=")[1];
	    
	    console.log(post_id);

	    jQuery.ajax({
	    	type : "post",
			dataType : "json",
			url : myAjax.ajaxurl,
			data : {action: "get_vendor_impacts_ajax", vendor_id : vendor, postId: post_id},
	    
	        success: function(res){
	            console.log(res);
	            if(res.impacts.success == 1){
	            	//console.log(res.impacts);
	            	// var impact = '';
	            	// var checkHtml = '';
	            	// for (var i = 0; i < res.impacts.length; i++) {
	            	// 	checkHtml += '<p><input  type="checkbox" name="multval[]" value="'+res.impacts[i].imp_id+'" /> '+res.impacts[i].title+'</p>';
	            	// }

	            	jQuery('.impacts_list').html(res.impacts.impacts);	
					//jQuery(".stamp_list").html(res.stamps.stamps);	
	            }
	            else{
	            	jQuery('.impacts_list').html('');
					//jQuery(".stamp_list").html('');	
	            }
	            if(res.stamps.success == 1){
	            	//console.log(res.stamps);
	            	// var impact = '';
	            	// var checkHtml = '';
	            	// for (var i = 0; i < res.impacts.length; i++) {
	            	// 	checkHtml += '<p><input  type="checkbox" name="multval[]" value="'+res.impacts[i].imp_id+'" /> '+res.impacts[i].title+'</p>';
	            	// }

	            	//jQuery('.impacts_list').html(res.impacts.impacts);	
					jQuery(".stamp_list").html(res.stamps.stamps);	
	            }
	            else{
	            	//jQuery('.impacts_list').html('');
					jQuery(".stamp_list").html('');	
	            }
	        }
	    });

	});
})

