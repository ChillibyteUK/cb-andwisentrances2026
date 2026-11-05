/**
 * Footer "Back to top?" pill: smooth scroll to the top (instant when the
 * visitor prefers reduced motion). When activated from the keyboard
 * (event.detail is 0 - no mouse click count) focus moves to the skip link
 * so the tab order restarts at the top; a mouse or touch click just drops
 * focus, because focusing the skip link would make it pop into view.
 */
export function initBackToTop() {
	document.querySelectorAll('[data-back-to-top]').forEach((button) => {
		button.addEventListener('click', (event) => {
			const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });

			if (event.detail === 0) {
				const target = document.querySelector('.skip-link') || document.body;
				target.focus({ preventScroll: true });
			} else {
				button.blur();
			}
		});
	});
}
