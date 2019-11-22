var genplan = 
{
	reverse_id: 1,
	isWait: 0,
	aparts: {},
	SelectHouse: function(num, isFull)
	{
		if(document.documentElement.clientWidth < 600)
		{
			location.href = '/catalog/';
			return;
		}
		if(!isFull)
		{
			location.href = '/genplan/?house=' + num;
			return;
		}
		$("#genplan .pHouses").fadeOut(300);
		$("#genplan .pFloors").fadeIn(300);
		$("#floor_" + num).show();
		$("#floor_" + num).addClass('active');
		$("#genplan .buts").hide();
	},
	Reverse: function()
	{
		$("#genplan .pHouses .plan[data-id='" + genplan.reverse_id + "']").fadeOut(300);
		++genplan.reverse_id;
		if(genplan.reverse_id > 2)
			genplan.reverse_id = 1;
		$("#genplan .pHouses .plan[data-id='" + genplan.reverse_id + "']").fadeIn(300);
	},
	BackHouse: function(num)
	{
		genplan.reverse_id = 1;
		$("#genplan .pHouses").fadeIn(300);
		$("#genplan .pFloors").fadeOut(300);
		$("#floor_" + num).hide();
		$("#floor_" + num).removeClass('active');
		$("#genplan .buts").show();
	},
	BackFloor: function()
	{
		$("#genplan .pFloors").fadeIn(300);
		$("#genplan .pPlan").fadeOut(300);
		//genplan.AddHistory();
	},
	UpdFadeFloor: function()
	{
		var buts = $('#genplan .pPlan .params .but.active');
		if(!buts.length)
		{
			if(main.svg)
				main.svg.find(".apart[data-status='1']").removeClass('block');
		}
		else
		{
			main.svg.find(".apart[data-status='1']").addClass('block');
			for(var i = 0; i < buts.length; ++i)
			{
				main.svg.find(".apart[data-rooms='" + $(buts[i]).attr("data-id") + "'][data-status='1']").removeClass('block');
			}
		}
	},
	FadeFloor: function(el)
	{
		if($(el).hasClass('active'))
			$(el).removeClass('active');
		else
			$(el).addClass('active');
		genplan.UpdFadeFloor();
	},
	RecSend: function()//запись на экскурсию
	{
		if(genplan.isWait)
			return;
		var f = $("#recForm");
		genplan.isWait = 1;
		var data = {};
		data['name'] = f.find("input[name='name']").val().trim();
		data['phone'] = f.find("input[name='phone']").val().trim();
		if(data['name'] == '')
			data['name'] = 'Имя не указано';
		if(data['phone'] == '')
		{
			f.find("input[name='phone']").addClass("error").unbind("click").click(function() {$(this).removeClass("error");});
			genplan.isWait = 0;
		}
		if(!$("#agree2 input").prop("checked"))
		{
			f.find(".agree").addClass("error").unbind("click").click(function() {$(this).removeClass("error");});
			genplan.isWait = 0;
		}
		if(!genplan.isWait)
			return;
		$("#rSend").text("Подождите...");
		$.ajax(
		{
			type: "POST", cache: false, url: '/post/record.php', data: data,
			success: function(res)
			{
				$("#recForm .out").html(res);
			},
			error: function (res)
			{
				$("#recForm .out").html('<div class="alert alert-danger" role="alert">Ошибка отправки заявки</div>');
			},
			complete: function(res)
			{
				genplan.isWait = 0;
				$("#recForm .buts").hide();
				$("#recForm .out").show();
				$("#rSend").text("Отправить запрос");
			}
		});
	},
	SelectFloor: function(el)
	{
		apart.SelectFloorIds($(el).attr("data-housen"), $(el).attr("data-house"), $(el).attr("data-floor"), 1);
	},
	SelectFloorIds: function(house_num, house_id, floor_id, isHistory)
	{
		var data = {};
		data['house_id'] = house_id;
		data['floor'] = floor_id;
		$("#genplan .pFloors").fadeOut(300);
		$("#genplan .pPlan").fadeIn(300);
		$("#genplan .pPlan h2").html("Позиция №" + house_num + ", " + floor_id + ' этаж');
		$.ajax(
		{
			type: "POST", cache: false, url: '/post/get_aparts.php', data: data, floor: data['floor'], house_num: house_num, house_id: house_id, isHistory: isHistory,
			success: function(res)
			{
				if(res.r == 's')
				{
					if(this.isHistory)
						genplan.AddHistory('/genplan/?house=' + $("#genplan .plan object.active").attr("data-id") + '&housen=' + this.house_num + '&house_id=' + this.house_id + '&floor=' + data.floor);
					genplan.aparts = res.plan.aparts;
					var t = '';
					for(var i = res.floors; i > 0; --i)
					{
						t += '<span ';
						if(data.floor == i)
							t += 'class="active" ';
						t += 'data-housen="' + this.house_num + '" data-house="' + this.house_id + '" data-floor="' + i + '" onclick="apart.SelectFloor(this)">' + i + '</span>';
					}
					$(".pPlan .img").html('<div class="planm"><object type="image/svg+xml" data="' + res.plan.plan + '" style="width:100%;"></object></div><div class="planf">' + t + '</div>');
				}
			},
			error: function (res)
			{

			},
			complete: function(res)
			{

			}
		});
	},
	AddHistory: function(ref)
	{
		if(!window.history)
			return;
		window.history.pushState({href: ref}, null, ref);
	},
}