$(document).ready(function () {
  $('.logout').click(function (e) {
    e.preventDefault();

    $.ajax({
      url: '/api/logout',
      success: function (data, textStatus, xhr) {
        location.href = '../../../';
      },
    });
  });
});
