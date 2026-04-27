<?php
function getBpsData() {
    $apiKey = "77fbbd4893019eb559cd94e25e70bcd5";
    // Var 1498 = Produksi Padi, Domain 0000 = Nasional
    $url = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/1498/key/" . $apiKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Jangan tunggu terlalu lama
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    // LOGIKA FAIL-SAFE: Jika API Gagal, gunakan data simulasi agar UI tidak rusak
    if (!isset($data['data-content']) || empty($data['data-content'])) {
        return [
            ['name' => 'Jawa Timur', 'value' => 9523000],
            ['name' => 'Jawa Tengah', 'value' => 9356000],
            ['name' => 'Jawa Barat', 'value' => 9113000],
            ['name' => 'Sulawesi Selatan', 'value' => 5212000],
            ['name' => 'Sumatera Selatan', 'value' => 2552000],
            ['name' => 'Lampung', 'value' => 2473000],
            ['name' => 'Sumatera Utara', 'value' => 2022000],
            ['name' => 'Banten', 'value' => 1612000]
        ];
    }

    $provinces = $data['vervar'];
    $values = $data['data-content'];
    $result = [];

    foreach ($provinces as $p) {
        $id = $p['val'];
        $name = $p['label'];
        if (strpos(strtolower($name), 'indonesia') !== false) continue;

        if (isset($values[$id])) {
            $valArr = $values[$id];
            $val = (float)end($valArr); 
            if ($val > 0) {
                $result[] = ['name' => $name, 'value' => $val];
            }
        }
    }

    usort($result, function($a, $b) { return $b['value'] <=> $a['value']; });
    return array_slice($result, 0, 8);
}

$top8Data = getBpsData();
?>