<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;
use Src\Config\DatabaseConnector;

$dotenv = new DotEnv(__DIR__);
$dotenv->load();

$dbconnection = DatabaseConnector::getConnection();

echo 'IN API';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE');
header('Access-Control-Max-Age: 3600');
header(
    'Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$section = $uri[2] ?? null;

echo $section;

if (!in_array($section, ['department'])) {
    header('HTTP/1.1 404 Not Found');
    exit();
}

// $userId = null;
// if (false === empty($uri[3])) {
//     $userId = (int) $uri[3];
// }

// $requesMethod = $_SERVER['REQUEST_METHOD'];

if ($section == 'department') {
    $section = new Src\Controller\DepartmentController($dbconnection, 'POST');
    $section->processRequests();
}

?>
