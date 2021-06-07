<?php
    require '../vendor/autoload.php';
    require './database.php';
    require './models/users.php';
    require_once "recaptchalib.php";
    use OTPHP\TOTP;




    
    Flight::route('POST /register', function(){
        $database = new Database();
        $request = Flight::request();
        $data = $request->data;
        $db = $database->getConnection();
        $user = new User($db);
        $user->name = $data->name;
        $user->username = $data->username;
        $user->mobile = $data->mobile;
        $user->email = $data->email;
        $user->password = $data->password;
        $errors = $user->create();
        if(empty($errors)) {
        Flight::json($user, 201);
        // Flight::redirect('/login');
        }
        else{
        $errorsDisplay = new stdClass();
        $errorsDisplay->errors = $errors;
        Flight::json($errorsDisplay, 400);
        }
    });





    Flight::route('POST /login', function(){
        session_start();
        $database = new Database();
        $request = Flight::request();
        $ip = $request->ip;
        $data = $request->data;
        $db = $database->getConnection();
        $user = new User($db);
        $user->username = $data->username;
        $user->password = $data->password;

        $errors = $user->login();
        unset($user->password);

        if(isset($_SESSION['counter'])){
          $counter = (int)$_SESSION['counter'];
          if($counter > 4 && empty($data['g-recaptcha-response'])){
            array_push($errors, ' Check ReCaptcha!');
          }
        }

        if(empty($errors) && verifyGoogle($ip, $data['g-recaptcha-response'])) {
          unset($errors);
          unset($_SESSION["counter"]);
          Flight::json($user, 200);
        }
        else{
          if(isset($_SESSION["counter"])){
            $count = (int)$_SESSION["counter"];
            $count = $count + 1;
            $_SESSION["counter"] = $count;
          } else {
            $_SESSION["counter"] = 1;
          }
          $errorsDisplay = new stdClass();
          $errorsDisplay->errors = $errors;
          $errorsDisplay->count = $_SESSION["counter"];
          Flight::json($errorsDisplay, 400);
        }
    });





    Flight::route('GET|POST /qrcode/@email', function($email){
      $method = Flight::request()->method;

      if($method=='GET'){
        $totp = TOTP::create("XSLCUE5D3Z5PA===");
        $totp->setLabel($email);
        $googleChartUri = $totp->getQrCodeUri();
        //echo "<img src='{$googleChartUri}'>";
        Flight::json(array('img' => $googleChartUri));
      } 

      if($method=='POST'){
        $totp = TOTP::create("XSLCUE5D3Z5PA===");
        $totp->setLabel($email);
        $code = Flight::request()->data->otp;
        $result = $totp->verify($code);
        if ($result) {
          Flight::json(array('success' => true));
        } else {
          Flight::json(array('success' => false));
        }
      } 
    });





    Flight::route('POST /sms', function(){
      session_start();
      $ch = curl_init();
      $data = Flight::request()->data;
      $number = $data['number'];
      $code = $data['code'];

      $generatedNumber = rand(1000, 9999);

      if(empty($code)){
        curl_setopt($ch, CURLOPT_URL, "https://rest.nexmo.com/sms/json");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
          $ch,
          CURLOPT_POSTFIELDS,
          "from=Nexmo&text=Your verification code is: ".$generatedNumber."&to=".$number."&api_key=5f515a66&api_secret=V1f2Po9pjN0Mmxfh"
        );
  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
        $server_output = curl_exec($ch);
        $manage = json_decode($server_output, true);
        if ($manage['message-count'] == 1) {
          $_SESSION['sms_code'] = $generatedNumber;
          Flight::json("Message sent successfuly");
        } else {
          Flight::json("Message sent unsuccessfuly",400);
        }
        curl_close($ch);
      } else {
        $verfiyCode = (int)$_SESSION['sms_code'];
        if($code == $verfiyCode){
          unset($_SESSION['sms_code']);
          Flight::json('Successfully verified! REDIRECTING TO LOGIN....');
        } else {
          Flight::json('Incorrect code', 400);
        }
      }
    });





    Flight::route('POST /reset', function(){
      $data = Flight::request()->data;
      $generatedNumber = rand(1000000,9999999);

      $database = new Database();
      $db = $database->getConnection();
      $user = new User($db);
      $error = $user->findByEmail($data['email']);
      if(empty($error)) {
        $username = $user->username;
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("kenan@bektas.me", "Kenan Bektas");
        $email->setSubject('Password Renewal');
        $email->addTo($data['email']);
        $email->addContent("text/plain", 'Your password reset link: https://sssd-projectkenan.herokuapp.com/rest/verify/'.$username.'/'.$generatedNumber);
        $sendgrid = new \SendGrid('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');
        try {
          $response = $sendgrid->send($email);
          if ($response->statusCode() == 202) {
            echo "Email sent";
          } else {
            echo "Email not sent";
          }
        } catch (Exception $e) {
          echo "Email not sent";
        }
      } else {
        echo $error;
      }
    });




    Flight::route('GET /verify/@username/@code', function($username, $code){
      //Flight::redirect('http://localhost/sssdproject/#renew');
      echo $username.'   '.$code;
    });


    Flight::route('POST /renew', function(){
      $data = Flight::request()->data;

    });


    function verifyGoogle($ip, $data) {
      $secret = "6LeTY6IUAAAAALhBJk5CrXFUKZBOGuRbIzMWieEr";
        $response = null;
        $reCaptcha = null;
        if(isset($data)){
          $reCaptcha = new ReCaptcha($secret);
          $response = $reCaptcha->verifyResponse($ip,$data);
          if($response->success){
            return true;
          }
        } else {
          return true;
        }
        return false;
    }





    Flight::start();

?>