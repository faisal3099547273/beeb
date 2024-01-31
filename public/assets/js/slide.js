// 

document.addEventListener("DOMContentLoaded", () => {
 
$(function()
{

	
	// $("#gallery ul li").attr('style','width: '+(window.innerWidth)+'px');
	var liWidth = $("#gallery ul li").outerWidth(),
					speed = 4000,
					timer = setInterval(auto, speed);
	
	// mostara botoes
	$("section#gallery").hover(
		function(){		
			clearInterval(timer);
			$("section#buttons").stop().fadeIn();
		},
		function(){
			$(this).filter(':animated').stop();
			
			timer = setInterval(auto, speed);
			$("section#buttons").stop().fadeOut();
		});
	
	
	//proximo
	$(".next").click(function(e){
		e.preventDefault();
		
		if($("#gallery ul").is(':animated'))
			return false;
		
		// $("#gallery ul")
		// .css("width","999%")
		// .animate({left:-liWidth}, function(){		
			$("#gallery ul li").last().after( $("#gallery ul li").first() );
			$(this).css({left:0, width:"auto"});
		// });
	});
	
	//voltar
	$(".prev").click(function(e){
		e.preventDefault();
		
		if($("#gallery ul").is(':animated'))
			return false;
		
		$("#gallery ul li").first().before( $("#gallery ul li").last().css('margin-left', -liWidth) );
		$("#gallery ul").css("width","999%").animate({left:liWidth}
		,function(){
			$("#gallery ul li").first().css('margin-left', 0);
			$(this).css({left:0, width:"auto"});
		});
	});
	
	function auto(){
		$('.next').click();
	}
	



	
	
	
	
	
});


 
});