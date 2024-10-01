/**
 * Dialog class.
 */
class Dialog {

	/**
	 * Element
	 */
	dialog;

	buttons = {};

	constructor(settings = {}) {
		this.settings = Object.assign(
			{
				title: '',
				dialogClass: '',
				content: '',
				onClose : function (){ return true; },
				onOpen : function (){ return true; },
				onAccept : function (){ return true; },
				// TODO : reprendre les modifs effectuées sur le module cookiesBlender pour wordpress (dont le travail sur les traductions)
				template: '<header ></header>' +
					'<form method="dialog" data-ref="form">\n' +
					'   <div class="body" ></div>\n' +
					'   <footer>\n' +
					'        <button class="kanban-btn" data-btn-role="cancel" value="cancel">Cancel</button>\n' +
					'        <button class="kanban-btn"  data-btn-role="accept" value="default">Ok</button>\n' +
					'   </footer>\n' +
					'</form>'
			},
			settings
		)
		this.init()
	}

	/**
	 * @param $functionName
	 * @returns {boolean}
	 */
	isCallableFunction($functionName) {
		return window[$functionName] instanceof Function;
	}

	init() {

		this.dialog = document.createElement('dialog');
		this.dialog.classList.add('kanban-dialog');


		this.dialog.role = 'dialog';
		if(this.settings.dialogClass.length > 0) {
			this.dialog.classList.add(this.settings.dialogClass);
		}
		this.dialog.insertAdjacentHTML('afterbegin', this.settings.template);
		this.dialog.querySelector('header').textContent = this.settings.title;

		this.buttons.accept = this.dialog.querySelector('[data-btn-role="accept"]');
		this.buttons.accept.addEventListener("click", (e)=>{
			e.preventDefault(); // Cancel the native event
			e.stopPropagation();// Don't bubble/capture the event any further
			if(this.settings.onAccept(this)){
				this.toggle();
			}
		});



		this.buttons.cancel = this.dialog.querySelector('[data-btn-role="cancel"]');
		if(this.buttons.cancel !== null){
			// $(this.dialog.querySelector('header')).dragsDialog(); //TODO : faut-il le mettre ou pas ? // rend les boites de dialogue draggable

			this.buttons.cancel.addEventListener("click", (e)=>{
				e.preventDefault(); // Cancel the native event
				e.stopPropagation();// Don't bubble/capture the event any further
				this.toggle();
			});
		}

		this.setContent(this.settings.content);

		// la dialogue est détruite à la fermeture
		this.dialog.addEventListener("close", () => {
			// Remove the child element from the document
			this.dialog.parentNode.removeChild(this.dialog);
		});

		document.body.appendChild(this.dialog)

		this.dialog.addEventListener('keydown', e => {
			// if (e.key === 'Enter') {
			// 	if (!this.dialogSupported) e.preventDefault()
			// 	this.elements.accept.dispatchEvent(new Event('click'))
			// }
			if (e.key === 'Escape') this.dialog.dispatchEvent(new Event('cancel'))
			if (e.key === 'Tab') {
				e.preventDefault()
				const len =  this.focusable.length - 1;
				let index = this.focusable.indexOf(e.target);
				index = e.shiftKey ? index - 1 : index + 1;
				if (index < 0) index = len;
				if (index > len) index = 0;
				this.focusable[index].focus();
			}
		})
		this.toggle()
	}

	setContent(content){
		this.dialog.querySelector('.body').insertAdjacentHTML('afterbegin', content);
	}

	open(settings = {}) {
		const dialog = Object.assign({}, this.settings, settings)
		this.toggle()
	}

	toggle() {
		if(this.dialog.hasAttribute('open')){
			this.close();
		}
		else{
			this.dialog.showModal();
			this.settings.onOpen(this);
			this.buttons.accept.focus();
		}
	}


	close() {
		if(this.dialog.hasAttribute('open')){
			if(this.settings.onClose(this)){
				this.dialog.close();
			}
		}
	}

	waitForUser() {
		return new Promise(resolve => {
			this.dialog.addEventListener('cancel', () => {
				this.toggle()
				resolve(false)
			}, { once: true })
			this.buttons.accept.addEventListener('click', () => {
				this.toggle()
				resolve(true)
			}, { once: true })
		})
	}
}

/**
 * rend les boites de dialogue draggable mais je sais pas si je le garde
 */
// (function ($) {
// 	$.fn.dragsDialog = function (opt) {
//
// 		opt = $.extend({ handle: "", cursor: "move" }, opt);
//
// 		var $el = null;
// 		if (opt.handle === "") {
// 			$el = this;
// 		} else {
// 			$el = this.find(opt.handle);
// 		}
//
// 		return $el.css('cursor', opt.cursor).on("mousedown", function (e) {
// 			var $drag = null;
// 			if (opt.handle === "") {
// 				$drag = $(this).parents('dialog').addClass('draggable');
// 			} else {
// 				$drag = $(this).parents('dialog').addClass('active-handle').parent().addClass('draggable');
// 			}
// 			var z_idx = $drag.css('z-index'),
// 				drg_h = $drag.outerHeight(),
// 				drg_w = $drag.outerWidth(),
// 				pos_y = $drag.offset().top + drg_h - e.pageY,
// 				pos_x = $drag.offset().left + drg_w - e.pageX;
// 			$drag.css('z-index', 1000).parents().on("mousemove", function (e) {
// 				$('.draggable').offset({
// 					top: e.pageY + pos_y - drg_h,
// 					left: e.pageX + pos_x - drg_w
// 				}).on("mouseup", function () {
// 					$(this).removeClass('draggable').css('z-index', z_idx);
// 				});
// 			});
// 			e.preventDefault(); // disable selection
// 		}).on("mouseup", function () {
// 			if (opt.handle === "") {
// 				$(this).removeClass('draggable');
// 			} else {
// 				$(this).removeClass('active-handle').parent().removeClass('draggable');
// 			}
// 		});
//
// 	}
// })(jQuery);
