/* Run: node tests/configurator-view.test.js */
'use strict';

var fs = require('fs');
var path = require('path');
var vm = require('vm');

var root = path.join(__dirname, '..');
var functionsSource = fs.readFileSync(path.join(root, 'functions.php'), 'utf8');
var cssPath = path.join(root, 'assets', 'css', 'configurator-view.css');
var jsPath = path.join(root, 'assets', 'js', 'configurator-view.js');
var failures = [];

function assert(condition, message) {
	if (!condition) failures.push(message);
}

assert(
	functionsSource.indexOf("is_page( 'configurator-view' )") !== -1,
	'Configurator assets must be scoped to the private page slug.'
);
assert(
	functionsSource.indexOf("'stingray-configurator-view'") !== -1 &&
	functionsSource.indexOf("'stingray-configurator-view-print'") !== -1,
	'Configurator stylesheet and script handles must be registered.'
);
assert(fs.existsSync(cssPath), 'Private configurator stylesheet must exist.');
assert(fs.existsSync(jsPath), 'Private configurator script must exist.');

if (!fs.existsSync(cssPath) || !fs.existsSync(jsPath)) {
	console.error(failures.join('\n'));
	process.exit(1);
}

var css = fs.readFileSync(cssPath, 'utf8');
var source = fs.readFileSync(jsPath, 'utf8');

[
	'--bs-modal-color: #161616',
	'padding: 0.75rem 1rem',
	'@media print',
	'@page',
	'size: letter portrait',
	'margin: 0.25in',
	'#sc-configurator-print-sheet',
	'height: auto !important',
	'max-height: none !important',
	'overflow: visible !important'
].forEach(function (marker) {
	assert(css.indexOf(marker) !== -1, 'Missing CSS contract: ' + marker);
});

function createHarness(options) {
	options = options || {};
	var measurement = options.measurement || null;
	var observer = null;
	var printed = 0;
	var afterPrint = null;
	var timerSequence = 0;
	var timers = {};

	function ClassList(element) {
		this.element = element;
		this.values = [];
	}
	ClassList.prototype.add = function (name) {
		if (this.values.indexOf(name) === -1) this.values.push(name);
		this.element.attributes.class = this.values.join(' ');
	};
	ClassList.prototype.remove = function (name) {
		this.values = this.values.filter(function (value) { return value !== name; });
		if (this.values.length) {
			this.element.attributes.class = this.values.join(' ');
		} else {
			delete this.element.attributes.class;
		}
	};
	ClassList.prototype.contains = function (name) {
		return this.values.indexOf(name) !== -1;
	};
	ClassList.prototype.replace = function (value) {
		this.values = String(value || '').split(/\s+/).filter(Boolean);
		if (this.values.length) {
			this.element.attributes.class = this.values.join(' ');
		} else {
			delete this.element.attributes.class;
		}
	};

	function Element(tagName) {
		this.tagName = String(tagName || 'div').toUpperCase();
		this.children = [];
		this.classList = new ClassList(this);
		this.listeners = {};
		this.style = {};
		this.attributes = {};
		this._textContent = '';
		this.clientWidth = 768;
		this.clientHeight = 1008;
		this._scrollWidth = 768;
		this._scrollHeight = 1008;
	}
	Object.defineProperty(Element.prototype, 'textContent', {
		get: function () {
			return this._textContent + this.children.map(function (child) {
				return child.textContent;
			}).join('');
		},
		set: function (value) {
			this._textContent = String(value);
			this.children = [];
		}
	});
	Object.defineProperty(Element.prototype, 'scrollWidth', {
		get: function () {
			if (this.classList.contains('sc-configurator-print-inner') && measurement) {
				return measurement.width;
			}
			if (this.classList.contains('sc-configurator-print-inner')) {
				return this.children.reduce(function (width, child) {
					return Math.max(width, child.layoutWidth());
				}, 0);
			}
			return this._scrollWidth;
		},
		set: function (value) {
			this._scrollWidth = value;
		}
	});
	Object.defineProperty(Element.prototype, 'scrollHeight', {
		get: function () {
			if (this.classList.contains('sc-configurator-print-inner') && measurement) {
				return measurement.height;
			}
			if (this.classList.contains('sc-configurator-print-inner')) {
				return this.children.reduce(function (height, child) {
					return height + child.layoutHeight();
				}, 0);
			}
			return this._scrollHeight;
		},
		set: function (value) {
			this._scrollHeight = value;
		}
	});
	Element.prototype.layoutWidth = function () {
		return this._scrollWidth;
	};
	Element.prototype.layoutHeight = function () {
		var constrained = this.classList.contains('wpdt-c-scrollable') ||
			this.hasAttribute('style') ||
			this.style.height ||
			this.style.maxHeight ||
			this.style.overflow;
		return constrained ? this.clientHeight : this._scrollHeight;
	};
	Element.prototype.appendChild = function (child) {
		this.children.push(child);
		child.parentNode = this;
		return child;
	};
	Element.prototype.remove = function () {
		if (!this.parentNode) return;
		var parent = this.parentNode;
		parent.children = parent.children.filter(function (child) {
			return child !== this;
		}, this);
		this.parentNode = null;
	};
	Element.prototype.addEventListener = function (name, handler) {
		this.listeners[name] = handler;
	};
	Element.prototype.setAttribute = function (name, value) {
		this.attributes[name] = String(value);
		if ('class' === name) this.classList.replace(value);
	};
	Element.prototype.getAttribute = function (name) {
		return Object.prototype.hasOwnProperty.call(this.attributes, name) ?
			this.attributes[name] :
			null;
	};
	Element.prototype.getAttributeNames = function () {
		return Object.keys(this.attributes);
	};
	Element.prototype.hasAttribute = function (name) {
		return Object.prototype.hasOwnProperty.call(this.attributes, name);
	};
	Element.prototype.removeAttribute = function (name) {
		delete this.attributes[name];
		if ('class' === name) this.classList.replace('');
		if ('style' === name) this.style = {};
	};
	Element.prototype.cloneNode = function (deep) {
		var copy = new Element(this.tagName);
		copy._textContent = this._textContent;
		copy.clientWidth = this.clientWidth;
		copy.clientHeight = this.clientHeight;
		copy._scrollWidth = this._scrollWidth;
		copy._scrollHeight = this._scrollHeight;
		Object.keys(this.attributes).forEach(function (name) {
			copy.setAttribute(name, this.attributes[name]);
		}, this);
		Object.keys(this.style).forEach(function (name) {
			copy.style[name] = this.style[name];
		}, this);
		if (deep) {
			this.children.forEach(function (child) {
				copy.appendChild(child.cloneNode(true));
			});
		}
		return copy;
	};
	Element.prototype.matches = function (selector) {
		if ('.' === selector.charAt(0)) return this.classList.contains(selector.slice(1));
		if ('#' === selector.charAt(0)) return this.getAttribute('id') === selector.slice(1);
		return this.tagName.toLowerCase() === selector.toLowerCase();
	};
	Element.prototype.querySelector = function (selector) {
		var match = null;
		function visit(node) {
			node.children.some(function (child) {
				if (child.matches(selector)) {
					match = child;
					return true;
				}
				visit(child);
				return Boolean(match);
			});
		}
		visit(this);
		return match;
	};

	function makeModal(bodyHeight, bodyWidth) {
		var modal = new Element('div');
		var header = new Element('header');
		var title = new Element('h2');
		var titleName = new Element('span');
		var titleOrder = new Element('strong');
		var modalBody = new Element('div');
		var summary = new Element('section');
		var list = new Element('ul');
		var belowFold = new Element('li');

		modal.classList.add('modal-content');
		header.classList.add('modal-header');
		title.classList.add('modal-title');
		title.clientHeight = 80;
		title._scrollHeight = 80;
		title._scrollWidth = 300;
		titleName.textContent = 'Tim Hottel';
		titleOrder.textContent = ' — Order 123';
		title.appendChild(titleName);
		title.appendChild(titleOrder);
		header.appendChild(title);

		modalBody.setAttribute('id', 'plugin-modal-body');
		modalBody.setAttribute('class', 'modal-body wpdt-c-scrollable plugin-body');
		modalBody.setAttribute('style', 'height: 320px; max-height: 320px; overflow: auto;');
		modalBody.setAttribute('data-plugin-state', 'active');
		modalBody.setAttribute('role', 'document');
		modalBody.style.height = '320px';
		modalBody.style.maxHeight = '320px';
		modalBody.style.overflow = 'auto';
		modalBody.clientHeight = 320;
		modalBody._scrollHeight = bodyHeight;
		modalBody._scrollWidth = bodyWidth;
		summary.classList.add('submission-summary');
		belowFold.setAttribute('data-test', 'below-fold');
		belowFold.textContent = 'Nested below-viewport option';
		list.appendChild(belowFold);
		summary.appendChild(list);
		modalBody.appendChild(summary);

		modal.appendChild(header);
		modal.appendChild(modalBody);
		return modal;
	}

	var body = new Element('body');
	var modals = [];
	var document = {
		body: body,
		createElement: function (tagName) {
			var element = new Element(tagName);
			if ('h1' === String(tagName).toLowerCase()) {
				element.clientHeight = 80;
				element._scrollHeight = 80;
				element._scrollWidth = 300;
			}
			return element;
		},
		querySelector: function (selector) {
			if ('#sc-configurator-print-sheet' === selector) {
				return body.querySelector(selector);
			}
			return null;
		},
		querySelectorAll: function (selector) {
			return selector.indexOf('.modal-content') !== -1 ? modals.slice() : [];
		}
	};
	var window = {
		addEventListener: function (name, handler) {
			if ('afterprint' === name) afterPrint = handler;
		},
		print: function () { printed++; },
		requestAnimationFrame: function (handler) { handler(); }
	};
	function MutationObserver(handler) {
		this.handler = handler;
		observer = this;
	}
	MutationObserver.prototype.observe = function () {};

	vm.runInNewContext(source, {
		window: window,
		document: document,
		MutationObserver: MutationObserver,
		setTimeout: function (handler) {
			timerSequence++;
			timers[timerSequence] = handler;
			return timerSequence;
		},
		clearTimeout: function (timerId) {
			delete timers[timerId];
		},
		Math: Math,
		Number: Number,
		isFinite: isFinite
	});

	return {
		body: body,
		document: document,
		makeModal: makeModal,
		modals: modals,
		triggerMutation: function () { observer.handler(); },
		getPrinted: function () { return printed; },
		getAfterPrint: function () { return afterPrint; },
		runCleanupTimers: function () {
			Object.keys(timers).forEach(function (timerId) {
				var handler = timers[timerId];
				delete timers[timerId];
				handler();
			});
		}
	};
}

function printButtonCount(modal) {
	var header = modal.querySelector('.modal-header');
	return header.children.filter(function (child) {
		return child.classList.contains('sc-configurator-print-button');
	}).length;
}

function clickPrint(modal) {
	modal.querySelector('.sc-configurator-print-button').listeners.click();
}

var longHarness = createHarness();
var longModal = longHarness.makeModal(1800, 900);

assert(0 === printButtonCount(longModal), 'No button should exist before the delayed modal is discovered.');
longHarness.modals.push(longModal);
longHarness.triggerMutation();
longHarness.triggerMutation();
longHarness.triggerMutation();
assert(
	1 === printButtonCount(longModal),
	'Repeated modal mutations must inject exactly one Print order button.'
);

var originalBody = longModal.querySelector('.modal-body');
var originalAttributes = originalBody.getAttributeNames().sort().join('|');
clickPrint(longModal);

var longSheet = longHarness.document.querySelector('#sc-configurator-print-sheet');
var longInner = longSheet && longSheet.children[0];
var titleClone = longInner && longInner.children[0];
var bodyClone = longInner && longInner.children[1];

assert(1 === longHarness.getPrinted(), 'Valid long content must call window.print once.');
assert(longHarness.body.classList.contains('sc-configurator-printing'), 'Print state must be active for printing.');
assert(longSheet, 'Print action must append a temporary sheet.');
assert(
	titleClone && 'Tim Hottel — Order 123' === titleClone.textContent,
	'Nested modal title text must be complete in the print sheet.'
);
assert(
	titleClone && titleClone.querySelector('strong') &&
	' — Order 123' === titleClone.querySelector('strong').textContent,
	'Deep title cloning must preserve nested title markup and content.'
);
assert(
	bodyClone && bodyClone.textContent.indexOf('Nested below-viewport option') !== -1,
	'Deep body cloning must preserve nested content below the modal viewport.'
);
assert(
	bodyClone && 'sc-configurator-print-body' === bodyClone.getAttribute('class'),
	'Print body must replace plugin classes with only the print class.'
);
assert(
	bodyClone && bodyClone.getAttributeNames().sort().join('|') === 'class',
	'Print body must discard plugin attributes that can retain modal behavior or constraints.'
);
assert(
	originalAttributes === originalBody.getAttributeNames().sort().join('|') &&
	originalBody.classList.contains('modal-body') &&
	'320px' === originalBody.style.maxHeight,
	'Preparing a print clone must not mutate the original modal body.'
);
assert(
	longInner && longInner.style.transform.indexOf('scale(') === 0,
	'Print content must receive a measured scale.'
);
assert(
	Number(longSheet && longSheet.getAttribute('data-print-scale')) < 1,
	'Complete long modal content must be measured and scaled below 100%.'
);
var transformMatch = longInner && /^scale\(([^)]+)\)$/.exec(longInner.style.transform);
var appliedLongScale = transformMatch ? Number(transformMatch[1]) : NaN;
assert(
	appliedLongScale === Number(longSheet && longSheet.getAttribute('data-print-scale')),
	'The applied transform and recorded print scale must match.'
);
assert(
	longInner && appliedLongScale * longInner.scrollWidth <= longSheet.clientWidth,
	'Serialized print scale must keep the complete content width inside the sheet.'
);
assert(
	longInner && appliedLongScale * longInner.scrollHeight <= longSheet.clientHeight,
	'Serialized print scale must keep the complete content height inside the sheet.'
);

longHarness.runCleanupTimers();
assert(
	!longHarness.body.classList.contains('sc-configurator-printing'),
	'Fallback cleanup must clear print state when afterprint is not emitted.'
);
assert(
	!longHarness.document.querySelector('#sc-configurator-print-sheet'),
	'Fallback cleanup must remove the temporary sheet when afterprint is not emitted.'
);

var shortHarness = createHarness();
var shortModal = shortHarness.makeModal(300, 600);
shortHarness.modals.push(shortModal);
shortHarness.triggerMutation();
clickPrint(shortModal);

var shortSheet = shortHarness.document.querySelector('#sc-configurator-print-sheet');
var shortScale = Number(shortSheet && shortSheet.getAttribute('data-print-scale'));
assert(
	shortScale > 0 && shortScale <= 1,
	'Short print content must use a positive scale capped at 100%.'
);
assert(1 === shortScale, 'Short print content should remain at 100% rather than upscale.');
if (shortHarness.getAfterPrint()) shortHarness.getAfterPrint()();
assert(
	!shortHarness.document.querySelector('#sc-configurator-print-sheet'),
	'afterprint must remove the temporary sheet.'
);

[
	{ name: 'zero', width: 0, height: 300 },
	{ name: 'nonfinite', width: 600, height: Infinity }
].forEach(function (invalidMeasurement) {
	var failedHarness = createHarness({
		measurement: {
			width: invalidMeasurement.width,
			height: invalidMeasurement.height
		}
	});
	var failedModal = failedHarness.makeModal(300, 600);
	failedHarness.modals.push(failedModal);
	failedHarness.triggerMutation();
	clickPrint(failedModal);

	assert(
	0 === failedHarness.getPrinted(),
		'Measurement failure (' + invalidMeasurement.name + ') must abort without calling window.print.'
	);
	assert(
		!failedHarness.body.classList.contains('sc-configurator-printing'),
		'Measurement failure (' + invalidMeasurement.name + ') must leave no print state.'
	);
	assert(
		!failedHarness.document.querySelector('#sc-configurator-print-sheet'),
		'Measurement failure (' + invalidMeasurement.name + ') must remove the temporary sheet.'
	);
});

if (failures.length) {
	console.error(failures.join('\n'));
	process.exit(1);
}

console.log('Configurator modal and one-sheet print regression tests passed.');
