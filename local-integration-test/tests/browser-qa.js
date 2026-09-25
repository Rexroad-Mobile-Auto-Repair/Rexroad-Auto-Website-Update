const { chromium } = require('/opt/node22/lib/node_modules/playwright');

const BASE = 'http://localhost:8089';
const pages = [
  { path: '/vehicles/', name: 'vehicles' },
  { path: '/vehicles/ford/', name: 'ford' },
  { path: '/vehicles/ford/f-150/', name: 'f150' },
  { path: '/vehicles/chevrolet/', name: 'chevrolet' },
  { path: '/vehicles/chevrolet/silverado-1500/', name: 'silverado' },
  { path: '/vehicles/toyota/', name: 'toyota' },
  { path: '/vehicles/toyota/camry/', name: 'camry' },
  { path: '/vehicles/toyota/tacoma/', name: 'tacoma' },
  { path: '/vehicles/honda/', name: 'honda' },
  { path: '/vehicles/honda/civic/', name: 'civic' },
  { path: '/vehicles/ram/', name: 'ram' },
  // /vehicles/ram/1500/ intentionally excluded: known defect (see report),
  // WordPress 301-redirects it to /vehicles/ram/ before any page renders.
];
const viewports = [
  { width: 1440, height: 900, label: 'desktop' },
  { width: 390, height: 844, label: 'mobile' },
];

(async () => {
  const browser = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium' });
  const report = [];

  for (const p of pages) {
    for (const vp of viewports) {
      const page = await browser.newPage({ viewport: { width: vp.width, height: vp.height } });
      const consoleErrors = [];
      page.on('console', (msg) => { if (msg.type() === 'error') consoleErrors.push(msg.text()); });
      page.on('pageerror', (e) => consoleErrors.push('pageerror: ' + e.message));

      await page.goto(BASE + p.path, { waitUntil: 'networkidle' });

      const scrollWidth = await page.evaluate(() => document.documentElement.scrollWidth);
      const clientWidth = await page.evaluate(() => document.documentElement.clientWidth);
      const overflow = scrollWidth > clientWidth + 1; // 1px tolerance

      await page.screenshot({ path: `/tmp/rexroad-qa-artifacts/${p.name}-${vp.label}.png`, fullPage: true });

      report.push({
        page: p.path,
        viewport: vp.label,
        scrollWidth,
        clientWidth,
        horizontalOverflow: overflow,
        consoleErrors,
      });

      await page.close();
    }
  }

  // JS filtering check on /vehicles/
  const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
  const filterErrors = [];
  page.on('console', (msg) => { if (msg.type() === 'error') filterErrors.push(msg.text()); });
  await page.goto(BASE + '/vehicles/', { waitUntil: 'networkidle' });
  await page.fill('#rexroad-vehicle-search-input', 'F150');
  await page.dispatchEvent('#rexroad-vehicle-search-input', 'input');
  await page.waitForTimeout(300);
  const filterState = await page.evaluate(() => {
    const fordMake = document.querySelector('.rr-vehicle-make[data-search-make="ford"]');
    const otherMakes = Array.from(document.querySelectorAll('.rr-vehicle-make:not([data-search-make="ford"])'));
    return {
      fordVisible: fordMake ? !fordMake.hidden : null,
      someOtherMakeHidden: otherMakes.some((m) => m.hidden),
      totalOtherMakes: otherMakes.length,
    };
  });
  const searchInputValue = await page.inputValue('#rexroad-vehicle-search-input');
  report.push({
    page: '/vehicles/ (JS live filter)',
    searchInputAcceptedValue: searchInputValue === 'F150',
    fordMakeStaysVisibleForF150Query: filterState.fordVisible === true,
    nonMatchingMakesGetHidden: filterState.someOtherMakeHidden === true,
    jsRanWithoutErrors: filterErrors.length === 0,
    consoleErrors: filterErrors,
  });
  await page.close();

  await browser.close();
  console.log(JSON.stringify(report, null, 2));
})();
