(() => {
  const box = document.querySelector('[data-event-url]');
  if (!box) return;
  const section = box.closest('[data-event-actions]');
  const data = {title: box.dataset.eventTitle, url: box.dataset.eventUrl};
  const status = section.querySelector('.event-share-status');
  const share = box.querySelector('[data-share-event]');
  const fallback = section.querySelector('[data-copy-fallback]');
  const input = section.querySelector('[data-copy-url]');
  let canShare = typeof navigator.share === 'function';
  if (canShare && typeof navigator.canShare === 'function') {
    try { canShare = navigator.canShare(data); }
    catch (_) { canShare = false; }
  }
  const copyLink = async () => {
    if (navigator.clipboard && window.isSecureContext) {
      try {
        await navigator.clipboard.writeText(data.url);
        status.textContent = 'Lien copié. Vous pouvez le coller dans votre application.';
        return;
      } catch (_) { /* Proposer la copie manuelle si l’accès est refusé. */ }
    }
    fallback.hidden = false;
    input.focus();
    input.select();
    input.setSelectionRange(0, input.value.length);
    status.textContent = 'Copiez le lien sélectionné avec le menu de votre appareil ou Ctrl+C / ⌘C.';
  };
  share.hidden = false;
  share.addEventListener('click', async () => {
    status.textContent = '';
    fallback.hidden = true;
    share.disabled = true;
    try {
      if (canShare) {
        try { await navigator.share(data); }
        catch (error) { if (error.name !== 'AbortError') await copyLink(); }
      } else {
        await copyLink();
      }
    } finally { share.disabled = false; }
  });
})();
