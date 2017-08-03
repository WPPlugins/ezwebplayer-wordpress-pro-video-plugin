var delay = 300;
var dT = new Date();
var tab1 = 0;

(function($){
    $(function () {
    	var tabContainers = $('div.ezwp-tabs > div');
        tabContainers.hide();
        if (by_categories){
        	filter_param = "*";
        }else{
        	filter_param = ':first';
	    }
        
        tabContainers.filter(filter_param).show();
        $(".ezwp-tabs").css("visibility", "visible");
        
        $('div.ezwp-tabs ul.tabNavigation a').click(function () {
        	tabContainers.hide();
            tabContainers.filter(this.hash).show();
            $('div.ezwp-tabs ul.tabNavigation a').removeClass('selected');
            $(this).addClass('selected');
            
            /**
            Load videos of selected categories. Only if post was posted by video(s) and 
            category tab was active. This load was one time only - when we first time
            go to page
            */
            if(tab1 == 0 && by_categories){
            	$(".ezwp-by-videos").click();
            }
            
            if($("#ezwp-post-type").val() == "by-videos" && tab1 >0){
                $("#ezwp-post-type").attr('value','by-categories');
            }
            else{
                $("#ezwp-post-type").attr('value','by-videos');
				tab1 = 1;
            }
            return false;
        }).filter(filter_param).click();
    });
    
    /*
    Select/Deselect extra options
    */
    $(".ezwp-rceo input").click(function(){
     	if($(".ezwp-rceo input:checked").attr('value') == "0"){
            $("#ezwp-extra input:checked").each(function(){
                $(this).removeAttr('checked');
				$(this).prop("checked", false);
                //jQuery(".ezwp-by-videos").click();
            })
        }
        else{
            $("#ezwp-extra input").each(function(){
                $(this).attr('checked',"checked");
				$(this).prop("checked", true);
                //jQuery(".ezwp-by-videos").click();
            })
        }
    });
    
    //ezwp-vfcc - selector for videos by selected categories
    //ezwp-rccs - selector for categories
    //ezwp-rcvl - for all videos
    $(".ezwp-rccs input").click(function(){
    	if($(".ezwp-rccs input:checked").attr('value') == "0"){
            $(".ezwp-by-videos option:selected").each(function(){
                $(this).removeAttr('selected');
				$(this).prop("selected", false);
            })
        }
        else{
            $(".ezwp-by-videos option").each(function(){
                $(this).attr('selected',"selected");
				$(this).prop("selected", true);
            })
        }
        $(".ezwp-by-videos").click();
    });
    
    $(".ezwp-vfcc input").click(function(){
    	if($(".ezwp-vfcc input:checked").attr('value') == "0"){
            $(".ezwp-videos-by-categories option:selected").each(function(){
                $(this).removeAttr('selected');
				$(this).prop("selected", false);
            })
        }
        else{
            $(".ezwp-videos-by-categories option").each(function(){
                $(this).attr('selected',"selected");
				$(this).prop("selected", true);
            })
        }
    });
    $(".ezwp-rcvl input").click(function(){
    	if($(".ezwp-rcvl input:checked").attr('value') == "0"){
            $(".ezwp-sbv-list-block option:selected").each(function(){
                $(this).removeAttr('selected');
				$(this).prop("selected", false);
            })
        }
        else{
            $(".ezwp-sbv-list-block option").each(function(){
                $(this).attr('selected',"selected");
				$(this).prop("selected", true);
            })
        }
    });

})(jQuery)

function getVideos(){
    var curT = new Date();
    if((curT.getTime() - dT.getTime()) > delay){
        var cats = jQuery(".ezwp-by-videos").val();
        alert('nnn');
        jQuery(".ezwp-videos-by-categories").html('');
        jQuery.ajax({
            type: "POST",
            cache: false,
            url: wp_home + "/showvideoslist.php",
            data: {"cats[]":cats},
            success: function(msg){
                jQuery(".ezwp-videos-by-categories").html(msg);
            }
        });

    }
}
jQuery(".ezwp-by-videos").click(function(){
    // dT = new Date();
    // setTimeout("getVideos()", delay)
    jQuery("#search_ajax_preloader").show();
    var cats = jQuery(".ezwp-by-videos").val();
    jQuery(".ezwp-videos-by-categories").html('');
    jQuery.ajax({
        type: "POST",
        cache: false,
        url: wp_home + "/showvideoslist.php",
        data: {"cats[]":cats},
        success: function(msg){
            jQuery(".ezwp-videos-by-categories").html(msg);
            jQuery("#search_ajax_preloader").hide();
        }
    });
})