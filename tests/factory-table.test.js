/* Run: node tests/factory-table.test.js */
'use strict';

var fs = require('fs');
var path = require('path');
var vm = require('vm');

function FakeElement(tagName, text) {
	this.tagName = tagName.toUpperCase();
	this.textContent = text || '';
	this.className = '';
	this.hidden = false;
	this.children = [];
	this.attributes = {};
	this.cells = [];
	this.tabIndex = -1;
	this.colSpan = 1;
	this.listeners = {};
	this.classList = {
		add: function (name) {
			var names = this.owner.className.split(/\s+/).filter(Boolean);
			if (-1 === names.indexOf(name)) names.push(name);
			this.owner.className = names.join(' ');
		},
		remove: function (name) {
			this.owner.className = this.owner.className.split(/\s+/).filter(function (item) {
				return item && item !== name;
			}).join(' ');
		},
		contains: function (name) {
			return -1 !== this.owner.className.split(/\s+/).indexOf(name);
		},
		owner: this
	};
}

FakeElement.prototype.appendChild = function (child) {
	this.children.push(child);
	return child;
};
FakeElement.prototype.setAttribute = function (name, value) {
	this.attributes[name] = String(value);
};
FakeElement.prototype.getAttribute = function (name) {
	return this.attributes[name] || null;
};
FakeElement.prototype.removeAttribute = function (name) {
	delete this.attributes[name];
	if ('tabindex' === name) this.tabIndex = -1;
};
FakeElement.prototype.addEventListener = function (name, handler) {
	this.listeners[name] = handler;
};
FakeElement.prototype.querySelectorAll = function () {
	return [];
};
FakeElement.prototype.focus = function () {};

function makeCell(text, className, colSpan) {
	var cell = new FakeElement('td', text);
	cell.className = className || '';
	cell.colSpan = colSpan || 1;
	return cell;
}

function makeRow(cells, className) {
	var row = new FakeElement('tr');
	row.cells = cells;
	row.className = className || '';
	return row;
}

var headers = ['Order #', 'Last Updated @ Factory', 'Current', 'TPW'].map(function (text) {
	return new FakeElement('th', text);
});
var valid = makeRow([
	makeCell('FMNPZX'),
	makeCell('10/13/2025'),
	makeCell('5000'),
	makeCell('')
]);
var empty = makeRow([makeCell('No matching records found', 'dataTables_empty', 4)]);
var child = makeRow([
	makeCell('Child detail'), makeCell(''), makeCell(''), makeCell('')
], 'child');
var group = makeRow([
	makeCell('Group'), makeCell(''), makeCell(''), makeCell('')
], 'dtrg-group');
var malformed = makeRow([makeCell('BROKEN'), makeCell(''), makeCell('')]);
var rows = [valid, empty, child, group, malformed];

var table = new FakeElement('table');
table.className = 'wpDataTable wpDataTableID-12';
table.tBodies = [];
table.querySelectorAll = function (selector) {
	if ('thead th' === selector) return headers;
	if ('tbody tr' === selector) return rows;
	return [];
};
table.contains = function (row) {
	return -1 !== rows.indexOf(row);
};

var body = new FakeElement('body');
var requestedSelector = null;
var document = {
	body: body,
	activeElement: body,
	querySelector: function (selector) {
		requestedSelector = selector;
		return '.sc-embed table.wpDataTable' === selector ? table : null;
	},
	createElement: function (tagName) {
		return new FakeElement(tagName);
	},
	addEventListener: function () {}
};

var observerCallback = null;
function MutationObserver(callback) {
	observerCallback = callback;
}
MutationObserver.prototype.observe = function () {};

var source = fs.readFileSync(path.join(__dirname, '..', 'assets', 'js', 'factory-table.js'), 'utf8');
vm.runInNewContext(source, {
	document: document,
	MutationObserver: MutationObserver,
	Array: Array
});

var failures = [];
function assert(condition, message) {
	if (!condition) failures.push(message);
}

assert('.sc-embed table.wpDataTable' === requestedSelector, 'The Factory runtime must select the page-configured wpDataTable without requiring a staging table ID.');
assert(valid.classList.contains('sc-factory-row'), 'A complete data row should be prepared for the detail dialog.');
assert(0 === valid.tabIndex, 'A complete data row should be keyboard focusable.');
assert(!empty.classList.contains('sc-factory-row'), 'A DataTables empty-result row must not become an order dialog trigger.');
assert(!child.classList.contains('sc-factory-row'), 'A DataTables child row must not become an order dialog trigger.');
assert(!group.classList.contains('sc-factory-row'), 'A DataTables group row must not become an order dialog trigger.');
assert(!malformed.classList.contains('sc-factory-row'), 'A malformed row with the wrong cell count must not become an order dialog trigger.');

valid.classList.add('child');
if (observerCallback) observerCallback();
assert(!valid.classList.contains('sc-factory-row'), 'A prepared row must be unprepared when DataTables later turns it into a child row.');
assert(-1 === valid.tabIndex, 'A row that becomes invalid must no longer be keyboard focusable.');
assert(null === valid.getAttribute('aria-haspopup'), 'A row that becomes invalid must lose its dialog ARIA metadata.');

if (failures.length) {
	console.error(failures.join('\n'));
	process.exit(1);
}

console.log('Factory table row-preparation regression tests passed.');
