 /**
 *The Campus Judge
 *@file: tcj_mcq.js
 *author: Shubham vishwakarma <shubhamvishwakarma987@gmail.com>
  */
 mcq={};
 mcq.questions='';
 mcq.total=0;
 mcq.loading_start= function()
 {
 	//alert('started');
 }
 mcq.finish=function()
 {
 	$("#loading_icon").css("display","none");
 }
 mcq.setdata=function(id)
 {
 	$("#mcq_view").css("display","block");
 	$('#pname').text(mcq.questions[id].name);
 	$('#score').text(mcq.questions[id].score);
 	$('#desc').html(mcq.questions[id].description);
 	$('#o1 .data').html(mcq.questions[id].o1);
 	$('#o2 .data').html(mcq.questions[id].o2);
 	$('#o3 .data').html(mcq.questions[id].o3);
 	$('#o4 .data').html(mcq.questions[id].o4);
 	$('#problem_id').val(mcq.questions[id].id);
 	if(mcq.questions[id].star=="1")
 		$('#star').addClass("fa-asterisk");
 	else
 		$('#star').removeClass("fa-asterisk");

	if(id==0)$('#prev').prop('disabled',true);
	else $('#prev').prop('disabled',false);
	if(id==mcq.total-1)$('#next').prop('disabled',true);
	else $('#next').prop('disabled',false);
	$('.option').removeClass("selected");
	var dataid=mcq.questions[id].id;
	if(mcq.response[dataid])
	{$('#o'+mcq.response[dataid]).addClass("selected");
	$('#message').css("display","block");}
	else
	{
		$('#message').css("display","none");
	}
 }
 $(document).ready(function(){
 	var assignment=$("#assignment_id").val();
		$.ajax({
			dataType:'json',
			type: 'POST',
			url: shj.site_url + 'assignment.json',
			beforeSend: mcq.loading_start,
			complete:mcq.finish,
			data: {
				"assignment":assignment,
				shj_csrf_token: shj.csrf_token
			},
			error: shj.loading_error,
			success: function (response) {
					noty({text: 'file uploaded', layout: 'bottomRight', type: 'success', timeout: 2500});
					mcq.questions=JSON.parse(response[0]);
					mcq.total=mcq.questions.length;
					mcq.response={};
					response[1].map(function(key){
						mcq.response[key.id]=key.response;
					});
					mcq.setdata(mcq.no);
			}
		});
		$('#next').click(function(){
			mcq.no++;
			mcq.setdata(mcq.no);
		});
		$('#prev').click(function(){
			mcq.no--;
			mcq.setdata(mcq.no);
		});
		$(".option").click(function(){
			var clicked=this.id;
			var no={"o1":1,"o2":2,"o3":3,"o4":4};
			var submitid=mcq.questions[mcq.no].id;
			if(mcq.response[submitid])return;
			var clicked_id=no[clicked];
			$.ajax({
				type:'POST',
				url:shj.site_url + "mcq/submit_response",
				error:shj.loading_error,
				data:{
					'id':submitid,
					'response':clicked_id,
					'update':mcq.response[submitid]?true:false,
					shj_csrf_token: shj.csrf_token
				},
				success: function(){
					noty({text: 'Response submitted', layout: 'bottomRight', type: 'success', timeout: 2500});
					$('.option').removeClass("selected");
					$('#'+clicked).addClass("selected");
					mcq.response[submitid]=clicked_id;
					$('#message').css("display","block");
				}
			});
		});
		$('#reset').click(function(){
		var submitid=mcq.questions[mcq.no].id;
			$.ajax({
				type:'POST',
				url:shj.site_url + "mcq/delete_response",
				error:shj.loading_error,
				data:{
					'id':submitid,
					shj_csrf_token: shj.csrf_token
				},
				success: function(){
					noty({text: 'Response Deleted', layout: 'bottomRight', type: 'success', timeout: 2500});
					$('.option').removeClass("selected");
					delete mcq.response[submitid];
					$('#message').css("display","none");
				}
			})
		});
 });