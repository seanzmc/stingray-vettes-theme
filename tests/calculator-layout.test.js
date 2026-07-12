/* Run: node tests/calculator-layout.test.js */
'use strict';

var fs = require('fs');
var path = require('path');

var styles = fs.readFileSync(path.join(__dirname, '..', 'assets', 'calculator', 'calculator.css'), 'utf8');
var failures = [];

function assert(condition, message) {
	if (!condition) failures.push(message);
}

assert(
	styles.includes('#quick-pencil .qp-row[style*="display: flex"] {\n  display: grid !important;\n}'),
	'Quick Pencil CSS must override the runtime inline flex display for visible rows.'
);
assert(
	styles.includes('grid-template-columns: minmax(0, 1fr) 280px;'),
	'Quick Pencil desktop rows must use a shared 280px control column.'
);
assert(
	styles.includes('#quick-pencil .qp-row input,\n#quick-pencil .qp-row select {\n  width: 100%;'),
	'Quick Pencil controls must fill the shared control column.'
);
assert(
	styles.includes('@media (max-width: 600px)') && styles.includes('grid-template-columns: 1fr;'),
	'Quick Pencil rows must retain the single-column mobile layout.'
);

if (failures.length) {
	console.error(failures.join('\n'));
	process.exit(1);
}

console.log('Quick Pencil layout regression tests passed.');
