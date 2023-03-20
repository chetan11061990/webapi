<?php
namespace Src\Controller;
use Src\Table\DepartmentTable;

class DepartmentController
{
    private $db;
    private $requestMethod;

    private $department;

    public function __construct($dbconnection, $requestMethod)
    {
        $this->db = $dbconnection;
        $this->requestMethod = $requestMethod;
        $this->department = new DepartmentTable($dbconnection);
    }

    public function processRequests()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $response = $this->createDepartment();
            default:
                return null;
        }

        header($response['status_code']);
        echo $response['body'];
    }

    private function createDepartment()
    {
        $requestData = (array) json_decode(
            file_get_contents('php://input'),
            true
        );
        if (!$this->validateInput($requestData)) {
            return $this->unprocessableResponse();
        }
        $this->department->insert($requestData);
        $response['status_code'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function validateInput($requestData)
    {
        if (!isset($requestData['department'])) {
            return false;
        }
        return true;
    }

    private function unprocessableResponse()
    {
        $response['status_code'] = 'HTTP/1.1 422 Invalid Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input',
        ]);
        return $response;
    }
}
?>
