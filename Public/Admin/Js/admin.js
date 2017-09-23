$(function(){ 

	//阻止回车提交表单

    $("input").each(

        function(){

            $(this).keypress( function(e) {

                var key = window.event ? e.keyCode : e.which;

                if(key.toString() == "13"){

                	return false;

				}

            });

        });

    

})