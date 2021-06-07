$(document).ready(function() {
  $("main#spapp > section").height($(document).height() - 60);

  var app = $.spapp({
    defaultView: "#login",
    templateDir: "./views/",
    pageNotFound: "error_404"
  }); // initialize

  // define routes
  app.route({ view: "login", load: "login.html" });
  app.route({ view: "register", load: "register.html" });
  app.route({ view: "reset", load: "reset.html" });
  app.route({ view: "renew", load: "renew.html" });
  app.route({
    view: "qrcode",
    load: "qrcode.html",
    onCreate: function() {
      if (!localStorage.getItem("email")) window.location.replace("/#login");
    },
    onReady: function() {
      if (!localStorage.getItem("email")) window.location.replace("/#login"); 
    }
  });
  app.route({
    view: "sms",
    load: "sms.html",
    onCreate: function() {
      let number = localStorage.getItem("mobile");
      if (number) {
        $.ajax({
          type: "POST",
          url: `rest/sms`,
          data: { number },
          success: function(res) {
            alert(res);
          },
          error: function(res) {
            alert(res);
          }
        });
      } else {
        window.location.replace("/#login");
      }
    },
    onReady: function() {
      if (!localStorage.getItem("mobile")) window.location.replace("/#login");
    }
  });

  // run app
  app.run();
});
