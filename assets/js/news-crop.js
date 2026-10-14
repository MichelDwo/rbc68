document.addEventListener('change', function (event) {
  if (!event.target.matches('input[name="rbc68_news_crop"]')) return;
  const control = event.target.closest('.rbc68-crop-control');
  const preview = control && control.querySelector('.rbc68-crop-preview');
  if (preview && ['left', 'center', 'right'].includes(event.target.value)) {
    preview.style.objectPosition = event.target.value + ' center';
  }
});
