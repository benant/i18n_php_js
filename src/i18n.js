(() => {
    const in_array = (val, array) => {
        for (i in array) {
            if (array[i] == val) return true;
        }
        return false;
	}
	const getCookie = (name) => {
		var nameOfCookie = name + "=";
		var x = 0;
		while(x <= document.cookie.length) {
			var y = (x+nameOfCookie.length);
			if(document.cookie.substring( x, y ) == nameOfCookie ){
			if((endOfCookie=document.cookie.indexOf( ";", y )) == -1)
				endOfCookie = document.cookie.length;
				var r = unescape( document.cookie.substring( y, endOfCookie ) );
				return (r=="undefined") ? '' : r;
			}
			x = document.cookie.indexOf( " ", x ) + 1;
			if(x == 0) break;
		}
		return "";
	}
	const setCookie = (name, value, expiredays) => {
		var todayDate = new Date();
		todayDate.setDate( todayDate.getDate() + expiredays );
		document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";"
	}
    const support_lang = ['ko','en','zh','ja','th','vi','km','uz','my'],
		default_lang = 'en';
    var lang_data = {},
        lang = navigator.language || navigator.userLanguage,
        cookielang = getCookie('lang');
    lang = lang.substr(0, 2);
    lang = in_array(lang, support_lang) ? lang : default_lang;
    lang = cookielang && cookielang !== lang && in_array(cookielang, support_lang) ? cookielang : lang;
    if (cookielang !== lang) {
        setCookie('lang', lang, 365);
	}
	if (window.lang !== lang) {
		window.lang = lang;
	}

    const get_lang_data = (callback) => {
        // console.log('번역 언어:', lang);
        let data_file = '/i18n/' + lang + '/LC_MESSAGES/WWW.json';
        console.log('data_file:', data_file);
        httpRequest = new XMLHttpRequest();
        if (httpRequest) {
            httpRequest.onreadystatechange = function() {
                if (httpRequest.readyState === XMLHttpRequest.DONE) {
                    if (httpRequest.status === 200) {
                        r = JSON.parse(httpRequest.responseText);
						lang_data = r.data;
                    } else {
                        console.error('번역 데이터 가져오지 못함.');
					}
					if(callback) {callback();}
                }
            };
            httpRequest.open('GET', data_file);
            httpRequest.send();
        }
	}
	get_lang_data();
	window.__ = (key) => {
		return lang_data && typeof lang_data[key] != typeof undefined && lang_data[key] ? lang_data[key] : key;
	};
	window._e = (key) => {
		document.write(__(key));
	};
	window._c = (l, callback) => {
		if(!in_array(l, support_lang)) {l=default_lang;}
		if(l!=lang) {
			lang = l;
			setCookie('lang', l, 365);
			get_lang_data(callback);
		}
	}
	window._l = () => {
		return lang;
	}

})();