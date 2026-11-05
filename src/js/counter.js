/**
 * Count-up for stat values: <span data-count="300" data-prefix="" data-suffix="+">
 * Runs once when the element scrolls into view. Non-numeric values (e.g.
 * "Top 5") are left exactly as rendered.
 */
export function initCounters() {
	const counters = document.querySelectorAll('[data-count]');
	if (!counters.length) return;

	const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function run(el) {
		const target = parseFloat(el.dataset.count);
		if (Number.isNaN(target)) return;
		const prefix = el.dataset.prefix || '';
		const suffix = el.dataset.suffix || '';
		const decimals = (String(el.dataset.count).split('.')[1] || '').length;
		const duration = 1400;
		const start = performance.now();

		function frame(now) {
			const t = Math.min(1, (now - start) / duration);
			const eased = 1 - Math.pow(1 - t, 3);
			el.textContent = prefix + (target * eased).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',') + suffix;
			if (t < 1) requestAnimationFrame(frame);
		}

		requestAnimationFrame(frame);
	}

	if (reduced || !('IntersectionObserver' in window)) return;

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					run(entry.target);
					observer.unobserve(entry.target);
				}
			});
		},
		{ threshold: 0.4 }
	);

	counters.forEach((el) => observer.observe(el));
}
