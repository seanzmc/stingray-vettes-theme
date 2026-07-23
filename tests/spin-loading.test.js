/* Run: node tests/spin-loading.test.js */
'use strict';

var fs = require('fs');
var path = require('path');
var vm = require('vm');

var root = path.join(__dirname, '..');
var spinRoot = path.join(root, 'assets', 'homepage', 'spin');
var source = fs.readFileSync(path.join(root, 'assets', 'homepage', 'spin.js'), 'utf8');
var failures = [];

function assert(condition, message) {
	if (!condition) failures.push(message);
}

function collect(directory, suffix) {
	var files = [];
	fs.readdirSync(directory, { withFileTypes: true }).forEach(function (entry) {
		var target = path.join(directory, entry.name);
		if (entry.isDirectory()) files = files.concat(collect(target, suffix));
		else if (target.endsWith(suffix)) files.push(target);
	});
	return files;
}

function fakeNode() {
	return {
		style: { setProperty: function () {} },
		closest: function () { return null; },
		getBoundingClientRect: function () { return { width: 750, height: 375, top: 0, bottom: 0 }; },
		addEventListener: function () {},
		getContext: function () {
			return {
				clearRect: function () {},
				drawImage: function () {},
				globalAlpha: 1
			};
		},
		width: 750,
		height: 375
	};
}

function runViewer(options) {
	options = options || {};
	var requests = [];
	var images = [];
	var canvas = fakeNode();
	var loader = fakeNode();
	var hint = fakeNode();

	function FakeImage() {
		this.complete = false;
		this.naturalWidth = 0;
		this.naturalHeight = 0;
		images.push(this);
	}
	Object.defineProperty(FakeImage.prototype, 'src', {
		set: function (value) {
			this.currentSrc = value;
			requests.push(value);
		}
	});
	FakeImage.prototype.succeed = function () {
		this.complete = true;
		this.naturalWidth = 1500;
		this.naturalHeight = 750;
		if (this.onload) this.onload();
	};
	FakeImage.prototype.fail = function () {
		if (this.onerror) this.onerror();
	};

	var document = {
		documentElement: fakeNode(),
		querySelector: function () { return null; },
		getElementById: function (id) {
			if ('spinCanvas' === id) return canvas;
			if ('spinLoader' === id) return loader;
			if ('spinHint' === id) return hint;
			return null;
		}
	};
	var window = {
		SC_SPIN_BASE: 'https://example.test/theme/spin/',
		devicePixelRatio: 1,
		matchMedia: function (query) {
			return {
				matches: query.indexOf('prefers-reduced-motion: reduce') !== -1 ? !!options.reduce : false,
				addEventListener: function () {},
				addListener: function () {}
			};
		},
		addEventListener: function () {}
	};
	var context = {
		window: window,
		document: document,
		navigator: { connection: { saveData: !!options.saveData } },
		Image: FakeImage,
		ResizeObserver: function () { this.observe = function () {}; },
		requestAnimationFrame: function () { return 1; },
		cancelAnimationFrame: function () {},
		performance: { now: function () { return 0; } },
		Math: Math,
		Array: Array,
		String: String
	};
	vm.runInNewContext(source, context);
	return { requests: requests, images: images };
}

var webpFrames = collect(spinRoot, '.webp');
var pngFrames = collect(spinRoot, '.png');
assert(150 === webpFrames.length, 'Exactly 150 WebP frames must exist.');
assert(150 === pngFrames.length, 'Exactly 150 PNG fallback frames must remain.');

webpFrames.forEach(function (webp) {
	var png = webp
		.replace(/-webp([/\\])/, '$1')
		.replace(/-cmp\.webp$/, '.png');
	assert(fs.existsSync(png), 'Missing PNG fallback for ' + path.relative(root, webp));
});

var normal = runViewer();
assert(4 === normal.requests.length, 'Normal boot must start exactly four requests.');
assert(normal.requests.every(function (url) {
	return /gkz-red-webp\/gkz-ext\.\d{3}-cmp\.webp$/.test(url);
}), 'Normal boot must request Torch Red WebP frames first.');

normal.images[0].fail();
assert(5 === normal.requests.length, 'A failed WebP must issue one fallback request.');
assert(/gkz-red\/gkz-ext\.001\.png$/.test(normal.requests[4]), 'The first WebP must fall back to its matching PNG.');
assert(4 === normal.images.length, 'PNG fallback must reuse the same request slot and Image object.');

normal.images[0].succeed();
assert(6 === normal.requests.length, 'Completing a fallback must advance the bounded queue once.');

var saveData = runViewer({ saveData: true });
assert(1 === saveData.requests.length, 'Save-Data mode must request one static frame.');
assert(/gkz-red-webp\/gkz-ext\.001-cmp\.webp$/.test(saveData.requests[0]), 'Save-Data mode must use frame one WebP.');

var reduced = runViewer({ reduce: true });
assert(1 === reduced.requests.length, 'Reduced-motion mode must request one static frame.');

[
	"maxConcurrent: 4",
	'colorLeadFrames: 2',
	'navigator.connection && navigator.connection.saveData',
	'loadStaticFrame(colorIdx)'
].forEach(function (marker) {
	assert(source.indexOf(marker) !== -1, 'Missing runtime marker: ' + marker);
});

if (failures.length) {
	console.error(failures.join('\n'));
	process.exit(1);
}

console.log('Homepage spin-loading regression tests passed.');
