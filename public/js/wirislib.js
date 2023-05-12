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
 function updateQuestionFunction() {
 	console.log('question update');
 	updatetextareaquestionpreview();
 	updatequestionlatexpreview();
 }

 function updatequestionlatexpreview() {
	// Using plugin custom method for retreving data.
	// This data is a raw data with the format defined by save mode (xml, image or base64 images).
	var question_preview = getQuestionEditorData();
	if(question_preview)
	{
		console.log(question_preview,'question_preview');

		mathhtmlpreview = question_preview.replace(/<mo[^>]*>&#xA0;<\/mo[^>]*>/g,'<mspace/>');
		mathhtmlpreview = mathhtmlpreview.replace(/<mo[^>]*> <\/mo[^>]*>/g,'<mspace/>');
		console.log(mathhtmlpreview,'mathhtmlpreview');

		var questionlatex = MathML2LaTeX.convert(mathhtmlpreview);
	}
	else{
		var questionlatex="";
	}

	console.log(questionlatex,'questionlatex');

    var previewlatex_div = document.getElementById("get_question_preview_latex");
	// Setting data on preview div.
	previewlatex_div.innerHTML = questionlatex;
}

 function updatetextareaquestionpreview() {
	// Using plugin custom method for retreving data.
	// This data is a raw data with the format defined by save mode (xml, image or base64 images).
	var data = getQuestionEditorData();
	// This div simulates a render page without any editor.
	var preview_div = document.getElementById("get_question_preview");
	// Setting data on preview div.
	preview_div.innerHTML = data;
	// Rendering data on preview using JsPluginViewer.
	// Set titles for images. For demo purposes.
	imgQuestionSetTitle(preview_div);
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
 function QuestionchangeMode(mode) {
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
	updateQuestionFunction();
}

/**
 * Auxiliary function. Highlights demo technology logo. For demo purposes.
 */
 function QuestionactivateTechLogo() {
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
 function imgQuestionSetTitle(preview_div) {
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
	QuestionactivateTechLogo();	
   } catch (error) {
	console.log('demo activation', error);
} 
function Question_wrs_addEvent(element, event, func) {
	if (element.addEventListener) {
		element.addEventListener(event, func, false);
	}
	else if (element.attachEvent) {
		element.attachEvent('on' + event, func);
	}
}

Question_wrs_addEvent(window, 'load', function () {
	// Hide the textarea

	var textarea = document.getElementById('question');
	textarea.style.display = 'none';

	//var example = document.getElementById('example');

	// Create the toolbar
	var toolbar = document.createElement('div');
	toolbar.id = textarea.id + '_toolbar';

	_wrs_conf_CASEnabled = false;

	// Create the WYSIWYG editor
	var iframe = document.createElement('iframe');
	iframe.id = textarea.id + '_iframe';

	Question_wrs_addEvent(iframe, 'load', function () {
		// Setting design mode ON
		iframe.contentWindow.document.designMode = 'on';
		// Setting the content
		if (iframe.contentWindow.document.body) {
			iframe.contentWindow.document.body.innerHTML = textarea.innerHTML;
		}
		// Init MathType integration
		wrs_int_init(iframe, toolbar);
		function QuestionwaitForCore() {
			if (typeof WirisPlugin !== 'undefined') {
				// First load of display Preview and display HTML code.
				//updateQuestionFunction();
			} else {
				setTimeout(waitForCore, 200);
			}
		}
		QuestionwaitForCore();
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

	//Question_wrs_addEvent(form, 'submit', function () {
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
 function getQuestionEditorData() {
 	var iframe = document.getElementById('question_iframe');
 	return WirisPlugin.Parser.endParse(iframe.contentWindow.document.body.innerHTML);
 }


function getQuestionDataPreview(data) {
	return WirisPlugin.Parser.initParse(data);
}

function QuestionchangeDPI() {
	ls = document.getElementsByClassName('Wirisformula');
	for (i=0;i<ls.length;i++) {
		img = ls[i];
		img.width = img.clientWidth;
		img.src = img.src + "&dpi=600";
	}
}

function QuestionsetParametersSpecificPlugin(wiriseditorparameters) {
	_wrs_int_wirisProperties = wiriseditorparameters;
}

 /**
 * Gets wiriseditorparameters from Generic Plugin.
 * @return {object} MathType Web parameters as JSON. An empty JSON if is not defined.
 */
 function getQuestionWirisEditorParameters() {
 	if (typeof _wrs_int_wirisProperties != 'undefined') {
 		return _wrs_int_wirisProperties;
 	}
 	return {};
 }
