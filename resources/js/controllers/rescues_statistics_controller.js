import { Controller } from "@hotwired/stimulus"
import { CountUp } from 'countup.js';
// Connects to data-controller="rescues-statistics"
export default class extends Controller {
  connect() {
    const elements = this.element.querySelectorAll('[data-count]');
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
  }
}
