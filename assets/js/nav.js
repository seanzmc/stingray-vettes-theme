/* Mobile drawer toggle for the shared topbar. Ported unchanged in behavior
   from stingray-homepage/index.html. */
(function () {
	'use strict';
	var drawer = document.getElementById('drawer');
	var menuBtn = document.getElementById('menuBtn');
	var closeBtn = document.getElementById('closeBtn');
	if (!drawer || !menuBtn || !closeBtn) return;
	menuBtn.addEventListener('click', function () {
		drawer.classList.add('open');
		menuBtn.setAttribute('aria-expanded', 'true');
	});
	function close() {
		drawer.classList.remove('open');
		menuBtn.setAttribute('aria-expanded', 'false');
	}
	closeBtn.addEventListener('click', close);
	drawer.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', close); });
})();
