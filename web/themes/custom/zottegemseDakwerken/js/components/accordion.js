(function ($) {
  console.log("accordion");
  Drupal.behaviors.accordion = {
    attach: function (context, settings) {
      // Ensure the accordion functionality is applied only once
      $('.accordion', context).each(function () {
        var $accordion = $(this);
        
        // Check if accordion functionality has already been applied
        if (!$accordion.hasClass('accordion-initialized')) {
          // Add custom class to mark initialization
          $accordion.addClass('accordion-initialized');
          
          // Hide all accordion content
          $accordion.find('.accordion-content').hide();
          
          // Toggle accordion content on click
          $accordion.find('.accordion-header').click(function () {
            var $header = $(this);
            var $content = $header.next('.accordion-content');
            
            // Toggle the active class
            $header.toggleClass('active');
            
            // Toggle the visibility of the accordion content
            $content.slideToggle();
            
            // Hide other accordion content when one is clicked
            $accordion.find('.accordion-content').not($content).slideUp();
            $accordion.find('.accordion-header').not($header).removeClass('active');
          });
        }
      });
    }
  };
});
