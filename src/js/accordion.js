/**
 * Accordion: a group of <button aria-controls aria-expanded> toggles whose
 * panels are [hidden] when closed. Inside a [data-accordion="single"] group
 * opening one closes the rest. Used by the Feature Accordion and FAQ blocks.
 */
export function initAccordions() {
	document.querySelectorAll('[data-accordion]').forEach((group) => {
		const single = group.dataset.accordion === 'single';
		const toggles = group.querySelectorAll('[data-accordion-toggle]');

		function setOpen(toggle, open) {
			const panel = document.getElementById(toggle.getAttribute('aria-controls'));
			if (!panel) return;
			toggle.setAttribute('aria-expanded', String(open));
			panel.hidden = !open;
			toggle.closest('[data-accordion-item]')?.classList.toggle('is-open', open);
		}

		toggles.forEach((toggle) => {
			setOpen(toggle, toggle.getAttribute('aria-expanded') === 'true');
			toggle.addEventListener('click', () => {
				const willOpen = toggle.getAttribute('aria-expanded') !== 'true';
				if (single && willOpen) {
					toggles.forEach((other) => other !== toggle && setOpen(other, false));
				}
				setOpen(toggle, willOpen);
			});
		});
	});
}
