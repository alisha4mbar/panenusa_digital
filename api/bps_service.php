<?php
function fetchBpsData() {
    $url = "https://webapi.bps.go.id/v1/api/list/model/data/lang/ind/domain/0000/var/1498/th/124/key/77fbbd4893019eb559cd94e25e70bcd5";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // SOLUSI UTAMA FORBIDDEN BPS:
    // Menambahkan User-Agent agar API BPS menganggap permintaan ini dari Browser
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36');

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Jika server BPS tetap menolak (bukan 200 OK)
    if ($httpCode !== 200) {
        return [];
    }

    $json = json_decode($response, true);
    $results = [];

    if (!empty($json['data'])) {
        foreach ($json['data'] as $item) {
            $results[] = [
                'provinsi' => $item['label'],
                'produksi' => (float)$item['value']
            ];
        }
        // Urutkan dari hasil terbesar
        usort($results, function($a, $b) {
            return $b['produksi'] <=> $a['produksi'];
        });
        return array_slice($results, 0, 8);
    }
    return [];
}