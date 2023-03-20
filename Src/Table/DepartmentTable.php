<?php
namespace Src\Table;

class DepartmentTable
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function insert(array $input)
    {
        $statement = "
            INSERT INTO public.department 
                (name)
            VALUES
                (:department);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute([
                'department' => $input['department'],
            ]);
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                id,name
            FROM
                public.department
            WHERE id = :deptId;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(['deptId' => $id]);
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
}

?>
