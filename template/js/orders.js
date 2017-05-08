$(function() {
  
  $(".update_qty").blur(function() {		
    
				var id = $(this).attr("data-id"),
           column = $(this).attr("data-column"),
					value = $.trim($(this).val()),
					post_data = {
						id: id,
						value: value
					};
				if (value != "") {
					$.post("?/update_orderitem_quantity", {
						id: id,
            column : column,
						value: value
					}, function(data) {            
            console.log(data);
          }); 
				}
			});	
  
  $('#order_submit').click(function(){
    
      var id = $('[name=collected_qty_qty]').attr("data-id"),
          order_id = $('[name=collected_qty_qty]').attr("data-order-id"),
          profile_id = $('[name=collected_qty_qty]').attr("data-profile-id"),
          post_data = {}; 
      $('[name=collected_qty_qty]').each(function(){
          
        if(post_data[$(this).attr("data-order-ref")] == null)
                  post_data[$(this).attr("data-order-ref")] = {};
            
        post_data[$(this)
                  .attr("data-order-ref")][$(this).attr("data-product-code")]  = $(this).val();

      });          
				
				if (!$.isEmptyObject(post_data)) {
					$.post("?/update_collected_quantity", {
						user_id: order_id,
            profile_id:profile_id,
            collections :post_data ,
            skip : true
					}, function(data) {            
            console.log(data);
          }); 
				}
    
  });
	
	$('#order_cancel').click(function(){
		if (confirm("Are you sure you want to cancel the order?")) {
				$("#order_details_form").submit();
		}
	});
	
  
  
});