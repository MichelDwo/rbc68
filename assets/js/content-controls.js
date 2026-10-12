(function ($) {
  'use strict';
  wp.customize.bind('ready', function () {
    document.querySelectorAll('.rbc68-list-control').forEach(function (root) {
      const hidden = root.querySelector('.rbc68-list-value');
      const fields = JSON.parse(root.dataset.fields);
      const rows = root.querySelector('.rbc68-list-rows');
      let items;
      try { items = JSON.parse(hidden.value); } catch (_) { items = []; }
      if (!Array.isArray(items)) items = [];
      function save() { hidden.value = JSON.stringify(items); $(hidden).trigger('change'); }
      function render() {
        rows.replaceChildren();
        items.forEach(function (item, index) {
          const box = document.createElement('fieldset');
          box.style.cssText = 'border:1px solid #aaa;padding:10px;margin:12px 0';
          Object.entries(fields).forEach(function (entry) {
            const key = entry[0], title = entry[1];
            const label = document.createElement('label');
            label.textContent = title;
            label.style.cssText = 'display:block;margin-bottom:8px';
            if (key === 'logo') {
              const preview = document.createElement('img');
              preview.style.cssText = 'display:block;max-width:100%;max-height:100px;margin:6px 0';
              function previewImage() {
                if (!item.logo) { preview.removeAttribute('src'); preview.hidden = true; return; }
                const attachment = wp.media.attachment(item.logo);
                attachment.fetch().then(function () { preview.src = attachment.get('url'); preview.alt = item.name || 'Logo'; preview.hidden = false; });
              }
              previewImage();
              const button = document.createElement('button');
              button.type = 'button'; button.className = 'button'; button.textContent = 'Choisir une image';
              button.onclick = function () {
                const frame = wp.media({ title: 'Image du sponsor', library: { type: 'image' }, multiple: false });
                frame.on('select', function () { item.logo = frame.state().get('selection').first().id; save(); previewImage(); });
                frame.open();
              };
              label.append(preview, button);
            } else {
              const input = document.createElement('input');
              input.type = key === 'url' ? 'url' : 'text'; input.value = item[key] || '';
              input.addEventListener('input', function () { item[key] = input.value; save(); });
              label.append(input);
            }
            box.append(label);
          });
          const remove = document.createElement('button');
          remove.type = 'button'; remove.className = 'button'; remove.textContent = 'Retirer';
          remove.onclick = function () { items.splice(index, 1); save(); render(); };
          box.append(remove); rows.append(box);
        });
      }
      root.querySelector('.rbc68-list-add').onclick = function () { items.push({}); render(); };
      render();
    });
  });
})(jQuery);
