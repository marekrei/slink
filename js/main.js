function checkLinkType(){
	if($("input#long_url").val().length > 0)
		$("input#upload_file").attr("disabled", "disabled");
	else
		$("input#upload_file").attr("disabled", "");
	if($("input#upload_file").val().length > 0)
		$("input#long_url").attr("disabled", "disabled");
	else
		$("input#long_url").attr("disabled", "");
}

function checkPasswordProtection(){
	if ($("input[name='password_protected']:checked").val() == 'no')
	{
		$("input#link_password").val("");
        $("input#link_password").attr("disabled", "disabled");
	}
    else
    {
    	$("input#link_password").attr("disabled", "");
    }
}

function updateShortUrlPreview(){
	$("#short_url_preview").html($("input#url_prefix").val() + $("input#short_url").val());
}

$(document).ready(function() {
	$("input#long_url").keydown(function(){checkLinkType();});
	$("input#long_url").blur(function(){checkLinkType();});
	$("input#upload_file").keydown(function(){checkLinkType();});
	$("input#upload_file").blur(function(){checkLinkType();});
	$("input#upload_file").change(function(){checkLinkType();});
	
	$("input#short_url").focus(function(){
		if($(this).hasClass("initial"))
		{
			$(this).val("");
			$(this).removeClass("initial");
		}
	});
	
	$("input#short_url").blur(function(){
		if($(this).val().length == 0)
		{
			$(this).val($("input#generated_short_url").val());
			$(this).addClass("initial");
		}
	});
	
	$("input#short_url").change(function(){updateShortUrlPreview();});
	
	checkPasswordProtection();
	$("input[name='password_protected']").change(function(){checkPasswordProtection()});
	
	updateShortUrlPreview();
	
	$("a.deletelink").click(function(){
		  var answer = confirm('Delete link \'' + $(this).parents("tr").find(">:nth-child(3) a").html()+ '\'?');
		  return answer;
		}); 
	

	$("a.deleteuser").click(function(){
		  var answer = confirm('Delete user \'' + $(this).parents("tr").find(">:first-child").html()+ '\'?');
		  return answer;
		}); 
	
	function positionThumbnail(e) {
		xOffset = 30;
		yOffset = 10;
		$("#thumb").css("left",(e.clientX + xOffset) + "px");

		diff = 0;
		if(e.clientY + $("#thumb").height() > $(window).height())
			diff = e.clientY + $("#thumb").height() - $(window).height();
		
		$("#thumb").css("top",(e.pageY - yOffset - diff) + "px");
	}
	
	$("a.thumb").hover(function(e){
		$("#thumb").remove();
		$("body").append("<div id=\"thumb\"><img src=\"index.php?thumb="+ $(this).parents("tr").find("span.link_id").html() +"\" alt=\"Preview\" \/><\/div>");
		positionThumbnail(e);
		$("#thumb").fadeIn("medium");
	},
	function(){
		$("#thumb").remove();
	});

	$("a.thumb").mousemove(function(e){
		positionThumbnail(e);
		});

	$("a.thumb").click(function(e){$("#thumb").remove(); return true;});
	
	
	
	 $("button#checkForUpdate").click(function(){
		    $("div#version").load('index.php?checkForUpdate');
		    return false;
		  });
	 
	 
	 /* Add loading icon after submission */
	 $("#main .button").click(function(){$("#main img.loading").css('visibility', 'visible');});
});