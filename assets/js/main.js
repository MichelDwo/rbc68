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

  const animatedItems = document.querySelectorAll('.blog-card');
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
    contactForm.addEventListener('invalid', function (event) {
      const field = event.target;
      if (field.validity.customError) return;
      if (field.validity.valueMissing) field.setCustomValidity(field.type === 'checkbox' ? 'Veuillez accepter que nous vous recontactions.' : field.tagName === 'SELECT' ? 'Veuillez sélectionner un sujet.' : 'Veuillez remplir ce champ.');
      else if (field.validity.typeMismatch) field.setCustomValidity('Veuillez saisir une adresse e-mail valide.');
      else if (field.validity.tooShort) field.setCustomValidity('Le message doit contenir au moins 10 caractères.');
      else field.setCustomValidity('Veuillez vérifier le contenu de ce champ.');
    }, true);
    ['input', 'change'].forEach(function (type) {
      contactForm.addEventListener(type, function (event) {
        if (event.target.setCustomValidity) event.target.setCustomValidity('');
      });
    });
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
