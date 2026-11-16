/**
 * Nav dropdowns. Each dropdown-toggle button shows/hides its linked
 * .dropdown-menu and keeps aria-expanded in sync; clicking elsewhere or
 * pressing Escape closes whatever is open.
 *
 * From the lg breakpoint a real mouse also opens them on hover, with a short
 * grace period on leaving so the pointer can cross the gap into the panel.
 * Hover and click must not fight: a tap on a touch screen, or a click into a
 * window that was not receiving hover (Safari sends none to a background
 * window), delivers "pointer arrives" and "click" together, and a plain
 * toggle would open the menu and close it again in the same instant. So:
 *
 *   - only pointerType "mouse" opens on hover, never touch or pen;
 *   - a click on a menu that hover opened pins it open instead of closing
 *     it; a second click (or Escape / an outside click) closes it;
 *   - a pinned menu ignores the pointer leaving.
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

	// Toggles opened by a click (or the keyboard) rather than by hover.
	const pinned = new WeakSet();

	function setOpen(toggle, open) {
		const menu = menuFor(toggle);
		if (!menu) return;
		menu.classList.toggle('is-open', open);
		toggle.setAttribute('aria-expanded', String(open));
		toggle.closest('.dropdown')?.classList.toggle('is-open', open);
		if (!open) pinned.delete(toggle);
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
			window.clearTimeout(leaveTimer);
			closeAllExcept(toggle);

			// Open and pinned: this click closes it. Anything else (closed, or
			// open only because hover opened it) ends up open and pinned.
			if (menu.classList.contains('is-open') && pinned.has(toggle)) {
				setOpen(toggle, false);
				return;
			}

			setOpen(toggle, true);
			pinned.add(toggle);
		});

		if (item) {
			item.addEventListener('pointerenter', (event) => {
				if (event.pointerType !== 'mouse' || !DESKTOP.matches) return;
				window.clearTimeout(leaveTimer);
				if (menu.classList.contains('is-open')) return;
				closeAllExcept(toggle);
				setOpen(toggle, true);
			});

			item.addEventListener('pointerleave', (event) => {
				if (event.pointerType !== 'mouse' || !DESKTOP.matches || pinned.has(toggle)) return;
				leaveTimer = window.setTimeout(() => {
					if (!pinned.has(toggle)) setOpen(toggle, false);
				}, 180);
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
