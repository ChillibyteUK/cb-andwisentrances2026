/**
 * Scroll-in reveal for anything carrying .cb-fade (src/css/components.css).
 * Adds .is-visible once the element enters the viewport; children of a
 * [data-stagger] parent get a staggered delay so card grids ripple in.
 * Reduced-motion users get everything visible immediately via the CSS.
 */
export function initFade() {
	const items = document.querySelectorAll('.cb-fade');
	if (!items.length) return;

	if (!('IntersectionObserver' in window)) {
		items.forEach((item) => item.classList.add('is-visible'));
		return;
	}

	document.querySelectorAll('[data-stagger]').forEach((parent) => {
		parent.querySelectorAll(':scope > .cb-fade, :scope > * > .cb-fade').forEach((child, i) => {
			child.style.transitionDelay = `${Math.min(i, 8) * 90}ms`;
		});
	});

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					observer.unobserve(entry.target);
				}
			});
		},
		{ rootMargin: '0px 0px -10% 0px', threshold: 0.1 }
	);

	items.forEach((item) => observer.observe(item));
}
