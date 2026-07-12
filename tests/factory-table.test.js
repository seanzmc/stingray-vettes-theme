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

var headerLabels = [
	'Order #', 'Last Updated @ Factory', 'Current', 'TPW', 'Model', 'Trim', 'Exterior',
	'Interior', 'Status', 'Event', 'Dealer', 'VIN', 'Ship Date', 'Notes'
];
var labelHeaders = headerLabels.map(function (text) {
	return new FakeElement('th', text);
});
var filterHeaders = headerLabels.map(function () {
	return new FakeElement('th', '');
});
var valid = makeRow([
	makeCell('FMNPZX'),
	makeCell('10/13/2025'),
	makeCell('5000'),
	makeCell(''),
	makeCell('Corvette'),
	makeCell('Stingray'),
	makeCell('Torch Red'),
	makeCell('Jet Black'),
	makeCell('In Production'),
	makeCell('Produced'),
	makeCell('Stingray Chevrolet'),
	makeCell('1G1YA2D4XR5100001'),
	makeCell('10/20/2025'),
	makeCell('Customer order')
]);
var empty = makeRow([makeCell('No matching records found', 'dataTables_empty', 14)]);
var child = makeRow(headerLabels.map(function (text, index) {
	return makeCell(0 === index ? 'Child detail' : '');
}), 'child');
var group = makeRow(headerLabels.map(function (text, index) {
	return makeCell(0 === index ? 'Group' : '');
}), 'dtrg-group');
var malformed = makeRow(headerLabels.slice(0, 13).map(function (text, index) {
	return makeCell(0 === index ? 'BROKEN' : '');
}));
var rows = [valid, empty, child, group, malformed];

var table = new FakeElement('table');
table.className = 'wpDataTable wpDataTableID-12';
table.tBodies = [];
table.querySelectorAll = function (selector) {
	if ('thead th' === selector) return labelHeaders.concat(filterHeaders);
	if ('thead tr:first-child th' === selector) return labelHeaders;
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
var styles = fs.readFileSync(path.join(__dirname, '..', 'assets', 'css', 'embeds.css'), 'utf8');
vm.runInNewContext(source, {
	document: document,
	MutationObserver: MutationObserver,
	Array: Array
});

var failures = [];
function assert(condition, message) {
	if (!condition) failures.push(message);
}

assert(-1 === styles.indexOf('wpDataTableID-7'), 'Factory styles must not require the staging wpDataTable ID.');
assert(
	-1 === styles.indexOf('th:nth-child(n + 4)') &&
	-1 === styles.indexOf('td:nth-child(n + 4)'),
	'Factory styles must leave fourth and subsequent column visibility to wpDataTables.'
);
assert(
	-1 === styles.indexOf('table.wpDataTable th:nth-child(2)') &&
	-1 === styles.indexOf('table.wpDataTable td:nth-child(2)'),
	'Factory styles must not hide the last-updated column on mobile.'
);
assert('.sc-embed table.wpDataTable' === requestedSelector, 'The Factory runtime must select the page-configured wpDataTable without requiring a staging table ID.');
assert(valid.classList.contains('sc-factory-row'), 'A complete data row should be prepared for the detail dialog.');
assert(0 === valid.tabIndex, 'A complete data row should be keyboard focusable.');
assert('dialog' === valid.getAttribute('aria-haspopup'), 'A complete data row should expose click/keyboard dialog behavior.');
assert(!empty.classList.contains('sc-factory-row'), 'A DataTables empty-result row must not become an order dialog trigger.');
assert(!child.classList.contains('sc-factory-row'), 'A DataTables child row must not become an order dialog trigger.');
assert(!group.classList.contains('sc-factory-row'), 'A DataTables group row must not become an order dialog trigger.');
assert(!malformed.classList.contains('sc-factory-row'), 'A malformed row with the wrong cell count must not become an order dialog trigger.');

valid.classList.add('child');
if (observerCallback) observerCallback();
assert(!valid.classList.contains('sc-factory-row'), 'A prepared row must be unprepared when DataTables later turns it into a child row.');
assert(-1 === valid.tabIndex, 'A row that becomes invalid must no longer be keyboard focusable.');
assert(null === valid.getAttribute('aria-haspopup'), 'A row that becomes invalid must lose its dialog ARIA metadata.');

valid.classList.remove('child');
if (observerCallback) observerCallback();
assert(valid.classList.contains('sc-factory-row'), 'A newly valid row must be prepared after a DataTables redraw.');
assert(0 === valid.tabIndex, 'A newly valid row must become keyboard focusable after a DataTables redraw.');

if (failures.length) {
	console.error(failures.join('\n'));
	process.exit(1);
}

console.log('Factory table row-preparation regression tests passed.');
