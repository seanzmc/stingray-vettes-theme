/* Factory table row-detail dialog. Keeps the public lookup table compact while
   exposing the complete Google Sheet row without changing wpDataTables data. */
(function () {
	'use strict';

	var table = document.querySelector('.sc-embed table.wpDataTable');
	if (!table) return;

	var headers = Array.prototype.map.call(table.querySelectorAll('thead tr:first-child th'), function (header) {
		return header.textContent.trim();
	});
	if (headers.length < 4) return;

	var overlay = document.createElement('div');
	overlay.className = 'sc-factory-modal';
	overlay.hidden = true;

	var panel = document.createElement('section');
	panel.className = 'sc-factory-modal__panel';
	panel.setAttribute('role', 'dialog');
	panel.setAttribute('aria-modal', 'true');
	panel.setAttribute('aria-labelledby', 'sc-factory-modal-title');

	var modalHeader = document.createElement('header');
	modalHeader.className = 'sc-factory-modal__header';

	var headingGroup = document.createElement('div');
	var eyebrow = document.createElement('p');
	eyebrow.className = 'sc-factory-modal__eyebrow';
	eyebrow.textContent = 'Factory order details';
	var title = document.createElement('h2');
	title.id = 'sc-factory-modal-title';
	headingGroup.appendChild(eyebrow);
	headingGroup.appendChild(title);

	var closeButton = document.createElement('button');
	closeButton.className = 'sc-factory-modal__close';
	closeButton.type = 'button';
	closeButton.setAttribute('aria-label', 'Close factory order details');
	closeButton.textContent = '×';

	modalHeader.appendChild(headingGroup);
	modalHeader.appendChild(closeButton);

	var details = document.createElement('dl');
	details.className = 'sc-factory-modal__details';

	panel.appendChild(modalHeader);
	panel.appendChild(details);
	overlay.appendChild(panel);
	document.body.appendChild(overlay);

	var previousFocus = null;

	function isFactoryRow(row) {
		if (!row || row.cells.length !== headers.length) return false;
		if (
			row.classList.contains('child') ||
			row.classList.contains('dtrg-group') ||
			row.classList.contains('group')
		) return false;

		var orderCell = row.cells[0];
		if (
			!orderCell ||
			orderCell.colSpan > 1 ||
			orderCell.classList.contains('dataTables_empty') ||
			!orderCell.textContent.trim()
		) return false;

		return true;
	}

	function unprepareRow(row) {
		row.classList.remove('sc-factory-row');
		row.removeAttribute('tabindex');
		row.removeAttribute('aria-haspopup');
		row.removeAttribute('aria-label');
	}

	function prepareRows() {
		Array.prototype.forEach.call(table.querySelectorAll('tbody tr'), function (row) {
			if (!isFactoryRow(row)) {
				unprepareRow(row);
				return;
			}
			var orderCell = row.cells[0];
			row.classList.add('sc-factory-row');
			row.tabIndex = 0;
			row.setAttribute('aria-haspopup', 'dialog');
			row.setAttribute('aria-label', 'View details for factory order ' + orderCell.textContent.trim());
		});
	}

	function openDialog(row) {
		var cells = Array.prototype.slice.call(row.cells);
		var orderNumber = cells[0] ? cells[0].textContent.trim() : '';
		previousFocus = document.activeElement;
		title.textContent = orderNumber ? 'Order ' + orderNumber : 'Factory order';
		details.replaceChildren();

		headers.forEach(function (header, index) {
			var value = cells[index] ? cells[index].textContent.trim() : '';
			if (!header || !value) return;

			var item = document.createElement('div');
			item.className = 'sc-factory-modal__detail';
			var term = document.createElement('dt');
			term.textContent = header;
			var description = document.createElement('dd');
			description.textContent = value;
			item.appendChild(term);
			item.appendChild(description);
			details.appendChild(item);
		});

		overlay.hidden = false;
		document.body.classList.add('sc-dialog-open');
		closeButton.focus();
	}

	function closeDialog() {
		if (overlay.hidden) return;
		overlay.hidden = true;
		document.body.classList.remove('sc-dialog-open');
		if (previousFocus && typeof previousFocus.focus === 'function') {
			previousFocus.focus();
		}
	}

	function rowFromEvent(event) {
		if (event.target.closest('a, button, input, select, textarea')) return null;
		var row = event.target.closest('tbody tr');
		if (!row || !table.contains(row) || !isFactoryRow(row)) {
			if (row && table.contains(row)) unprepareRow(row);
			return null;
		}
		return row.classList.contains('sc-factory-row') ? row : null;
	}

	table.addEventListener('click', function (event) {
		var row = rowFromEvent(event);
		if (!row) return;
		event.stopImmediatePropagation();
		openDialog(row);
	}, true);

	table.addEventListener('keydown', function (event) {
		if ('Enter' !== event.key && ' ' !== event.key) return;
		var row = rowFromEvent(event);
		if (!row || event.target !== row) return;
		event.preventDefault();
		event.stopImmediatePropagation();
		openDialog(row);
	});

	closeButton.addEventListener('click', closeDialog);
	overlay.addEventListener('click', function (event) {
		if (event.target === overlay) closeDialog();
	});

	document.addEventListener('keydown', function (event) {
		if (overlay.hidden) return;
		if ('Escape' === event.key) {
			event.preventDefault();
			closeDialog();
			return;
		}
		if ('Tab' !== event.key) return;

		var focusable = panel.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
		if (!focusable.length) return;
		var first = focusable[0];
		var last = focusable[focusable.length - 1];
		if (event.shiftKey && document.activeElement === first) {
			event.preventDefault();
			last.focus();
		} else if (!event.shiftKey && document.activeElement === last) {
			event.preventDefault();
			first.focus();
		}
	});

	prepareRows();
	new MutationObserver(prepareRows).observe(table.tBodies[0] || table, {
		childList: true,
		subtree: true
	});
})();
