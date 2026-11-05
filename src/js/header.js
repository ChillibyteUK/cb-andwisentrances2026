/**
 * Header solid/transparent state. #masthead starts transparent over a page
 * that opens with the Hero block (body.has-hero) and gets .is-solid once the
 * page has scrolled past the top, or whenever the mobile nav is open — see
 * src/css/nav.css for what each state looks like.
 */
export function initHeader() {
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

	window.addEventListener('scroll', onScroll, { passive: true });
	if (toggler) toggler.addEventListener('click', () => window.requestAnimationFrame(update));
	update();
}
