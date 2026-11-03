/**
 * TEKSIKA — Bi-Directional Scroll Engine, Parallax, Counters & Modals
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // ================= 1. MOBILE NAVIGATION =================
  const navToggle = document.getElementById('navToggle');
  const navMenu = document.getElementById('navMenu');

  if (navToggle && navMenu) {
    navToggle.addEventListener('click', function () {
      const isOpen = navMenu.classList.toggle('active');
      navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.querySelectorAll('.nav-item').forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // ================= 2. BI-DIRECTIONAL SCROLL REVEAL (DOWN & UP) =================
  const scrollElements = document.querySelectorAll('[data-scroll]');
  let lastScrollY = window.scrollY;
  let scrollDirection = 'down';

  window.addEventListener('scroll', function () {
    const currentY = window.scrollY;
    scrollDirection = currentY >= lastScrollY ? 'down' : 'up';
    lastScrollY = currentY;
  }, { passive: true });

  // Initialize starting position classes based on current viewport position
  scrollElements.forEach(el => {
    const rect = el.getBoundingClientRect();
    if (rect.top < 0) {
      el.classList.add('from-above');
    } else {
      el.classList.add('from-below');
    }
  });

  if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        const el = entry.target;
        const delay = parseInt(el.getAttribute('data-delay') || '0', 10);

        if (entry.isIntersecting) {
          el.style.transitionDelay = delay ? `${delay}ms` : '0ms';
          el.classList.add('is-inview');
        } else {
          // Reset delay on exit so re-preparation is immediate
          el.style.transitionDelay = '0ms';
          el.classList.remove('is-inview');

          // Determine whether element is now above or below the viewport
          // so that scrolling back up OR scrolling back down animates from the right direction
          if (entry.boundingClientRect.top < 0) {
            el.classList.remove('from-below');
            el.classList.add('from-above');
          } else {
            el.classList.remove('from-above');
            el.classList.add('from-below');
          }
        }
      });
    }, {
      threshold: 0.12,
      rootMargin: '0px 0px -40px 0px'
    });

    scrollElements.forEach(el => revealObserver.observe(el));
  } else {
    scrollElements.forEach(el => el.classList.add('is-inview'));
  }

  // ================= 3. ANIMATED NUMBER COUNTERS =================
  const counterElements = document.querySelectorAll('.counter-value');

  function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-count') || '0', 10);
    const duration = 1100;
    const startTime = performance.now();

    function tick(now) {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const current = Math.round(eased * target);
      el.textContent = current.toLocaleString('id-ID');

      if (progress < 1) {
        requestAnimationFrame(tick);
      }
    }

    requestAnimationFrame(tick);
  }

  if ('IntersectionObserver' in window && counterElements.length > 0) {
    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
        }
      });
    }, { threshold: 0.35 });

    counterElements.forEach(el => counterObserver.observe(el));
  }

  // ================= 4. PARALLAX, SCROLL PROGRESS & ACTIVE NAV =================
  const scrollProgress = document.getElementById('scrollProgress');
  const siteHeader = document.getElementById('siteHeader');
  const parallaxImages = document.querySelectorAll('.parallax-img');
  const sections = document.querySelectorAll('section[id]');
  const navItems = document.querySelectorAll('.nav-item[data-section]');

  let ticking = false;

  function updateOnScroll() {
    const scrollY = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;

    // Top progress bar
    if (scrollProgress && docHeight > 0) {
      const pct = Math.min(Math.max((scrollY / docHeight) * 100, 0), 100);
      scrollProgress.style.width = `${pct}%`;
    }

    // Header surface elevation
    if (siteHeader) {
      if (scrollY > 24) {
        siteHeader.classList.add('is-scrolled');
      } else {
        siteHeader.classList.remove('is-scrolled');
      }
    }

    // Subtle bi-directional image parallax
    parallaxImages.forEach(img => {
      const speed = parseFloat(img.getAttribute('data-speed') || '0.05');
      const rect = img.parentElement.getBoundingClientRect();
      const centerOffset = (window.innerHeight * 0.5) - (rect.top + rect.height * 0.5);
      const yShift = centerOffset * speed;
      img.style.transform = `translate3d(0, ${yShift.toFixed(1)}px, 0)`;
    });

    // Active section indicator in navbar
    let currentSectionId = '';
    sections.forEach(sec => {
      const top = sec.offsetTop - 160;
      if (scrollY >= top) {
        currentSectionId = sec.getAttribute('id');
      }
    });

    navItems.forEach(item => {
      if (item.getAttribute('data-section') === currentSectionId) {
        item.classList.add('active');
      } else {
        item.classList.remove('active');
      }
    });

    ticking = false;
  }

  window.addEventListener('scroll', function () {
    if (!ticking) {
      requestAnimationFrame(updateOnScroll);
      ticking = true;
    }
  }, { passive: true });

  updateOnScroll();

  // ================= 5. PORTFOLIO POPUP MODALS =================
  const detailButtons = document.querySelectorAll('.btn-secondary');
  const popups = document.querySelectorAll('.popup');
  const closeButtons = document.querySelectorAll('.close');

  const popupMap = {
    0: 'popup-listrik',
    1: 'popup-ac',
    2: 'popup-plumbing'
  };

  detailButtons.forEach((btn, index) => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const targetId = btn.getAttribute('data-target') || popupMap[index];
      if (targetId) {
        const targetModal = document.getElementById(targetId);
        if (targetModal) {
          targetModal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
        }
      }
    });
  });

  function closeAllPopups() {
    popups.forEach(popup => {
      popup.style.display = 'none';
    });
    document.body.style.overflow = '';
  }

  closeButtons.forEach(btn => {
    btn.addEventListener('click', closeAllPopups);
  });

  window.addEventListener('click', function (e) {
    popups.forEach(popup => {
      if (e.target === popup) {
        closeAllPopups();
      }
    });
  });

  window.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeAllPopups();
    }
  });
});
