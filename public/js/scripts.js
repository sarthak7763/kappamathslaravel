var isMobile = mobileAndTabletcheck();

function spread(source, obj) {
	var result = {};
	
	for (var key in source) {
		result[key] = source[key];
	}

	for (var key in obj) {
		result[key] = obj[key];
	}

	return result;
}

function removeElement($element) {
	var $parent = $element.parentNode;
	if ($parent) {
		$parent.removeChild($element);
	}
}

function wrs_urlencode(clearString) {
	var output = '';
	var x = 0;
	clearString = clearString.toString();
	var regex = /(^[a-zA-Z0-9_.]*)/;
	
	var clearString_length = ((typeof clearString.length) == 'function') ? clearString.length() : clearString.length;

	while (x < clearString_length) {
		var match = regex.exec(clearString.substr(x));
		if (match != null && match.length > 1 && match[1] != '') {
			output += match[1];
			x += match[1].length;
		}
		else {
			var charCode = clearString.charCodeAt(x);
			var hexVal = charCode.toString(16);
			output += '%' + ( hexVal.length < 2 ? '0' : '' ) + hexVal.toUpperCase();
			++x;
		}
	}
	
	return output;
}

function wrs_mathmlEntities(mathml) {
	var toReturn = '';
	
	for (var i = 0; i < mathml.length; ++i) {
		//parsing > 128 characters
		if (mathml.charCodeAt(i) > 128) {
			toReturn += '&#' + mathml.charCodeAt(i) + ';';
		}
		else {
			toReturn += mathml.charAt(i);
		}
	}

	return toReturn;
}

function openResource(url, mathml) {
	wnd = window.open(url + '?mml=' + wrs_urlencode(wrs_mathmlEntities(mathml)) + '&backgroundColor=%23fff',"new_window","width=350,height=200,location=0,status=0,toolbar=0,top=100,left=500");
	wnd.focus();
}


function setLanguage() {
	var i, str;
	str=""+location;
	i=str.lastIndexOf("/index.html",i);
	if (str[i-3]=="/") {
		// .../xx/demo.html
		str = str.substring(i-2,i)
		setCookie("lang",str,1);
	}

}

function changeLanguage(lang, page) {
	if (lang.length>0) {
		location.href="../"+lang+"/"+page;
	}
}

function getMathML(latex) {
	var req = new XMLHttpRequest();
	req.open("POST",js_path+"/latex2mathml", false);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	var params = "latex="+encodeURIComponent(latex);
	req.send(params);
	if (req.status != 200)  {
		return "Error generating MathML.";
	}
	return req.responseText;
}

function getLaTeX(mathml, callback) {
	var req = new XMLHttpRequest();
	req.open("POST",js_path+"/mathml2latex", false);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	var params = "mml="+encodeURIComponent(mathml);

	req.onreadystatechange = function () {
		if (req.readyState == 4) {
			if (req.status != 200)  {
				callback("Error generating LaTeX.");
			}
			else {
				callback(req.responseText);
			}
		}
	}

	req.send(params);
}

function getAccessible(mathml, callback, lang) {
	var req = new XMLHttpRequest();
	req.open("POST",js_path+"/mathml2accessible", true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	var params = "mml="+encodeURIComponent(mathml)+"&lang="+lang;

	req.onreadystatechange = function () {
		if (req.readyState == 4) {
			if (req.status != 200)  {
				callback("Error generating accessible text.");
			}
			else {
				callback(req.responseText);
			}
		}
	}

	req.send(params);
}

function getMathMLFromAccessible(accessible) {
	var req = new XMLHttpRequest();
	req.open("POST",js_path+"/accessible2mathml", false);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	var params = "accessible="+encodeURIComponent(accessible) + '&lang=en';

	req.send(params);
	if (req.status != 200)  {
		return "Error generating MathML.";
	}
	return req.responseText;
}

function getParameter(param, deft)
{
	var i, str;
	str=""+location;
	i=str.indexOf(param+"=");
	if (i>=0)
	{
		str=str.substr(i+param.length+1);
		i=str.indexOf("&");
		if (i>=0) str=str.substring(0,i);
		str=str.replace(/\+/g," ");
		return decodeURIComponent(str);
	}
	else
	{
		return deft;
	}
}
function getCookie(c_name)
{
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++)
	{
		x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		x=x.replace(/^\s+|\s+$/g,"");
		if (x==c_name)
		{
			return unescape(y);
		}
	}
}
function setCookie(c_name,value,exdays,path)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	c_value += ";path=/";
	document.cookie=c_name + "=" + c_value;
}
function changeToolbar(toolbar, instance)
{
	com.wiris.jsEditor.JsEditor.getInstance(document.getElementById(instance)).setParams({'toolbar': toolbar});
}

function mobileAndTabletcheck() {
	var check = false;
	// eslint-disable-next-line
	(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
	
	return check;
}


function swicthHandPalette() {
	var buttons = document.getElementsByClassName('wrs_handWrapper');
	for (var i = 0; i < buttons.length; ++i) buttons[i].lastChild.click();
}

// --- Polyfills

// Promises from taylorhakes/promise-polyfill
!function(e,n){"object"==typeof exports&&"undefined"!=typeof module?n():"function"==typeof define&&define.amd?define(n):n()}(0,function(){"use strict";function e(e){var n=this.constructor;return this.then(function(t){return n.resolve(e()).then(function(){return t})},function(t){return n.resolve(e()).then(function(){return n.reject(t)})})}function n(e){return!(!e||"undefined"==typeof e.length)}function t(){}function o(e){if(!(this instanceof o))throw new TypeError("Promises must be constructed via new");if("function"!=typeof e)throw new TypeError("not a function");this._state=0,this._handled=!1,this._value=undefined,this._deferreds=[],c(e,this)}function r(e,n){for(;3===e._state;)e=e._value;0!==e._state?(e._handled=!0,o._immediateFn(function(){var t=1===e._state?n.onFulfilled:n.onRejected;if(null!==t){var o;try{o=t(e._value)}catch(r){return void f(n.promise,r)}i(n.promise,o)}else(1===e._state?i:f)(n.promise,e._value)})):e._deferreds.push(n)}function i(e,n){try{if(n===e)throw new TypeError("A promise cannot be resolved with itself.");if(n&&("object"==typeof n||"function"==typeof n)){var t=n.then;if(n instanceof o)return e._state=3,e._value=n,void u(e);if("function"==typeof t)return void c(function(e,n){return function(){e.apply(n,arguments)}}(t,n),e)}e._state=1,e._value=n,u(e)}catch(r){f(e,r)}}function f(e,n){e._state=2,e._value=n,u(e)}function u(e){2===e._state&&0===e._deferreds.length&&o._immediateFn(function(){e._handled||o._unhandledRejectionFn(e._value)});for(var n=0,t=e._deferreds.length;t>n;n++)r(e,e._deferreds[n]);e._deferreds=null}function c(e,n){var t=!1;try{e(function(e){t||(t=!0,i(n,e))},function(e){t||(t=!0,f(n,e))})}catch(o){if(t)return;t=!0,f(n,o)}}var a=setTimeout;o.prototype["catch"]=function(e){return this.then(null,e)},o.prototype.then=function(e,n){var o=new this.constructor(t);return r(this,new function(e,n,t){this.onFulfilled="function"==typeof e?e:null,this.onRejected="function"==typeof n?n:null,this.promise=t}(e,n,o)),o},o.prototype["finally"]=e,o.all=function(e){return new o(function(t,o){function r(e,n){try{if(n&&("object"==typeof n||"function"==typeof n)){var u=n.then;if("function"==typeof u)return void u.call(n,function(n){r(e,n)},o)}i[e]=n,0==--f&&t(i)}catch(c){o(c)}}if(!n(e))return o(new TypeError("Promise.all accepts an array"));var i=Array.prototype.slice.call(e);if(0===i.length)return t([]);for(var f=i.length,u=0;i.length>u;u++)r(u,i[u])})},o.resolve=function(e){return e&&"object"==typeof e&&e.constructor===o?e:new o(function(n){n(e)})},o.reject=function(e){return new o(function(n,t){t(e)})},o.race=function(e){return new o(function(t,r){if(!n(e))return r(new TypeError("Promise.race accepts an array"));for(var i=0,f=e.length;f>i;i++)o.resolve(e[i]).then(t,r)})},o._immediateFn="function"==typeof setImmediate&&function(e){setImmediate(e)}||function(e){a(e,0)},o._unhandledRejectionFn=function(e){void 0!==console&&console&&console.warn("Possible Unhandled Promise Rejection:",e)};var l=function(){if("undefined"!=typeof self)return self;if("undefined"!=typeof window)return window;if("undefined"!=typeof global)return global;throw Error("unable to locate global object")}();"Promise"in l?l.Promise.prototype["finally"]||(l.Promise.prototype["finally"]=e):l.Promise=o});

// Fetch from github/fetch
(function(self) {
	'use strict';
  
	if (self.fetch) {
	  return
	}
  
	var support = {
	  searchParams: 'URLSearchParams' in self,
	  iterable: 'Symbol' in self && 'iterator' in Symbol,
	  blob: 'FileReader' in self && 'Blob' in self && (function() {
		try {
		  new Blob()
		  return true
		} catch(e) {
		  return false
		}
	  })(),
	  formData: 'FormData' in self,
	  arrayBuffer: 'ArrayBuffer' in self
	}
  
	if (support.arrayBuffer) {
	  var viewClasses = [
		'[object Int8Array]',
		'[object Uint8Array]',
		'[object Uint8ClampedArray]',
		'[object Int16Array]',
		'[object Uint16Array]',
		'[object Int32Array]',
		'[object Uint32Array]',
		'[object Float32Array]',
		'[object Float64Array]'
	  ]
  
	  var isDataView = function(obj) {
		return obj && DataView.prototype.isPrototypeOf(obj)
	  }
  
	  var isArrayBufferView = ArrayBuffer.isView || function(obj) {
		return obj && viewClasses.indexOf(Object.prototype.toString.call(obj)) > -1
	  }
	}
  
	function normalizeName(name) {
	  if (typeof name !== 'string') {
		name = String(name)
	  }
	  if (/[^a-z0-9\-#$%&'*+.\^_`|~]/i.test(name)) {
		throw new TypeError('Invalid character in header field name')
	  }
	  return name.toLowerCase()
	}
  
	function normalizeValue(value) {
	  if (typeof value !== 'string') {
		value = String(value)
	  }
	  return value
	}
  
	// Build a destructive iterator for the value list
	function iteratorFor(items) {
	  var iterator = {
		next: function() {
		  var value = items.shift()
		  return {done: value === undefined, value: value}
		}
	  }
  
	  if (support.iterable) {
		iterator[Symbol.iterator] = function() {
		  return iterator
		}
	  }
  
	  return iterator
	}
  
	function Headers(headers) {
	  this.map = {}
  
	  if (headers instanceof Headers) {
		headers.forEach(function(value, name) {
		  this.append(name, value)
		}, this)
	  } else if (Array.isArray(headers)) {
		headers.forEach(function(header) {
		  this.append(header[0], header[1])
		}, this)
	  } else if (headers) {
		Object.getOwnPropertyNames(headers).forEach(function(name) {
		  this.append(name, headers[name])
		}, this)
	  }
	}
  
	Headers.prototype.append = function(name, value) {
	  name = normalizeName(name)
	  value = normalizeValue(value)
	  var oldValue = this.map[name]
	  this.map[name] = oldValue ? oldValue+','+value : value
	}
  
	Headers.prototype['delete'] = function(name) {
	  delete this.map[normalizeName(name)]
	}
  
	Headers.prototype.get = function(name) {
	  name = normalizeName(name)
	  return this.has(name) ? this.map[name] : null
	}
  
	Headers.prototype.has = function(name) {
	  return this.map.hasOwnProperty(normalizeName(name))
	}
  
	Headers.prototype.set = function(name, value) {
	  this.map[normalizeName(name)] = normalizeValue(value)
	}
  
	Headers.prototype.forEach = function(callback, thisArg) {
	  for (var name in this.map) {
		if (this.map.hasOwnProperty(name)) {
		  callback.call(thisArg, this.map[name], name, this)
		}
	  }
	}
  
	Headers.prototype.keys = function() {
	  var items = []
	  this.forEach(function(value, name) { items.push(name) })
	  return iteratorFor(items)
	}
  
	Headers.prototype.values = function() {
	  var items = []
	  this.forEach(function(value) { items.push(value) })
	  return iteratorFor(items)
	}
  
	Headers.prototype.entries = function() {
	  var items = []
	  this.forEach(function(value, name) { items.push([name, value]) })
	  return iteratorFor(items)
	}
  
	if (support.iterable) {
	  Headers.prototype[Symbol.iterator] = Headers.prototype.entries
	}
  
	function consumed(body) {
	  if (body.bodyUsed) {
		return Promise.reject(new TypeError('Already read'))
	  }
	  body.bodyUsed = true
	}
  
	function fileReaderReady(reader) {
	  return new Promise(function(resolve, reject) {
		reader.onload = function() {
		  resolve(reader.result)
		}
		reader.onerror = function() {
		  reject(reader.error)
		}
	  })
	}
  
	function readBlobAsArrayBuffer(blob) {
	  var reader = new FileReader()
	  var promise = fileReaderReady(reader)
	  reader.readAsArrayBuffer(blob)
	  return promise
	}
  
	function readBlobAsText(blob) {
	  var reader = new FileReader()
	  var promise = fileReaderReady(reader)
	  reader.readAsText(blob)
	  return promise
	}
  
	function readArrayBufferAsText(buf) {
	  var view = new Uint8Array(buf)
	  var chars = new Array(view.length)
  
	  for (var i = 0; i < view.length; i++) {
		chars[i] = String.fromCharCode(view[i])
	  }
	  return chars.join('')
	}
  
	function bufferClone(buf) {
	  if (buf.slice) {
		return buf.slice(0)
	  } else {
		var view = new Uint8Array(buf.byteLength)
		view.set(new Uint8Array(buf))
		return view.buffer
	  }
	}
  
	function Body() {
	  this.bodyUsed = false
  
	  this._initBody = function(body) {
		this._bodyInit = body
		if (!body) {
		  this._bodyText = ''
		} else if (typeof body === 'string') {
		  this._bodyText = body
		} else if (support.blob && Blob.prototype.isPrototypeOf(body)) {
		  this._bodyBlob = body
		} else if (support.formData && FormData.prototype.isPrototypeOf(body)) {
		  this._bodyFormData = body
		} else if (support.searchParams && URLSearchParams.prototype.isPrototypeOf(body)) {
		  this._bodyText = body.toString()
		} else if (support.arrayBuffer && support.blob && isDataView(body)) {
		  this._bodyArrayBuffer = bufferClone(body.buffer)
		  // IE 10-11 can't handle a DataView body.
		  this._bodyInit = new Blob([this._bodyArrayBuffer])
		} else if (support.arrayBuffer && (ArrayBuffer.prototype.isPrototypeOf(body) || isArrayBufferView(body))) {
		  this._bodyArrayBuffer = bufferClone(body)
		} else {
		  throw new Error('unsupported BodyInit type')
		}
  
		if (!this.headers.get('content-type')) {
		  if (typeof body === 'string') {
			this.headers.set('content-type', 'text/plain;charset=UTF-8')
		  } else if (this._bodyBlob && this._bodyBlob.type) {
			this.headers.set('content-type', this._bodyBlob.type)
		  } else if (support.searchParams && URLSearchParams.prototype.isPrototypeOf(body)) {
			this.headers.set('content-type', 'application/x-www-form-urlencoded;charset=UTF-8')
		  }
		}
	  }
  
	  if (support.blob) {
		this.blob = function() {
		  var rejected = consumed(this)
		  if (rejected) {
			return rejected
		  }
  
		  if (this._bodyBlob) {
			return Promise.resolve(this._bodyBlob)
		  } else if (this._bodyArrayBuffer) {
			return Promise.resolve(new Blob([this._bodyArrayBuffer]))
		  } else if (this._bodyFormData) {
			throw new Error('could not read FormData body as blob')
		  } else {
			return Promise.resolve(new Blob([this._bodyText]))
		  }
		}
  
		this.arrayBuffer = function() {
		  if (this._bodyArrayBuffer) {
			return consumed(this) || Promise.resolve(this._bodyArrayBuffer)
		  } else {
			return this.blob().then(readBlobAsArrayBuffer)
		  }
		}
	  }
  
	  this.text = function() {
		var rejected = consumed(this)
		if (rejected) {
		  return rejected
		}
  
		if (this._bodyBlob) {
		  return readBlobAsText(this._bodyBlob)
		} else if (this._bodyArrayBuffer) {
		  return Promise.resolve(readArrayBufferAsText(this._bodyArrayBuffer))
		} else if (this._bodyFormData) {
		  throw new Error('could not read FormData body as text')
		} else {
		  return Promise.resolve(this._bodyText)
		}
	  }
  
	  if (support.formData) {
		this.formData = function() {
		  return this.text().then(decode)
		}
	  }
  
	  this.json = function() {
		return this.text().then(JSON.parse)
	  }
  
	  return this
	}
  
	// HTTP methods whose capitalization should be normalized
	var methods = ['DELETE', 'GET', 'HEAD', 'OPTIONS', 'POST', 'PUT']
  
	function normalizeMethod(method) {
	  var upcased = method.toUpperCase()
	  return (methods.indexOf(upcased) > -1) ? upcased : method
	}
  
	function Request(input, options) {
	  options = options || {}
	  var body = options.body
  
	  if (input instanceof Request) {
		if (input.bodyUsed) {
		  throw new TypeError('Already read')
		}
		this.url = input.url
		this.credentials = input.credentials
		if (!options.headers) {
		  this.headers = new Headers(input.headers)
		}
		this.method = input.method
		this.mode = input.mode
		if (!body && input._bodyInit != null) {
		  body = input._bodyInit
		  input.bodyUsed = true
		}
	  } else {
		this.url = String(input)
	  }
  
	  this.credentials = options.credentials || this.credentials || 'omit'
	  if (options.headers || !this.headers) {
		this.headers = new Headers(options.headers)
	  }
	  this.method = normalizeMethod(options.method || this.method || 'GET')
	  this.mode = options.mode || this.mode || null
	  this.referrer = null
  
	  if ((this.method === 'GET' || this.method === 'HEAD') && body) {
		throw new TypeError('Body not allowed for GET or HEAD requests')
	  }
	  this._initBody(body)
	}
  
	Request.prototype.clone = function() {
	  return new Request(this, { body: this._bodyInit })
	}
  
	function decode(body) {
	  var form = new FormData()
	  body.trim().split('&').forEach(function(bytes) {
		if (bytes) {
		  var split = bytes.split('=')
		  var name = split.shift().replace(/\+/g, ' ')
		  var value = split.join('=').replace(/\+/g, ' ')
		  form.append(decodeURIComponent(name), decodeURIComponent(value))
		}
	  })
	  return form
	}
  
	function parseHeaders(rawHeaders) {
	  var headers = new Headers()
	  // Replace instances of \r\n and \n followed by at least one space or horizontal tab with a space
	  // https://tools.ietf.org/html/rfc7230#section-3.2
	  var preProcessedHeaders = rawHeaders.replace(/\r?\n[\t ]+/g, ' ')
	  preProcessedHeaders.split(/\r?\n/).forEach(function(line) {
		var parts = line.split(':')
		var key = parts.shift().trim()
		if (key) {
		  var value = parts.join(':').trim()
		  headers.append(key, value)
		}
	  })
	  return headers
	}
  
	Body.call(Request.prototype)
  
	function Response(bodyInit, options) {
	  if (!options) {
		options = {}
	  }
  
	  this.type = 'default'
	  this.status = options.status === undefined ? 200 : options.status
	  this.ok = this.status >= 200 && this.status < 300
	  this.statusText = 'statusText' in options ? options.statusText : 'OK'
	  this.headers = new Headers(options.headers)
	  this.url = options.url || ''
	  this._initBody(bodyInit)
	}
  
	Body.call(Response.prototype)
  
	Response.prototype.clone = function() {
	  return new Response(this._bodyInit, {
		status: this.status,
		statusText: this.statusText,
		headers: new Headers(this.headers),
		url: this.url
	  })
	}
  
	Response.error = function() {
	  var response = new Response(null, {status: 0, statusText: ''})
	  response.type = 'error'
	  return response
	}
  
	var redirectStatuses = [301, 302, 303, 307, 308]
  
	Response.redirect = function(url, status) {
	  if (redirectStatuses.indexOf(status) === -1) {
		throw new RangeError('Invalid status code')
	  }
  
	  return new Response(null, {status: status, headers: {location: url}})
	}
  
	self.Headers = Headers
	self.Request = Request
	self.Response = Response
  
	self.fetch = function(input, init) {
	  return new Promise(function(resolve, reject) {
		var request = new Request(input, init)
		var xhr = new XMLHttpRequest()
  
		xhr.onload = function() {
		  var options = {
			status: xhr.status,
			statusText: xhr.statusText,
			headers: parseHeaders(xhr.getAllResponseHeaders() || '')
		  }
		  options.url = 'responseURL' in xhr ? xhr.responseURL : options.headers.get('X-Request-URL')
		  var body = 'response' in xhr ? xhr.response : xhr.responseText
		  resolve(new Response(body, options))
		}
  
		xhr.onerror = function() {
		  reject(new TypeError('Network request failed'))
		}
  
		xhr.ontimeout = function() {
		  reject(new TypeError('Network request failed'))
		}
  
		xhr.open(request.method, request.url, true)
  
		if (request.credentials === 'include') {
		  xhr.withCredentials = true
		} else if (request.credentials === 'omit') {
		  xhr.withCredentials = false
		}
  
		if ('responseType' in xhr && support.blob) {
		  xhr.responseType = 'blob'
		}
  
		request.headers.forEach(function(value, name) {
		  xhr.setRequestHeader(name, value)
		})
  
		xhr.send(typeof request._bodyInit === 'undefined' ? null : request._bodyInit)
	  })
	}
	self.fetch.polyfill = true
  })(typeof self !== 'undefined' ? self : this);  