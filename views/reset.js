
$("#reset-form").submit(function(event) {
    $(".alert").remove();
    let data = $("#reset-form").serializeArray();
    postData = createDataObject(data);
        $.ajax({
            type: "POST",
            url: "rest/reset",
            data: postData,
            success: function(res) {
              $(
                `<div class="alert alert-success mt-3" role="alert">${res}</div>`
              ).insertAfter("#reset-form");
            },
            error: function(res) {
              $(
                `<div class="alert alert-success mt-3" role="alert">Server error</div>`
              ).insertAfter("#reset-form");
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
  