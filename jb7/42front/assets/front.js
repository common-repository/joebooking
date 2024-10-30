( function(){

var self = this;
var el;
var hcsName = 'hcs';
var hcsValue = 'jb7';

// https://plainjs.com/javascript/ajax/send-ajax-get-and-post-requests-47/
this.getAjax = function( url, success )
{
	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
	xhr.open( 'GET', url );
	xhr.onreadystatechange = function(){
		if (xhr.readyState>3 && xhr.status==200) success(xhr.responseText);
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.send();
	return xhr;
}
// example request
// getAjax('http://foo.bar/?p1=1&p2=Hello+World', function(data){ console.log(data); });

this.postAjax = function( url, data, success )
{
	var params = typeof data == 'string' ? data : Object.keys(data).map(
		function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
		).join('&');

	var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	xhr.open( 'POST', url );
	xhr.onreadystatechange = function(){
		if (xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send(params);
	return xhr;
}

// example request
// postAjax('http://foo.bar/', 'p1=1&p2=Hello+World', function(data){ console.log(data); });

// example request with data object
// postAjax('http://foo.bar/', { p1: 1, p2: 'Hello World' }, function(data){ console.log(data); });

this.serializeForm = function( form )
{
	var field, l, s = [];
	if (typeof form == 'object' && form.nodeName == "FORM") {
		var len = form.elements.length;
		for (var i=0; i<len; i++) {
			field = form.elements[i];
			if (field.name && !field.disabled && field.type != 'file' && field.type != 'reset' && field.type != 'submit' && field.type != 'button') {
				if (field.type == 'select-multiple') {
					l = form.elements[i].options.length; 
					for (var j=0; j<l; j++) {
						if(field.options[j].selected)
							s[s.length] = encodeURIComponent(field.name) + "=" + encodeURIComponent(field.options[j].value);
					}
				} else if ((field.type != 'checkbox' && field.type != 'radio') || field.checked) {
					s[s.length] = encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value);
				}
			}
		}
	}
	return s.join('&').replace(/%20/g, '+');
}

this.setLoader = function( el )
{
	var loader = document.createElement( 'div' );
	loader.className = 'hc-loader';
	var shader = document.createElement( 'div' );
	shader.className = 'hc-loader-shader';

	el.setAttribute( 'style', 'position: relative;' );
	el.appendChild( loader );
	el.appendChild( shader );
}

this.unsetLoader = function( el )
{
	var ii = 0;

	var shaders = el.getElementsByClassName( 'hc-loader-shader' );
	for( ii = 0; ii < shaders.length; ii++ ){
		shaders[ii].parentNode.removeChild( shaders[ii] );
	}

	var loaders = el.getElementsByClassName( 'hc-loader' );
	for( ii = 0; ii < loaders.length; ii++ ){
		loaders[ii].parentNode.removeChild( shaders[ii] );
	}
}

this.linkClick = function( e ){
	// var href = e.target.getAttribute('data-href');
	var goAjax = e.target.getAttribute('data-ajax');
	if( goAjax ){
		var href = e.target.getAttribute('href');
		href += ( href.indexOf('?') > -1 ) ? '&' : '?';
		href += hcsName + '=' + hcsValue;

		e.preventDefault();
		self.setLoader( el );

		self.getAjax( href, function(data){
			el.innerHTML = data;
			self.scan( el );
			self.unsetLoader( el );
			// el.scrollIntoView();
			// console.log( data );
		});

		return false;
	}
};

this.formSubmit = function( e ){
	// var href = e.target.getAttribute('data-action');
	var goAjax = e.target.getAttribute('data-ajax');
	if( goAjax ){
		var href = e.target.getAttribute('action');
		href += ( href.indexOf('?') > -1 ) ? '&' : '?';
		href += hcsName + '=' + hcsValue;

		e.preventDefault();

		self.setLoader( el );
		var data = self.serializeForm( e.target );
		data += '&' + hcsName + '=' + hcsValue;

		self.postAjax( href, data, function(data){
			// console.log( data );
			el.innerHTML = data;
			self.scan( el );
			self.unsetLoader( el );
			// el.scrollIntoView();
		});

		return false;
	}
};

this.scan = function( el ){
	var ii = 0;

	var allLinks = el.getElementsByTagName('a');
	for( ii = 0; ii < allLinks.length; ii++ ){
		allLinks[ii].addEventListener( 'click', self.linkClick );
	}

	var submitForms = el.getElementsByTagName('form');
	for( ii = 0; ii < submitForms.length; ii++ ){
		submitForms[ii].addEventListener( 'submit', self.formSubmit );
	}

	// run inline JavaScript
	var scripts = el.getElementsByTagName('script');
	for( ii = 0; ii < scripts.length; ii++ ){
		var src = scripts[ii].getAttribute('src');
		if( src ){
			var s = document.createElement('script');
			s.setAttribute( 'src', src );
			document.head.appendChild( s );
		}
		else {
			eval( scripts[ii].innerHTML );
		}
	}
	// jQuery(this).find('script').each( function(){
		// eval( jQuery(this).text() );
	// });
}

document.addEventListener('DOMContentLoaded', function()
{
	el = document.getElementById('jb7-front');
	// el = document.getElementById('jb7-front/new');
	self.scan( el );
});

})();
