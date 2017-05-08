$(document).ready(function(){
	
	/*---------------------------------------------------*/
	
		$(this).on("click","#set_status",function(){
			var survey_id = $("body").attr("selected-survey-id"),
					survey_type = $("body").attr("selected-survey-type"),
					status = $("#status_change_select").val();
					
					$("*[data-survey-id="+survey_id+"]").text(status);
					
					$.post("?/ajax-survey-change-status",{
						survey_id:survey_id,
						status:status,
						survey_type:survey_type
					},function(d){
						console.log(d);
					},'JSON');
			
		}).on("click",".change-status-btn",function(){
          			var survey_id = $(this).attr("data-survey-id"),
          				survey_type = $(this).attr("data-survey-type");
          			$("body").attr("selected-survey-id", survey_id);
          			$("body").attr("selected-survey-type", survey_type);

        }).on("click","#set_address",function(){

					var survey_id = $("body").attr("selected-survey-id"),
					address = $("#status_address").val();

					$("#address_display"+survey_id).text(address);
					$.post("?/ajax-survey-update-address",{
						survey_id:survey_id,
						address:address
					},function(d){
						console.log(d);
					},'JSON');

		}).on("click",".address_change",function(){
			var survey_id = $(this).attr("data-id");
			$("body").attr("selected-survey-id",survey_id);
			$("#status_address").val($('#address_display'+survey_id).text());
		});
	
	/*---------------------------------------------------*/

});