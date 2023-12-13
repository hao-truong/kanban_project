<?php
declare(strict_types=1);

use app\core\Application;

require_once __DIR__ . '/../vendor/autoload.php';

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, PUT, DELETE");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$container = require __DIR__ . '/../src/shared/configs/php-di/config.php';
$app = $container->get(Application::class);

require_once __DIR__ . '/routes/authRoute.php';
require_once __DIR__ . '/routes/boardRoute.php';
require_once __DIR__ . '/routes/userRoute.php';
require_once __DIR__ . '/routes/columnRoute.php';
require_once __DIR__ . '/routes/cardRoute.php';

$app->run();
