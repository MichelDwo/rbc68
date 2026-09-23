(() => {
  const box = document.querySelector('[data-event-url]');
  if (!box) return;
  const data = {title: box.dataset.eventTitle, url: box.dataset.eventUrl};
  const status = document.querySelector('.event-share-status');
  const share = box.querySelector('[data-share-event]');
  if (navigator.share) {
    share.hidden = false;
    share.addEventListener('click', async () => {
      try { await navigator.share(data); }
      catch (error) { if (error.name !== 'AbortError') status.textContent = 'Utilisez un lien de partage ou copiez l’adresse.'; }
    });
  }
  const copy = box.querySelector('[data-copy-event]');
  if (navigator.clipboard && window.isSecureContext) {
    copy.hidden = false;
    copy.addEventListener('click', async () => {
      try { await navigator.clipboard.writeText(data.url); status.textContent = 'Lien copié.'; }
      catch (_) { status.textContent = 'Copiez l’adresse depuis la barre du navigateur.'; }
    });
  }
  const fileButton = document.querySelector('[data-share-file]');
  if (!fileButton || !navigator.canShare || !navigator.share) return;
  const fileStatus = document.querySelector('.event-file-status');
  let file;
  // Préparation au premier clic, puis partage au second : conserve le geste utilisateur.
  fileButton.hidden = false;
  fileButton.addEventListener('click', async () => {
    if (file) {
      try { await navigator.share({files: [file], title: data.title}); }
      catch (error) { if (error.name !== 'AbortError') fileStatus.textContent = 'Téléchargez l’affiche pour la partager depuis vos fichiers.'; }
      return;
    }
    fileButton.disabled = true;
    fileStatus.textContent = 'Préparation de l’affiche…';
    try {
      const response = await fetch(fileButton.dataset.shareFile, {credentials: 'same-origin'});
      if (!response.ok) throw new Error('download');
      const blob = await response.blob();
      file = new File([blob], fileButton.dataset.fileName, {type: fileButton.dataset.fileType});
      if (!navigator.canShare({files: [file]})) throw new Error('unsupported');
      fileButton.textContent = 'Choisir l’application de partage';
      fileStatus.textContent = 'Affiche prête. Cliquez pour choisir votre application.';
    } catch (_) {
      file = null;
      fileStatus.textContent = 'Téléchargez l’affiche pour la partager depuis vos fichiers.';
    } finally { fileButton.disabled = false; }
  });
})();
