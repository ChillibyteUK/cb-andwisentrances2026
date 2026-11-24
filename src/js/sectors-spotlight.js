/**
 * Sectors Spotlight block ([data-spotlight], src/blocks/sectors-spotlight.css).
 *
 * Moves the mint circle to the pointer by writing --x / --y (relative to the
 * panel) for the CSS mask, and drifts the column a little as the section
 * scrolls through the viewport. Both are desktop, real-pointer effects: the
 * CSS only shows the mint layer from the lg breakpoint with a fine pointer,
 * and the drift is skipped below lg and for reduced motion.
 */
const DESKTOP = window.matchMedia('(min-width: 992px)');
const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)');
const DRIFT = 2; // same strength as the lifts block's data-rellax-speed="2"

export function initSectorSpotlights() {
	const panels = Array.from(document.querySelectorAll('[data-spotlight]'));
	if (!panels.length) return;

	panels.forEach((panel) => {
		function follow(event) {
			if (event.pointerType && event.pointerType !== 'mouse') return;
			const rect = panel.getBoundingClientRect();
			panel.style.setProperty('--x', `${(event.clientX - rect.left).toFixed(1)}px`);
			panel.style.setProperty('--y', `${(event.clientY - rect.top).toFixed(1)}px`);
		}

		panel.addEventListener('pointerenter', follow);
		panel.addEventListener('pointermove', follow);
	});

	function drift() {
		panels.forEach((panel) => {
			if (!DESKTOP.matches || REDUCED.matches) {
				panel.style.transform = '';
				return;
			}

			const section = panel.closest('section') || panel;
			const rect = section.getBoundingClientRect();
			const viewport = window.innerHeight;
			if (rect.bottom <= 0 || rect.top >= viewport) return;

			const progress = Math.max(0, Math.min(1, (viewport - rect.top) / (viewport + rect.height)));
			const y = (progress - 0.5) * DRIFT * 80 - 40;
			panel.style.transform = `translate3d(0, ${y.toFixed(1)}px, 0)`;
		});
	}

	let ticking = false;
	function onScroll() {
		if (ticking) return;
		ticking = true;
		window.requestAnimationFrame(() => {
			drift();
			ticking = false;
		});
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll);
	drift();
}
