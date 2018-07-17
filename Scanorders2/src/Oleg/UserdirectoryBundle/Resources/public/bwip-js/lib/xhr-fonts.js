// bwipjs/lib/xhr-fonts.js
//
// Creates the browser global bwipjs_fonts font manager and demand-loads
// fonts using XHR.

var bwipjs_fonts = {
	monocolor: false,	// aka monochrome
	fontsets: [],
	names: {},
	toload:	{},
};

function bwipjsGetFontDit() {
	var fontDir = null;
	// Get the url to this script.  The fonts will be in: ../fonts/
	if(0) {
		//var srcpath = document.querySelector('script[src$="xhr-fonts.js"]').getAttribute('src');
		var srcpath = "/order/bundles/oleguserdirectory/bwip-js/lib/xhr-fonts.js";
		fontDir = srcpath.replace(/lib\/xhr-fonts.js$/, 'fonts/');
		//correct url: bwipjs_fonts.fontdir=/order/bundles/oleguserdirectory/bwip-js/fonts/
	} else {
		var srcpath = document.querySelector('script[src*="xhr-fonts.js"]').getAttribute('src');
		//var srcpath = "/order/bundles/oleguserdirectory/bwip-js/lib/xhr-fonts.js";
		console.log("srcpath="+srcpath);
		//order/bundles/oleguserdirectory/bwip-js/lib/xhr-fonts.js?1531837781
		var urlArr = srcpath.split("lib/xhr-fonts.js");
		if( urlArr.length > 0 ) {
			fontDir = urlArr[0]+"fonts/";
		}
	}
	console.log("fontDir="+fontDir);

	return fontDir;
}

// Load the font metrics
(function() {
	// Get the url to this script.  The fonts will be in: ../fonts/
	//var srcpath = document.querySelector('script[src$="xhr-fonts.js"]').getAttribute('src');
	//bwipjs_fonts.fontdir = srcpath.replace(/lib\/xhr-fonts.js$/, 'fonts/');

	// Get the url to this script.  The fonts will be in: ../fonts/
	// if(0) {
	// 	//var srcpath = document.querySelector('script[src$="xhr-fonts.js"]').getAttribute('src');
	// 	var srcpath = "/order/bundles/oleguserdirectory/bwip-js/lib/xhr-fonts.js";
	// 	bwipjs_fonts.fontdir = srcpath.replace(/lib\/xhr-fonts.js$/, 'fonts/');
	// 	//correct url: bwipjs_fonts.fontdir=/order/bundles/oleguserdirectory/bwip-js/fonts/
	// } else {
	// 	var srcpath = document.querySelector('script[src*="xhr-fonts.js"]').getAttribute('src');
	// 	//var srcpath = "/order/bundles/oleguserdirectory/bwip-js/lib/xhr-fonts.js";
	// 	console.log("srcpath="+srcpath);
	// 	///order/bundles/oleguserdirectory/bwip-js/lib/xhr-fonts.js?1531837781
	// 	//bwipjs_fonts.fontdir = srcpath.replace(/lib\/xhr-fonts.js$/, 'fonts/');
	// 	//bwipjs_fonts.fontdir = srcpath.replace(/lib\/xhr-fonts.js$/, 'fonts/');
	// 	var urlArr = srcpath.split("lib/xhr-fonts.js");
	// 	if( urlArr.length > 0 ) {
	// 		bwipjs_fonts.fontdir = urlArr[0]+"fonts/";
	// 	}
	// }
	bwipjs_fonts.fontdir = bwipjsGetFontDit();
	console.log("bwipjs_fonts.fontdir="+bwipjs_fonts.fontdir);

	// Begin loading the font metrics
	metrics(0);

	function metrics(id) {
		var xhr = new XMLHttpRequest;
		xhr.open('GET', bwipjs_fonts.fontdir + 'fnt' + id + '-desc.js', true);
		xhr.onreadystatechange = function () {
			// The description files always start with //
			if (xhr.status == 200 && xhr.readyState == 4 &&
					/^\s*\/\//.test(xhr.responseText)) {
				eval(xhr.responseText);
				metrics(id+1);
			}
		}
		xhr.onerror = function (e) {
			console.log(e);
		}
		xhr.send(null);
	}
})();

bwipjs_fonts.lookup = function lookup(name) {
	var fontid = this.names[name.toUpperCase()];
	return fontid === undefined ? 1 : fontid;		// OCR B default
}

bwipjs_fonts.monochrome = function monochrome(mono) {
	this.monocolor = mono;
}

bwipjs_fonts.loadfonts = function loadfonts(callback) {
	var count = 0;
	for (var temp in this.toload) {
		(function(fontpath, font) {
			var xhr = new XMLHttpRequest;
			xhr.open('GET', bwipjs_fonts.fontdir + fontpath, true);
			xhr.responseType = 'arraybuffer';
			xhr.onload = function(e) {
				if (xhr.status == 200) {
					font.bytes = new Uint8Array(xhr.response);
					font.view  = new DataView(xhr.response);
				}
				if (--count == 0) {
					callback();
				}
			}

			count++;
			xhr.send(null);
		})(temp, this.toload[temp]);
	}
	this.toload = {};

	if (!count) {
		callback();
	}
}


// `width` and `height` are the same size, scaled respectively by the
// x,y factors.  Because bar code width is what we care about, font
// width is the only metric we use to match the font.
bwipjs_fonts.getglyph = function getglyph(fontid, charcode, width, height) {
	if (fontid < 0 || fontid >= this.fontsets.length) {
		fontid = 1;		// OCR B default
	}
	var fontset = this.fontsets[fontid];
	if (width < fontset.minsize) {
		width = fontset.minsize;
	} else if (width > fontset.maxsize) {
		width = fontset.maxsize;
	}

	// Find the largest size not exceeding
	var size = width;
	while (size > 5 && !fontset.sizes[size]) {
		size--;
	}
	if (size == 5) {
		throw 'font-size not supported (' + width + ')';
	}

	var key  = (this.monocolor ? 'm' : 'a') + size;
	var font = fontset.fonts[key];

	// Need to demand-load the font bitmap?
	if (!font) {
		fontset.fonts[key] = font = { glyphs:[] };
		this.toload['fnt' + fontid + (this.monocolor ? 'm-' : 'a-') + size + '.bin'] = font;
	}

	// Convert the charcode to a glyph index.  Simple 7-bit printable ascii.  No special mapping supported.
	if (charcode < fontset.minchar || charcode > fontset.maxchar) {
		charcode = fontset.minchar;
	}
	charcode -= fontset.minchar;

	var glyph = font.glyphs[charcode];
	if (!glyph) {
		var glidx = charcode * 5;
		var metrics = fontset.metrics[key];
		font.glyphs[charcode] = glyph = {
				advance: metrics[glidx + 0],
				top:	 metrics[glidx + 1],
				left:	 metrics[glidx + 2],
				width:	 metrics[glidx + 3],
				height:	 metrics[glidx + 4],
			};

		// The maxicode bulls-eye is generated with left=0, top=height, but needs to be
		// centered on 0,0
		//if (fontid == 2 && charcode == 4) {
		//	glyph.top  -= ~~(glyph.height / 2);
		//	glyph.left -= ~~(glyph.width / 2);
		//}

	}

	// Once the glyph bitmaps have been loaded, we can initialize the remainder of each glyph
	if (!glyph.offset && font.bytes) {
		glyph.offset = font.view.getUint32(charcode * 4, true) + 10;
		glyph.bytes  = font.bytes;
	}

	return glyph;
}

