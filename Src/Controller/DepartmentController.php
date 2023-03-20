<?php
namespace Src\Controller;
use Src\Table\DepartmentTable;
use Src\Controller\MainController;

class DepartmentController extends MainController
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
                break;
            default:
                return null;
        }

        header($response['status_code']);
        echo $response['body'];
        exit();
    }

    private function createDepartment()
    {
        $requestData = (array) json_decode(
            file_get_contents('php://input'),
            true
        );
        if (!$this->validateInput($requestData)) {
            $this->unprocessableResponse('Invalid Input');
        }
        $this->department->insert($requestData);
        $response['status_code'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode([
            'success' => 'Department created succesfully',
        ]);
        return $response;
    }

    protected function validateInput($requestData)
    {
        if (!isset($requestData['department'])) {
            return false;
        }
        return true;
    }
}
?>
