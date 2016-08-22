(function() {

var opacity = 0;
var maxOpacity = 90;
var delay = 1;
var targetId = 'letskillie6';
var flag = 'false';
var cssUrl = 'javascript/style.zh.css';
var offset = -165;
var html = '<div id="letskillie6"><div class="r4"></div><div class="r2"></div><div class="r1"></div><div class="r1"></div><div class="content"><a rel="nofollow" id="letskillie6_close" href="">Close</a><span class="pic"></span><p><strong>虽然不太清楚是怎么回事</strong><BR />但如果您能放弃使用 IE  <BR /><strong>而使用更厉害的浏览器</strong><BR />本站将能提供更优质的服务</p><div class="fixed"></div><p class="browsers"><a rel="nofollow" class="chrome" href="http://www.google.com/chrome/">Chrome</a><a rel="nofollow" class="firefox" href="http://www.mozilla.com/">Firefox</a><a rel="nofollow" class="opera" href="http://www.opera.com/">Opera</a><a rel="nofollow" class="safari" href="http://www.apple.com/safari/">Safari</a><div class="fixed"></div></p><p class="meta">说实话,这个提示框做的实在是太棒了!(图片仅供参考)</p></div><div class="r1"></div><div class="r1"></div><div class="r2"></div><div class="r4"></div></div>';

function cookie(name, value, day) {
	if (typeof value != 'undefined') {
		if(typeof LETSKILLIE6_DELAY != 'undefined' && LETSKILLIE6_DELAY != null) {
			day = LETSKILLIE6_DELAY;
		}
		if (value === null) {
			value = '';
		}
		var expires = '';

		date = new Date();
		date.setTime(date.getTime() + (day * 86400000));
		expires = '; expires=' + date.toUTCString();

		var path = '';
		var domain = '';
		var secure = '';
		document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
	} else {
		var cookieValue = null;
		if (document.cookie && document.cookie != '') {
			var cookies = document.cookie.split(';');
			for (var i = 0; i < cookies.length; i++) {
				var cookie = cookies[i];
				while(cookie.charAt(0) == ' ') {
					cookie = cookie.substring(1, cookie.length);
				}
				if (cookie.substring(0, name.length + 1) == (name + '=')) {
					cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
					break;
				}
			}
		}
		return cookieValue;
	}
}

function float(id) {
	var target = document.getElementById(id);
	target.style.top = (document.documentElement.scrollTop
				 + document.documentElement.clientHeight
				 + offset) + 'px';
	setTimeout('HKIE6.float("' + id + '")', 100);
}

function show(id) {
	var obj = document.getElementById(id);
	opacity = 0;
	obj.style.filter = 'alpha(opacity=' + opacity +')';
	obj.style.display = 'block';
	tid = setInterval('HKIE6.fadeIn("' + id + '")', 40);
}

function hide(id) {
	var obj = document.getElementById(id);
	tid = setInterval('HKIE6.fadeOut("' + id + '")', 20);
}

function fadeIn(id) {
	var obj = document.getElementById(id);
	opacity += 5;
	if(opacity >= maxOpacity) {
		opacity = maxOpacity;
		clearTimeout(tid);
	}
	obj.style.filter = 'alpha(opacity=' + opacity +')';
}

function fadeOut(id) {
	var obj = document.getElementById(id);
	opacity -= 5;
	if(opacity <= 0) {
		opacity = 0;
		clearTimeout(tid);
		obj.style.display = 'none';
	}
	obj.style.filter = 'alpha(opacity=' + opacity +')';
}

function popup(obj) {
	window.open(obj.href);
}

function loadCss(url) {
	var head = document.getElementsByTagName("head")[0];
	var cssNode = document.createElement('link');
	cssNode.type = 'text/css';
	cssNode.rel = 'stylesheet';
	cssNode.href = url;
	cssNode.media = 'screen';
	head.appendChild(cssNode);
}

if(cookie(targetId) != flag) {
	loadCss(cssUrl);
	document.write(html);

	var closeButton = document.getElementById('letskillie6_close');
	closeButton.onclick = function(){
		hide(targetId);
		cookie(targetId, flag, delay);
		return false;
	};

	var links = document.getElementById(targetId).getElementsByTagName('a');
	for(var i=0; i<links.length; i++) {
		if(links[i].id != 'letskillie6_close') {
			links[i].onclick = function(){popup(this);return false;};
		}
	}

	show(targetId);
	float(targetId);
}

window['HKIE6'] = {};
window['HKIE6']['fadeIn'] = fadeIn;
window['HKIE6']['fadeOut'] = fadeOut;
window['HKIE6']['float'] = float;

}) ();