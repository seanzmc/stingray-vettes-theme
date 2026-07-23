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
	'#sc-configurator-print-sheet'
].forEach(function (marker) {
	assert(css.indexOf(marker) !== -1, 'Missing CSS contract: ' + marker);
});

function ClassList() {
	this.values = [];
}
ClassList.prototype.add = function (name) {
	if (this.values.indexOf(name) === -1) this.values.push(name);
};
ClassList.prototype.remove = function (name) {
	this.values = this.values.filter(function (value) { return value !== name; });
};
ClassList.prototype.contains = function (name) {
	return this.values.indexOf(name) !== -1;
};

function Element(tagName) {
	this.tagName = String(tagName || 'div').toUpperCase();
	this.children = [];
	this.classList = new ClassList();
	this.listeners = {};
	this.style = {};
	this.attributes = {};
	this.textContent = '';
	this.clientWidth = 768;
	this.clientHeight = 1008;
	this.scrollWidth = 768;
	this.scrollHeight = 1008;
}
Element.prototype.appendChild = function (child) {
	this.children.push(child);
	child.parentNode = this;
	this.scrollWidth = Math.max(this.scrollWidth, child.scrollWidth);
	this.scrollHeight = Math.max(this.scrollHeight, child.scrollHeight);
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
};
Element.prototype.getAttribute = function (name) {
	return this.attributes[name] || null;
};
Element.prototype.cloneNode = function () {
	var copy = new Element(this.tagName);
	copy.textContent = this.textContent;
	copy.scrollWidth = this.scrollWidth;
	copy.scrollHeight = this.scrollHeight;
	return copy;
};
Element.prototype.querySelector = function (selector) {
	if ('.modal-header' === selector) return this.header || null;
	if ('.modal-title' === selector) return this.title || null;
	if ('.modal-body' === selector) return this.body || null;
	if ('.sc-configurator-print-button' === selector && this.header) {
		return this.header.children.filter(function (child) {
			return child.classList.contains('sc-configurator-print-button');
		})[0] || null;
	}
	return null;
};

var modal = new Element('div');
modal.header = new Element('header');
modal.title = new Element('h2');
modal.title.textContent = 'Tim Hottel';
modal.body = new Element('div');
modal.body.textContent = 'Complete submission content';
modal.body.scrollHeight = 1800;
modal.header.appendChild(modal.title);

var body = new Element('body');
var sheet = null;
var document = {
	body: body,
	createElement: function (tagName) { return new Element(tagName); },
	querySelector: function (selector) {
		if (selector.indexOf('.modal-content') !== -1) return modal;
		if ('#sc-configurator-print-sheet' === selector) {
			return sheet && sheet.parentNode ? sheet : null;
		}
		return null;
	},
	querySelectorAll: function (selector) {
		return selector.indexOf('.modal-content') !== -1 ? [modal] : [];
	}
};
var printed = 0;
var afterPrint = null;
var window = {
	addEventListener: function (name, handler) {
		if ('afterprint' === name) afterPrint = handler;
	},
	print: function () { printed++; },
	requestAnimationFrame: function (handler) { handler(); }
};
function MutationObserver(handler) {
	this.handler = handler;
}
MutationObserver.prototype.observe = function () {};

var originalAppend = body.appendChild;
body.appendChild = function (child) {
	if ('sc-configurator-print-sheet' === child.attributes.id) sheet = child;
	return originalAppend.call(body, child);
};

vm.runInNewContext(source, {
	window: window,
	document: document,
	MutationObserver: MutationObserver,
	setTimeout: function () { return 1; },
	clearTimeout: function () {},
	Math: Math
});

assert(1 === modal.header.children.filter(function (child) {
	return child.classList.contains('sc-configurator-print-button');
}).length, 'Exactly one Print order button must be injected.');

var button = modal.header.children.filter(function (child) {
	return child.classList.contains('sc-configurator-print-button');
})[0];
button.listeners.click();

assert(1 === printed, 'Print button must call window.print once.');
assert(body.classList.contains('sc-configurator-printing'), 'Print state must be active for printing.');
assert(sheet, 'Print action must append a temporary sheet.');
assert(sheet.children[0].style.transform.indexOf('scale(') === 0, 'Print content must receive a measured scale.');
assert(Number(sheet.getAttribute('data-print-scale')) < 1, 'Long modal content must scale below 100%.');

if (afterPrint) afterPrint();
assert(!body.classList.contains('sc-configurator-printing'), 'afterprint must clear print state.');
assert(!document.querySelector('#sc-configurator-print-sheet'), 'afterprint must remove the temporary sheet.');

if (failures.length) {
	console.error(failures.join('\n'));
	process.exit(1);
}

console.log('Configurator modal and one-sheet print regression tests passed.');
