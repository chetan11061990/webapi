<?php
namespace Src\Controller;
use Src\Table\EmployeeTable;
use Src\Table\EmployeeAddressesTable;
use Src\Table\EmployeeContactnoTable;
use Src\Table\DepartmentTable;
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
        $this->department = new DepartmentTable($dbconnection);
    }

    public function processRequests()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $this->parseInput();
                $response = $this->addEmployeeDetails();
                break;
            case 'GET':
                $reqData = $this->getRequestData();

                if (false === empty($reqData['empId'])) {
                    $this->empId = (int) $reqData['empId'];
                    $response = $this->findEmployee();
                } elseif (false === empty($reqData['search'])) {
                    $searchText = filter_var(
                        $reqData['search'],
                        FILTER_SANITIZE_STRING
                    );
                    $response = $this->searchEmployees($searchText);
                } else {
                    return $this->unprocessableResponse('Invalid Input');
                }
                break;
            case 'DELETE':
                if (true === empty($this->empId)) {
                    return $this->unprocessableResponse('EmpID is missing');
                }
                $response = $this->removeEmployee();
                break;
            case 'PUT':
                if (true === empty($this->empId)) {
                    return $this->unprocessableResponse('EmpID is missing');
                }
                $response = $this->updateEmployeeDetails();
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
        $deptId = $this->department->find($this->empdetails['deptid']);
        if (true === empty($deptId)) {
            $this->unprocessableResponse('Department does not exist');
        }

        $lastInsertId = $this->employee->insert($this->empdetails);

        if (!$lastInsertId) {
            $this->unprocessableResponse('Error while adding employee data');
        }

        if (false === empty($this->empaddresses)) {
            $this->addEmployeeAddresses($lastInsertId);
        }

        if (false === empty($this->empcontactnos)) {
            $this->addEmployeeContactno($lastInsertId);
        }

        $response['status_code'] = 'HTTP/1.1 200 Ok';
        $response['body'] = json_encode([
            'success' => 'Employee data created succesfully',
        ]);

        return $response;
    }

    private function updateEmployeeDetails()
    {
        $empId = (int) $this->empId;
        $result = $this->employee->find($empId);

        if (true === empty($result)) {
            return $this->noDataFound();
        }

        $this->parseInput();

        $deptId = $this->department->find($this->empdetails['deptid']);
        if (true === empty($deptId)) {
            $this->unprocessableResponse('Department does not exist');
        }

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

    private function searchEmployees($searchText)
    {
        $empDetails = $this->employee->search($searchText);
        if (count($empDetails) == 0) {
            return $this->noDataFound();
        }

        print_r($empDetails);

        $response['status_code'] = 'HTTP/1.1 200 Ok';
        $response['body'] = json_encode([
            'data' => $empDetails,
        ]);

        return $response;
    }

    private function findEmployee()
    {
        $empDetails = $this->employee->find($this->empId);
        if (!$empDetails) {
            return $this->noDataFound();
        }
        $empAddresses = $this->employeeAddress->find($this->empId);
        if (false === empty($empAddresses)) {
            $addr = 1;
            foreach ($empAddresses as $val) {
                $empDetails['addresses']['address' . $addr] = $val['address'];
                $addr++;
            }
        }

        $empContactnos = $this->employeeContact->find($this->empId);
        if (false === empty($empContactnos)) {
            $cnt = 1;
            foreach ($empContactnos as $val) {
                $empDetails['contactnos']['contactno' . $cnt] =
                    $val['contactno'];
                $cnt++;
            }
        }

        $response['status_code'] = 'HTTP/1.1 200 Ok';
        $response['body'] = json_encode([
            'data' => $empDetails,
        ]);

        return $response;
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

    private function removeEmployee()
    {
        $empId = (int) $this->empId;
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
        $requestData = $this->getRequestData();

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

    private function getRequestData()
    {
        $requestData = (array) json_decode(
            file_get_contents('php://input'),
            true
        );

        return $requestData;
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
