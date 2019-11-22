var almini =
{
	images: [],
	isInit: false,
	num: 0,
	name: 'Объявление',
	Init: function()
	{
		this.isInit = true;
		$(".wrap").append('<div id="almini" onclick="almini.Close(this, event)"><i class="fa fa-times" onclick="almini.Close(this, event)"></i><div class="alImg"><div><div></div><div id="alminiImg"></div><div class="prev" onclick="almini.Prev()"><i class="fa fa-chevron-left"></i></div><div class="next" onclick="almini.Next()"><i class="fa fa-chevron-right"></i></div></div><div id="alminiImgs" class="scrollwhite"></div></div></div>');
	},
	Open: function(ad_id, num, imgs)
	{
		if(!this.isInit)
			this.Init();
		$("#almini").show();
		$("body").css('overflow-y', 'hidden');
		if(this.images != imgs)
		{
			this.images = imgs;
			this.num = 0;
		}
		if(imgs.length)
		{
			$("#alminiImg").html("<img src='" + imgs[this.num].large + "' alt='' />");
			var t = '';
			for(var i = 0; i < imgs.length; ++i)
			{
				t += '<div id="almini_' + i + '" onclick="almini.Change(this, ' + i  + ')"><img src="' + imgs[i].small + '" /></div>';
			}
			$("#alminiImgs").html(t);
			$("#alminiImgs > div:nth-child(" + (this.num + 1) + ")").addClass('sel');
		}
		if(imgs.length <= 1)
			$("#almini").addClass('single');
		else
			$("#almini").removeClass('single');
	},
	Change: function(el, num)
	{
		this.num = num;
		$(el.parentNode).find('> div').removeClass('sel');
		$(el).addClass('sel');
		$("#alminiImg").html("<img src='" + this.images[num].large + "' alt='' />");
	},
	Next: function()
	{
		++this.num;
		if(this.num >= this.images.length)
			this.num = 0;
		var el = $('#almini_' + this.num)[0];
		if(el)
			this.Change(el, this.num);
	},
	Prev: function()
	{
		--this.num;
		if(this.num < 0)
			this.num = this.images.length - 1;
		var el = $('#almini_' + this.num)[0];
		if(el)
			this.Change(el, this.num);
	},
	ChangeM: function(ad_id, el, num, imgs)
	{
		this.images = imgs;
		this.num = num;
		$(el.parentNode).find('> div').removeClass('sel');
		$(el).addClass('sel');
		$("#alminiImgM" + ad_id).html("<img src='" + this.images[this.num].small + "' alt='' />");
	},
	NextM: function(ad_id, imgs)
	{
		this.images = imgs;
		++this.num;
		if(this.num >= this.images.length)
			this.num = 0;
		var el = $('#almini_L' + this.num)[0];
		$("#alminiImgM" + ad_id).html("<img src='" + this.images[this.num].small + "' alt='' />");
		if(el)
			this.ChangeM(ad_id, el, this.num, imgs);
	},
	PrevM: function(ad_id, imgs)
	{
		this.images = imgs;
		--this.num;
		if(this.num < 0)
			this.num = this.images.length - 1;
		var el = $('#almini_L' + this.num)[0];
		$("#alminiImgM" + ad_id).html("<img src='" + this.images[this.num].small + "' alt='' />");
		if(el)
			this.ChangeM(ad_id, el, this.num, imgs);
	},
	Close: function(el, ev)
	{
		if(ev.target == el)
		{
			$("#almini").hide();
			$("body").css('overflow-y', 'auto');
		}
	}
}

var date =
{
	day: 0,
	month: 0,
	year: 0,
	CalcDays: function(root)
	{
		if(!this.month)
			return;
		var m = this.month;
		var count = 31;
		if(m == 4 || m == 6 || m == 9 || m == 11)
			count = 30;
		if(m == 2)
			count = 29;
		if(this.year && this.year%4 != 0)
			count = 28;
		if(this.day && this.day > count)
			this.day = count;
		var t = '<option value="0">День</option>';
		for(var i = 1; i <= count; ++i)
		{
			var day = i;
			if(i < 10)
				day = '0' + String(day);
			var s = '';
			if(this.day == i)
				s = ' selected="selected"';
			t += '<option value="' + i + '"' + s +'>' + day + '</option>';
		}
		$(root).find(".form-date-day").html(t);
	},
	Calc: function(root)
	{
		this.day = $(root).find(".form-date-day option:selected").val();
		this.month = $(root).find(".form-date-month option:selected").val();
		this.year = $(root).find(".form-date-year option:selected").val();
		if(this.year == 0 && this.month == 0 && this.day == 0)
		{
			$(root).find('input').val('');
		}
		else
		{
			var y = this.year;
			var m = this.month;
			var d = this.day;
			if(m < 10)
				m = '0' + String(m);
			if(d < 10)
				d = '0' + String(d);
			if(y < 1900)
				y = '0000';
			$(root).find('input').val(y + '-' + m + '-' + d);
		}
	},
	ChangeDay: function(el)
	{
		this.Calc(el.parentNode);
	},
	ChangeMonth: function(el)
	{
		this.Calc(el.parentNode);
		this.CalcDays(el.parentNode);
	},
	ChangeYear: function(el)
	{
		this.Calc(el.parentNode);
		this.CalcDays(el.parentNode);
	}
}

var wgImage = 
{
	Remove: function(elem)
	{
		var el = $(elem).closest(".wgItem");
		el.removeClass("isLoaded").removeClass("isLoad");
		el.css("background-image", '');
		el.find("input[type='hidden']").val("");
		el.find("input[type='file']").val("");
	},
	Upload: function(el, maxWidth)
	{
		if(el.files.length == 0)
			return;
		var formData = new FormData();
		formData.append("image", el.files[0]);
		formData.append("maxWidth", maxWidth);
		$.ajax({
			url: '/admin/site/image-upload',
			data: formData,
			type: 'POST',
			cache: false,
			contentType: false,
			processData: false,
			el: el,
			xhr: function()
			{
			var xhr = new window.XMLHttpRequest();
			xhr.upload.el = this.el;
			xhr.upload.addEventListener("progress", function(evt)
			{
				if (evt.lengthComputable)
				{
					var percentComplete = evt.loaded / evt.total;
					percentComplete = parseInt(percentComplete * 100);
					console.log(percentComplete);
					$(this.el.parentNode).find(".bar > i").css('width', percentComplete + '%');
					if (percentComplete === 100)
					{

					}
				}
			}, false);

				return xhr;
			},
			uploadProgress: function(event, position, total, percentComplete)
			{
				$(this.el.parentNode).find(".bar > i").css('width', percentComplete + '%');
			},
			beforeSend: function (e)
			{
				var el = $(this.el.parentNode);
				el.addClass("isLoad");
				el.css("background-image", '');
				el.find("input[type='hidden']").val("");
			},
			success: function (e)
			{
				var el = $(this.el.parentNode);
				el.addClass("isLoaded").removeClass("isLoad");
				el.css("background-image", "url('" + e.url + "')");
				el.find("input[type='hidden']").val(e.id);
			},
			error: function (e)
			{
				$(this.el.parentNode).find("input[type='hidden'])").val("");
				$(this.el.parentNode).addClass("isError").removeClass("isLoad");
			},
    });
	}
}
var wgFile = 
{
	Remove: function(elem)
	{
		var el = $(elem).closest(".wgItem");
		el.removeClass("isLoaded").removeClass("isLoad");
		el.find("input[type='hidden']").val("");
		el.find("input[type='file']").val("");
		el.find("input[type='text']").val("");
	},
	Upload: function(el)
	{
		if(el.files.length == 0)
			return;
		var formData = new FormData();
		formData.append("file", el.files[0]);
		$.ajax({
			url: '/admin/site/file-upload',
			data: formData,
			type: 'POST',
			cache: false,
			contentType: false,
			processData: false,
			el: el,
			xhr: function()
			{
			var xhr = new window.XMLHttpRequest();
			xhr.upload.el = this.el;
			xhr.upload.addEventListener("progress", function(evt)
			{
				if (evt.lengthComputable)
				{
					var percentComplete = evt.loaded / evt.total;
					percentComplete = parseInt(percentComplete * 100);
					console.log(percentComplete);
					$(this.el).closest(".wgItem").find(".bar > i").css('width', percentComplete + '%');
					$(this.el).closest(".wgItem").find(".bar span").html(parseInt(percentComplete) + '%');
					if (percentComplete === 100)
					{

					}
				}
			}, false);

				return xhr;
			},
			beforeSend: function (e)
			{
				var el = $(this.el).closest(".wgItem");
				el.addClass("isLoad");
			},
			success: function (e)
			{
				var el = $(this.el).closest(".wgItem");
				el.addClass("isLoaded").removeClass("isLoad");
				el.find("input[type='hidden']").val(e.id);
				el.find("input[type='text']").val(e.url);
			},
			error: function (e)
			{
				var el = $(this.el).closest(".wgItem");
				el.find("input[type='hidden']").val("");
				el.find("input[type='text']").val("");
				el.addClass("isError").removeClass("isLoad");
			},
    });
	}
}

var file = 
{
	file_id: {},
	ad_id: 0,
	uploadCounter: {},
	AddFileUpload: function(id, num)
	{
		var p = '<div class="progress"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width:0%"></div></div>';
		$('<div id="upload_' + id + '_' + num + '">' + p + '</div>').insertBefore("#fileUploadX_" + id);
	},
	RemoveFileUpload: function(id, el)
	{
		el.parentNode.parentNode.removeChild(el.parentNode);
		$("#addList_" + id + ".upload").show();
		$("#addList_" + id + ".upload input").val('');
	},
	UploadFile: function(id, attr, count, elId, className, url)
	{
		var bar = $('.bar');
		var F = $("#imageform_" + id);
		if(F[0].files.length == 0)
			return;
		var formData = new FormData();
		if(this.uploadCounter[id] == undefined)	
			this.uploadCounter[id] = 1;
		else
			++this.uploadCounter[id];
		var num = this.uploadCounter[id];
		if(num >= count)
			$('#fileUpload_' + id + ' .upload').hide();
		formData.append("file", F[0].files[0]);
		formData.append("widget_id", id);
		formData.append("id", elId);
		formData.append("name", attr);
		$("#addList_" + id + ".upload").hide();
		$.ajax({
       url: url,//'/upload-file',
			 num: num,
			 attrName: attr,
			 widget_id: id,
			 classN: className,
			 xhr: function()
			 {
				var xhr = new window.XMLHttpRequest();
				xhr.upload.num = this.num;
				xhr.upload.widget_id = this.widget_id;
				xhr.upload.addEventListener("progress", function(evt)
				{
					if (evt.lengthComputable) {
						var percentComplete = evt.loaded / evt.total;
						percentComplete = parseInt(percentComplete * 100);
						console.log(percentComplete);
						$('#upload_' + this.widget_id + '_' + this.num + ' .progress-bar').css('width', percentComplete + '%');
						if (percentComplete === 100)
						{

						}
					}
				}, false);

				return xhr;
			},
			 uploadProgress: function(event, position, total, percentComplete)
			{
				var percentVal = percentComplete + '%';
				bar.width(percentVal);
			},
       beforeSend: function (e) {
        file.AddFileUpload(this.widget_id, this.num);
       },
       success: function (e)
			 {
         $('#upload_' + this.widget_id + '_' + this.num).html(e);
				 $('#upload_' + this.widget_id + '_' + this.num + ' input[type=hidden].file_url').attr('name', this.classN + '[file_url]');
				 ++file.file_id;
       },
       error: function (e) {
         
       },
       // Form data
       data: formData,
       type: 'POST',
       //Options to tell jQuery not to process data or worry about content-type.
       cache: false,
       contentType: false,
       processData: false
    });
		
	},
	DragOver: function(el)
	{
		$(el).addClass('dragOver');
	},
	DragOut: function(el)
	{
		$(el).removeClass('dragOver');
	},
	AttachOpen(ad_id)
	{
		if(ad_id)
			file.ad_id = ad_id;
		var b = $("#attach-block");
		if(b.html() != '')
			return;
		b.html('Подождите...');
		var data = GetToken();
		data['ad_id'] = file.ad_id;
		$.ajax({
			type     :"POST",
			cache    : false,
			url  : "/post/files",
			data : data,
			ad_id: file.ad_id,
			block: b,
			success: function(res)
			{
				b.html(res);
			},
			error: function(res)
			{
				b.html(res);
			}
		});
	},
	Select: function(file_id, name)
	{
		$("#sendAttach_" + file.ad_id).html('<span>' + name + '</span> <i class="fa fa-times" title="Отменить" onclick="file.Cancel(this, ' + file.ad_id + ')"></i>');
		$("#send_attach_" + file.ad_id).val(file_id);
	},
	Cancel: function(el, ad_id)
	{
		el.parentNode.innerHTML = "";
		file.sel_file = 0;
		$("#send_attach_" + ad_id).val(0);
	}
}