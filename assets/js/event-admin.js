(() => {
  const select = document.getElementById('rbc68-select-poster');
  if (!select) return;
  const id = document.getElementById('rbc68_event_poster_id');
  const name = document.getElementById('rbc68-poster-name');
  let frame;
  select.addEventListener('click', () => {
    if (!frame) {
      frame = wp.media({title: 'Affiche', library: {type: ['image', 'application/pdf']}, multiple: false, button: {text: 'Utiliser cette affiche'}});
      frame.on('select', () => {
        const file = frame.state().get('selection').first().toJSON();
        id.value = file.id;
        name.textContent = file.filename || file.title;
      });
    }
    frame.open();
  });
  document.getElementById('rbc68-remove-poster').addEventListener('click', () => {
    id.value = '0'; name.textContent = 'Aucune affiche';
  });
})();
