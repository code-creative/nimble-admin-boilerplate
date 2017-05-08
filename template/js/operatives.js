$(function() {


		$(".password-model").click(function(){
			var user_id = $(this).attr("data-id");
			$("body").attr("data-selected-user",user_id);
			return true;
		});
		
		$("#changePassword").on("show.bs.modal",function(){
			$("#password").val("");
			$("#conf_password").val("");
		});
		
		$("#change_password").click(function(){
			var user_id = $("body").attr("data-selected-user"),  p = $("#password").val(),
				p_conf = $("#conf_password").val(),
				error = [];
			
				if(p == "")
				{
					error.push("Please enter password");	
				}
			
				if(p_conf == "")
				{
					error.push("Please confirm password");
				}
			
				if(p != p_conf)
				{
					error.push("The password you entered needs to match confirmation");
				}
			
				if(error.length > 0)
				{
					alert(error.join("\n"));
					return false;
				}else{
					$.post("?/ajax-operative-change-password",{
						id:user_id,
						new_password:p
					},function(d){	
						alert(d.msg.join("\n"));
					},"json");
					
				}
			
			 $("body").removeAttr("data-selected-user")
		});
	
	$(".signature").on("error",function(){
		$(this).attr("src","./ilec_signatures/no-sig.png");
	});
});
