let escaper = document.createElement('textarea');
function $(selector, arg1 = true, arg2 = document) {
	let context = arg2, all = arg1;
	if(typeof arg1 == 'object') context = arg1, arg2 = arg2 ? true : false, all = arg2;
	if(all)	return context.querySelectorAll(selector);
		else return context.querySelector(selector);
};

Object.prototype.each = function(callback) {
	if(NodeList.prototype.isPrototypeOf(this) || HTMLCollection.prototype.isPrototypeOf(this)) this.forEach(function(v, k) { callback(v, k); });
		else callback(this, 0);
	return this;
};

Object.prototype.css = function(property, value = null) {
	if(value === null) return typeof this.style[property] == 'undefined' ? this[0].style[property] : this.style[property];
	if(NodeList.prototype.isPrototypeOf(this) || HTMLCollection.prototype.isPrototypeOf(this)) this.forEach(function(v) { v.style[property] = value; });
		else this.style[property] = value;
	return this;
}

Object.prototype.on = function(ev, callback, bool = false) {
	if(NodeList.prototype.isPrototypeOf(this) || HTMLCollection.prototype.isPrototypeOf(this)) this.forEach(function(v) { v.addEventListener(ev, callback, bool); });
		else return this.addEventListener(ev, callback, bool);
	return this;
}

var ajax = {
	json: function(url, callback, error_callback = function() {}) {
		let xhr = new XMLHttpRequest();
		xhr.open('GET', url, true);
		xhr.addEventListener('readystatechange', function() {
			if(this.readyState == 4 && this.status == 200) {
				let json = JSON.parse(this.response);
				callback(json);
				if(json.error && json.error.error_action) {
					switch(json.error.error_action) {
						case 'logout': log.out(); break;
					}
				}
			}
		});
		xhr.send();
		xhr.onerror = error_callback;
		return xhr;
	},

	plain: function(url, callback) {
		let xhr = new XMLHttpRequest();
		xhr.open('POST', url, true);
		xhr.addEventListener('readystatechange', function() {
			if(this.readyState == 4 && this.status == 200) callback(this.response);
		});
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
		xhr.send('ajax=1');
		return xhr;
	},

};

var nav = {

	go: function(that, event)
	{
		if(event) event.preventDefault();
		let href = that;
		if(typeof that != 'string') href = that.href || that.getAttribute('href');

		load.start();

		if(href != location.href) history.pushState(null, null, href);  
		$('*').css("cursor", "progress");
		let xhr = ajax.plain(href, function(data) {
			$('.page-wrapper', false).innerHTML = data;
			$('*').css("cursor", "");
			$('body', false).scrollTop = 0;
			tooltips.init();
			scroll.scroll();
			tickets.get();
			load.end();
			dd.init();
		});
		xhr.upload.onprogress = function(evt) {
			let percent = Math.ceil(evt.loaded / evt.total * 100);
			load.set(percent);
		}
	},

};

window.on("popstate", function() {

	nav.go(location.href, 0);

});

var load = {

	start: function()
	{
		if(!$('.loading-bar', false))
		{
			$('body', false).insertAdjacentHTML('beforeend', '<div class="loading-bar" style="width: 10%"></div>');
		}
	},
	set: function(amount)
	{
		if($('.loading-bar', false))
		{
			amount = amount || this.get();
			$('.loading-bar', false).css('width', amount + "%");
		}
	},
	end: function()
	{
		$('.loading-bar', false).css('width', '100%');
		$('.loading-bar', false).css('opacity', 0);
		$('.loading-bar', false).on('transitionend', function() {
			this.remove();
		});
	},
	get: function()
	{
		var amount = ($('.loading-bar', false).offsetWidth / $('body', false).offsetWidth) * 100;
		return amount;
	}

}

var notifier = {

	message: function(mode, message, bordered, time, color2, textcol)
	{
		mode = mode || 0;
		bordered = bordered || 0;
		time = time || 1800;
		color2 = color2 || "";
		textcol = textcol || "fff";
		var not_color = "#4c4";
		if(mode == 1) not_color = "#c94";
		else if(mode == 2) not_color = "#c44";
		else if(mode >= 3) not_color = "#" + color2;
		if($('.notifier').length == 5)
		{
			$('.notifier')[4].remove();
		}
		var n_html = $('.notifier-bar', false).innerHTML || '';
		var add_html = '';
		add_html = add_html + "<div class='notifier' time='" + (Date.now() + time) + "' style='background-color: " + not_color + ";" + (bordered ? "border: 1px solid #555" : "") + "'>";
		add_html = add_html + "<div class='notifier-message' style='color: #" + textcol + "'>" + message + "</div></div>";
		n_html = add_html + n_html;
		$('.notifier-bar', false).innerHTML = n_html;
		if(!this.dAll) this.dAll = setInterval(notifier.deleteall, 10);
	},
	delete: function(_that)
	{
		_that.css("opacity", 0);
		_that.on("transitionend", function() {
			this.remove();
		})
	},
	deleteall: function()
	{
		$('.notifier', $('.notifier-bar', false)).each(function(v) {
			if(v.getAttribute) {
				if(Date.now() - parseInt(v.getAttribute('time')) >= 0)
				{
					notifier.delete(v);
				}
			}

		});
	}, dAll: 0

}

var modal = {

    show: function(loc, ev) {
        if(ev) ev.preventDefault();
        if(typeof loc != 'string') loc = loc.getAttribute('href');
        ajax.plain(loc, function(data) {
        	$('#modal_bg', false).css('visibility', 'visible').css('opacity', 1);
        	$('#modal_wrap', false).css('visibility', 'visible').css('opacity', 1).innerHTML = data;
            document.body.style.overflow = 'hidden';
            onBodyResize();
            dd.init();
            modal.active = true;
        });
    },

    hide: function() {
        if(modal.active) {
            document.body.style.overflow = '';
            $('#modal_bg', false).removeAttribute('style'),
            $('#modal_wrap', false).removeAttribute('style');
            modal.active = false;
        } return;
    }, 

    active: false

}

var HTML = {
	escape: function(html) {
		escaper.textContent = html;
    	return escaper.innerHTML;
	},

	unescape: function(html) {
		escaper.innerHTML = html;
    	return escaper.textContent;
	}
}

function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

function onBodyResize() {
	if(modal.active) { // modal resize
        if($('.modal-box', $('#modal_wrap', false), false).offsetHeight > window.innerHeight) {
            $('.modal-box', $('#modal_wrap', false), false).style.top = $('.modal-box', $('#modal_wrap', false), false).offsetHeight / 2;
        } else $('.modal-box', $('#modal_wrap', false), false).style.top = '';

        if($('.modal-box', $('#modal_wrap', false), false).offsetWidth > window.innerWidth) {
            $('.modal-box', $('#modal_wrap', false), false).style.left = $('.modal-box', $('#modal_wrap', false), false).offsetWidth / 2;
        } else $('.modal-box', $('#modal_wrap', false), false).style.left = '';
    }
    scroll.scroll();
}

var isSafari = navigator.vendor && navigator.vendor.indexOf('Apple') > -1 &&
               navigator.userAgent && !navigator.userAgent.match('CriOS');

document.addEventListener('DOMContentLoaded', function() {
	scroll.init();
	tooltips.init();
	tickets.init();
	dd.init();
	if(!isSafari) tickets.messageSound = new (window.Audio || window.webkitAudio)('/message.mp3');
		else tickets.messageSound = new (window.Audio || window.webkitAudio)('/message.mp3'), tickets.messageSound.preload = 'none';
});

var dd = {

    init: function() {
       	$('.dropdown-select').each(function(el) {
            var selectElement = $('select[id="' + el.getAttribute('for') + '"]', false, el.parentNode);
            if(selectElement && !$('.dropdown-variants[id="'+el.getAttribute('for')+'"]', false)) {
                if(getComputedStyle(el.parentNode)['position'] == 'static') el.parentNode.style.position = 'relative';
                var obj = $('option[value="' + selectElement.value + '"]', false, selectElement);
                el.innerHTML = obj ? obj.innerHTML : selectElement.value;
                var ddlist = document.createElement('div');
                var identifier = 0;
                ddlist.className = 'dropdown-variants';
                ddlist.style.width = el.offsetWidth * 0.93;
                ddlist.style.left = el.offsetWidth * 0.035;
                ddlist.style.top = el.offsetTop + el.offsetHeight;
                ddlist.setAttribute('id', el.getAttribute('for'));
                $('option', 1, selectElement).each(function(val) {
                    if(!val.disabled) ddlist.innerHTML += '<div class="dropdown-option" id="' + identifier + '">' + val.innerHTML + '</div>'
                    identifier++;
                }), el.parentNode.insertAdjacentHTML('beforeend', ddlist.outerHTML);
            }
            el.addEventListener('transitionend', function() {
                if(this.offsetHeight == 0) this.style.display = 'none';
            });
            el.addEventListener('click', function() {
                let dropdown = $('.dropdown-variants[id="' + this.getAttribute('for') + '"]', false, this.parentNode);
                dd.toggle(dropdown);
            });
        });
        $('.dropdown-option').each(function(el) { el.onclick = function() {
            let selectObject = $('select[id="' + this.parentNode.getAttribute('id') + '"]', false, this.parentNode.parentNode);
            let optionNum = parseInt(this.getAttribute('id'));
            selectObject.value = $('option', 1, selectObject)[optionNum].value;
            let obj = $('option[value="' + selectObject.value + '"]', false, selectObject);
            $('.dropdown-select[for="' + this.parentNode.getAttribute('id') + '"]', false, this.parentNode.parentNode).innerHTML = obj ? obj.innerHTML : selectObject.value;            
            dd.hide(this.parentNode);
        }});
    },

    toggle: function(dropdown) {
        if(dropdown.offsetHeight == 0) {
            dd.show(dropdown);
        } else {
            dd.hide(dropdown);
        }
    },

    show: function(dropdown) {
        dropdown.style.display = 'block';
        let ddMaxHeight = 0;
        $('*', true, dropdown).each(function(el) {
            ddMaxHeight += el.offsetHeight;
        }), dropdown.style.maxHeight = ddMaxHeight;
    },

    hide: function(dropdown) {
        dropdown.style.maxHeight = 0;
    }

}