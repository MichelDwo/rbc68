(function () {
  'use strict';

  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.querySelector('.mobile-menu');

  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', function () {
      const isOpen = mobileMenu.classList.toggle('active');
      hamburger.setAttribute('aria-expanded', String(isOpen));
    });

    mobileMenu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        mobileMenu.classList.remove('active');
        hamburger.setAttribute('aria-expanded', 'false');
      });
    });
  }

  const tabButtons = document.querySelectorAll('.tab-button');
  const tabTables = document.querySelectorAll('.schedule-table');
  tabButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      tabButtons.forEach(function (item) {
        item.classList.remove('active');
        item.setAttribute('aria-selected', 'false');
      });
      tabTables.forEach(function (table) {
        table.style.display = 'none';
      });

      button.classList.add('active');
      button.setAttribute('aria-selected', 'true');
      const table = document.getElementById(button.dataset.tab);
      if (table) {
        table.style.display = 'table';
      }
    });
  });

  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (event) {
      const target = document.querySelector(anchor.getAttribute('href'));
      if (target) {
        event.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  const themeToggle = document.querySelector('.theme-toggle');
  function storedTheme() {
    try {
      return window.localStorage.getItem('rbc68-theme');
    } catch (error) {
      return null;
    }
  }

  function saveTheme(theme) {
    try {
      window.localStorage.setItem('rbc68-theme', theme);
    } catch (error) {
      // Le stockage local peut être désactivé sans empêcher le thème de fonctionner.
    }
  }

  function applyTheme(dark) {
    document.body.classList.toggle('dark-mode', dark);
    document.documentElement.dataset.themePreference = dark ? 'dark' : 'light';
    if (themeToggle) {
      themeToggle.setAttribute('aria-checked', String(dark));
      themeToggle.setAttribute('aria-label', dark ? 'Mode sombre actif. Passer au mode clair' : 'Mode clair actif. Passer au mode sombre');
      themeToggle.title = dark ? 'Passer au mode clair' : 'Passer au mode sombre';
    }
  }

  const savedTheme = storedTheme();
  applyTheme(savedTheme ? savedTheme === 'dark' : true);

  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      const dark = !document.body.classList.contains('dark-mode');
      applyTheme(dark);
      saveTheme(dark ? 'dark' : 'light');
    });
  }

  const galleryItems = Array.from(document.querySelectorAll('.gallery-item'));
  const lightbox = document.getElementById('lightbox');
  const lightboxImage = document.getElementById('lightbox-img');
  let currentImage = 0;

  function updateLightbox() {
    const image = galleryItems[currentImage] && galleryItems[currentImage].querySelector('.gallery-img');
    if (image && lightboxImage) {
      lightboxImage.src = image.src;
      lightboxImage.alt = image.alt;
    }
  }

  function openLightbox(index) {
    if (!lightbox || !galleryItems.length) return;
    currentImage = index;
    updateLightbox();
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    if (!lightbox) return;
    lightbox.classList.remove('active');
    document.body.style.overflow = '';
  }

  function moveLightbox(direction) {
    currentImage = (currentImage + direction + galleryItems.length) % galleryItems.length;
    updateLightbox();
  }

  galleryItems.forEach(function (item, index) {
    item.setAttribute('role', 'button');
    item.setAttribute('tabindex', '0');
    item.addEventListener('click', function () { openLightbox(index); });
    item.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        openLightbox(index);
      }
    });
  });

  const closeButton = document.getElementById('lightbox-close');
  const previousButton = document.getElementById('lightbox-prev');
  const nextButton = document.getElementById('lightbox-next');
  if (closeButton) closeButton.addEventListener('click', closeLightbox);
  if (previousButton) previousButton.addEventListener('click', function () { moveLightbox(-1); });
  if (nextButton) nextButton.addEventListener('click', function () { moveLightbox(1); });
  if (lightbox) {
    lightbox.addEventListener('click', function (event) {
      if (event.target === lightbox) closeLightbox();
    });
  }
  document.addEventListener('keydown', function (event) {
    if (!lightbox || !lightbox.classList.contains('active')) return;
    if (event.key === 'Escape') closeLightbox();
    if (event.key === 'ArrowRight') moveLightbox(1);
    if (event.key === 'ArrowLeft') moveLightbox(-1);
  });

  const animatedItems = document.querySelectorAll('.gallery-item, .blog-card');
  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
    animatedItems.forEach(function (item) {
      item.style.opacity = '0';
      item.style.transform = 'translateY(20px)';
    });
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
          observer.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -80px' });
    animatedItems.forEach(function (item) { observer.observe(item); });
  }

  const contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (event) {
      const phone = contactForm.querySelector('#phone');
      const message = contactForm.querySelector('#message');
      const phonePattern = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/;

      if (phone && phone.value.trim() && !phonePattern.test(phone.value.trim())) {
        event.preventDefault();
        phone.setCustomValidity('Veuillez entrer un numéro français valide, par exemple 06 12 34 56 78.');
        phone.reportValidity();
        return;
      }
      if (phone) phone.setCustomValidity('');

      if (message && message.value.trim().length < 10) {
        event.preventDefault();
        message.setCustomValidity('Le message doit contenir au moins 10 caractères.');
        message.reportValidity();
        return;
      }
      if (message) message.setCustomValidity('');

      const submitButton = contactForm.querySelector('.submit-button');
      const loader = contactForm.querySelector('.button-loader');
      if (submitButton) submitButton.disabled = true;
      if (loader) loader.style.display = 'inline';
    });
  }
})();
