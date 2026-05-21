(function () {
	'use strict';

	var embedScriptLoaded = false;

	function loadEmbedScript(callback) {
		if (embedScriptLoaded) {
			if (callback) {
				callback();
			}
			return;
		}

		var script = document.createElement('script');
		script.src = 'https://www.tiktok.com/embed.js';
		script.async = true;
		script.onload = function () {
			embedScriptLoaded = true;
			if (callback) {
				callback();
			}
		};
		document.body.appendChild(script);
	}

	function activateEmbed(container) {
		if (container.dataset.tpeLoaded === '1') {
			return;
		}

		container.dataset.tpeLoaded = '1';
		loadEmbedScript();
	}

	function observeContainer(container) {
		if (!('IntersectionObserver' in window)) {
			activateEmbed(container);
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						activateEmbed(entry.target);
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
		var containers = document.querySelectorAll('.tpe-profile-embed--lazy');

		containers.forEach(function (container) {
			observeContainer(container);
		});
	});
})();
