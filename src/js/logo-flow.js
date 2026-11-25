/**
 * Logo Flow block ([data-logo-flow], src/blocks/logo-flow.css).
 *
 * Arms the entrance sequence (.is-ready hides the boxes) and plays it
 * (.is-in) once the row is a quarter of the way up the viewport. All the
 * timing is in the CSS. Skipped for reduced motion and where there is no
 * IntersectionObserver, which leaves the row plainly visible.
 */
export function initLogoFlows() {
	const flows = document.querySelectorAll('[data-logo-flow]');
	if (!flows.length) return;

	const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (reduced || !('IntersectionObserver' in window)) return;

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) return;
				entry.target.classList.add('is-in');
				observer.unobserve(entry.target);
			});
		},
		{ rootMargin: '0px 0px -25% 0px' }
	);

	flows.forEach((flow) => {
		flow.classList.add('is-ready');
		observer.observe(flow);
	});
}
