(() => {
  document.addEventListener('click', event => {
    const button = event.target.closest('button');
    if (!button) return;
    if (button.matches('[data-add-document]')) {
      const block = button.closest('[data-documents]');
      const next = Number(block.dataset.next);
      block.dataset.next = next + 1;
      block.querySelector('[data-document-rows]').insertAdjacentHTML('beforeend', block.querySelector('template').innerHTML.replaceAll('__INDEX__', String(next)));
      block.querySelector('[data-document-rows]').lastElementChild.querySelector('input').focus();
      block.querySelector('[data-document-status]').textContent = 'Document ajouté. Enregistrez les modifications après avoir renseigné son nom et son lien.';
    }
    if (button.matches('[data-remove-document]')) {
      const block = button.closest('[data-documents]');
      button.closest('.join-doc-row').remove();
      block.querySelector('[data-add-document]').focus();
      block.querySelector('[data-document-status]').textContent = 'Document retiré. Enregistrez les modifications pour confirmer.';
    }
    if (button.matches('[data-select-document]')) {
      const row = button.closest('.join-doc-row');
      const input = row.querySelector('input[type="url"]');
      const frame = wp.media({title: 'Choisir un document', multiple: false, button: {text: 'Utiliser ce document'}});
      frame.on('select', () => {
        const file = frame.state().get('selection').first().toJSON();
        input.value = file.url;
        const label = row.querySelector('input:not([type="url"])');
        if (label && !label.value) label.value = file.title || file.filename;
      });
      frame.open();
    }
  });
})();
