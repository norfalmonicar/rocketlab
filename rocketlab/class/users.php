<?php

    class Users {

        /**
         * PDO object
         * @var \PDO
         */
        private $pdo;

        /**
         * Initialize the object with a specified PDO object
         * @param \PDO $pdo
         */
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        /**
         * Get the tasks as an object list
         * @return an array of Task  objects
         */
        public function getListUsers() {
            $stmt = $this->pdo->query('SELECT * FROM users');

            $users = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $users[] = [
                    'user_id'       => $row['user_id'],
                    'first_name'    => $row['first_name'],
                    'last_name'     => $row['last_name'],
                ];
            }
            return $users;
        }


        /**
         * Insert a new task into the users table
         * @param type $firstname
         * @param type $lastname
         * @return int id of the inserted user
         */
        public function insertUser($data){
            $sql = 'INSERT INTO users(first_name, last_name) VALUES(:first_name, :last_name)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':first_name'   => $data['first_name'],
                ':last_name'    => $data['last_name'],
            ]);

            return $this->pdo->lastInsertId();
        }


        /**
         * Update user's information
         * @param type $firstname
         * @param type $lastname
         * @param type $userid
         * @return bool true if success and false on failure
         */
        public function updateUser($firstname, $lastname, $userid){

            // SQL statement to update user's information
            $sql = "UPDATE users SET first_name = :first_name , last_name = :last_name WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);

            // passing values to the parameters
            $stmt->bindValue(':first_name', $firstname);
            $stmt->bindValue(':last_name', $lastname);
            $stmt->bindValue(':user_id', $userid);

            // execute the update statement
            return $stmt->execute();
        }

        /*public function insertTask($taskName, $startDate, $completedDate, $completed, $projectId) {
            $sql = 'INSERT INTO tasks(task_name,start_date,completed_date,completed,project_id) '
                    . 'VALUES(:task_name,:start_date,:completed_date,:completed,:project_id)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':task_name' => $taskName,
                ':start_date' => $startDate,
                ':completed_date' => $completedDate,
                ':completed' => $completed,
                ':project_id' => $projectId,
            ]);

            return $this->pdo->lastInsertId();
        }*/
    }

?>