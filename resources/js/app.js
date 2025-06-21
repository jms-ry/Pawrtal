import './bootstrap';

import AOS from 'aos';
import 'aos/dist/aos.css';

// Initialize AOS
AOS.init();

import { CountUp } from 'countup.js';

document.addEventListener('DOMContentLoaded', () => {
  const counters = [
    { id: 'sheltered-count', value: 555 },
    { id: 'spayed-count', value: 555 },
    { id: 'vaccinated-count', value: 555 },
    { id: 'adopted-count', value: 555 },
  ];

  const options = {
    duration: 2,
    useEasing: true,
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      const el = entry.target;

      if (entry.isIntersecting) {
        const countUp = new CountUp(el, el.getAttribute('data-count'), options);
        if (!countUp.error) {
          countUp.start();
        }
      } else {
        // Reset value to 0 when it leaves viewport
        el.innerHTML = '0';
      }
    });
  }, {
    threshold: 0.5, // Trigger when 50% visible
  });

  counters.forEach(({ id, value }) => {
    const el = document.getElementById(id);
    if (el) {
      el.setAttribute('data-count', value);
      el.innerHTML = '0'; // initialize with 0
      observer.observe(el);
    }
  });
});

