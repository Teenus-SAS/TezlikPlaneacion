$(document).ready(function () {
  $(".auth-user-testimonial .owl-carousel").owlCarousel({
    loop: true,
    margin: 0,
    nav: false,
    dots: false,
    autoplay: true,
    autoplayTimeout: 4000,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 1,
      },
      1000: {
        items: 1,
      },
    },
  });

  //Initialize form
  $("#loginForm").validate({
    focusInvalid: false,
    rules: {
      "validation-email": {
        required: true,
        email: true,
      },
      "validation-password": {
        required: true,
      },
    },
    errorPlacement: function errorPlacement(error, element) {
      $(element).siblings(".validation-error").removeClass("d-none");
      return true;
    },
    highlight: function (element) {
      var $el = $(element);
      var $parent = $el.parents(".form-group");
      $parent.addClass("invalid-field");
    },
    unhighlight: function (element) {
      var $el = $(element);
      var $parent = $el.parents(".form-group");
      $parent.removeClass("invalid-field");
      $(element).siblings(".validation-error").addClass("d-none");
    },
    submitHandler: function (form) {
      var formdata = $(form).serializeArray();
      var data = {};
      $(formdata).each(function (index, obj) {
        data[obj.name] = obj.value;
      });
      /* alert("Data has been submitted. Please see console log");
                                    console.log("form data ===>", data); */
      login(data);
      $(form).trigger("reset");
      $(".floating-label").removeClass("enable-floating-label");
    },
  });

  const login = (data) => {
    $.ajax({
      type: "POST",
      url: "/api/userAutentication",
      data: data,
      success: function (data, textStatus, xhr) {
        if (data.error) {
          toastr.error(data.message);
          return false;
        } else if (success) {
          location.href = data.location;
        }
      },
    });
  };

  // const validationCode = () => {
  $("#btnCheckCode").click(function (e) {
    e.preventDefault();

    codeUser = $("#factor").val();
    $.ajax({
      type: "POST",
      url: "/api/checkCode",
      data: codeUser,
      success: function (data) {
        if (data.error) {
          toastr.error(data.message);
          return false;
        } else if (data.success) {
          location.href = "../../app/";
        }
      },
    });
  });
  // };
});
