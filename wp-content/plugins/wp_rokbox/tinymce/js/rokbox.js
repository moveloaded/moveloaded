((function(){

	var Plugin = new Class({
		initialize: function(){
			this.attach();
			//this.pickers();

			this.switchBlock(document.getElement('[data-switcher]:checked'));
		},

		attach: function(){
			var insertNew = document.retrieve('rokbox:insertNew', function(event, element){
					if (event) event.preventDefault();
					if (this.insert()) this.reset();
				}.bind(this)),
				insertClose = document.retrieve('rokbox:insertClose', function(event, element){
					if (event) event.preventDefault();
					if (this.insert()) this.close();
				}.bind(this)),
				cancel = document.retrieve('rokbox:cancel', function(event, element){
					if (event) event.preventDefault();
					this.close();
				}.bind(this)),
				switcher = document.retrieve('rokbox:switcher', function(event, element){
					this.switchBlock(element.get('id'));
				}.bind(this)),
				elementCheck = document.retrieve('rokbox:elementCheck', function(event, element){
					var value = element.get('value'),
						lnk   = document.getElement('#link');
					if (!value && !lnk.get('disabled')) return;
					lnk
						.set('disabled', (value.length ? 'disabled' : null))
						.set('value', (value.length ? '#' : ''))
						[value.length ? 'addClass' : 'removeClass']('disabled');

					//document.getElement('.picker.link').setStyle('display', (value.length ? 'none' : 'inline-block'));
				}.bind(this)),
				modal = document.retrieve('rokbox:modal', function(event, element){
					if (event) event.preventDefault();

					this.squeezeResize({width: 820, height: 520});
					if (window.parent.SqueezeBox){
						SqueezeBox.fromElement(element, {
							parse: 'rel',
							'onOpen': this.squeezeResize.bind(this, {width: 800, height: 500}),
							'onClose': this.squeezeResize.bind(this, {width: 520, height: 430})
						});
					}
				}.bind(this));

			document.addEvents({
				'click:relay(#button-insert-new)': insertNew,
				'click:relay(#button-insert-close)': insertClose,
				'click:relay(#button-cancel)': cancel,
				'click:relay(a.modal-button)': modal,
				'click:relay([data-switcher])': switcher,
				'keydown:relay(input#element)': elementCheck,
				'keyup:relay(input#element)': elementCheck
			});
		},

		insert: function(){
			var requiredElements = document.getElements('[data-required]'),
				required = true, broken = false;

			requiredElements.each(function(element){
				var compiled = !!element.get('value');
				required *= compiled;
				if (!compiled && !broken) broken = element;
			});

			if (!required){
				broken.focus();
				return false;
			}

			var contentValue = document.getElement('[name=content]:checked');
			contentValue = contentValue ? contentValue.get('value') : false;

			var content = !contentValue ? '' : ', [name='+contentValue+']',
				elements = document.getElements('input:not([class$=text]):not([name=content]):not([type=hidden])' + content),
				output = '', bits = [], name, value;

			elements.each(function(element){
				name = element.get('name');
				value = element.get('value');

				if (name == 'link') bits.push('href="'+value+'"');
				else if (contentValue == name){
					content = value;
					if (name == 'thumbnail'){
						if (!value) bits.push('data-rokbox-generate-thumbnail');
						else content = '<img src="'+value+'" alt="" />';
					}
				} else {
					value = htmlEntities(value);
					if (value) bits.push('data-rokbox-' + name + '="' + value + '"');
				}
			}, this);

			output += '<a data-rokbox ' + bits.join(" ") + '>';
			output += this.parseSelection(content, contentValue == 'text');
			output += '</a>\n';

			if (window.parent.jInsertEditorText) window.parent.jInsertEditorText(output, document.getElement('body[#tinymce.wp-editor]').get('text'));
			if (window.parent.send_to_editor) window.parent.send_to_editor(output);

			return true;
		},

		reset: function(){
			var elements = document.getElements('input[name=link], input[name=caption], input[name=text], input[name=thumbnail]');
			elements.set('value', '');
		},

		close: function(){
			if (window.parent.SqueezeBox) window.parent.SqueezeBox.close();
			if (window.parent.tb_remove){
				if (tinyMCEPopup) tinyMCEPopup.close();
				window.parent.tb_remove();
			}
		},

		parseSelection: function(content, value){
			if (content || !value) return content;
			var editor = document.getElement('body[#tinymce.wp-editor]').get('text');

			if (window.parent.RokPad) return '{text}';
			else if (window.parent.CodeMirror) return window.parent.Joomla.editors.instances[editor].selection();
			else if (window.parent.tinymce){
				var selection = window.parent.tinymce.activeEditor;
				selection = !selection ? WPGetSelection(window.parent.wpActiveEditor || 'content') : selection.selection.getContent();
				return selection;
			}
			else return content;
		},

		pickers: function(){
			var mediatypes = document.getElements('[data-mediatype]'),
				links = document.getElements('[data-picker]'),
				value, option, rel;

			mediatypes.forEach(function(mediatype, i){
				value = mediatype.get('value');
				option = mediatype.getElement('option[value='+value+']');

				this.setPicker(links[i], value, option.get('rel'));
			}, this);
		},

		setPicker: function(element, url, rel){
			element.set('href', url).set('rel', rel);
		},

		switchBlock: function(switcher){
			if (typeOf(switcher) == 'element') switcher = switcher.get('value');
			var switchers = document.getElements('[data-switcher]').get('id');
			switchers = '.' + switchers.join('_text, .') + '_text';
			document.getElements(switchers).setStyle('display', 'none');
			document.getElements('.' + switcher + '_text').setStyle('display', 'inline-block');//.getAllNext().setStyle('display', 'block');
		},

		squeezeResize: function(size){
			if (window.parent.SqueezeBox){
				window.parent.SqueezeBox.asset.setStyles(size);
				window.parent.SqueezeBox.resize({x: size.width, y: size.height}, true);
			}

		//	if (){}
		}
	});

	var WPGetSelection = function(el){
		var start = 0, end = 0, normalizedValue, range,
			textInputRange, len, endRange,
			doc = window.parent.document;

		el = doc.getElementById(el);
		el.focus();
		if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
			start = el.selectionStart;
			end = el.selectionEnd;
		} else {
			range = doc.selection.createRange();

			if (range && range.parentElement() == el) {
				len = el.value.length;
				normalizedValue = el.value.replace(/\r\n/g, "\n");

				// Create a working TextRange that lives only in the input
				textInputRange = el.createTextRange();
				textInputRange.moveToBookmark(range.getBookmark());

				// Check if the start and end of the selection are at the very end
				// of the input, since moveStart/moveEnd doesn't return what we want
				// in those cases
				endRange = el.createTextRange();
				endRange.collapse(false);

				if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
					start = end = len;
				} else {
					start = -textInputRange.moveStart("character", -len);
					start += normalizedValue.slice(0, start).split("\n").length - 1;

					if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
						end = len;
					} else {
						end = -textInputRange.moveEnd("character", -len);
						end += normalizedValue.slice(0, end).split("\n").length - 1;
					}
				}
			}
		}

		return el.value.substring(start, end);
	};

	window.addEvent('domready', function(){
		new Plugin();
	});

	window.jInsertEditorText = function(text, editor) {
		if (text.contains('<img')) text = text.match(/src="(.*?[^])"/)[1];
		document.getElement('#' + editor).set('value', text);
	};

	function htmlEntities(content){
		var entities = new Element('div', {text: content}).get('html');
		return entities.replace(/&amp;quot;|"/g, '&quot;');
	}

})());
