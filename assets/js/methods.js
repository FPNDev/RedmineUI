var log = {
	in: function(el, ev) {
		if(ev) ev.preventDefault();
		let username = $('#username-field', el.parentNode, false).value;
		let password = $('#password-field', el.parentNode, false).value;
		let server = $('#server-field', el.parentNode, false).value;
		if(!username || !password || !server) return notifier.message(2, 'Все поля должны быть заполнены');
		el.classList.add('btn-spinner');
		ajax.json('/method/user.login.php?login='+encodeURIComponent(username)+'&password='+encodeURIComponent(password)+'&url='+encodeURIComponent(server), function(data) {
			if(data.success) return nav.go(location.href);
			if(data.error) return el.classList.remove('btn-spinner'), notifier.message(2, data.error.error_msg);
		});
		return;
	},

	out: function(el, ev) {
		if(el) el.classList.add('btn-spinner');
		if(ev) ev.preventDefault();
		document.cookie = 'i=0; max-age: 0; path: /';
		return nav.go(location.href);
	}
}

var scroll = {
	init: function() {
		this.scroll();
		window.removeEventListener('scroll', this.scroll, true);
		window.addEventListener('scroll', this.scroll, true);
	},

	scroll: function(event) {
		if(!event) event = {target: document.body};
		$('[sAnim]', event.target).each(function(v) {
			if(scroll.isVisible(v)) {
				let dur = parseInt(v.getAttribute('sDuration')) || 1;
				let animName = v.getAttribute('sAnim').toString();
				v.classList.add('animated');
				v.classList.add(animName);
				v.style.animationDuration = window.getComputedStyle(v).animationDuration.split(', ').map(function(e) { return parseFloat(e) * dur + 's'; }).join(', ');
				v.removeAttribute('sAnim');
				v.removeAttribute('sDuration');
			}
		});
	},

	getParent: function(el) {
		if(!el) return null;
		return this.gpHelper(el.parentNode);
	},
	
	gpHelper: function(el) {
		if(!el) return null;
		let overflowY = window.getComputedStyle(el).overflowY; 
		let isScrollable = overflowY !== 'hidden' && overflowY !== 'visible';

		if((isScrollable && el.scrollHeight > el.clientHeight) || el == document.body) 
			return el; 

		return this.gpHelper(el.parentNode);
	},

	isVisible: function(el) {

	    var elementRect = el.getBoundingClientRect();
	    var parentRects = [];
	    var parentSearch = el.parentElement;

	    while(parentSearch != null){
		    parentRects.push(parentSearch.getBoundingClientRect());
		    parentSearch = parentSearch.parentElement;
	    }

	    var visibleInAllParents = parentRects.every(function(parentRect){
	        var visiblePixelX = Math.min(elementRect.right, parentRect.right) - Math.max(elementRect.left, parentRect.left);
	        var visiblePixelY = Math.min(elementRect.bottom, parentRect.bottom) - Math.max(elementRect.top, parentRect.top);
	        var visiblePercentageX = visiblePixelX / elementRect.width * 100;
	        var visiblePercentageY = visiblePixelY / elementRect.height * 100;
	        return visiblePercentageX + 0.01 > 20 && visiblePercentageY + 0.01 > 20;
	    });
	    return visibleInAllParents && window.innerHeight > elementRect.top + Math.min(elementRect.height / 10, 20);
	}
}

var tickets = {
	init: function() {
		tickets.get();
		tickets.listen();
		return true;
	},
	get: function(el, ev) {
		if(ev) ev.preventDefault();
		$('.tbl-row:not(.tbl-header)').each(function(v) {
			v.css('display', '');
			$('.tbl-header .tbl-cell select').each(function(sel) {
				if(sel.value && sel.value != $('#'+sel.getAttribute('for'), v, false).innerHTML) v.css('display', 'none');
			});
		});
		if(![].filter.call($('.tbl-row:not(.tbl-header)'), function(v) { return v.css('display') != 'none' }).length) $('#no-tickets').css('display', 'block');
			else $('#no-tickets').css('display', 'none');
		scroll.scroll();
	},

	refresh: function() {
		ajax.plain('/', function(data) {
			let el = document.createElement('html');
			el.innerHTML = data;
			$('.tbl', false).innerHTML = $('.tbl', el, false).innerHTML;
			tickets.get();
			scroll.scroll();
		});
	},

	refreshThis: function(btn, ev) {
		if(ev) ev.preventDefault();
		if(btn) btn.classList.add('btn-spinner');
		ajax.json('/method/tickets.get.php?id='+$('.ticket-wrap', false).id, function(data) {
			if(btn) btn.classList.remove('btn-spinner');
			if(data.error && data.error.error_msg) return notifier.message(2, data.error.error_msg);
			let el = $('.ticket-wrap[id="'+data.ticket.id+'"]', false);
			if(data.success && el) {
				document.title = data.ticket.title;
				Object.keys(data.ticket).map(function(k, index) {
				    let v = data.ticket[k];
				    let ie = $('.info-entry[id="'+k+'"]', el, false);
				    let nie = $('.ajax[id="'+k+'"]', el, false)
					if(ie) $('.info-value', ie, false).innerHTML = v;
					if(nie) nie.innerHTML = v;
				});
			}
		});

		return;
	},

	progress: function(el, ev) {
		if(ev) ev.preventDefault();
		if(el) el.classList.add('btn-spinner');
		ajax.json('/method/tickets.progress.php?id='+$('.progress-box', false).id+'&progress='+$('select#done', false).value+'&time='+$('input#time_spent', false).value, function(data) {
			el.classList.remove('btn-spinner');
			if(data.error && data.error.error_msg) return notifier.message(2, data.error.error_msg);
			if(data.success) {
				modal.hide();
				let w = $('.ticket-wrap', false);
				if(w && w.id == data.id) tickets.refreshThis($('.refresh-btn', false), 0);
				return notifier.message(0, 'Прогресс успешно обновлен!');
			}
		});
	},

	correct: function(el, ev) {
		if(ev) ev.preventDefault();
		if(el) el.classList.add('btn-spinner');
		ajax.json('/method/tickets.correct.php?id='+$('.progress-box', false).id+'&question='+encodeURIComponent($('input#correct_question', false).value), function(data) {
			el.classList.remove('btn-spinner');
			if(data.error && data.error.error_msg) return notifier.message(2, data.error.error_msg);
			if(data.success) {
				modal.hide();
				let w = $('.ticket-wrap', false);
				if(w && w.id == data.id) tickets.refreshThis($('.refresh-btn', false), 0);
				return notifier.message(0, 'Ваше питання відправлено!');
			}
		});
	},

	finish: function(el, ev) {
		if(ev) ev.preventDefault();
		if(el) el.classList.add('btn-spinner');
		ajax.json('/method/tickets.progress.php?id='+$('.progress-box', false).id+'&progress=100&time='+$('input#time_spent', false).value, function(data) {
			el.classList.remove('btn-spinner');
			if(data.error && data.error.error_msg) return notifier.message(2, data.error.error_msg);
			if(data.success) {
				modal.hide();
				let w = $('.ticket-wrap', false);
				if(w && w.id == data.id) tickets.refreshThis($('.refresh-btn', false), 0);
				return notifier.message(0, 'Прогресс успешно обновлен!');
			}
		});
	},

	listen: function() {
		ajax.json('/method/tickets.listen.php', 
			function(data) {
				if(data.error && data.error.error_msg) return notifier.message(2, data.error.error_msg);
				if(data.success) {
					let additions = false;
					let home = $('#tickets-list', false);
					data.changes.forEach(function(v) {
						let el = '';
						if(el = $('.tbl-row[id="'+v.id+'"]', false)) {
							if(v.action == 'add') {
								el.classList.add('tickets-new');
								additions = true;
							}
							else if(v.action == 'delete') el.remove();
							else if(v.action == 'edit') {
								switch(v.field) {
									case 'done':
										$('.tbl-row[id="'+v.id+'"]', false).style.background = 'linear-gradient(to right, #d5eed5 '+v.value+'%, #f2f2f2 '+v.value+'%)';
										break;
									default:
										$('.tbl-cell[id="'+v.field+'"]', $('.tbl-row[id="'+v.id+'"]', false), false).innerHTML = v.value;
								}
							}
						} else {
							if(v.action == 'add') {
								additions = true;
								if(home) {
									el = '<div class="tbl-cell" id="project">'+v.project+'</div>';
									if(!$('select[for="project"] > option[value="'+v.project.replace('"', '\"')+'"]', false)) $('select[for="project"]', false).insertAdjacentHTML('beforeend', '<option value="'+v.project.replace('"', '\"')+'">'+HTML.escape(v.project)+'</option>');
									el += '<div class="tbl-cell" id="type">'+v.type+'</div>';
									if(!$('select[for="type"] > option[value="'+v.type.replace('"', '\"')+'"]', false)) $('select[for="type"]', false).insertAdjacentHTML('beforeend', '<option value="'+v.type.replace('"', '\"')+'">'+HTML.escape(v.type)+'</option>');
									el += '<div class="tbl-cell" id="priority">'+v.priority+'</div>';
									if(!$('select[for="priority"] > option[value="'+v.priority.replace('"', '\"')+'"]', false)) $('select[for="priority"]', false).insertAdjacentHTML('beforeend', '<option value="'+v.priority.replace('"', '\"')+'">'+HTML.escape(v.priority)+'</option>');
									el += '<div class="tbl-cell">'+v.subject+'</div>';
									el += '<div class="tbl-cell" id="status">'+v.status+'</div>';
									if(!$('select[for="status"] > option[value="'+v.status.replace('"', '\"')+'"]', false)) $('select[for="status"]', false).insertAdjacentHTML('beforeend', '<option value="'+v.status.replace('"', '\"')+'">'+HTML.escape(v.status)+'</option>');
									el += '<div class="tbl-cell" id="estimated">'+v.estimated+'</div>';
									el += '<div class="tbl-cell" id="spent">'+v.spent+'</div>';
									el += '<div class="tbl-cell"><div class="action-list"><a class="iblock ticket-progress cursor-pointer" href="/modal/progress.php?id='+v.id+'?>" onclick="return modal.show(this, event)"></a><a class="iblock ticket-finish cursor-pointer" href="/modal/finish.php?id='+v.id+'" onclick="return modal.show(this, event)"></a><a class="iblock ticket-go cursor-pointer" href="/ticket/'+v.id+'" onclick="return nav.go(this, event)"></a></div></div>';
									let obj = document.createElement('div');
									obj.className = 'tbl-row small-size tickets-new';
									obj.style.background = 'linear-gradient(to right, #d5eed5 '+v.done+'%, #f2f2f2 '+v.done+'%)';
									obj.id = v.id;
									obj.setAttribute('sAnim', 'fadeInUp');
									obj.innerHTML = el;
									insertAfter(obj, $('#tickets-list > .tbl-header', false));
								}
							}

						}
					});

					if(additions) tickets.messageSound.play();
					if(home) {
						tickets.get();
						scroll.scroll();
					}
				}
				tickets.listen();
			},

			function() {
				tickets.listen();
			}
		);
	}, messageSound: 0
}

var tooltips = {

	init: function() {
		tooltips.hide();
		$(".tt_parent").on("mouseenter", function(event) {
			if(this.getAttribute("tt_mode"))
			{
				var _that = this;
				if(!this == tooltips.curElement)
				{
					$('.tooltip', false).css("display", "none");
					tooltips.hoverTimeout = setTimeout(function() {
						let tt_mode = _that.getAttribute('tt_mode') || 'top';
						let tt_header = _that.getAttribute('tt_title');
						let tt_text = _that.getAttribute('tt_text');
						let tt_selector = _that.getAttribute('tt_selector');
						let tt_fixed = _that.getAttribute('tt_fixed');
						let tt_am = _that.getAttribute('tt_am');
						$('.tooltip', false).classList.remove("tt_top");
						$('.tooltip', false).classList.remove("tt_bottom");
						$('.tooltip', false).classList.remove("tt_left");
						$('.tooltip', false).classList.remove("tt_right");
						$('.tooltip', false).classList.remove("tt_default");
						$('.tooltip', false).classList.remove("tt_out");
						$('.tooltip', false).classList.remove("tt_fixed");
						$('.tooltip', false).classList.remove("tt_am");

						$('.tooltip', false).css("display", "block");

						let fUp = 0, fLeft = 0, arrowOffset = 15;

						if(tt_fixed !== undefined) 
						{
							fUp = $("body", false).scrollTop;
							fLeft = $("body", false).scrollLeft;
							$('.tooltip', false).classList.add("tt_fixed");
						}
						$('.tooltip', false).innerHTML = '';
						if(!tt_selector)
						{
							if(tt_header) $('.tooltip', false).insertAdjacentHTML('beforeend',"<div class='tooltip_title'>" + tt_header + "</div>");
							if(tt_text) $('.tooltip', false).insertAdjacentHTML('beforeend',"<div class='tooltip_text'>" + tt_text + "</div>");
							$('.tooltip', false).classList.add("tt_default");
						} else $('.tooltip', false).innerHTML = document.querySelector(tt_selector).outerHTML;
						if(tt_am != undefined)
						{
							$('.tooltip', false).classList.add('tt_am');
							arrowOffset = $('.tooltip', false).offsetWidth / 2;
						}
						if(tt_mode == "top")
						{
							$('.tooltip', false).css("top", _that.offset().top - fUp - $('.tooltip', false).offsetHeight - 7 + "px");
							$('.tooltip', false).css("left", _that.offset().left - fLeft - arrowOffset + _that.offsetWidth / 2 + "px");
							$('.tooltip', false).classList.add("tt_top");
						} else if(tt_mode == "bottom")
						{
							$('.tooltip', false).css("top", _that.offset().top - fUp + _that.offsetHeight + 7 + "px");
							$('.tooltip', false).css("left", _that.offset().left - fLeft - arrowOffset + _that.offsetWidth / 2 + "px");
							$('.tooltip', false).classList.add("tt_bottom");
						} else if(tt_mode == "left")
						{
							$('.tooltip', false).css("top", _that.offset().top - fUp + _that.offsetHeight / 2 - $('.tooltip', false).offsetHeight / 2 + "px");
							$('.tooltip', false).css("left", _that.offset().left - fLeft - $('.tooltip', false).offsetWidth - 7 + "px");
							$('.tooltip', false).classList.add("tt_left");
						} else if(tt_mode == "right")
						{
							$('.tooltip', false).css("top", _that.offset().top - fUp + _that.offsetHeight / 2 - $('.tooltip', false).offsetHeight / 2 + "px");
							$('.tooltip', false).css("left", _that.offset().left - fLeft + _that.offsetWidth + 7 + "px");
							$('.tooltip', false).classList.add("tt_right");
						}
						$('.tooltip', false).css("opacity", "1");
						$('.tooltip', false).on("mouseenter", function()
						{
							tooltips.hovering = true;
						});
						tooltips.curElement = _that;
						tooltips.hovering = true;
					}, 200);
				} else tooltips.hovering = true;
				this.onmouseleave = function() { 
					clearTimeout(tooltips.hoverTimeout); 
					var _th = this;
					setTimeout(function() {
						if(!tooltips.hovering)
						{
							tooltips.hide();
							_th.onmouseleave = null;
						}
					}, 280);
					tooltips.hovering = false;
				};
			}
		});
		$('.tooltip', false).on('mouseleave', function()
		{
			var _th = this;
			setTimeout(function() {
				if(!tooltips.hovering)
				{
					tooltips.hide();
				}
			}, 280);
			tooltips.hovering = false;
		});
	},

	hide: function() {
		$('.tooltip', false).css("opacity", "0");
		$('.tooltip', false).classList.add("tt_out"); 
		tooltips.curElement = 0;
		$('.tooltip', false).on("transitionend", function() { if(this.css("opacity") == "0") { this.css("display", "none"); this.innerHTML = ''; }});
	}, hovering: false, curElement: 0, hoverTimeout: 0, posInterval: 0

}

Object.prototype.offset = function() {
	let bounding = this.getBoundingClientRect();
	return {top: document.body.scrollTop + bounding.top, left: document.body.scrollLeft + bounding.left};
}
