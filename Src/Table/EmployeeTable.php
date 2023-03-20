<?php
namespace Src\Table;

class EmployeeTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                e.id, e.firstname, e.lastname, d.name as department
            FROM
                public.employee e INNER JOIN 
                public.department d ON d.id = e.deptid
            WHERE e.id = :empId;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(['empId' => $id]);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if ($statement->rowCount() > 0) {
                return $result[0];
            } else {
                return [];
            }
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function search($searchTxt)
    {
        $statement = "
            SELECT 
                e.id,e.firstname,e.lastname,d.name as department,ea.address,ec.contactno
            FROM
                public.employee e 
                INNER JOIN public.department d ON d.id = e.deptid
                LEFT JOIN public.employee_address ea on ea.empid = e.id  
                LEFT JOIN public.employee_contactno ec on ec.empid  = e.id
            WHERE e.firstname LIKE :search
            OR e.lastname LIKE :search
            OR d.name LIKE :search
            OR ea.address LIKE :search
            OR ec.contactno LIKE :search;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(['search' => '%' . $searchTxt . '%']);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function insert(array $input)
    {
        $statement = "
            INSERT INTO public.employee 
                (firstname,lastname,deptid)
            VALUES
                (:firstname,:lastname,:deptid) 
            RETURNING id
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute([
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'deptid' => $input['deptid'],
            ]);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function update(array $input, int $empId)
    {
        $statement = "
            UPDATE public.employee
            SET 
            firstname = :firstname,
            lastname = :lastname,
            deptid = :deptid
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute([
                'firstname' => $input['firstname'],
                'lastname' => $input['lastname'],
                'deptid' => $input['deptid'],
                'id' => $empId,
            ]);
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM public.employee
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(['id' => $id]);
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}

?>
