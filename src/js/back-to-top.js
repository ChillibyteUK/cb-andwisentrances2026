/**
 * Footer "Back to top?" pill: smooth scroll to the top (instant when the
 * visitor prefers reduced motion) and move focus to the skip link so
 * keyboard users land at the start of the page too.
 */
export function initBackToTop() {
	document.querySelectorAll('[data-back-to-top]').forEach((button) => {
		button.addEventListener('click', () => {
			const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
			const target = document.querySelector('.skip-link') || document.body;
			target.focus({ preventScroll: true });
		});
	});
}
