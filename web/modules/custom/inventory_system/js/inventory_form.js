(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.inventoryForm = {
    attach: function (context, settings) {
      // Add event listener for the item search field.
      $('#edit-item-search', context).on('input', function () {
        var searchValue = $(this).val().toLowerCase();
        // Hide/show table rows based on search value.
        $('#edit-items tbody tr').each(function () {
          var title = $(this).find('td:eq(0)').text().toLowerCase();
          if (title.indexOf(searchValue) !== -1) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
