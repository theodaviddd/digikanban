jQuery(function ($) {
	// using $ here will be safely even jQuery.noConflict() will be enabled

	$(document).on('focus','.live-edit', function() {
		if($(this).data('ajax-target') == undefined){
			SpLiveEdit.setSPBadLiveEdit($(this));
			return false;
		}
	});


	$(document).on('blur','.live-edit', function(){
		return SpLiveEdit.sendLiveEditFromElement($(this));
	});

	$(document).on('keydown', '.live-edit', function(e) {
		if(e.key == 'Enter'){
			e.preventDefault();
			$(this).trigger('blur');
		}
	});


// Utilisation d'une sorte de namespace en JS
	SpLiveEdit = {};
	(function(o) {
		// lang par défaut, les valeurs son ecrasées lors du chargement de la page en fonction de la langue
		o.lang = {
			"Saved":"Sauvegard\u00e9",
			"errorAjaxCall":"Erreur d'appel ajax",
			"SearchProduct":"Recherche de produits\/services",
			"CloseDialog":"Fermer"
		};


		o.newToken = '';

		/**
		 * Get new token
		 */
		o.GetNewToken = function (){
			if($('input[name=token]').length > 0){
				o.newToken = $('input[name=token]').val();
			}
		}

		/**
		 * function to call on document ready
		 */
		o.initLiveEdit = function (){
			o.GetNewToken();
			o.setSPLiveEdit($('[data-live-edit=1]'));
		}

		/**
		 * @param {jQuery} el
		 */
		o.setSPBadLiveEdit = function (el) {
			el.attr('title', 'Bad live edit configuration');
			el.css('color', 'red');
			el.removeClass('live-edit');
			el.attr('contenteditable', false);
		};

		/**
		 * @param {jQuery} el
		 */
		o.setSPLiveEdit = function (el) {
			el.addClass('live-edit');
			el.attr('contenteditable', true);
		};

		/**
		 * @param {jQuery} el
		 */
		o.removeSPLiveEdit = function (el) {
			el.removeClass('live-edit');
			el.attr('contenteditable', false);
			el.removeAttr('contenteditable');
			el.removeAttr('data-live-edit');
		};

		/**
		 *
		 * @param {jQuery} el
		 * @param forceUpdate bool to force update when old and new value are same
		 */
		o.sendLiveEditFromElement = function (el, forceUpdate = false){

			if(el.data('ajax-target') == undefined){
				o.setSPBadLiveEdit(el);
				return false;
			}

			return o.sendLiveEdit(el,{
				urlInterface : el.data('ajax-target'),
				sendData : {
					'value': el.text(),
					'token': o.newToken,
					'action': 'liveFieldUpdate',
					'forceUpdate' : forceUpdate ? 1 : 0 // js bool is send as string ...
				},
				callback : {
					success: el.data('ajax-success-callback'),
					idle: el.data('ajax-idle-callback'),
					fail: el.data('ajax-fail-callback')
				}
			});
		}

		/**
		 *
		 * @param {object} el
		 * @param {object} conf
		 */
		o.sendLiveEdit = function (el, conf){

			$.ajax({
				method: 'POST',
				url: conf.urlInterface,
				dataType: 'json',
				data: conf.sendData,
				success: function (data) {
					if(data.result > 0) {
						// do stuff on success
						let callbackRes = 0;
						if(conf.callback.success != undefined){
							callbackRes = o.callBackFunction(conf.callback.success , el, data);
						}

						if(callbackRes==0 || !callbackRes){
							el.html(data.displayValue);
						}
					}
					else if(data.result == 0) {
						// do stuff on idle
						if(conf.callback.idle != undefined){
							o.callBackFunction(conf.callback.idle, el, data);
						}
					}
					else if(data.result < 0) {
						// do stuff on error
						if(conf.callback.fail != undefined){
							o.callBackFunction(conf.callback.fail, el, data);
						}
					}

					if(data.newToken != undefined){
						o.newToken = data.newToken;
					}

					if(data.msg.length > 0) {
						o.setEventMessage(data.msg, data.result > 0 ? true : false );
					}
				},
				error: function (err) {
					o.setEventMessage(o.lang.errorAjaxCall, false);
				}
			});
		}

		/**
		 * @param $functionName
		 * @returns {boolean}
		 */
		o.isCallableFunction = function ($functionName){
			return window[$functionName] instanceof Function;
		}

		/**
		 * @param $functionName
		 * @param {jQuery} el
		 * @returns {boolean}
		 */
		o.callBackFunction = function ($functionName, el = null, data = null){
			if(!o.isCallableFunction($functionName)){
				console.log('CallBack function ' + $functionName + ' not found !')
				return false;
			}

			console.log('CallBack function ' + $functionName + ' executed');

			// execute function callback
			let fn = window[$functionName];
			return fn(el, data);
		}

		/**
		 *
		 * @param msg
		 * @param status
		 */
		o.setEventMessage = function (msg, status = true){

			if(msg.length > 0){
				if(status){
					$.jnotify(msg, 'notice', {timeout: 5},{ remove: function (){} } );
				}
				else{
					$.jnotify(msg, 'error', {timeout: 0, type: 'error'},{ remove: function (){} } );
				}
			}
			else{
				$.jnotify('ErrorMessageEmpty', 'error', {timeout: 0, type: 'error'},{ remove: function (){} } );
			}
		}

		/**
		 * @param {JQuery} $el           dom element to pimp
		 * @param string $element             the commonobject element for dolibarr
		 * @param int    $fk_element          the object id
		 * @param string $field               field code to update
		 * @return string
		 */
		o.setLiveUpdateAttributeForDolField = function($el, {element, fk_element, field, liveEditInterfaceUrl}){
			let url = liveEditInterfaceUrl
				+ '?element=' 		+ element
				+ '&fk_element=' 	+ fk_element
				+ '&field=' 		+ field;

			$el.attr('data-ajax-target', url);
			$el.attr('data-live-edit', 1);
		}

	})(SpLiveEdit);

	/* Init live edit for compatible elements */
	SpLiveEdit.initLiveEdit();
});
