<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Для тестов на локалке

if (!isset($_GET['action']) || $_GET['action'] !== 'get_lobby') {
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

$steamId = isset($_GET['steam_id']) ? urlencode($_GET['steam_id']) : '';

if (empty($steamId)) {
    echo json_encode(['error' => 'Steam ID required']);
    exit;
}

// 1. Делаем запрос к API трекера (пример для условного deadlock-api / statlocker)
// В реальности тут будет реальный URL открытого API игры
$apiUrl = "https://api.deadlock-api.com/v1/players/" . $steamId . "/active-match";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
// Если нужен ключ API, он передается в заголовках:
// curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ТВОЙ_КЛЮЧ']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Если API ничего не вернуло или вернуло ошибку, отдадим заглушку (для тестов фронтенда)
if ($httpCode !== 200 || !$response) {
    // Временные тестовые данные, чтобы проверить, как работает наш интерфейс в ТГ
    $mockData = [
        'match_id' => '1234567',
        'players' => [
            ['username' => 'YamatoMain', 'hero_name' => 'Yamato', 'pp_score' => '2450', 'winrate' => '58'],
            ['username' => 'Garry_Dota', 'hero_name' => 'Abrams', 'pp_score' => '1980', 'winrate' => '49'],
            ['username' => 'ShadowFiend', 'hero_name' => 'Infernus', 'pp_score' => '3100', 'winrate' => '64']
        ]
    ];
    echo json_encode($mockData);
    exit;
}

// Если все успешно — перенаправляем ответ от игрового API на наш фронтенд
echo $response;