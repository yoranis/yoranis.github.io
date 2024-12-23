<?php
$ipLogFile = 'ip_log.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $ip = $input['ip'] ?? 'Bilinmiyor';
    $countryName = $input['country_name'] ?? 'Bilinmiyor';
    $countryCode = $input['country_code'] ?? '';
    $os = $input['os'] ?? 'Bilinmiyor';
    $countryEmoji = getCountryFlagEmoji($countryCode);
    $loggedIps = file_exists($ipLogFile) ? file($ipLogFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    if (!in_array($ip, $loggedIps)) {
        $webhookUrl = 'https://ptb.discord.com/api/webhooks/1308444147031609424/qNjpRDcMS4eR80rQt9cZXSmeudDOYQSNsz2ZEZFzjmdIH1po-xlFeObMAGyq8cyhR7IH';
        $embed = [
            'title' => 'Dosya İndirildi!',
            'color' => 0xFF7F00,
            'fields' => [
                [
                    'name' => 'IP Adresi',
                    'value' => $ip,
                    'inline' => true
                ],
                [
                    'name' => 'Ülke',
                    'value' => $countryName . ' ' . $countryEmoji,
                    'inline' => true
                ],
                [
                    'name' => 'İşletim Sistemi',
                    'value' => $os,
                    'inline' => true
                ]
            ],
            'timestamp' => date('c'),
            'footer' => [
                'text' => 'İndirme Bilgisi'
            ]
        ];
        $payload = json_encode(['embeds' => [$embed]]);
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        file_put_contents($ipLogFile, $ip . PHP_EOL, FILE_APPEND);
    }
    echo json_encode(['success' => true]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filePath = 'alpha/Yoranis Setup - V1.1.2.rar';
    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Dosya bulunamadı.']);
    }
}

function getCountryFlagEmoji($countryCode) {
    if (empty($countryCode)) {
        return '';
    }
    $emoji = '';
    foreach (str_split(strtoupper($countryCode)) as $char) {
        $emoji .= mb_chr(127397 + ord($char), 'UTF-8');
    }
    return $emoji;
}