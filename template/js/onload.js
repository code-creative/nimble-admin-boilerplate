$(function() {
    /* Setup popovers */
    $('[data-toggle="popover"]').popover();
    /* Setup datepicker */
    $('.has_datepicker').each(function(){
		var settings = {
			format: "dd/mm/yyyy",
			todayHighlight      : true
		};

		if($(this).attr("data-orientation"))
		{
			settings.orientation = $(this).attr("data-orientation");
		}
    	$(this).datepicker(settings);
    });
	
	$(".has_select2").each(function(){
		$(this).select2();
	});
		
	
	$("*[data-confirm]").on("click",function(){
		var href = $(this).attr("href"),
			msg = $(this).attr("data-confirm");
			var c = confirm(msg);
			if(c)
			{
				return;
			}else{
				return false;
			}
	});




});

var utils = {
	emailCheck:function(val)
	{
		var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
		return re.test(val);
	}
};

