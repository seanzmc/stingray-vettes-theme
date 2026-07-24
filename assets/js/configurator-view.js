(function () {
  'use strict';

  var modalSelector = '.wpdt-c .modal-content, .wdt-md-modal .modal-content, .frm-modal-sc .modal-content';
  var printButtonClass = 'sc-configurator-print-button';
  var sheetId = 'sc-configurator-print-sheet';
  var cleanupTimer = 0;

  function cleanupPrintSheet() {
    clearTimeout(cleanupTimer);
    cleanupTimer = 0;
    var oldSheet = document.querySelector('#' + sheetId);
    if (oldSheet) oldSheet.remove();
    document.body.classList.remove('sc-configurator-printing');
  }

  function calculateScale(contentWidth, contentHeight, boxWidth, boxHeight) {
    var measurements = [contentWidth, contentHeight, boxWidth, boxHeight];
    var valid = measurements.every(function (measurement) {
      return typeof measurement === 'number' && isFinite(measurement) && measurement > 0;
    });
    if (!valid) return null;

    var scale = Math.min(1, boxWidth / contentWidth, boxHeight / contentHeight);
    return isFinite(scale) && scale > 0 ? scale : null;
  }

  function resetPrintNode(element, className) {
    element.getAttributeNames().forEach(function (attributeName) {
      element.removeAttribute(attributeName);
    });
    element.classList.add(className);
  }

  function buildPrintSheet(modal) {
    var summary = modal.querySelector('[data-key="plaintextbuildsummary"]');
    if (!summary) return null;

    cleanupPrintSheet();

    var sheet = document.createElement('section');
    var inner = document.createElement('div');
    var summaryClone = summary.cloneNode(true);

    sheet.setAttribute('id', sheetId);
    sheet.setAttribute('aria-hidden', 'true');
    inner.classList.add('sc-configurator-print-inner');
    resetPrintNode(summaryClone, 'sc-configurator-print-body');

    inner.appendChild(summaryClone);
    sheet.appendChild(inner);
    document.body.appendChild(sheet);

    var scale = calculateScale(
      inner.scrollWidth,
      inner.scrollHeight,
      sheet.clientWidth,
      sheet.clientHeight
    );
    if (null === scale) {
      cleanupPrintSheet();
      return null;
    }

    var serializedScale = String(scale);
    inner.style.transform = 'scale(' + serializedScale + ')';
    sheet.setAttribute('data-print-scale', serializedScale);
    return sheet;
  }

  function printModal(modal) {
    if (!buildPrintSheet(modal)) return;
    document.body.classList.add('sc-configurator-printing');
    window.requestAnimationFrame(function () {
      window.requestAnimationFrame(function () {
        window.print();
        cleanupTimer = setTimeout(cleanupPrintSheet, 1000);
      });
    });
  }

  function addPrintButton(modal) {
    var header = modal.querySelector('.modal-header');
    if (!header || modal.querySelector('.' + printButtonClass)) return;

    var button = document.createElement('button');
    button.classList.add(printButtonClass);
    button.setAttribute('type', 'button');
    button.textContent = 'Print order';
    button.addEventListener('click', function () {
      printModal(modal);
    });
    header.appendChild(button);
  }

  function scanForModals() {
    Array.prototype.forEach.call(document.querySelectorAll(modalSelector), addPrintButton);
  }

  window.addEventListener('afterprint', cleanupPrintSheet);
  new MutationObserver(scanForModals).observe(document.body, {
    childList: true,
    subtree: true
  });
  scanForModals();
})();
