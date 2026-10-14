document.addEventListener('DOMContentLoaded', () => {
  const root = document.getElementById('rbc-import'); if (!root) return;
  const status = root.querySelector('#rbc-import-status'), preview = root.querySelector('#rbc-import-preview'), confirm = root.querySelector('#rbc-import-confirm');
  let token = '', pending = [], busy = false;
  async function request(step, values = {}) {
    const body = new URLSearchParams({ action: 'rbc_icbad', nonce: root.dataset.nonce, token, step, ...values });
    const response = await fetch(ajaxurl, { method: 'POST', body });
    const data = await response.json(); if (!data.success) throw new Error(data.data?.message || 'Import interrompu. Réessayez.'); return data.data;
  }
  function setBusy(value) { busy = value; root.querySelectorAll('button, input, select').forEach(e => { e.disabled = value; }); }
  async function saveRows(indices) {
    let added = 0, skipped = 0, failed = 0;
    for (const index of indices) {
      try { const result = await request('save', { index }); result.skipped ? skipped++ : added++; }
      catch (error) { failed++; const p = document.createElement('p'); p.textContent = error.message; preview.append(p); }
      status.textContent = `${added} ajoutée(s), ${skipped} déjà présente(s), ${failed} erreur(s).`;
    }
    if (!indices.length) status.textContent = 'Aucune nouvelle rencontre à importer.';
  }
  root.querySelectorAll('[data-import]').forEach(button => button.addEventListener('click', async () => {
    if (busy) return; setBusy(true); preview.replaceChildren(); confirm.hidden = true; pending = [];
    try {
      status.textContent = 'Lecture de la liste ICBaD…';
      const start = await request('start', { url: root.querySelector('#rbc-import-url').value.trim(), season: root.dataset.season, number: root.querySelector('#rbc-import-number').value }); token = start.token;
      for (let i = 0; i < start.count; i++) {
        status.textContent = `Vérification des dates : ${i + 1}/${start.count}`;
        const p = document.createElement('p');
        try {
          const data = await request('prepare', { index: i }), r = data.row;
          const label = document.createElement('label'), check = document.createElement('input'); check.type = 'checkbox'; check.checked = !data.exists; check.dataset.index = i; check.dataset.existing = data.exists ? '1' : ''; check.disabled = true;
          label.append(check, document.createTextNode(` ${r.date} — ${r.home} ${r.home_score} – ${r.away_score} ${r.away}${data.exists ? ' (déjà enregistrée, sera ignorée)' : ''}`)); p.append(label); if (!data.exists) pending.push(i);
        } catch (error) { p.textContent = `Rencontre ${i + 1} : ${error.message}`; }
        preview.append(p);
      }
      if (button.dataset.import === 'diff') { await saveRows(pending); }
      else { status.textContent = `${pending.length} nouvelle(s) rencontre(s) disponible(s). Vérifiez puis confirmez.`; confirm.hidden = !pending.length; }
    } catch (error) { status.textContent = error.message; }
    finally { setBusy(false); preview.querySelectorAll('[data-existing="1"]').forEach(e => { e.disabled = true; }); }
  }));
  confirm.addEventListener('click', async () => {
    if (busy) return; const indices = [...preview.querySelectorAll('input:checked')].filter(e => !e.dataset.existing).map(e => e.dataset.index); setBusy(true);
    try { await saveRows(indices); confirm.hidden = true; } finally { setBusy(false); }
  });
});
