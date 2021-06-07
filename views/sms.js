$("#sms-form").submit(function(event) {
  $(".alert").remove();
  let data = $("#sms-form").serializeArray();
  postData = createDataObject(data);
  $.ajax({
    type: "POST",
    url: "rest/sms",
    data: postData,
    success: function(res) {
      $(".alert").remove();
      $(
        `<div class="alert alert-success mt-3" role="alert">${res}</div>`
      ).insertAfter("#sms-form");
      localStorage.clear();
      setTimeout(function() {
        window.location.replace("/#login");
      }, 2500);
    },
    error: function(res) {
      $(".alert").remove();
      $(
        `<div class="alert alert-success mt-3" role="alert">Server error</div>`
      ).insertAfter("#sms-form");
    }
  });
  event.preventDefault();
});

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
