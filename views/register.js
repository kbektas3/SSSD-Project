//register form submit handler
$("#register-form").submit(function(event) {
    let data = $("#register-form").serializeArray();
    postData = createDataObject(data);
    $.ajax({
      type: "POST",
      url: "rest/register",
      data: postData,
      success: function(res) {
          $(
            `<div class="alert alert-success mt-3" role="alert">Registered Successfully! - REDIRECTING TO LOGIN PAGE...</div>`
          ).insertAfter("#register-form");
          setTimeout(function(){
              window.location.replace('/SSSD/#login');
          },2500)
        },
        error: function(res) {
          let errors = res.responseJSON.errors;
          $(
            `<div class="alert alert-danger mt-3" role="alert">${errors}</div>`
          ).insertAfter("#register-form");
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
  