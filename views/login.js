
$("#login-form").submit(function(event) {
  $(".alert").remove();
  let data = $("#login-form").serializeArray();
  postData = createDataObject(data);
  $.ajax({
    type: "POST",
    url: "rest/login",
    data: postData,
    success: function(res) {
      $(".alert").remove();
      $(
        `<div class="alert alert-success mt-3" role="alert">Success!  REDIRECTING TO SECOND STEP....</div>`
      ).insertAfter("#login-form");
      localStorage.setItem('mobile',res.mobile);
      localStorage.setItem('email',res.email);
      $(".g-recaptcha").remove();
      if(postData.auth === 'sms'){
        setTimeout(function() {
          window.location.replace("/#sms");
        }, 2000);
      } else {
        setTimeout(function() {
          window.location.replace("/#qrcode");
        }, 2000);
      }
    },
    error: function(res) {
      let errors = res.responseJSON.errors;
      let count = res.responseJSON.count;
      let captcha = $(".g-recaptcha");
      $(".alert").remove();
      $(
        `<div class="alert alert-danger mt-3" role="alert">${errors}</div>`
      ).insertAfter("#login-form");
      if(count > 4 && captcha.length === 0){
        $( '<div class="form-group"><div class="g-recaptcha d-flex justify-content-center" data-sitekey="6LeTY6IUAAAAACUaKXhqE1l6cmJbNK6dg5siuqeP"></div></div>' ).insertBefore( "#after-captcha" );
        $.getScript('https://www.google.com/recaptcha/api.js', function(){})
      }
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
