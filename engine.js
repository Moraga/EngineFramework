if (!Array.prototype.filter) {
	Array.prototype.filter = function(fn) {
		if (!this || typeof fn !== 'function')
			throw new TypeError();
		
		var objects = Object(this),
			len = objects.length >>> 0,
			res = [],
			thisp = arguments[1];
		
		for (var i in objects)
			if (objects.hasOwnProperty(i) && fn.call(thisp, objects[i], i, objects))
				res.push(objects[i]);
		
		return res;
	};
}

if (!Object.keys) {
	Object.keys = function(obj) {
		var keys = [];
		for (k in obj)
			keys.push(k);
		return keys;
	};
}

/**
 * Base namespace for Engine library
 * @const
 */
var engine = {};

/**
 * Strip whitespace (or other characters) from the beginning and end of a string
 * @param {string} str The string that will be trimmed
 * @param {string} charlist Optionally, the stripped characters can also be specified using the charlist parameter
 * @return {string} Returns the trimmed string
 */
engine.trim = function(str, charlist) {
	if (!charlist)
		charlist = ' ';
	return this.ltrim(this.rtrim(str, charlist), charlist);
};

/**
 * Strip whitespace (or other characters) from the beginning of a string
 * @param {string} str The string that will be trimmed
 * @param {string} charlist Optionally, the stripped characters can also be specified using the charlist parameter
 * @return {string} Returns the trimmed string from the beginning
 */
engine.ltrim = function(str, charlist) {
	return str.replace(new RegExp('^[' + (charlist || ' ') + ']+'), '');
};

/**
 * Strip whitespace (or other characters) from the end of a string
 * @param {string} str The string that will be trimmed
 * @param {string} charlist Optionally, the stripped characters can also be specified using the charlist parameter
 * @return {string} Returns the trimmed string from the end
 */
engine.rtrim = function(str, charlist) {
	return str.replace(new RegExp('[' + (charlist || ' ') + ']+$'), '');
};

/**
 * Pad a string to a certain length with another string
 * @param {string} The input string
 * @param {number} len
 * @param {string} pad Pad string
 * @param {boolean} direction True for pad from beginning, otherwise at end
 * @return {string} Returns the padded string
 */
engine.strpad = function(str, len, pad, direction) {
	if (str.length < len) {
		// pad to multiple pad
		pad = (new Array(len - str.length + 1)).join(typeof pad != undefined ? pad : ' ');
		return direction ? pad + str : str + pad;
	}
	return str;
};

/**
 * Reverse a string
 * @param {string} str The string to be reversed
 * @return {string} Returns the reversed string
 */
engine.strrev = function(str) {
	return str.split('').reverse().join('');
};

/**
 * Make a string's first character uppercase
 * @param {string} str The input string
 * @return {string} Returns the resulting string
 */
engine.ucfirst = function(str) {
	return str.charAt(0).toUpperCase() + str.slice(1);
};

/**
 * Format a number with grouped thousands
 * @param {Number} number The number being formatted
 * @param {number} decimals Sets the number of decimal points
 * @param {string} dec_point Sets the separator for the decimal point
 * @param {string} thousands_sep Sets the thousands separator
 * @return {string} A formatted version of number
 */
engine.number_format = function(number, decimals, dec_point, thousands_sep) {
	// separate integer and decimal
	number = (number + '').split('.');
	
	// thousands
	if (number[0].length > 3)
		number[0] = number[0].split('').reverse().join('').split(/(\d{3})/).filter(function(v) {return v}).join(thousands_sep || ',').split('').reverse().join('');
	
	// decimals
	if (decimals) {
		if (!number[1])
			number[1] = '0';
		
		if (number[1].length >= decimals)
			number[1] = number[1].substr(0, decimals);
		else
			number[1] += (new Array(decimals - number.length + 1)).join('0');
	}
	else if (number[1])
		number.pop();
	
	return number.join(dec_point || '.');
};

/**
 * Strip accents
 * @param {string} str The input string
 * @return {string} Returns the string without accents
 */
engine.unaccent = function(str) {
	var tbl = {
		a: 'áàãâä',
		A: 'ÀÁÃÂÄ',
		c: 'ç',
		C: 'Ç',
		e: 'éèêë',
		E: 'ÉÈÊË',
		i: 'ìíîï',
		I: 'ÍÌÎÏ',
		o: 'óòõôö',
		O: 'ÓÒÕÔÖ',
		u: 'úùûü',
		U: 'ÚÙÛÜ'
	};
	
	// short string
	// one by one
	if (str.length < 300) {
		var tmp = '';
		for (var i=0; i < str.length; i++) {
			var c = str.charAt(i), t;
			for (k in tbl)
				if ((t = tbl[k].indexOf(c)) !== -1)
					break;
			tmp += t > -1 ? k : c;
		}
		str = tmp;
	}
	// large string
	// regexp replacement
	else
		for (k in tbl)
			str = str.replace(new RegExp('['+ tbl[k] +']', 'g'), k);
	
	return str;
};

/**
 * Generate a random integer
 * @param {number} min The lowest value to return
 * @param {number} max The highest value to return
 * @return {number} Returns a pseudo random value between min and max
 */
engine.rand = function(min, max) {
	return Math.floor(Math.random() * (max - min + 1)) + min;
};

/**
 * Generate a random string
 * @param {number} len The number of characters of the random string thats defaults to 10
 * @param {number} lvl The level of complexity of the random string thats defaults to 3
 * @return {string} Returns the pseudo random string
 */
engine.randstr = function(len, lvl) {
	var set = [
		'1234567890',
		'abcdefghijklmnopqrstuvwxyz',
		'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		'-_', ',.!@$%*()[]{}<>/\=+|'].slice(0, lvl || 3).join(''),
		max = set.length - 1;
		len = len || 10,
		ret = '';
	
	for (; len; len--, ret += set.charAt(engine.rand(0, max)));
	return ret;
};

/**
 * Formats a string following URL pattern
 * @param {string} str The input string
 * @return {string} Returns the input string as URL formatted
 */
engine.strtourl = function(str) {
	return engine.unaccent(str).replace(/[^a-z0-9-_]/ig, '-').replace(/\-{2,}/g, '-').replace(/^-|-$/g, '').toLowerCase();
};

/**
 * Sets a cookie
 * @param {string} name Cookie name
 * @param {mixed} value Cookie value
 * @param {Date|number} expires Time or days to expires the cookie
 * @param {string} path The path which the cookie will be available. Defaults to /
 * @param {string|boolean} domain Cookie domain. Defaults to document.domain
 */
engine.setCookie = function(name, value, expires, path, domain) {
	var cookie = name +'='+ escape(value) +';';
	
	if (expires) {
		// expires in days
		if (typeof expires == 'number') {
			var date = new Date();
			date.setDate(date.getDate() + expires);
			expires = date;
		}
		
		cookie += 'expires='+ expires.toGMTString() +';';
	}
	
	cookie += 'path='+ (path || '/') +';';
	
	if (domain !== false)
		cookie += 'domain='+ (typeof domain == 'string' ? domain : document.domain) +';';
	
	document.cookie = cookie;
};

/**
 * Gets a cookie value
 * @param {string} $name Cookie name
 * @return {string|null}
 */
engine.getCookie = function(name) {
	return (result = new RegExp('(?:^|; )'+ name +'=([^;]+)').exec(document.cookie)) ? result[1] : null;
};

/**
 * Deletes a cookie
 * @param {string} name Cookie name
 * @param {string} path Optional cookie path
 * @param {string} domain Optional cookie domain
 */
engine.unsetCookie = function(name, path, domain) {
	this.setCookie(name, '', -1, path || null, domain || null);
};