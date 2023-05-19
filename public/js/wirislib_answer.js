// Render script.
// This script renders non editable DOM objects.
var js = document.createElement("script");
js.type = "text/javascript";
js.src = "https://kappamaths.ezxdemo.com/generic_wiris/integration/WIRISplugins.js?viewer=image";
document.head.appendChild(js);

// Global variables. For demo purposes.
// This variables should be configurated on configuration.ini file.
// Fore more information about custom configuration see http://www.wiris.com/plugins/docs/resources/configuration-table
// We overwrite them in order to show the changes.

// Specifies how the formulas are stored in the database.
// On configuration.ini the name of the variable is wiriseditorsavemode.
var saveMode;

// Specifies how the images are displayed on the editor.
// On configuration.ini the name of variable is wiriseditoreditmode.
var editMode;

/**
 * This method simulates how the formula rendering on a non editable area using JsPluginViewer (Preview tab)
 * and formulas are stored in the database (HTML tab).
 */
 function updateanswerFunction() {
 	updatetextareaanswerpreview();
 	updateanswerlatexpreview();
 }

 function updateanswerlatexpreview() {
	// Using plugin custom method for retreving data.
	// This data is a raw data with the format defined by save mode (xml, image or base64 images).
	var answer_preview = getAnswerEditorData();
	if(answer_preview!="")
	{
		mathhtmlanswerpreview = answer_preview.replace(/<mo[^>]*>&#xA0;<\/mo[^>]*>/g,'<mspace/>');
		mathhtmlanswerpreview = mathhtmlanswerpreview.replace(/<mo[^>]*>&#160;<\/mo[^>]*>/g,'<mspace/>');

		var answerlatex = MathML2LaTeX.convert(mathhtmlanswerpreview);
	}
	else{
		var answerlatex="";
	}

    var previewlatex_div = document.getElementById("get_answer_exp_preview_latex");
	// Setting data on preview div.
	previewlatex_div.innerHTML = answerlatex;
}

 function updatetextareaanswerpreview() {
	// Using plugin custom method for retreving data.
	// This data is a raw data with the format defined by save mode (xml, image or base64 images).
	var data = getAnswerEditorData();
	// This div simulates a render page without any editor.
	var preview_div = document.getElementById("get_answer_exp_preview");
	// Setting data on preview div.
	preview_div.innerHTML = data;
	// Rendering data on preview using JsPluginViewer.
	// Set titles for images. For demo purposes.
	imgAnswerSetTitle(preview_div);
}

/**
 * Changes MathType integration save mode.
 * 1.- xml: default mode, stores formulas as mathml.
 * 2.- image: stores formulas as images.
 * 3.- base64: stores formulas as base64 images.
 *
 * This method is only for demo purposes. In order to
 * change save mode edit the configuration.ini file (wiriseditorsavemode variable).
 * See http://www.wiris.com/plugins/docs/resources/configuration-table for more information.
 */
 function AnswerchangeMode(mode) {
	// Mathml mode.
	if (mode == 'xml') {
		saveMode = 'xml';
	}
	// Image mode.
	else if (mode == 'image') {
		saveMode = 'image';
	}
	// Base64 mode.
	else if (mode == 'base64') {
		saveMode = 'base64';
		// With this variable, formulas are stored as a base64 image on the database but showed with
		// a showimage src on the editor.
		editMode = "image";
	}
	// Updating Preview and HTML tabs.
	updateanswerFunction();
}

/**
 * Auxiliary function. Highlights demo technology logo. For demo purposes.
 */
 function AnsweractivateTechLogo() {
	var wrs_tech = "php";
	var logo = document.getElementById(wrs_tech + "_logo");
 	if (logo !== null) {
		logo.style.opacity = 0.9;
	};
 }

/**
 * Set atitles for images. For demo purposes.
 *
 */
 function imgAnswerSetTitle(preview_div) {
 	var imgs = preview_div.getElementsByTagName("img");
 	for (var i = 0; i < imgs.length; i++) {
 		imgs[i].title = imgs[i].alt;

 	}
 }

 String.prototype.splice = function splice (idx, rem, str) {
 	return this.slice(0, idx) + str + this.slice(idx + Math.abs(rem));
 };

 String.prototype.getMatchIndices = function (find) {
 	var indices = [], data, exp = (typeof find == 'string' ? new RegExp(find, 'g') : find);

 	while ((data = exp.exec(this))) {
 		indices.push(data.index);
 	}

 	return indices.length ? indices : [];
 };

// Set demo's initial state.
try {
	// Use error-handling in case some resource is not available at the moment.
	AnsweractivateTechLogo();	
   } catch (error) {
	console.log('demo activation', error);
} 
function Answer_wrs_addEvent(element, event, func) {
	if (element.addEventListener) {
		element.addEventListener(event, func, false);
	}
	else if (element.attachEvent) {
		element.attachEvent('on' + event, func);
	}
}

Answer_wrs_addEvent(window, 'load', function () {
	// Hide the textarea

	var textarea = document.getElementById('answer_exp');
	textarea.style.display = 'none';

	//var example = document.getElementById('example');

	// Create the toolbar
	var toolbar = document.createElement('div');
	toolbar.id = textarea.id + '_toolbar';

	_wrs_conf_CASEnabled = false;

	// Create the WYSIWYG editor
	var iframe = document.createElement('iframe');
	iframe.id = textarea.id + '_iframe';

	Answer_wrs_addEvent(iframe, 'load', function () {
		// Setting design mode ON
		iframe.contentWindow.document.designMode = 'on';
		// Setting the content
		if (iframe.contentWindow.document.body) {
			iframe.contentWindow.document.body.innerHTML = textarea.innerHTML;
		}
		// Init MathType integration
		wrs_int_init(iframe, toolbar);
		function AnswerwaitForCore() {
			if (typeof WirisPlugin !== 'undefined') {
				// First load of display Preview and display HTML code.
				//updateoptiondFunction();
			} else {
				setTimeout(waitForCore, 200);
			}
		}
		AnswerwaitForCore();
	});

	iframe.src = 'https://kappamaths.ezxdemo.com/generic_demo.html';		// We set an empty document instead of about:blank for use relative paths for images
	iframe.width = "100%";
	iframe.height = 300;

	// Insert the WYSIWYG editor before the textarea
	textarea.parentNode.insertBefore(iframe, textarea);

	// Insert the toolbar before the WYSIWYG editor
	iframe.parentNode.insertBefore(toolbar, iframe);

	// When the user submits the form, set the textarea value with the WYSIWYG editor content
	//var form = document.getElementById('exampleForm');

	//Optiona_wrs_addEvent(form, 'submit', function () {
	// WE CALL THE PLUGIN HERE
	// textarea.value = wrs_endParse(iframe.contentWindow.document.body.innerHTML);

	//});
});


/**
 * Getting data from editor.
 * Formulas are parsed to save mode format (mathml, image or base64)
 * For more information see: http://www.wiris.com/es/plugins/docs/full-mathml-mode.
 * @return {string} Generic Plugin parsed data.
 */
 function getAnswerEditorData() {
 	var iframe = document.getElementById('answer_exp_iframe');
 	return WirisPlugin.Parser.endParse(iframe.contentWindow.document.body.innerHTML);
 }


function getanswerDataPreview(data) {
	return WirisPlugin.Parser.initParse(data);
}

function AnswerchangeDPI() {
	ls = document.getElementsByClassName('Wirisformula');
	for (i=0;i<ls.length;i++) {
		img = ls[i];
		img.width = img.clientWidth;
		img.src = img.src + "&dpi=600";
	}
}

function AnswersetParametersSpecificPlugin(wiriseditorparameters) {
	_wrs_int_wirisProperties = wiriseditorparameters;
}

 /**
 * Gets wiriseditorparameters from Generic Plugin.
 * @return {object} MathType Web parameters as JSON. An empty JSON if is not defined.
 */
 function getAnswerWirisEditorParameters() {
 	if (typeof _wrs_int_wirisProperties != 'undefined') {
 		return _wrs_int_wirisProperties;
 	}
 	return {};
 }
