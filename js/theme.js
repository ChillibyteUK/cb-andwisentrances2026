/*!
 * cb-andwisentrances2026 v1.0.0 (https://github.com/ChillibyteUK/cb-andwisentrances2026)
 * Copyright 2026 Chillibyte - DS
 * Licensed under GPL-3.0
 */
(function () {
	'use strict';

	/**
	 * Mobile nav toggle. Wires any button with aria-controls pointing at a
	 * .navbar-collapse to show/hide it and keep aria-expanded in sync — this is
	 * the entire replacement for Bootstrap's Collapse component for this use case.
	 */
	function initNavToggle() {
	  document.querySelectorAll('.navbar-toggler[aria-controls]').forEach(toggler => {
	    const target = document.getElementById(toggler.getAttribute('aria-controls'));
	    if (!target) return;
	    toggler.addEventListener('click', () => {
	      const isOpen = target.classList.toggle('is-open');
	      toggler.setAttribute('aria-expanded', String(isOpen));
	    });

	    // Close after choosing a link — expected mobile nav behaviour.
	    target.querySelectorAll('a').forEach(link => {
	      link.addEventListener('click', () => {
	        target.classList.remove('is-open');
	        toggler.setAttribute('aria-expanded', 'false');
	      });
	    });
	  });
	}

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
	function initNavDropdowns() {
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
	    toggles.forEach(toggle => {
	      if (toggle !== except) setOpen(toggle, false);
	    });
	  }
	  toggles.forEach(toggle => {
	    const menu = menuFor(toggle);
	    if (!menu) return;
	    const item = toggle.closest('.dropdown');
	    let leaveTimer = 0;
	    toggle.addEventListener('click', event => {
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
	      sets.forEach(set => {
	        const match = set.dataset.exampleSet === id;
	        set.hidden = !match;
	        if (match && label) label.textContent = set.dataset.exampleTitle || '';
	      });
	    }
	    menu.querySelectorAll('[data-example]').forEach(link => {
	      ['mouseenter', 'focus'].forEach(type => {
	        link.addEventListener(type, () => showSet(link.dataset.example));
	      });
	    });
	  });
	  document.addEventListener('click', event => {
	    if (event.target.closest('.dropdown-menu')) return;
	    closeAllExcept();
	  });
	  document.addEventListener('keydown', event => {
	    if (event.key !== 'Escape') return;
	    const openToggle = toggles.find(toggle => toggle.getAttribute('aria-expanded') === 'true');
	    closeAllExcept();
	    if (openToggle) openToggle.focus();
	  });

	  // Tabbing out of an open dropdown closes it.
	  document.addEventListener('focusin', event => {
	    toggles.forEach(toggle => {
	      if (toggle.getAttribute('aria-expanded') !== 'true') return;
	      const item = toggle.closest('.dropdown');
	      if (item && !item.contains(event.target)) setOpen(toggle, false);
	    });
	  });
	}

	/**
	 * Native <dialog> wiring — replaces Bootstrap's Modal component entirely.
	 * showModal()/close() do the heavy lifting (focus trap, Escape-to-close,
	 * ::backdrop); this just connects trigger/close buttons to a target dialog.
	 *
	 * Markup:
	 *   <button data-dialog-target="my-dialog">Open</button>
	 *   <dialog id="my-dialog">
	 *     <button data-dialog-close>Close</button>
	 *     ...
	 *   </dialog>
	 *
	 * A dialog containing <iframe data-src="..."> (click-to-play video embeds)
	 * only loads the iframe when the dialog opens — nothing is fetched or
	 * tracked until asked for — and unloads it again on close, so background
	 * playback stops the moment the modal closes.
	 */
	function initDialogs() {
	  document.querySelectorAll('[data-dialog-target]').forEach(trigger => {
	    const dialog = document.getElementById(trigger.getAttribute('data-dialog-target'));
	    if (!(dialog instanceof HTMLDialogElement)) return;
	    trigger.addEventListener('click', () => {
	      dialog.querySelectorAll('iframe[data-src]').forEach(frame => {
	        if (!frame.getAttribute('src')) frame.setAttribute('src', frame.getAttribute('data-src'));
	      });
	      dialog.showModal();
	    });
	    dialog.querySelectorAll('[data-dialog-close]').forEach(closeBtn => {
	      closeBtn.addEventListener('click', () => dialog.close());
	    });

	    // Click on the backdrop (the dialog element itself, outside its content) closes it.
	    dialog.addEventListener('click', event => {
	      if (event.target === dialog) dialog.close();
	    });

	    // Unload lazy iframes on close — removes the video from the page so
	    // playback (and tracking) stops, ready to load fresh next open.
	    dialog.addEventListener('close', () => {
	      dialog.querySelectorAll('iframe[data-src]').forEach(frame => {
	        frame.removeAttribute('src');
	      });
	    });
	  });
	}

	/**
	 * Header solid/transparent state. #masthead starts transparent over a page
	 * that opens with the Hero block (body.has-hero) and gets .is-solid once the
	 * page has scrolled past the top, or whenever the mobile nav is open — see
	 * src/css/nav.css for what each state looks like.
	 */
	function initHeader() {
	  const header = document.getElementById('masthead');
	  if (!header) return;
	  const toggler = header.querySelector('.navbar-toggler');
	  let ticking = false;
	  function update() {
	    const scrolled = window.scrollY > 24;
	    const navOpen = toggler && toggler.getAttribute('aria-expanded') === 'true';
	    header.classList.toggle('is-solid', scrolled || navOpen);
	    ticking = false;
	  }
	  function onScroll() {
	    if (!ticking) {
	      window.requestAnimationFrame(update);
	      ticking = true;
	    }
	  }
	  window.addEventListener('scroll', onScroll, {
	    passive: true
	  });
	  if (toggler) toggler.addEventListener('click', () => window.requestAnimationFrame(update));
	  update();
	}

	/**
	 * Scroll-in reveal for anything carrying .cb-fade (src/css/components.css).
	 * Adds .is-visible once the element enters the viewport; children of a
	 * [data-stagger] parent get a staggered delay so card grids ripple in.
	 * Reduced-motion users get everything visible immediately via the CSS.
	 */
	function initFade() {
	  const items = document.querySelectorAll('.cb-fade');
	  if (!items.length) return;
	  if (!('IntersectionObserver' in window)) {
	    items.forEach(item => item.classList.add('is-visible'));
	    return;
	  }
	  document.querySelectorAll('[data-stagger]').forEach(parent => {
	    parent.querySelectorAll(':scope > .cb-fade, :scope > * > .cb-fade').forEach((child, i) => {
	      child.style.transitionDelay = `${Math.min(i, 8) * 90}ms`;
	    });
	  });
	  const observer = new IntersectionObserver(entries => {
	    entries.forEach(entry => {
	      if (entry.isIntersecting) {
	        entry.target.classList.add('is-visible');
	        observer.unobserve(entry.target);
	      }
	    });
	  }, {
	    rootMargin: '0px 0px -10% 0px',
	    threshold: 0.1
	  });
	  items.forEach(item => observer.observe(item));
	}

	/**
	 * Count-up for stat values: <span data-count="300" data-prefix="" data-suffix="+">
	 * Runs once when the element scrolls into view. Non-numeric values (e.g.
	 * "Top 5") are left exactly as rendered.
	 */
	function initCounters() {
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
	  const observer = new IntersectionObserver(entries => {
	    entries.forEach(entry => {
	      if (entry.isIntersecting) {
	        run(entry.target);
	        observer.unobserve(entry.target);
	      }
	    });
	  }, {
	    threshold: 0.4
	  });
	  counters.forEach(el => observer.observe(el));
	}

	/**
	 * Client-side chip filter, shared by every index (news, case studies, FAQs).
	 *
	 * Markup contract:
	 *   [data-filter-root]
	 *     .cb-chip[data-filter="*|term"] ...        (buttons; aria-pressed kept in sync)
	 *     [data-filter-items] > [data-filter-terms="a b c"] ...
	 *     [data-filter-empty] (optional, shown when nothing matches)
	 */
	function initFilters() {
	  document.querySelectorAll('[data-filter-root]').forEach(root => {
	    const chips = root.querySelectorAll('[data-filter]');
	    const items = root.querySelectorAll('[data-filter-terms]');
	    const empty = root.querySelector('[data-filter-empty]');
	    if (!chips.length || !items.length) return;
	    function apply(term) {
	      let shown = 0;
	      items.forEach(item => {
	        const terms = (item.dataset.filterTerms || '').split(/\s+/);
	        const match = term === '*' || terms.includes(term);
	        item.hidden = !match;
	        if (match) shown += 1;
	      });
	      if (empty) empty.hidden = shown > 0;
	      chips.forEach(chip => {
	        const active = chip.dataset.filter === term;
	        chip.classList.toggle('is-active', active);
	        chip.setAttribute('aria-pressed', String(active));
	      });
	    }
	    chips.forEach(chip => {
	      chip.addEventListener('click', () => apply(chip.dataset.filter));
	    });
	  });
	}

	/**
	 * Accordion: a group of <button aria-controls aria-expanded> toggles whose
	 * panels are [hidden] when closed. Inside a [data-accordion="single"] group
	 * opening one closes the rest. Used by the Feature Accordion and FAQ blocks.
	 */
	function initAccordions() {
	  document.querySelectorAll('[data-accordion]').forEach(group => {
	    const single = group.dataset.accordion === 'single';
	    const toggles = group.querySelectorAll('[data-accordion-toggle]');
	    function setOpen(toggle, open) {
	      const panel = document.getElementById(toggle.getAttribute('aria-controls'));
	      if (!panel) return;
	      toggle.setAttribute('aria-expanded', String(open));
	      panel.hidden = !open;
	      toggle.closest('[data-accordion-item]')?.classList.toggle('is-open', open);
	    }
	    toggles.forEach(toggle => {
	      setOpen(toggle, toggle.getAttribute('aria-expanded') === 'true');
	      toggle.addEventListener('click', () => {
	        const willOpen = toggle.getAttribute('aria-expanded') !== 'true';
	        if (single && willOpen) {
	          toggles.forEach(other => other !== toggle && setOpen(other, false));
	        }
	        setOpen(toggle, willOpen);
	      });
	    });
	  });
	}

	document.addEventListener('DOMContentLoaded', () => {
	  initHeader();
	  initNavToggle();
	  initNavDropdowns();
	  initDialogs();
	  initFade();
	  initCounters();
	  initFilters();
	  initAccordions();
	});

})();
//# sourceMappingURL=theme.js.map
