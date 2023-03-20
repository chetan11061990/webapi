<?php
namespace Src\Table;

class EmployeeContactnoTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function insert(string $contactno, int $empId)
    {
        $statement = "
            INSERT INTO public.employee_contactno 
                (contactno,empid)
            VALUES
                (:contactno,:empid) 
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute([
                'contactno' => $contactno,
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
            DELETE FROM public.employee_contactno
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
}

?>
