document.addEventListener('DOMContentLoaded', function() {
  const hamburger = document.getElementById('hamburger');
  const navContainer = document.getElementById('main-nav');
  const expandedItems = document.querySelectorAll('.nav-item--expanded');
  const closeBtn = document.getElementById('close-btn');

  hamburger.addEventListener('click', function() {
    navContainer.classList.toggle('active');
    hamburger.classList.toggle('active');

    expandedItems.forEach(function(item) {
      if (navContainer.classList.contains('active')) {
        item.querySelector('.nav').style.display = 'block';
      } else {
        item.querySelector('.nav').style.display = 'none';
      }
    });
  });

  closeBtn.addEventListener('click', function() {
    navContainer.classList.remove('active');
    hamburger.classList.remove('active');

    expandedItems.forEach(function(item) {
      item.querySelector('.nav').style.display = 'none';
    });
  });
});
