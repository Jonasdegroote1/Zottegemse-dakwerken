document.addEventListener("DOMContentLoaded", function() {
  const elements = document.querySelectorAll('[data-animation="fade-in"]');
  elements.forEach(element => {
    const delay = parseFloat(element.dataset.delay) || 0.5;

    gsap.from(element, {
      opacity: 0,
      duration: 1,
      delay: delay,
      scrollTrigger: {
        trigger: element,
        start: "top 80%",
        end: "bottom bottom",
        toggleActions: "play none none none",
      }
    });
  });
});
