<?php
namespace Src\Controller;
use Src\Table\EmployeeTable;
use Src\Table\EmployeeAddressesTable;
use Src\Table\EmployeeContactnoTable;
use Src\Controller\MainController;

class EmployeeController extends MainController
{
    private $db;
    private $requestMethod;
    private $employee;
    private $employeeAddress;
    private $employeeContact;
    private $empdetails = null;
    private $empcontactnos = null;
    private $empaddresses = null;
    private $empId;

    public function __construct($dbconnection, $requestMethod, $empId = null)
    {
        $this->db = $dbconnection;
        $this->requestMethod = $requestMethod;
        $this->empId = $empId;
        $this->employee = new EmployeeTable($dbconnection);
        $this->employeeAddress = new EmployeeAddressesTable($dbconnection);
        $this->employeeContact = new EmployeeContactnoTable($dbconnection);
    }

    public function processRequests()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $this->parseInput();
                $response = $this->addEmployeeDetails();
                break;
            case 'DELETE':
                if (true === empty($this->empId)) {
                    return $this->unprocessableResponse('EmpID is missing');
                }
                $response = $this->removeEmployee($this->empId);
                break;
            case 'PUT':
                if (true === empty($this->empId)) {
                    return $this->unprocessableResponse('EmpID is missing');
                }
                $response = $this->updateEmployeeDetails($this->empId);
                break;
            default:
                return null;
        }

        header($response['status_code']);
        echo $response['body'];
        exit();
    }

    private function addEmployeeDetails()
    {
        try {
            $lastInsertId = $this->createEmployee();

            if (!$lastInsertId) {
                throw new Exception('Error while adding employee data');
            }

            if (false === empty($this->empaddresses)) {
                $this->addEmployeeAddresses($lastInsertId);
            }

            if (false === empty($this->empcontactnos)) {
                $this->addEmployeeContactno($lastInsertId);
            }
        } catch (Exception $e) {
            $response['status_code'] = 'HTTP/1.1 200 Ok';
            $response['error'] = $e->getMessage();
        }

        $response['status_code'] = 'HTTP/1.1 200 Ok';
        $response['body'] = json_encode([
            'success' => 'Employee data created succesfully',
        ]);

        return $response;
    }

    private function updateEmployeeDetails(int $empId)
    {
        $result = $this->employee->find($empId);
        if (!$result) {
            return $this->noDataFound();
        }
        $this->parseInput();

        $this->employee->update($this->empdetails, $empId);

        if (false === empty($this->empaddresses)) {
            //Remove all prev entries
            $this->removeEmployeeAddresses($empId);
            //Adding all new updated entries
            $this->addEmployeeAddresses($empId);
        }

        if (false === empty($this->empcontactnos)) {
            //Remove all prev entries
            $this->removeEmployeeContactnos($empId);
            //Adding all new updated entries
            $this->addEmployeeContactno($empId);
        }

        $response['status_code'] = 'HTTP/1.1 200 Ok';
        $response['body'] = json_encode([
            'success' => 'Employee data updated succesfully',
        ]);

        return $response;
    }

    private function createEmployee()
    {
        $empId = $this->employee->insert($this->empdetails);
        return $empId;
    }

    private function addEmployeeAddresses(int $empId)
    {
        foreach ($this->empaddresses as $address) {
            $this->employeeAddress->insert($address, $empId);
        }
    }

    private function addEmployeeContactno(int $empId)
    {
        foreach ($this->empcontactnos as $contactno) {
            $this->employeeContact->insert($contactno, $empId);
        }
    }

    private function removeEmployee(int $empId)
    {
        $result = $this->employee->find($empId);
        if (!$result) {
            return $this->noDataFound();
        }

        $this->removeEmployeeAddresses($empId);

        $this->removeEmployeeContactnos($empId);

        $this->employee->delete($empId);

        $response['status_code'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'success' => 'Employee data deleted successfully',
        ]);
        return $response;
    }

    private function removeEmployeeAddresses($empId)
    {
        $this->employeeAddress->delete($empId);
    }

    private function removeEmployeeContactnos($empId)
    {
        $this->employeeContact->delete($empId);
    }

    private function parseInput()
    {
        // $requestData = (array) json_decode(
        //     file_get_contents('php://input'),
        //     true
        // );

        $requestData = [
            'firstname' => 'Chhaya',
            'lastname' => '',
            'deptid' => 1,
            'addresses' => [
                'address1' => 'Pune,Maharashtra',
                'address2' => 'Kalyan,Maharashtra',
            ],
            'contactnos' => [
                'contactno1' => '121151515',
                'contactno2' => '123455878',
                'contactno3' => '124455878',
            ],
        ];

        if (!$this->validateInput($requestData)) {
            $this->unprocessableResponse('Invalid Input');
        }

        $this->empdetails = $requestData;
        $this->empaddresses =
            true === array_key_exists('addresses', $requestData)
                ? $requestData['addresses']
                : null;
        $this->empcontactnos =
            true === array_key_exists('contactnos', $requestData)
                ? $requestData['contactnos']
                : null;
    }

    protected function validateInput($requestData)
    {
        if (true === empty($requestData['firstname'])) {
            return false;
        }

        if (true === empty($requestData['lastname'])) {
            return false;
        }

        if (true === empty($requestData['deptid'])) {
            return false;
        }
        return true;
    }
}
?>
