// Exécute le banc d'essai du plancher publicitaire et rend le verdict.
// Remplacer <chemin-du-depot> par le chemin absolu du dépôt avant exécution.
// Chaque conteneur doit faire 280px, sauf le cas 6 où une annonce plus haute
// (336px) ne doit surtout pas être tronquée.
async (page) => {
  const c = await page.context().browser().newContext({ viewport: { width: 1000, height: 900 } });
  try {
    const p = await c.newPage();
    await p.goto("file://<chemin-du-depot>/scripts/banc-plancher.html",
                 { waitUntil: "load", timeout: 20000 });
    const r = await p.evaluate(() => {
      const ATTENDU = { 1: 280, 2: 280, 3: 280, 4: 280, 5: 280, 6: 336, 7: 280, 8: 280 };
      const out = [];
      for (const n of document.querySelectorAll(".ad-slot[data-cas]")) {
        const cas = n.dataset.cas;
        const num = Number(cas.slice(1));
        const h = Math.round(n.getBoundingClientRect().height);
        const ins = n.querySelector(".ad-slot__unit");
        out.push({
          cas,
          hauteur: h,
          attendu: ATTENDU[num],
          ok: h === ATTENDU[num],
          ins: ins ? Math.round(ins.getBoundingClientRect().height) : null,
          styleInline: n.getAttribute("style") || "(aucun)",
        });
      }
      return out;
    });
    return JSON.stringify(r);
  } finally { await c.close(); }
}
