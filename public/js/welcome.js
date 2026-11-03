/**
 * TEKSIKA - Minimalist Splash Screen Controller
 */

(function () {
  'use strict';

  const progressBar = document.getElementById('progressBar');
  const progressPercent = document.getElementById('progressPercent');
  const targetUrl = document.body.dataset.targetUrl || '/landingpageuser';

  let isRedirecting = false;
  const duration = 1800; // 1.8 seconds for a snappy, clean experience
  const startTime = performance.now();

  function finishLoading() {
    if (isRedirecting) return;
    isRedirecting = true;

    try {
      sessionStorage.setItem('teksika_splash_seen', 'true');
    } catch (e) {}

    if (progressBar) progressBar.style.width = '100%';
    if (progressPercent) progressPercent.textContent = '100%';

    setTimeout(() => {
      document.body.classList.add('exit-animation');
      setTimeout(() => {
        window.location.href = targetUrl;
      }, 350);
    }, 150);
  }

  function step(now) {
    if (isRedirecting) return;

    const elapsed = now - startTime;
    const rawProgress = Math.min(elapsed / duration, 1);

    // Natural ease-out
    const progress = Math.round((1 - Math.pow(1 - rawProgress, 2.2)) * 100);

    if (progressBar) progressBar.style.width = progress + '%';
    if (progressPercent) progressPercent.textContent = progress + '%';

    if (rawProgress < 1) {
      requestAnimationFrame(step);
    } else {
      finishLoading();
    }
  }

  requestAnimationFrame(step);

  // Click anywhere or press key to skip immediately
  window.addEventListener('click', finishLoading);
  window.addEventListener('keydown', finishLoading);
})();
