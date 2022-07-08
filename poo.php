<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
session_start();
?>
<html><head><?php include('includes/head.php'); ?></head>

<body>
<script>
    function getCookie(cookieName) {
        let cookie = {};
        document.cookie.split(';').forEach(function(el) {
            let [key,value] = el.split('=');
            cookie[key.trim()] = value;
        })
        return cookie[cookieName];
    }

    let externalUserId = decodeURIComponent(getCookie("email"));

    OneSignal.push(function() {
        OneSignal.setExternalUserId(externalUserId);
    });

    OneSignal.push(function() {
        OneSignal.getExternalUserId().then(function(externalUserId){
            console.log("externalUserId: ", externalUserId);
        });
    });


</script>
<?php

    function sendMessage($message,$email){
        $content = array(
            "en" => $message
            );

        $fields = array(
            'app_id' => "3e374b55-473b-404a-adc5-f630bc9bf216",
            'include_external_user_ids' => array($email),
      'channel_for_external_user_ids' => 'push',
            'data' => array("foo" => "bar"),
            'contents' => $content
        );

        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   'Authorization: Basic NWYxM2M1ZTUtMzQ0ZS00Njk0LWFkNDYtNjFlOGE4NTA0NmQz'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
$popo = 156;
    $response = sendMessage("Merci pour votre achat vous avez gagné".$popo."points Vous avez maintenant ","rootos@boutos.fr");
    $return["allresponses"] = $response;
    $return = json_encode( $return);

    print("\n\nJSON received:\n");
    print($return);
    print("\n");
?>

</body>
</html>