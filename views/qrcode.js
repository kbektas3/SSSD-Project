$(function() {
  let email = localStorage.getItem('email')
  $.ajax({
    type: "GET",
    url: `rest/qrcode/${email}`,
    success: function(res) {
      $(`<img src=${res.img} class="mx-auto d-block">`).appendTo("#barcode");
      $("#alert-text").replaceWith(
        "Scan the barcode with your google authenticator"
      );
    },
    error: function(res) {
      $("#alert-text").replaceWith("Error loading barcode");
    }
  });
});

$("#qr-form").submit(function(event) {
  let data = $("#qr-form").serializeArray();
  postData = createDataObject(data);
  let email = localStorage.getItem('email');
  $.ajax({
    type: "POST",
    url: `rest/qrcode/${email}`,
    data: postData,
    success: function(res) {
      if (res.success) {
        $(".alert").remove();
        $(
          `<div class="alert alert-success mt-3" role="alert">Verified successfully!  REDIRECTING TO LOGIN....</div>`
        ).insertAfter("#qr-form");
        localStorage.clear();
        setTimeout(function() {
        window.location.replace("/#login");
      }, 2500);
      }
      if (!res.success) {
        $(".alert").remove();
        $(
          `<div class="alert alert-danger mt-3" role="alert">Incorrect code</div>`
        ).insertAfter("#qr-form");
      }
    },
    error: function(res) {
      $(".alert").remove();
      $(
        `<div class="alert alert-success mt-3" role="alert">Server error</div>`
      ).insertAfter("#qr-form");
    }
  });
  event.preventDefault();
});

//helper functions
function createDataObject(data) {
  var o = {};
  $.each(data, function() {
    if (o[this.name]) {
      if (!o[this.name].push) {
        o[this.name] = [o[this.name]];
      }
      o[this.name].push(this.value || "");
    } else {
      o[this.name] = this.value || "";
    }
  });
  return o;
}
