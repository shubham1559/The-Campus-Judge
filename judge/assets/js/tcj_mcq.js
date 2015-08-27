 /**
 *The Campus Judge
 *@file: tcj_mcq.js
 *author: Shubham vishwakarma <shubhamvishwakarma987@gmail.com>
  */
 mcq={};
 mcq.questions='';
 mcq.total=0;
 mcq.attempted=0;
 mcq.correct=0;
 mcq.wrong=0;
 mcq.score=0;
 mcq.total_score=0;
 mcq.starred=0;
 mcq.flag={};
 mcq.loading_start= function()
 {
 	//alert('started');
 }
 mcq.finish=function()
 {
 	$("#loading_icon").css("display","none");
 	 $("#mcq_view").css("display","block");
 }
 mcq.filltable=function(){
	for(var i=0;i<mcq.total;i++)
	{
		var star=mcq.questions[i].star=="1"?"*":"-";
		var name=mcq.questions[i].name.substring(0,27);
		var score=mcq.questions[i].score;
		var negative=mcq.questions[i].negative;
		var dataid=mcq.questions[i].id;
		var marked=mcq.response[dataid]?"color4":"";
		var row="<tr><td>"+star+'</td><td>'+(i+1)+'.</td><td>'+name+'</td><td>'+score+'/-'+negative+'</td><td class="'+marked+'"><i class="fa fa-circle"></i></td><td class=""><i class="fa fa-flag"></i></td></tr>';
		$("#sidebox table tbody").append(row);
		mcq.flag[i]=0;
	}
		$('#sidebox table ').find('tr').click( function(){
			mcq.no=$(this).index();
			mcq.setdata(mcq.no);
	});
 }
 mcq.blocked=function(){
 noty({text: 'The Problem is locked, to change your response; Reset it first', layout: 'bottomRight', type: 'warning', timeout: 2500});
 }
 mcq.check_refresh=function(){
	$.ajax({
		type: 'POST',
		url:shj.site_url + "mcq/is_update",
		data:{
			'assignment':mcq.assignment,
			shj_csrf_token: shj.csrf_token
		},
		success: function(response){
			if(response=="Assignment Finished")
			{noty({text: 'Assignment finished, Refresh the page for Updates', layout: 'bottomRight', type: 'information', timeout: 2500});
		}else if(mcq.time_refresh.diff(response)<0)
		{
			noty({text: 'Assignment Updated, Refresh to get the updates', layout: 'bottomRight', type: 'information', timeout: 2500});		}
		}
	});
 }
 mcq.setdata=function(id)
{
	$('#pname').text((id+1)+'. '+mcq.questions[id].name);
	var scr=mcq.questions[id].score+'/-'+mcq.questions[id].negative;
 	$('#score').text(scr);
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


	var dataid=mcq.questions[id].id;
	$('.option').removeClass("selected");
	$('.wrong').removeClass("wrong");
	$('.correct').removeClass("correct");
	if(mcq.response[dataid])
	{
		if(mcq.status!="public")
		{
			$('#o'+mcq.response[dataid]).addClass("selected");
		}
		else
		{
			if(mcq.response[dataid]==mcq.questions[id].correct)
				$('#o'+mcq.response[dataid]).addClass("correct");
			else
			{
				$('#o'+mcq.response[dataid]).addClass("wrong");
				$('#o'+mcq.questions[id].correct).addClass("correct");
			}
		}
	}else if(mcq.status=="public")
	$('#o'+mcq.questions[id].correct).addClass("correct");

	if(mcq.response[dataid]&&mcq.allowed_submit)
		$('#reset').prop('disabled',false);
	else
		$('#reset').prop('disabled',true);
	if(mcq.flag[id]==1)
		$('#flag').addClass("flag_r");
	else
		$('#flag').removeClass("flag_r");
 }
 mcq.set_response=function(){
			var clicked=this.id;
			var no={"o1":1,"o2":2,"o3":3,"o4":4};
			var submitid=mcq.questions[mcq.no].id;
			if(mcq.response[submitid]){mcq.blocked();
				return;}
			var clicked_id=no[clicked];
			$.ajax({
				type:'POST',
				url:shj.site_url + "mcq/submit_response",
				error:shj.loading_error,
				data:{
					'id':submitid,
					'response':clicked_id,
					'assignment':mcq.assignment,
					shj_csrf_token: shj.csrf_token
				},
				success: function(response){
					if(response=="Success")
					{	noty({text: 'Response submitted', layout: 'bottomRight', type: 'success', timeout: 2500});
						$('.option').removeClass("selected");
						$('#'+clicked).addClass("selected");
						mcq.response[submitid]=clicked_id;
						$('#reset').prop('disabled',false);
						$('#sidebox tr:nth-child('+(mcq.no+1)+') td:nth-child(5)').addClass("color4");
						mcq.attempted++;
						$('#attempted').text(mcq.attempted);
					}
					else
					{
						noty({text: response, layout: 'bottomRight', type: 'error', timeout: 2500});
					}
				}
			});
		}
mcq.remove_response=function(){
			var submitid=mcq.questions[mcq.no].id;
			$.ajax({
				type:'POST',
				url:shj.site_url + "mcq/delete_response",
				error:shj.loading_error,
				data:{
					'id':submitid,
					'assignment':mcq.assignment,
					shj_csrf_token: shj.csrf_token
				},
				success: function(response){
					if(response=="Success")
					{noty({text: 'Response Deleted', layout: 'bottomRight', type: 'success', timeout: 2500});
					$('.option').removeClass("selected");
					delete mcq.response[submitid];
					$('#reset').prop('disabled',true);
					$('#sidebox tr:nth-child('+(mcq.no+1)+') td:nth-child(5)').removeClass("color4");
					mcq.attempted--;
					$('#attempted').text(mcq.attempted);
				}
				else{
					noty({text: response, layout: 'bottomRight', type: 'error', timeout: 2500});
				}
				}
			});
}
 $(document).ready(function(){
		$.ajax({
			dataType:'json',
			type: 'POST',
			url: shj.site_url + 'assignment.json',
			beforeSend: mcq.loading_start,
			complete:mcq.finish,
			data: {
				"assignment":mcq.assignment,
				shj_csrf_token: shj.csrf_token
			},
			error: shj.loading_error,
			success: function (response) {
					mcq.questions=JSON.parse(response[0]);
					mcq.total=mcq.questions.length;
					mcq.response={};
					response[1].map(function(key){
						mcq.response[key.id]=key.response;
					});
					mcq.status=response[2];
					mcq.questions.map(function(key){
						mcq.total_score+=parseInt(key.score);
					})
					$('span .public').css("display","none");
					if(mcq.status=="started")
					{
						$(".option").on("click",mcq.set_response);
						$("#reset").on("click",mcq.remove_response);
						mcq.allowed_submit=true;
						window.setInterval(mcq.check_refresh,(shj.notif_check_delay*1000));
					}else if(mcq.status=="public")
					{
						mcq.allowed_submit=false;
						noty({text: "You can see the solutions now", layout: 'bottomRight', type: 'information', timeout: 2500});
						mcq.questions.map(function(key){
							if(mcq.response[key.id])
							{
								if(mcq.response[key.id]==key.correct)
								{	mcq.correct++;
									mcq.score+=parseInt(key.score);
									if(key.star=="1")
										mcq.starred++;
								}
								else
								{
									mcq.wrong++;
									mcq.score-=parseInt(key.negative);
								}
							}
							$('span .public').css("display","inline-block");
							$('span#correct').text(mcq.correct);
							$('span#incorrect').text(mcq.wrong);
							$('span#final_score').text(mcq.score);
							$('span#total_star').text(mcq.starred);
						});
					}
					else{
						mcq.allowed_submit=false;
						noty({text: "You are not allowed to submit Now", layout: 'bottomRight', type: 'information', timeout: 2500});
					}
					mcq.attempted=Object.keys(mcq.response).length;
					mcq.setdata(mcq.no);
					mcq.filltable();
					$('#total').text(mcq.total);
					$('#attempted').text(mcq.attempted);
					$('#total_score').text(mcq.total_score);
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
		$('#flag').click(function(){
			mcq.flag[mcq.no]=1-mcq.flag[mcq.no];
			$('#flag').toggleClass("flag_r");
			$('#sidebox tr:nth-child('+(mcq.no+1)+') td:nth-child(6)').toggleClass("color1");
		});

 });
