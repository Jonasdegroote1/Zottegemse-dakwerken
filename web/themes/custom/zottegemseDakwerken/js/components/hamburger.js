document.addEventListener('DOMContentLoaded', function() {
  console.log("hamburger");
  const hamburger = document.getElementById('hamburger');
  const navContainer = document.getElementById('main-nav');
  const expandedItems = document.querySelectorAll('.nav-item--expanded');

  // Check if elements exist before adding event listeners
  if (!hamburger) {
    console.error('Element with id "hamburger" not found.');
  }

  if (!navContainer) {
    console.error('Element with id "main-nav" not found.');
  }

  if (hamburger && navContainer) {
    hamburger.addEventListener('click', function() {
      navContainer.classList.toggle('active');
      hamburger.classList.toggle('active');

      expandedItems.forEach(function(item) {
        const nav = item.querySelector('.nav');
        if (nav) {
          nav.style.display = navContainer.classList.contains('active') ? 'block' : 'none';
        } else {
          console.error('Element with class "nav" not found in expanded item:', item);
        }
      });
    });
  } else {
    console.error('One or more required elements are missing.');
  }
});
