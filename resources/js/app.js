import './bootstrap';

import AOS from 'aos';
import 'aos/dist/aos.css';

// Initialize AOS
AOS.init();

import { CountUp } from 'countup.js';

document.addEventListener('DOMContentLoaded', () => {
  const elements = document.querySelectorAll('[data-count]');
  const options = { duration: 2, useEasing: true };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      const el = entry.target;

      if (entry.isIntersecting) {
        const targetValue = parseInt(el.getAttribute('data-count'));
        const countUp = new CountUp(el, targetValue, options);
        if (!countUp.error) {
          countUp.start();
        }
      } else {
        el.innerHTML = '0';
      }
    });
  }, {
    threshold: 0.5,
  });

  elements.forEach(el => {
    el.innerHTML = '0';
    observer.observe(el);
  });
});


