<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$apiKey = $_ENV['API_KEY'];

$request = json_decode(file_get_contents("php://input"), true);
$lat = $request['lat'];
$lon = $request['lon'];
$range = $request['range'];
$start = 1;

$url = 'https://webservice.recruit.co.jp/hotpepper/gourmet/v1/';
$data = [
    'key' => $apiKey,
    'lat' => $lat,
    'lng' => $lon,
    'range' => $range,
    'start' => $start,
    'format' => 'json',
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = json_decode(curl_exec($curl), true);
$shops = [];
$shops = $response['results']['shop'];
foreach($shops as $key => $shop){
    $shopinfo['shop'][$key]['access'] = $shop['access'];
    $shopinfo['shop'][$key]['name'] = $shop['name'];
    $shopinfo['shop'][$key]['address'] = $shop['address'];
    $shopinfo['shop'][$key]['open'] = $shop['open'];
    $shopinfo['shop'][$key]['thumbnail'] = $shop['photo']['pc']['l'];
    $shopinfo['shop'][$key]['photo'] = $shop['photo']['pc']['l'];
}
$shopinfo['available'] = $response['results']['results_available'];
$shopinfo['start'] = $response['results']['results_start'];
header("Content-Type: application/json; charset=UTF-8");
$json = json_encode($shopinfo, JSON_UNESCAPED_UNICODE);
echo $json;
curl_close($curl);
?>