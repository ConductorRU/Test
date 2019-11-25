$(document).ready(function()
{
	var owls = $('#plans .owl-carousel');
	for(var i = 0; i < owls.length; ++i)
	{
		$(owls[i]).owlCarousel({
				loop:true,
				margin:0,
				nav:true,
				dots:false,
				navText: ['<img src="/local/img/left_r.png" alt="<" />', '<img src="/local/img/right_r.png" alt=">" />'],
				navContainer: "#plans .owl-carousel[data-tab='" + (i + 1) + "'] .owl-stage-outer",
				items:1
		});
	}
	$("#plans .tabText .item > span").click(function()
	{
		$(this).closest(".tabText").find(".item > span").removeClass("active");
		$(this).addClass("active");
	});
	$("#plans .tab li").click(function()
	{
		$("#plans .owl-carousel").removeClass('active');
		$("#plans .owl-carousel[data-tab='" + $(this).attr('data-tab') + "']").addClass('active');
	});
	$("#plans .tabItem > span").click(function()
	{
		$("#plans .owl-carousel[data-tab='" + $(this).attr('data-tab') + "']").trigger('to.owl.carousel', [$(this).attr('data-id'), 300]);
	});
	$("#plans .owl-carousel").on('changed.owl.carousel', function(event)
	{
		var current = event.item.index;
		var slide = $(event.target).find(".owl-item").eq(current).find('.item');
		var itm = $("#plans .tabItem > span[data-id='" + slide.attr('data-id') + "'][data-tab='" + slide.attr('data-tab') + "']");
		itm.closest(".tabText").find(".item > span").removeClass("active");
		itm.addClass("active");
	});
});