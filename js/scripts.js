jQuery(document).ready(function () {
    jQuery('#vendor-slide-list-wrapper').owlCarousel({
        loop:true,
        margin:30,
        nav:false,
        dots: false,
        autoplay:false,
        autoplayTimeout:3000,
        autoplayHoverPause:true,
        responsive:{
            0:{
                items:1,
                margin:0
            },
            600:{
                items:2
            },
            1000:{
                items:4
            }
        }
    });



    jQuery('#vendor-detail-slider-list-wrapper').owlCarousel({
        loop:true,
        margin:30,
        nav:true,
        dots: false,
        autoplay:false,
        autoplayTimeout:3000,
        autoplayHoverPause:true,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:2
            },
            1000:{
                items:3
            }
        }
    });
     
 

    jQuery('#starfish-products-wrapper').owlCarousel({
        loop:true,
        margin:30,
        nav:true,
        dots: false,
        autoplay:false,
        autoplayTimeout:3000,
        autoplayHoverPause:true,
        responsive:{
            0:{
                items:1,
                margin:0
            },
            600:{
                items:2
            },
            1000:{
                items:3
            }
        }
    });



    jQuery('#yoobi-products-wrapper').owlCarousel({
        loop:true,
        margin:30,
        nav:true,
        dots: false,
        autoplay:false,
        autoplayTimeout:3000,
        autoplayHoverPause:true,
        responsive:{
            0:{
                items:1,
                margin:0
            },
            600:{
                items:2
            },
            1000:{
                items:3
            }
        }
    });

    jQuery('#penpillar-products-wrapper').owlCarousel({
        loop:true,
        margin:30,
        nav:true,
        dots: false,
        autoplay:false,
        autoplayTimeout:3000,
        autoplayHoverPause:true,
        responsive:{
            0:{
                items:1,
                margin:0
            },
            600:{
                items:2
            },
            1000:{
                items:3
            }
        }
    });


    jQuery('#handinhand-products-wrapper').owlCarousel({
        loop:true,
        margin:30,
        nav:true,
        dots: false,
        autoplay:false,
        autoplayTimeout:3000,
        autoplayHoverPause:true,
        responsive:{
            0:{
                items:1,
                margin:0
            },
            600:{
                items:2
            },
            1000:{
                items:3
            }
        }
    });

    //Vendor Follower
    jQuery(document).on("click", ".vendor-follow", function() {
        // When btn is pressed.
        jQuery(this).attr("disabled",true); // Disable the button, temp.
        var vendor = jQuery(this).data('vendor');
        var vendor_followers = jQuery(this).data('followers');
        
        if (localStorage.getItem("followers") === null) {
            //alert('in');
            var followers = [vendor];
            localStorage.setItem("followers", JSON.stringify(followers));
            add_vendor_followers(vendor, vendor_followers);
        }
        else{
            //alert('out');
            var followers = JSON.parse(localStorage.getItem("followers"));  
            if(followers.indexOf(vendor) !== -1){ // found
                console.log(vendor+' Added already in localstorage');
                return false;
            }
            else{
                followers.push(vendor);
                localStorage.setItem("followers", JSON.stringify(followers));
                add_vendor_followers(vendor, vendor_followers);
            }
        }
    });
    
     //Vendor Like
     jQuery(document).on("click", ".vendor-like", function() {
      // When btn is pressed.
         jQuery(this).attr("disabled",true); // Disable the button, temp.
         var vendor = jQuery(this).data('vendor');
         var likes = jQuery(this).data('likes');
         var user = jQuery(this).data('user');
         
         if (localStorage.getItem("vendor_ids") === null) {
             //alert('in');
             var vendor_ids = [vendor];
             localStorage.setItem("vendor_ids", JSON.stringify(vendor_ids));
             add_vendor_likes(vendor, likes, user);
         }
         else{
             //alert('out');
             var vendor_ids = JSON.parse(localStorage.getItem("vendor_ids"));  
             if(vendor_ids.indexOf(vendor) !== -1){ // found
                 console.log(vendor+' Added already in localstorage');
                 jQuery(".vendorid-"+ vendor +" > i").removeClass();
                 jQuery(".vendorid-"+ vendor +" > i").addClass("fas fa-thumbs-up");
                 return false;
             }
             else{
                 vendor_ids.push(vendor);
                 localStorage.setItem("vendor_ids", JSON.stringify(vendor_ids));
                 add_vendor_likes(vendor, likes, user);
             }
         }
     });
 



});

//Add vendor followers
function add_vendor_followers(vendor, vendor_followers)
{
    vendor_followers = vendor_followers + 1;
    var str = '&vendor_followers=' + vendor_followers + '&vendor=' + vendor + '&action=add_vendor_followers';
    jQuery.ajax({
        type: "POST",
        dataType: "html",
        url: my_ajax_object.ajax_url,
        data: str,
        success: function(data){
            //var jQuerydata = jQuery(data);
            console.log(data);
            if(data != ""){
                //alert(product);
                jQuery(".vendor-followers-"+ vendor).html(data);
                jQuery(".vendor-follow").attr("disabled",false);
            } else{
                jQuery(".vendor-follow").attr("disabled",true);
            }
        },
        error : function(jqXHR, textStatus, errorThrown) {
            jQueryloader.html(jqXHR + " :: " + textStatus + " :: " + errorThrown);
        }

    });
    return false;
}

//Add Vendor Likes
function add_vendor_likes(vendor, likes, user){

    //likes = likes + 1;
    //alert('test');
    jQuery(".vendorid-"+ vendor +" > i").removeClass();
    jQuery(".vendorid-"+ vendor +" > i").addClass("fas fa-thumbs-up");
    var str = '&user=' + user + '&likes=' + likes + '&vendor=' + vendor + '&action=add_vendor_likes';
    jQuery.ajax({
        type: "POST",
        dataType: "html",
        url: my_ajax_object.ajax_url,
        data: str,
        success: function(data){
            //var jQuerydata = jQuery(data);
            console.log(data);
            if(data != ""){
                //alert(product);
                jQuery(".likes-vendor-add-"+ vendor).html(data);
                jQuery(".vendor-like").attr("disabled",false);
            } else{
                jQuery(".vendor-like").attr("disabled",true);
            }
        },
        error : function(jqXHR, textStatus, errorThrown) {
            jQueryloader.html(jqXHR + " :: " + textStatus + " :: " + errorThrown);
        }

    });
    return false;
}


