/**
 * Nav dropdowns. Each dropdown-toggle button shows/hides its linked
 * .dropdown-menu and keeps aria-expanded in sync; clicking elsewhere or
 * pressing Escape closes whatever is open. From the lg breakpoint the
 * dropdowns also open on hover (with a short grace period on leaving, so
 * the pointer can cross the gap into the panel) - the click toggle stays
 * for keyboard and touch.
 *
 * Mega dropdowns carry a "Case study examples" panel with one hidden set
 * per child link; hovering or focusing a child reveals its set.
 */
const DESKTOP = window.matchMedia('(min-width: 992px)');

export function initNavDropdowns() {
	const toggles = Array.from(document.querySelectorAll('.dropdown-toggle[aria-controls]'));

	function menuFor(toggle) {
		return document.getElementById(toggle.getAttribute('aria-controls'));
	}

	function setOpen(toggle, open) {
		const menu = menuFor(toggle);
		if (!menu) return;
		menu.classList.toggle('is-open', open);
		toggle.setAttribute('aria-expanded', String(open));
		toggle.closest('.dropdown')?.classList.toggle('is-open', open);
	}

	function closeAllExcept(except) {
		toggles.forEach((toggle) => {
			if (toggle !== except) setOpen(toggle, false);
		});
	}

	toggles.forEach((toggle) => {
		const menu = menuFor(toggle);
		if (!menu) return;
		const item = toggle.closest('.dropdown');
		let leaveTimer = 0;

		toggle.addEventListener('click', (event) => {
			event.stopPropagation();
			const open = !menu.classList.contains('is-open');
			closeAllExcept(toggle);
			setOpen(toggle, open);
		});

		if (item) {
			item.addEventListener('mouseenter', () => {
				if (!DESKTOP.matches) return;
				window.clearTimeout(leaveTimer);
				closeAllExcept(toggle);
				setOpen(toggle, true);
			});

			item.addEventListener('mouseleave', () => {
				if (!DESKTOP.matches) return;
				leaveTimer = window.setTimeout(() => setOpen(toggle, false), 180);
			});
		}

		// Mega: swap the examples set to match the hovered / focused child.
		const label = menu.querySelector('[data-example-label]');
		const sets = Array.from(menu.querySelectorAll('[data-example-set]'));
		if (!sets.length) return;

		function showSet(id) {
			sets.forEach((set) => {
				const match = set.dataset.exampleSet === id;
				set.hidden = !match;
				if (match && label) label.textContent = set.dataset.exampleTitle || '';
			});
		}

		menu.querySelectorAll('[data-example]').forEach((link) => {
			['mouseenter', 'focus'].forEach((type) => {
				link.addEventListener(type, () => showSet(link.dataset.example));
			});
		});
	});

	document.addEventListener('click', (event) => {
		if (event.target.closest('.dropdown-menu')) return;
		closeAllExcept();
	});

	document.addEventListener('keydown', (event) => {
		if (event.key !== 'Escape') return;
		const openToggle = toggles.find((toggle) => toggle.getAttribute('aria-expanded') === 'true');
		closeAllExcept();
		if (openToggle) openToggle.focus();
	});

	// Tabbing out of an open dropdown closes it.
	document.addEventListener('focusin', (event) => {
		toggles.forEach((toggle) => {
			if (toggle.getAttribute('aria-expanded') !== 'true') return;
			const item = toggle.closest('.dropdown');
			if (item && !item.contains(event.target)) setOpen(toggle, false);
		});
	});
}
