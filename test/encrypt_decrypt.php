<?php

include '../functions/commonFunction.php';
$user_id = 'chain';
$user_id_encrypt = encrypt_text($user_id);
echo "encrypt: ".$user_id_encrypt;
echo "<br />";
$user_id_encrypt = '+UGpyUUhXQ1Rxd2RVUU5memJ6UWp3dVJSSVV2UDl5aTJHWVZUWms1WVk9';
//if(isset($_GET['id'])) $id = $_GET['id'];
//$id = 'UGpyUUhXQ1Rxd2RVUU5memJ6UWp3dVJSSVV2UDl5aTJHWVZUWms1WVk9';
//echo $id;
//$id = 'UGpyUUhXQ1Rxd2RVUU5memJ6UWp3dVJSSVV2UDl5aTJHWVZUWms1WVk9';
$user_id_decrypt = decrypt_text($user_id_encrypt);
echo "<br />";
echo "Original: ".$user_id_decrypt;

?>
