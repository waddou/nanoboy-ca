/**
 * Browser regression test against production HTML with the built theme CSS.
 * Replace the INLINE_THEME_CSS marker with JSON.stringify(app.min.css), then
 * execute this callback with the Playwright browser_run_code tool.
 * AdSense is blocked only in this test; its observed resize/collapse mutations
 * are replayed deterministically. No production response is changed on server.
 */
async (page) => {
  const compiledCSS = /* INLINE_THEME_CSS */ "";
  if (!compiledCSS) throw new Error('Embed the built theme CSS before running.');
  const results = [];
  for (const width of [375, 768, 1024, 1440]) {
    for (const path of ['/', '/duree-validite-attestation-de-droits.html']) {
      const context = await page.context().browser().newContext({viewport: {width, height: 900}});
      try {
        const testPage = await context.newPage();
        await testPage.route('**/*', route => {
          const url = route.request().url();
          if (url.includes('/themes/nanoboy-ca/assets/css/app.min.css')) {
            return route.fulfill({status: 200, contentType: 'text/css', body: compiledCSS});
          }
          if (url.includes('pagead2.googlesyndication.com/')) return route.abort();
          return route.continue();
        });
        const response = await testPage.goto('https://choix-assurances.fr' + path, {waitUntil: 'load'});
        if (response.status() !== 200) throw new Error('Production HTML unavailable');
        results.push({width, path, ...await testPage.evaluate(async () => {
          const nextFrame = () => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
          const checks = [];
          const same = (name, before, after) => checks.push({name, before, after, pass: Math.abs(before - after) < 0.5});
          const slots = [...document.querySelectorAll('.ad-slot--list-top, .ad-slot--list-in-feed, .ad-slot--article-top')];
          if (!slots.length) throw new Error('Expected theme slot absent');
          for (const slot of slots) {
            const unit = slot.querySelector('ins');
            // Emulate the AdSense host that remains inside a collapsed <ins>.
            const host = document.createElement('div');
            host.style.cssText = 'width:100px;height:280px';
            unit.append(host);
            await nextFrame();
            const before = {slot: slot.getBoundingClientRect().height, host: host.getBoundingClientRect().top, left: host.getBoundingClientRect().left};
            unit.style.height = '0px';
            await nextFrame();
            same('empty ad stays at the top of a reserved slot', before.host, host.getBoundingClientRect().top);
            unit.style.height = '280px';
            for (const height of [250, 0]) {
              slot.style.setProperty('height', 'auto', 'important');
              slot.style.setProperty('min-height', '0px', 'important');
              unit.style.height = height + 'px';
              if (!height) {
                unit.style.width = '0px';
                unit.setAttribute('data-ad-status', 'unfilled');
              }
              await nextFrame();
              same('slot preserves space at ad height ' + height, before.slot, slot.getBoundingClientRect().height);
              same('ad stays vertically anchored at height ' + height, before.host, host.getBoundingClientRect().top);
              same('ad stays horizontally anchored at height ' + height, before.left, host.getBoundingClientRect().left);
            }
          }
          const widgets = [...document.querySelectorAll('.sidebar__widget')];
          const adWidgets = widgets.filter(e => e.querySelector('ins.adsbygoogle'));
          const guide = widgets.find(e => !e.querySelector('ins.adsbygoogle'));
          if (innerWidth >= 768) {
            if (!adWidgets.length || !guide) throw new Error('Expected legacy sidebar widgets absent');
            const beforeGuide = guide.getBoundingClientRect().top;
            for (const height of [600, 0]) {
              for (const widget of adWidgets) {
                widget.querySelector('ins.adsbygoogle').style.height = height + 'px';
              }
              await nextFrame();
              same('following sidebar content stays still at ad height ' + height, beforeGuide, guide.getBoundingClientRect().top);
            }
          } else {
            checks.push({name: 'sidebar stays hidden on mobile', pass: document.querySelector('.sidebar').getBoundingClientRect().height === 0});
          }
          checks.push({name: 'no horizontal overflow', pass: document.documentElement.scrollWidth <= innerWidth});
          return {checks};
        })});
      } finally {
        await context.close();
      }
    }
  }
  const failed = results.flatMap(r => r.checks.filter(c => !c.pass).map(c => ({width: r.width, path: r.path, ...c})));
  if (failed.length) throw new Error(JSON.stringify({passed: false, failed}));
  return {passed: true, results};
}
