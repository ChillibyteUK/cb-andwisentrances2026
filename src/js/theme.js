import { initNavToggle } from './nav-toggle';
import { initNavDropdowns } from './nav-dropdown';
import { initDialogs } from './dialog';
import { initHeader } from './header';
import { initFade } from './fade';
import { initCounters } from './counter';
import { initFilters } from './filter';
import { initAccordions } from './accordion';
import { initBackToTop } from './back-to-top';
import { initSectorSpotlights } from './sectors-spotlight';
import { initLogoFlows } from './logo-flow';

document.addEventListener('DOMContentLoaded', () => {
	initHeader();
	initNavToggle();
	initNavDropdowns();
	initDialogs();
	initFade();
	initCounters();
	initFilters();
	initAccordions();
	initBackToTop();
	initSectorSpotlights();
	initLogoFlows();
});
