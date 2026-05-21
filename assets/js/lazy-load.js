(function () {
	'use strict';

	function activateIframe(container) {
		var iframe = container.querySelector('.ipe-profile-embed__iframe');
		if (!iframe || iframe.dataset.ipeLoaded === '1') {
			return;
		}

		var src = iframe.getAttribute('data-ipe-src');
		if (!src) {
			return;
		}

		iframe.src = src;
		iframe.dataset.ipeLoaded = '1';
		container.dataset.ipeLoaded = '1';
	}

	function observeContainer(container) {
		if (!('IntersectionObserver' in window)) {
			activateIframe(container);
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						activateIframe(entry.target);
						observer.unobserve(entry.target);
					}
				});
			},
			{
				rootMargin: '200px 0px',
			}
		);

		observer.observe(container);
	}

	document.addEventListener('DOMContentLoaded', function () {
		var containers = document.querySelectorAll('.ipe-profile-embed--lazy');
		containers.forEach(function (container) {
			observeContainer(container);
		});
	});
})();
