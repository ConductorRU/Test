var main =
{
	isWait: 0,
	isAjax: 0,
	isAni: 0,
	aniInterval: 0,
	sliderSpeed: 5000,
	more: 0,
	MenuClose: function()
	{
		if($("header").hasClass("active"))
		{
			$("header").removeClass("active");
			$("header .menu").slideUp(300);
			$("#fadeMask").fadeOut(300);
		}
	},
	MenuOpen: function()
	{
		if(!$("header").hasClass("active"))
		{
			$("header").addClass("active");
			$("header .menu").slideDown(300);
			$("#fadeMask").fadeIn(300);
		}
	},
	MenuToggle: function()
	{
		if($("header").hasClass("active"))
			main.MenuClose();
		else
			main.MenuOpen();
	},
	AjaxComplete: function()
	{

	},
	SpoilerToggle: function(el)
	{
		var it = $(el).closest(".item");
		if(it.hasClass("active"))
		{
			it.removeClass("active");
			it.find(".text").slideUp(300);
		}
		else
		{
			it.addClass("active");
			it.find(".text").slideDown(300);
		}
	},
	AjaxRef: function(url, isPush, dir)
	{
		//event.stopPropagation();
		if(main.isAjax)
			return;
		$("#sliderTime i").stop().css("width", "0");
		main.isAjax = 1;
		main.MenuClose();
		$("#ajaxFade").css('opacity', 0);
		var ani = {};
		var dur = 500;
		$("#ajaxFade").css('top', '0').css('bottom', '0').css('left', '0').css('right', '0');

		if(dir == "top")
		{
			$("#ajaxFade").css('bottom', '100%');
			$("#ajaxFade").css('top', 0);
			ani = {opacity: 1, bottom:0};
		}
		else if(dir == "left")
		{
			$("#ajaxFade").css('right', '100%');
			$("#ajaxFade").css('left', 0);
			ani = {opacity: 1, right:0};
		}
		else if(dir == "right")
		{
			$("#ajaxFade").css('left', '100%');
			$("#ajaxFade").css('right', 0);
			ani = {opacity: 1, left:0};
		}
		else if(dir == "ring")
		{
			$("#callRed i").animate({top:"-2000px", bottom:"-2000px", left:"-3000px", right:"-3000px"}, 1000);
			$("#callRed").addClass("active");
			dur = 500;
			ani = {opacity: 0, top:0};
		}
		else//bottom
		{
			$("#ajaxFade").css('top', '100%');
			$("#ajaxFade").css('bottom', 0);
			dir = "bottom";
			ani = {opacity: 1, top:0};
		}
		$("#ajaxFade").attr('data-href', url);
		$("#ajaxFade").attr('data-push', isPush);
		$("#ajaxFade").attr('data-dir', dir);
		$("#ajaxFade").animate(ani, dur, function()
		{
			var url = $("#ajaxFade").attr('data-href');
			var isPush = parseInt($("#ajaxFade").attr('data-push'));
			var dir = $("#ajaxFade").attr('data-dir');
			$('html, body').animate({scrollTop:0}, 0);
			$.ajax(
			{
				type: "POST", cache: false, url: url, isPush: isPush, dir: dir,
				success: function(res)
				{
					isSlider = 0;
					if(this.url == "/")
						$("body").removeClass("noMain");
					else
						$("body").addClass("noMain");
					if(this.url == "/callback")
						$("body").addClass("isRed");
					else
						$("body").removeClass("isRed");
					$("#main").html(res.html);
					main.Init();
					document.title = res.title;
					if(this.isPush)
						history.pushState({url: this.url, dir: this.dir}, res.title, this.url);
					main.StartAnimate();
				},
				error: function (res)
				{
					window.location = this.url;
				},
				complete: function (res)
				{
					var ani = {};
					if(this.dir == "ring")
					{
						$("#callRed i").animate({opacity:0}, 500, function()
						{
							$("#callRed i").css({top:"", bottom:"", left:"", right: "", opacity: ""});
							setTimeout(function() {$("#callRed").removeClass("active");}, 100);
						});
					}
					if(this.dir == "top")
					{
						ani = {opacity: 0, top:'100%'};
					}
					else if(this.dir == "left")
					{
						ani = {opacity: 0, left:'100%'};
					}
					else if(this.dir == "right")
					{
						ani = {opacity: 0, right:'100%'};
					}
					else//bottom
					{
						ani = {opacity: 0, bottom:'100%'};
					}
					$("#ajaxFade").animate(ani, 500,  function()
					{
						main.isAjax = 0;
					});
				}
			});
		});
	},
	BindAjaxRef: function()
	{
		$("a.ajax").unbind("click").click(function(event)
		{
			event.preventDefault();
			main.AjaxRef($(this).attr('href'), 1, $(this).attr('data-dir'));
		});
		window.onpopstate = function(event)
		{
			//alert("location: " + document.location + ", state: " + JSON.stringify(event.state));
			if(event.state && typeof(event.state.url) !== "undefined")
			{
				var dir = event.state.dir;
				if(dir == 'right')
					dir = 'left';
				else if(dir == 'left')
					dir = 'right';
				else if(dir == 'top')
					dir = 'bottom';
				else
					dir = 'top';	
				main.AjaxRef(event.state.url, 0, dir);
			}
		};
	},
	AniSlider: function(time)
	{
		isSlider = 1;
		var cnt = parseInt($("#slider").attr("data-count"));
		if(cnt < 2)
		{
			$("#sliderTime").hide();
			$("#sliderBar").hide();
			$("#slideControl").hide();
			return;
		}
		$("#sliderTime i").stop().css("width", "0");
		$("#sliderTime i").animate({width: "100%"}, time, function()
		{
			$("#sliderTime i").css("width", "0%");
			$('#slider').trigger('next.owl.carousel');
			main.AniSlider(time);
		});
	},
	AniProjects: function(time)
	{
		if(main.isAni)
			return;
		var projs = $("#projAll a");
		var projL = $("#projL");
		var projR = $("#projR");
		while(projs.length)
		{
			var projC = projL;
			if(projL.height() > projR.height())
				projC = projR;
			projC.append($(projs[0]));
			projs = $("#projAll a");
		}
		var sPos = $(document).scrollTop();
		var sHeight = $(window).height();
		var curPos = sPos + (sHeight/3.0)*2.0;
		main.isAni = 1;
		var loads = [];
		var items = $(".projList .item");
		for(var i = 0; i < items.length; ++i)
		{
			var item = $(items[i]);
			var t = item.offset().top;
			if(!item.hasClass("showed") && curPos > t)
			{
				if(sPos > item.offset().top)
				{
					item.addClass("showed");
					item.css('top', "300px");
					item.animate({opacity: 1, top: 0}, time);
				}
				else
					loads.push(item);
			}
		}
		for(var i = 0; i < loads.length; ++i)
		{
			var item = loads[i];
			item.addClass("showed");
			item.css('top', "300px");
			item.animate({opacity: 1, top: 0}, time);
			//break;
		}
		var exs = $(".projList .item:not(.showed)");
		if(loads.length > 1)
		{
			setTimeout(function() {main.isAni = 0; main.AniProjects(time)}, time);
		}
		else
			main.isAni = 0;
		var more = parseInt($("#projects").attr("data-more"));
		if($("#projects").length && exs.length == 0 && !main.more && more)
		{
			main.more = 1;
			var data = {};
			data["category_id"] = $("#projects").attr("data-category");
			data["offset"] = $("#projects").attr("data-offset");
			$.ajax(
			{
				type: "POST", cache: false, url: '/projects/more', data: data,
				success: function(res)
				{
					$("#projAll").html(res.html);
					main.BindAjaxRef();
					$("#projects").attr("data-offset", res.offset)
					if($("#projAll a").length == 0)
						$("#projects").attr("data-more", 0);
				},
				error: function (res)
				{

				},
				complete: function (res)
				{
					main.more = 0;
				}
			});
		}
	},
	OnWhell: function(pos)
	{
		if(pos < 0 && $("#slider").length && $("#slider").attr("data-scrolled") != "1")
		{
			$("#slider").attr("data-scrolled", "1");
			main.AjaxRef("/projects", 1, "");
		}
	},
	InitAniSlider: function()
	{
		var buts = $("#sliderNav button.onAni");
		if(buts.length)
		{
			main.aniInterval = setTimeout(function()
			{
				var buts = $("#sliderNav button.onAni");
				if(buts.length)
				{
					$(buts[0]).stop().animate({opacity: 1, bottom: "0"}, 300);
					$(buts[0]).removeClass('onAni');
					main.InitAniSlider();
				}
				else
					main.aniInterval = 0;
			}, 100);
		}
		else
			main.aniInterval = 0;
	},
	InitAniStart: function()
	{
		var sel = ".anim1, .anim2, .anim3, #sliderNav button";
		$(sel).css({opacity: 0, position: 'relative', bottom: "-30px"});
		$("#sliderNav button").addClass('onAni');
	},
	InitAni: function()
	{
		$(".anim1").stop().animate({opacity: 1, bottom: "0"}, 300);
		main.aniInterval = setTimeout(function()
		{
			$(".anim2").stop().animate({opacity: 1, bottom: "0"}, 300);
			main.aniInterval = setTimeout(function()
			{
				$(".anim3").stop().animate({opacity: 1, bottom: "0"}, 300);
				main.InitAniSlider();
			}, 300);
		}, 300);
	},
	StartAnimate: function()
	{
		if(main.aniInterval)
			clearTimeout(main.aniInterval);
		main.aniInterval = setTimeout(function() {main.InitAni();}, 300);
	},
	GalleryPrepare: function()
	{
		$(".projPhotos .item .img").unbind().click(function() {main.GalleryOpen(this);});
	},
	GalleryOpen: function(el)
	{
		var img = $(el).find("img").attr("src");
		$("#gallery .gMain").html('<div style="background:url(\'' + img + '\') center center no-repeat;background-size:contain;"></div>');
		$("#gallery").fadeIn(300, function() {$("body").css('overflow', 'hidden');});
	},
	GalleryClose: function()
	{
		$("#gallery").fadeOut(300);
		$("body").css('overflow', 'auto');
	},
	Init: function()
	{
		main.BindAjaxRef();
		$('img.svg').each(function()
		{
			var $img = $(this);
			var imgID = $img.attr('id');
			var imgClass = $img.attr('class');
			var imgURL = $img.attr('src');
			$.get(imgURL, function(data)
			{
				var $svg = $(data).find('svg');
				if(typeof imgID !== 'undefined')
					$svg = $svg.attr('id', imgID);
				if(typeof imgClass !== 'undefined')
					$svg = $svg.attr('class', imgClass + ' replaced-svg');
				$svg = $svg.removeAttr('xmlns:a');
				$img.replaceWith($svg);
			}, 'xml');
		});
		setInterval(function()
		{
			var date = new Date();
			var m = date.getMinutes();
			if(m < 10)
				m = "0" + m;
			$("#clockT").html(date.getHours() + ":" + m);
		}, 1000);
		var cnt = $("#slider > div").length;
		if(cnt < 10)
			cnt = '0' + cnt;
		$('#slider').owlCarousel(
		{
			loop:true,
			nav:false,
			dots:true,
			smartSpeed: 600,
			//navContainer: "#sliderNav",
			dotsContainer: "#sliderNav",
			//navText: ['01', cnt],
			items:1,
			onChange: function(event)
			{
				main.AniSlider(sliderSpeed);
			},
			onChanged: function(event)
			{
				var num = event.page.index + 1;
				if(num == 0)
					num = 1;
				if(num < 10)
					num = '0' + num;
				$("#sliderBar .numL").html(num);
				$("#slideFade").addClass("active");
				setTimeout(function() {$("#slideFade").removeClass("active");}, 400);
			}
		});
		$('#slider').append("<div id='slideFade'></div>");
		$("#fadeMask").unbind("click").click(function() {main.MenuToggle();});
		$("#sliderBar .numL").unbind("click").click(function() {$('#slider').trigger('prev.owl.carousel');});
		$("#sliderBar .numR").unbind("click").click(function() {$('#slider').trigger('next.owl.carousel');});
		$("#callback .form input[type='text']").unbind("focus").focus(function() {$(this).closest(".field").addClass("active");});
		$("#callback .form input[type='text']").unbind("blur");
		$("#callback .form input[name='phone']").mask("+7 (999) 999-9999");
		$("#callback .form input[type='text']").blur(function() {if($(this).val() == "") $(this).closest(".field").removeClass("active");});
		$(".spoilers > div .name:not(.dis)").unbind("click").click(function() {main.SpoilerToggle(this);});
		if(!isSlider)
			main.AniSlider(sliderSpeed);
		$(document).unbind("DOMMouseScroll").bind('DOMMouseScroll', function(e){ main.OnWhell(-e.originalEvent.detail); });
		$(document).unbind("mousewheel").bind('mousewheel', function(e){ main.OnWhell(e.originalEvent.wheelDelta); });
		main.AniProjects(1000);
		$(document).scroll(function(event)
		{
			main.AniProjects(1000);
		});
		$("#slideControl > div").unbind();
		$("#slideControl > div").mouseover(function() {$("#slideCursor").text($(this).attr("data-label")).addClass("active");});
		$("#slideControl > div").mouseout(function() {$("#slideCursor").removeClass("active");});
		$("#slideControl > div").click(function()
		{
			if($(this).hasClass("left"))
				$('#slider').trigger('prev.owl.carousel');
			else
				$('#slider').trigger('next.owl.carousel');
		});
		$("#slideControl > div").mousemove(function(e)
		{
			$("#slideCursor").css('left', (event.pageX - $("#slideCursor").width() - 10) + "px");
			$("#slideCursor").css('top', (event.pageY - 430) + "px");
		});
		$(".bureau .photos .rows > div").unbind();
		$(".bureau .photos .rows > div").mousemove(function(e)
		{
			var posX = (e.offsetX - $(this).width()/2)/15.0;
			var posY = -(e.offsetY - $(this).height()/2)/15.0;
			$(this).find("> div").css('transform', 'perspective(500px) rotateY(' + posX + 'deg) rotateX(' + posY + 'deg)');
		});
		$(".pages .stages .name i").unbind();
		$(".pages .stages .name i").mouseover(function()
		{
			var off = $(this).offset();
			var par = $("#stageDesc").closest(".projects").offset();
			var pos = $(this).position();
			var posP = $(this).closest(".row").position();
			var w = (off.left - par.left - $("#stageDesc").width() + 30);
			if((w + par.left) < 10)
				w = 10 - par.left;
			$("#stageDesc").css({top: (pos.top + posP.top + $(this).height() + 20) + "px"});
			$("#stageDesc").css({left: w + "px"});
			$("#stageDesc").html($(this).closest(".item").find(".desc").html());
			$("#stageDesc").stop().fadeIn(300);
		});
		$(".pages .stages .name i").mouseout(function()
		{
			$("#stageDesc").stop().fadeOut(300);
		});
		main.GalleryPrepare();
		main.InitAniStart();
	}
}

$(document).ready(function()
{
	main.Init();
});