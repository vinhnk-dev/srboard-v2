"use strict";

(function (NioApp, $) {
  'use strict'; // Basic Sweet Alerts

  $('.eg-swal-av3').on("click", function (e) {
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!'
    }).then(function (result) {
      if (result.value) {
        Swal.fire('Deleted!', 'Your file has been deleted.', 'success');
      }
    });
    e.preventDefault();
  });
})(NioApp, jQuery);