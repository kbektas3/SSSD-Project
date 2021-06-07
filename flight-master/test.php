<?php
require 'vendor/autoload.php';

use OTPHP\TOTP;

/*echo strtoupper(generateRandomString(16));

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}*/

$secret_key="PBMKOS7SXPOWRNHA";
$otp = TOTP::create($secret_key);
echo 'The current OTP is: '.$otp->now();

echo "<br>";
$otp->setLabel("kenan-test");
echo $otp->getProvisioningUri();
?>
