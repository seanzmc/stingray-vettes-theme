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
	'.frm-modal-sc .modal-content',
	'.frm-modal-sc .modal-header',
	'.frm-modal-sc .modal-title',
	'.frm-modal-sc .modal-body',
	'.frm-modal-sc .modal-body *',
	'.wpdt-c .wpDataTablesWrapper',
	'.wpdt-c .wpDataTablesWrapper label',
	'.wpdt-c .wpDataTablesWrapper .dataTables_filter label',
	'.wpdt-c .wpDataTablesWrapper .dataTables_length label',
	'.wpdt-c .wpDataTablesWrapper .wdt-filter-control',
	'.wpdt-c .wpDataTablesWrapper table.wpDataTable thead th',
	'.wpdt-c .wpDataTablesWrapper table.wpDataTable tbody td',
	'.wpdt-c .dataTables_info',
	'.wpdt-c .dataTables_paginate .paginate_button',
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
assert(
	source.indexOf('.frm-modal-sc .modal-content') !== -1,
	'Print discovery must target the live Formidable submission-modal wrapper.'
);

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
		var dataKeyMatch = /^\[data-key="([^"]+)"\]$/.exec(selector);
		if (dataKeyMatch) return this.getAttribute('data-key') === dataKeyMatch[1];
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
		var customerName = new Element('div');
		var summary = new Element('div');
		var entryId = new Element('div');

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
		customerName.setAttribute('data-key', 'name');
		customerName.textContent = 'Tim Hottel';
		summary.setAttribute('data-key', 'plaintextbuildsummary');
		summary.setAttribute('class', 'detailColumn plugin-summary');
		summary.setAttribute('style', 'display: block; max-height: 320px; overflow: auto;');
		summary.textContent = [
			'Tim Hottel',
			'Submitted: 2026-06-26',
			'Variant',
			'• Corvette Stingray Convertible 2LT',
			'Total MSRP: $101,715'
		].join('\n');
		summary._scrollHeight = bodyHeight;
		summary._scrollWidth = bodyWidth;
		entryId.setAttribute('data-key', 'id');
		entryId.textContent = '243746';
		modalBody.appendChild(customerName);
		if (!options.omitSummary) modalBody.appendChild(summary);
		modalBody.appendChild(entryId);

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
			var requiredSelector = options.requiredModalSelector || '.modal-content';
			return selector.indexOf(requiredSelector) !== -1 ? modals.slice() : [];
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

var formidableHarness = createHarness({
	requiredModalSelector: '.frm-modal-sc .modal-content'
});
var formidableModal = formidableHarness.makeModal(900, 700);
formidableHarness.modals.push(formidableModal);
formidableHarness.triggerMutation();
assert(
	1 === printButtonCount(formidableModal),
	'Live Formidable submission modals must receive exactly one Print order button.'
);

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

clickPrint(longModal);

var longSheet = longHarness.document.querySelector('#sc-configurator-print-sheet');
var longInner = longSheet && longSheet.children[0];
var summaryClone = longInner && longInner.children[0];
var expectedSummary = [
	'Tim Hottel',
	'Submitted: 2026-06-26',
	'Variant',
	'• Corvette Stingray Convertible 2LT',
	'Total MSRP: $101,715'
].join('\n');

assert(1 === longHarness.getPrinted(), 'Valid long content must call window.print once.');
assert(longHarness.body.classList.contains('sc-configurator-printing'), 'Print state must be active for printing.');
assert(longSheet, 'Print action must append a temporary sheet.');
assert(
	longInner && 1 === longInner.children.length,
	'Print sheet must contain only the plaintext build summary.'
);
assert(
	summaryClone && expectedSummary === summaryClone.textContent,
	'Print sheet must preserve the complete selected plaintext build summary.'
);
assert(
	summaryClone && 'sc-configurator-print-body' === summaryClone.getAttribute('class'),
	'Summary clone must replace plugin classes with the print-body class.'
);
assert(
	summaryClone && summaryClone.getAttributeNames().sort().join('|') === 'class',
	'Summary clone must discard plugin attributes and inline constraints.'
);
assert(
	longInner.textContent.indexOf('Order 123') === -1 &&
	longInner.textContent.indexOf('243746') === -1,
	'Print sheet must not add the modal title or entry ID.'
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
	longInner && appliedLongScale * longInner.scrollHeight <= longSheet.clientHeight + 0.001,
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

var missingSummaryHarness = createHarness({ omitSummary: true });
var missingSummaryModal = missingSummaryHarness.makeModal(300, 600);
missingSummaryHarness.modals.push(missingSummaryModal);
missingSummaryHarness.triggerMutation();
clickPrint(missingSummaryModal);

assert(
	0 === missingSummaryHarness.getPrinted(),
	'Missing plaintextbuildsummary must abort without opening the print dialog.'
);
assert(
	!missingSummaryHarness.document.querySelector('#sc-configurator-print-sheet'),
	'Missing plaintextbuildsummary must leave no temporary print sheet.'
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
