<?php
namespace Src\Table;

class EmployeeAddressesTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function insert(string $address, int $empId)
    {
        $statement = "
            INSERT INTO public.employee_address 
                (address,empid)
            VALUES
                (:address,:empid)
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute([
                'address' => $address,
                'empid' => $empId,
            ]);
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM public.employee_address
            WHERE empid = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(['id' => $id]);
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                address
            FROM
                public.employee_address
            WHERE empid = :empId;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(['empId' => $id]);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}

?>
