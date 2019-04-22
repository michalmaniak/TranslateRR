<?php
function curl($jsonData, $curl)
{
	$ch = curl_init($curl);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $result = curl_exec($ch);
}
$token='( ?� ?? ?�)';
$input = json_decode(file_get_contents('php://input'), true);


$sender=$input['message']['chat']['id'];
$message=$input['message']['text'];
$url = 'https://api.telegram.org/bot'.$token.'/sendMessage?parse_mode=html';

$key = '( ?� ?? ?�)';

$host = "https://api.cognitive.microsofttranslator.com";
$path = "/translate?api-version=3.0";


$params = "&to=en&to=fr&to=pl&to=es";

$text = $message;

if (!function_exists('com_create_guid')) {
  function com_create_guid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
  }
}

function Translate ($host, $path, $key, $params, $content) {

    $headers = "Content-type: application/json\r\n" .
        "Content-length: " . strlen($content) . "\r\n" .
        "Ocp-Apim-Subscription-Key: $key\r\n" .
        "X-ClientTraceId: " . com_create_guid() . "\r\n";

    // NOTE: Use the key 'http' even if you are making an HTTPS request. See:
    // http://php.net/manual/en/function.stream-context-create.php
    $options = array (
        'http' => array (
            'header' => $headers,
            'method' => 'POST',
            'content' => $content
        )
    );
    $context  = stream_context_create ($options);
    $result = file_get_contents ($host . $path . $params, false, $context);
    return $result;
}

$requestBody = array (
    array (
        'Text' => $text,
    ),
);
$content = json_encode($requestBody);

$result = Translate ($host, $path, $key, $params, $content);

// Note: We convert result, which is JSON, to and from an object so we can pretty-print it.
// We want to avoid escaping any Unicode characters that result contains. See:
// http://php.net/manual/en/function.json-encode.php
$json = json_decode($result, true);
$code=$json[0]['detectedLanguage']['language'];
$wynik="";
if($code!=='en')
{
	$wynik=$wynik."🇬🇧 ".$json[0]['translations'][0]['text']."\n";
}
if($code!=='fr')
{
	$wynik=$wynik."🇫🇷 ".$json[0]['translations'][1]['text']."\n";
}
if($code!=='pl')
{
	$wynik=$wynik."🇵🇱 ".$json[0]['translations'][2]['text']."\n";
}
if($code!=='es')
{
	$wynik=$wynik."🇪🇸 ".$json[0]['translations'][3]['text']."\n";
}
		$jsonData='{
 "text": "'.$wynik.'",
"chat_id": '.$sender.',
"reply_to_message_id": '.$input['message']['message_id'].'
}';

		

curl($jsonData, $url);

?>