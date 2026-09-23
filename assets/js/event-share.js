(() => {
  const box = document.querySelector('[data-event-url]');
  if (!box) return;
  const section = box.closest('section');
  const data = {title: box.dataset.eventTitle, url: box.dataset.eventUrl};
  const status = section.querySelector('.event-share-status');
  const share = box.querySelector('[data-share-event]');
  let canShare = typeof navigator.share === 'function';
  if (canShare && typeof navigator.canShare === 'function') {
    try { canShare = navigator.canShare(data); }
    catch (_) { canShare = false; }
  }
  if (canShare) {
    share.hidden = false;
    share.addEventListener('click', async () => {
      status.textContent = '';
      share.disabled = true;
      try { await navigator.share(data); }
      catch (error) {
        if (error.name !== 'AbortError') status.textContent = 'Le partage est indisponible. Utilisez « Copier le lien ».';
      } finally { share.disabled = false; }
    });
  }
  const copy = box.querySelector('[data-copy-event]');
  const fallback = section.querySelector('[data-copy-fallback]');
  const input = section.querySelector('[data-copy-url]');
  copy.hidden = false;
  copy.addEventListener('click', async () => {
    status.textContent = '';
    if (navigator.clipboard && window.isSecureContext) {
      try {
        await navigator.clipboard.writeText(data.url);
        fallback.hidden = true;
        status.textContent = 'Lien copié.';
        return;
      } catch (_) { /* Proposer la copie manuelle si l’accès est refusé. */ }
    }
    fallback.hidden = false;
    input.focus();
    input.select();
    input.setSelectionRange(0, input.value.length);
    status.textContent = 'Copiez le lien sélectionné avec le menu de votre appareil ou Ctrl+C / ⌘C.';
  });
})();
