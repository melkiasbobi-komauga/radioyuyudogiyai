<?php
/**
 * check_stream.php
 * Mengambil data status dan pendengar real-time dari server radio
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Mencegah blokir akses

// URL Status JSON Icecast (Sesuai dengan URL Streaming Anda di index.php)
// Host: b.alhastream.com, Port: 5049
$stream_url_json = "https://b.alhastream.com:5049/status-json.xsl";
$mountpoint = "/radio"; // Mountpoint spesifik yang digunakan

$response = [
    'status' => 'offline',
    'listeners' => 0,
    'current_song' => 'Terhubung' // Default teks jika tidak ada judul lagu
];

// Gunakan cURL untuk mengambil data
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $stream_url_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Abaikan SSL jika ada masalah sertifikat
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200 && !empty($result)) {
    $data = json_decode($result, true);

    // Cek apakah struktur JSON valid sesuai format Icecast
    if (isset($data['icestats']['source'])) {
        $sources = $data['icestats']['source'];

        // Jika hanya ada 1 mountpoint, Icecast mengembalikannya sebagai object, bukan array
        // Kita ubah jadi array agar loop di bawah tetap jalan
        if (isset($sources['listenurl'])) {
            $sources = [$sources];
        }

        foreach ($sources as $source) {
            // Cocokkan dengan mountpoint '/radio'
            if (strpos($source['listenurl'], $mountpoint) !== false || count($sources) == 1) {
                $response['status'] = 'online';
                $response['listeners'] = isset($source['listeners']) ? (int)$source['listeners'] : 0;
                
                // Ambil judul lagu jika ada
                if (!empty($source['title']) && $source['title'] != '-') {
                    $response['current_song'] = $source['title'];
                } elseif (!empty($source['yp_currently_playing']) && $source['yp_currently_playing'] != '-') {
                    $response['current_song'] = $source['yp_currently_playing'];
                }
                break;
            }
        }
    }
}

echo json_encode($response);
?>