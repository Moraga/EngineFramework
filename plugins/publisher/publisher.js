/**
 * Base namespace for the Publisher plugin
 */
var publisher = {};

/**
 * @param {Object} module HTML .module element
 */
publisher.module = function(module) {
	module.find('h2').on('click', function() {
		if (module.hasClass('open'))
			module.removeClass('open').trigger('close');
		else
			module.addClass('open').trigger('open');
	});
	
	module.find('.group').each(function() {
		publisher.group($(this));
	});
	
	this.multiple(module);
};

/**
 * @param {Object} group HTML .group element
 */
publisher.group = function(group) {	
	var open = group.data('open'),
		collapse = group.data('collapse');
	
	group.on('click', function() {
		if (group.hasClass('open') && !group.hasClass('focus')) {
			$('.group').removeClass('focus');
			group.addClass('focus');
		}
	});
	
	group.find('h3').click(function() {
		// close
		if (group.hasClass('focus')) {
			group.removeClass('open focus').trigger('blur').trigger('close');
		}
		// set focus
		else if (group.hasClass('open')) {
			$('.module').find('.focus').removeClass('focus');
			group.addClass('focus').trigger('focus');
		}
		// open
		else {
			$('.module').find('.focus').removeClass('focus');
			group.addClass('open focus').trigger('open').trigger('focus');
			
			if (open) {
				eval(open).apply(group);
			}
		}
	});
	
	group.find('.row').each(function() {
		publisher.row($(this));
	});
	
	this.multiple(group);
};

/**
 * @param {Object} row HTML .row element
 */
publisher.row = function(row) {
	row.find('input, textarea').each(function() {
		var self = $(this),
			type = self.data('type'),
			charcount = self.data('charcount'),
			maxlength = parseInt(self.data('maxlength')),
			minlength = parseInt(self.data('minlength'));
		
		switch (type) {
			case 'file':
				var btnFile = $(
				'<div class="btnFile event">'+
					'<input type="file" name="file"/>'+
					'<label>Upload file</label>'+
				'</div>').insertAfter(self);
				
				btnFile.find('input').fileUpload({
					success: function(response) {
						self.val(response.file);
					},
					error: function() {
						
					}
				});
				
				break;
		}
		
		if (charcount) {
			// creates a counter element
			var counter = $(document.createElement('span')).addClass('counter event').insertAfter(self);
			
			self.bind('keyup', function() {
				counter.text(this.value.length);
				if (maxlength || minlength)
					counter[maxlength && this.value.length > maxlength || minlength >>> 0 > this.value.length ? 'addClass' : 'removeClass']('outrange');
			}).trigger('keyup');
		}
	});
	
	this.multiple(row);
};

/**
 * loads meta-templates from media
 */
publisher.loadmt = function() {
	var self = this;
	
	if (!self.data('loaded')) {
		self.find('h3 input')
			.on('keyup', function(event) {
				switch (event.which) {
					// enter
					case 13:
						if (mt = $(this).parents('.group').find('.item:visible .itemLink').get(0))
							window.location = mt.href;
						break;
					
					// esc
					case 27:
						this.value = '';
						$(this).parents('.group').find('.item').show();
						this.blur();
						break;
					
					default:
						var srch = new RegExp(engine.unaccent(this.value).replace(/\s+/g, '.*'), 'i')
						$(this).parents('.group').find('.itemLink').each(function() {
							$(this).parent()[srch.test(engine.unaccent($(this).text())) ? 'show' : 'hide']();
						});
						break;
				}
			})
			.on('click', function(event) {
				if (self.hasClass('open'))
					event.stopPropagation();
			});
	}
	
	$.ajax({
		url: PUBLISHER + 'load/metatemplate?media=' + this.parent().attr('id'),
		success: function(data) {
			var html = '';
			
			$.each(data, function(k, v) {
				html += 
					'<div class="item">'+
						'<a class="itemAdd" href="'+ PUBLISHER + v.name +'/add/">Add</a>'+
						'<a class="itemLink" href="'+ PUBLISHER + v.name +'/">'+
							['>> ' + v.portal, v.station, v.channel, v.title].filter(function(e) { return e }).join(' &raquo; ').replace(/&raquo;|>>/g, '<span>&raquo;</span>') +
						'</a>'+
					'</div>';
			});
			
			self.find('.content').html(html);
		}
	});
	
	self.data('loaded', true);
};

publisher.multiple_re = {
	module	: [/^([^\[]+\[)(\d+)(\])/i, /(-)(\d+)(_)/],
	group	: [/(\]\[)(\d+)(\])/i, /(-)(\d+)(_[^_]+)$/],
	row		: [/(\[)(\d+)(\])$/, /(-)(\d+)$/]
};

/**
 * @param {Object} set Module, group or row
 */
publisher.multiple = function(set) {
	set.filter('[data-multiple]').on('dblclick', function(event) {
		event.stopPropagation();
		
		var self = $(this),
			type = self.attr('class').split(' ')[0],
			clone = self.clone(false).reset();
		
		clone.find('.event').remove();
		
		clone.find('input, select, textarea').each(function() {
			$(this)
				.attr('name', function(k, v) {
					return !v ? false : v.replace(publisher.multiple_re[type][0], function(matches, p, i, n) {
						return p + (parseInt(i) + 1) + n;
					});
				})
				.attr('id', function(k, v) {
					return !v ? false : v.replace(publisher.multiple_re[type][1], function(matches, p, i, n) {
						return p + (parseInt(i) + 1) + (typeof n == 'string' ? n : '');
					});
				});
		});
		
		clone.insertAfter(self);
		
		publisher[type](clone);
	});
};


/**
 * jQuery plugins
 */

/**
 * Resets form fields
 * @return {Object} jQuery chain
 */
$.fn.reset = function() {
	this.find('input[type="text"], input[type="password"], input[type="file"], select, textarea').val('');
	this.find('input[type="radio"], input[type="checkbox"]').removeAttr('checked').removeAttr('selected');
	return this;
};

/**
 * Populates form fields
 * @param {Object} data
 * @return {Object} jQuery chain
 */
$.fn.populate = function(data) {
	var self = this;
	
	$.each(data, function(k, v) {
		while (v && typeof v == 'object') {
			var key = Object.keys(v)[0];
			
			if (key == 0) {
				k += '\\[\\]';
				break;
			}
			else {
				k += '\\['+ key +'\\]';
				v = v[key];
			}
		}
		
		if (!v || !(ipt = $('[name='+ k +']', self)).length)
			return;
		
		switch (ipt.attr('type')) {
			case 'checkbox':
			case 'radio':
				ipt.each(function() {
					if (this.value == v || $.isArray(v) && $.inArray(this.value, v) != -1)
						this.checked = true;
				});
				break;
			
			default:
				ipt.val(v);
				break;
		}
	});
	
	return this;
};

/**
 * @param {Object} options
 * @return {Object} jQuery chain
 */
$.fn.fileUpload = function(options) {
	var settings = $.extend({
		url: PUBLISHER + 'upload',
		beforeSend: function() {},
		success: function() {},
		error: function() {}
	}, options);
	
	this.on('change', function() {
		var form = $(document.createElement('form')).hide();
		var file = $(this);
		
		var ifrm = $('<iframe src="javascript:false;" name="fileUpload"></iframe>');
		
		ifrm.bind('load', function() {
			ifrm.unbind('load');
			
			form.prop('action', settings.url);
			form.prop('method', 'POST');
			form.prop('target', ifrm.prop('name'));
			form.prop('enctype' , 'multipart/form-data');
			form.prop('encoding', 'multipart/form-data');
			
			file.clone(true).val('').insertAfter(file);
			file.clone(false).appendTo(form).replaceWith(file);
			
			settings.beforeSend();
			
			form.submit();
			
			ifrm.on('load', function() {
				var response = ifrm.contents().find('body').text();
				
				try {
					response = eval('('+ response +')');
					settings.success(response);
				}
				catch (e) {
					settings.error(e);
				}
			});
		});
		
		form.append(ifrm).appendTo(document.body);
	});
};