<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$apiKey = getenv('BPS_API_KEY') ?: '77fbbd4893019eb559cd94e25e70bcd5';
$url    = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/1498/key/{$apiKey}";

// Fallback data (dipakai jika API BPS tidak tersedia)
$fallback = [
    ['name'=>'Jawa Timur',       'value'=>9523000],
    ['name'=>'Jawa Tengah',      'value'=>9356000],
    ['name'=>'Jawa Barat',       'value'=>9113000],
    ['name'=>'Sulawesi Selatan', 'value'=>5212000],
    ['name'=>'Sumatera Selatan', 'value'=>2552000],
    ['name'=>'Lampung',          'value'=>2473000],
    ['name'=>'Sumatera Utara',   'value'=>2022000],
    ['name'=>'Banten',           'value'=>1612000],
];

try {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Panenusa/1.0)',
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $json = json_decode($response, true);
        if (!empty($json['vervar']) && !empty($json['data-content'])) {
            $result = [];
            foreach ($json['vervar'] as $p) {
                $id   = $p['val'];
                $name = $p['label'];
                if (stripos($name,'indonesia')!==false) continue;
                if (isset($json['data-content'][$id])) {
                    $val = (float)end($json['data-content'][$id]);
                    if ($val > 0) $result[] = ['name'=>$name,'value'=>$val];
                }
            }
            usort($result,fn($a,$b)=>$b['value']<=>$a['value']);
            $result = array_slice($result,0,8);
            if (!empty($result)) { echo json_encode(['source'=>'bps','data'=>$result]); exit; }
        }
    }
} catch (Exception $e) {}

// Fallback
echo json_encode(['source'=>'fallback','data'=>$fallback]);