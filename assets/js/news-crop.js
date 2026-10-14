document.addEventListener('change', function (event) {
  if (!event.target.matches('input[name="rbc68_news_crop"]')) return;
  const control = event.target.closest('.rbc68-crop-control');
  const preview = control && control.querySelector('.rbc68-crop-preview');
  if (preview && ['full', 'left', 'center', 'right'].includes(event.target.value)) {
    const full = event.target.value === 'full';
    preview.style.height = full ? 'auto' : '140px';
    preview.style.objectFit = full ? 'contain' : 'cover';
    preview.style.objectPosition = full ? 'center center' : event.target.value + ' center';
  }
});
