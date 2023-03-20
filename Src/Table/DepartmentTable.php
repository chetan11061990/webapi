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
}

?>
