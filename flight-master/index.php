<?php
require 'flight/Flight.php';
require 'flight/autoload.php';
/**
     * @OA\Post(
     *     path="/register",
     *     tags={"login"},
     *     summary="Login module",
     *     description="This can only be done by the logged in user.",
     *     operationId="login",
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     ),
     *     @OA\RequestBody(
     *         description="Create user object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     )
     * )
    */

Flight::route('POST /register', function () {
 
  //$raw_data = Flight::request()->getBody();
  //$data = json_decode($raw_data, true);

  //echo 'Register procedure '. $data['name'];

  $password = "kenan123";

  $hashed_password = strtoupper(sha1($password));
  $hashed_prefix = substr($hashed_password, 0, 5);
  $hashed_sufix = substr($hashed_password, 5);

  //echo "Prefix".$hashed_prefix;
  //echo "Sufix".$hashed_sufix;

  // Get cURL resource
  $curl = curl_init();
  curl_setopt_array($curl, [
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => 'https://api.pwnedpasswords.com/range/'.$hashed_prefix,
      CURLOPT_USERAGENT => 'Codular Sample cURL Request',
  ]);

  $resp = curl_exec($curl);
  //print_r($resp);

  $pos = strpos($resp, $hashed_sufix);

  if ($pos) {
      echo "Password has been breached";
  } else {
      echo "Password hasn't been breached";
  }
  curl_close($curl);
});

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"login"},
     *     summary="Login module",
     *     description="This can only be done by the logged in user.",
     *     operationId="login",
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     ),
     *     @OA\RequestBody(
     *         description="Create user object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/loginrequest.php")
     *     )
     * )
    */


    Flight::route('POST /login', function(){
        $raw_data=Flight::request()->data->getDate();
        $data=json_decode($raw_data,true);
        
    
        echo 'login procedure: ' .print_r($data, true);
    });

Flight::start();
?>