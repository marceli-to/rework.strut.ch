// Pages and viewports for the visual comparison (tests/visual/compare.js).
//
// `path` is requested on both sites. `mask` hides regions whose content is
// dynamic on purpose (legacy shuffles the slideshow per request, the map needs
// an API key); they are compared separately. `states` are interactive states,
// each with one action per site because the markup differs.

// Legacy CSS breakpoints 600/900/1200 (±1; the JS switches at 900/901)
// plus the standard widths from the brief.
export const viewports = [
	375, 390, 599, 600, 601, 768, 899, 900, 901,
	1024, 1199, 1200, 1201, 1280, 1440, 1920, 2560,
];

export const sites = {
	ref: 'https://strut.ch.test',
	act: 'https://rework.strut.ch.test',
};

const click = (selector) => async (page) => {
	await page.locator(selector).first().click({ timeout: 5000 });
};

// Clicks nav entries by their text, in order (legacy uses links, the rework buttons).
const clickNav = (...labels) => async (page) => {
	for (const label of labels) {
		await page.locator('nav a, nav button', { hasText: label }).first().click({ timeout: 5000 });
		await page.waitForTimeout(100);
	}
};

// Scrolls like a user (the header reacts to direction), then stays there.
const scrollBy = (...deltas) => async (page) => {
	for (const dy of deltas) {
		await page.mouse.wheel(0, dy);
		await page.waitForTimeout(250);
	}
};

// Projects covering every project grid layout, plus one without a detail text.
// `--all-projects` adds every project with a detail page.
export const sampleProjects = [1, 2, 14, 15, 60];

export const pages = [
	{
		key: 'home',
		path: '/',
		navStates: true,
		mask: { ref: ['.is-highlight'], act: ['[data-slideshow]'] },
	},
	{ key: 'werkliste-status', path: '/werkliste/status' },
	{ key: 'werkliste-jahr', path: '/werkliste/jahr' },
	{ key: 'werkliste-typ', path: '/werkliste/typ' },
	{ key: 'presse', path: '/presse' },
	{
		key: 'buecher',
		path: '/buecher',
		states: [
			{ name: 'info-open', ref: click('.js-msnry-btn'), act: click('[data-masonry-toggle]') },
		],
	},
	{ key: 'downloads', path: '/downloads' },
	{
		key: 'kontakt',
		path: '/kontakt',
		mask: { ref: ['#js-maps'], act: ['[data-map]'] },
		states: [
			{ name: 'impressum-open', ref: click('.contact__imprint .js-btn-toggle'), act: click('[aria-controls="impressum"]') },
			{ name: 'datenschutz-open', ref: click('.contact__privacy .js-btn-toggle'), act: click('[aria-controls="datenschutz"]') },
		],
	},
	{
		key: 'ueber-uns',
		path: '/ueber-uns',
		states: [
			{ name: 'cv-open', ref: click('.js-msnry-btn'), act: click('[data-masonry-toggle]') },
		],
	},
	{ key: 'jobs', path: '/jobs' },
	{ key: 'auszeichnungen', path: '/auszeichnungen' },
	{ key: 'vortraege', path: '/vortraege' },
];

export const projectPage = (id) => ({
	key: `projekt-${id}`,
	path: `/bauten/${id}`,
	navStates: id === sampleProjects[0],
	states: [
		{ name: 'info-open', ref: click('.btn-project-toggle'), act: click('[data-project-toggle]') },
	],
});

// Navigation states, captured on pages with `navStates`, at the given viewports.
export const globalStates = [
	{ name: 'menu-open', viewports: [375, 768, 899], ref: click('.js-btn-menu'), act: click('[data-menu-button]') },
	{
		name: 'menu-nested',
		viewports: [375, 899],
		ref: async (page) => { await click('.js-btn-menu')(page); await clickNav('Büro', 'Bauten', 'Wohnen', 'Wohnhäuser')(page); },
		act: async (page) => { await click('[data-menu-button]')(page); await clickNav('Büro', 'Bauten', 'Wohnen', 'Wohnhäuser')(page); },
	},
	{ name: 'submenu-open', viewports: [1280], ref: click('.site-nav .is-parent'), act: click('[data-submenu-button]') },
	{ name: 'submenu-nested', viewports: [901, 1280], ref: clickNav('Bauten', 'Wohnen', 'Wohnhäuser'), act: clickNav('Bauten', 'Wohnen', 'Wohnhäuser') },
	{ name: 'submenu-other', viewports: [1280], ref: clickNav('Bauten', 'Büro'), act: clickNav('Bauten', 'Büro') },
	// Header states; screenshots of the viewport only.
	{ name: 'scrolled-down', viewports: [375, 900, 1280], viewportOnly: true, ref: scrollBy(600), act: scrollBy(600) },
	{ name: 'scrolled-up', viewports: [375, 900, 1280], viewportOnly: true, ref: scrollBy(600, -200), act: scrollBy(600, -200) },
];
