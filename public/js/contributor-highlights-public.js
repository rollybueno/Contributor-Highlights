(function () {
	'use strict';

	function onReady(callback) {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', callback);
			return;
		}

		callback();
	}

	onReady(function () {
		document.querySelectorAll('.contributor-profile img').forEach(function (img) {
			img.addEventListener('error', function () {
				img.src = 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y';
			});
		});

		document.querySelectorAll('.contributor-bio-toggle').forEach(function (toggle) {
			toggle.addEventListener('click', function () {
				var section = toggle.closest('.contributor-bio');
				if (!section) {
					return;
				}

				var preview = section.querySelector('.contributor-bio-preview');
				var full = section.querySelector('.contributor-bio-full');
				var isExpanded = toggle.getAttribute('aria-expanded') === 'true';
				var readMore = toggle.getAttribute('data-read-more') || 'Read more';
				var readLess = toggle.getAttribute('data-read-less') || 'Read less';

				if (isExpanded) {
					if (full) {
						full.hidden = true;
					}
					if (preview) {
						preview.hidden = false;
					}
					toggle.setAttribute('aria-expanded', 'false');
					toggle.textContent = readMore;
					return;
				}

				if (preview) {
					preview.hidden = true;
				}
				if (full) {
					full.hidden = false;
				}
				toggle.setAttribute('aria-expanded', 'true');
				toggle.textContent = readLess;
			});
		});
	});
})();
