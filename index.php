<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;
use Src\Config\DatabaseConnector;

$dotenv = new DotEnv(__DIR__);
$dotenv->load();

$dbconnection = DatabaseConnector::getConnection();

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

$empId = null;
if (false === empty($uri[3])) {
    $empId = (int) $uri[3];
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

/*----Debug Inputs-----*/
// $requestMethod = 'GET';
// $empId = 12;
// $section = 'employee';

switch ($section) {
    case 'department':
        $section = new Src\Controller\DepartmentController(
            $dbconnection,
            $requestMethod
        );
        break;
    case 'employee':
        $section = new Src\Controller\EmployeeController(
            $dbconnection,
            $requestMethod,
            $empId
        );
        break;
    default:
        header('HTTP/1.1 404 Not Found');
        exit();
}

$section->processRequests();

?>
