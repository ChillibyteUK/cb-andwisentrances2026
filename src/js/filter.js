/**
 * Client-side chip filter, shared by every index (news, case studies, FAQs).
 *
 * Markup contract:
 *   [data-filter-root]
 *     .cb-chip[data-filter="*|term"] ...        (buttons; aria-pressed kept in sync)
 *     [data-filter-items] > [data-filter-terms="a b c"] ...
 *     [data-filter-empty] (optional, shown when nothing matches)
 */
export function initFilters() {
	document.querySelectorAll('[data-filter-root]').forEach((root) => {
		const chips = root.querySelectorAll('[data-filter]');
		const items = root.querySelectorAll('[data-filter-terms]');
		const empty = root.querySelector('[data-filter-empty]');
		if (!chips.length || !items.length) return;

		function apply(term) {
			let shown = 0;
			items.forEach((item) => {
				const terms = (item.dataset.filterTerms || '').split(/\s+/);
				const match = term === '*' || terms.includes(term);
				item.hidden = !match;
				if (match) shown += 1;
			});
			if (empty) empty.hidden = shown > 0;
			chips.forEach((chip) => {
				const active = chip.dataset.filter === term;
				chip.classList.toggle('is-active', active);
				chip.setAttribute('aria-pressed', String(active));
			});
		}

		chips.forEach((chip) => {
			chip.addEventListener('click', () => apply(chip.dataset.filter));
		});
	});
}
