<?php

class Tasks {

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
    public function getListTask( $request ) {

        $draw       = $request['draw'];
        $start      = $request["start"];
        $rowperpage = $request["length"]; // Rows display per page

        $columnIndex_arr    = $request['order'];
        $columnName_arr     = $request['columns'];
        $order_arr          = $request['order'];
        $search_arr         = $request['search'];

        $columnIndex        = $columnIndex_arr[0]['column']; // Column index
        $columnSortOrder    = $order_arr[0]['dir']; // asc or desc
        $columnName         = $columnName_arr[$columnIndex]['data']; // Column name
        
        $searchValue        = $search_arr['value']; // Search value

        // Count Records
        $sql_total = "SELECT count(*) FROM tasks t " .
               "LEFT JOIN users u on u.user_id = t.user_id " .
               "LEFT JOIN task_priority tp on tp.priority_id = t.task_priority " .
               "LEFT JOIN task_status ts on ts.task_status_id = t.task_status " ;
        
        if( $columnName == 'assigned_to' ){
            $sql_total .= "ORDER BY u.first_name, u.last_name " . $columnSortOrder ;
        }else{
            $sql_total .= "ORDER BY " . $columnName . ' ' . $columnSortOrder ;
        }
        
        // Total records
        $totalRecords = $this->pdo->query($sql_total)->fetchColumn(); 

        // Count Completed
        $sql_complete = "SELECT count(*) FROM tasks t " .
               "LEFT JOIN users u on u.user_id = t.user_id " .
               "LEFT JOIN task_priority tp on tp.priority_id = t.task_priority " .
               "LEFT JOIN task_status ts on ts.task_status_id = t.task_status WHERE task_status = 4" ;
        
        // Total Completed
        $totalCompleted = $this->pdo->query($sql_complete)->fetchColumn(); 

        // Default SQL
        $sql = "SELECT t.*, u.first_name, u.last_name, tp.priority_name, ts.task_status_name FROM tasks t " .
               "LEFT JOIN users u on u.user_id = t.user_id " .
               "LEFT JOIN task_priority tp on tp.priority_id = t.task_priority " .
               "LEFT JOIN task_status ts on ts.task_status_id = t.task_status " ;
        
        if( $columnName == 'assigned_to' ){
            $sql .= "ORDER BY u.first_name, u.last_name " . $columnSortOrder ;
        }else{
            $sql .= "ORDER BY " . $columnName . ' ' . $columnSortOrder ;
        }

        $sql .= " LIMIT " . $start . "," . $rowperpage;

        $stmt = $this->pdo->query( $sql );

        $tasks = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tasks[] = [
                'task_id'       => $row['task_id'],
                'task_title'    => $row['task_title'],
                'task_desc'     => $row['task_desc'],
                'task_status'   => $row['task_status_name'],
                'task_priority' => $row['priority_name'],
                'task_estimate' => $row['task_estimate'],
                'task_started'  => date( 'M d' , strtotime($row['task_started']) ) . ' - ' . date( 'M d' , strtotime($row['task_ended']) ) ,
                'user_id'       => $row['user_id'],
                'assigned_to'   => $row['first_name'] . ' ' . $row['last_name'],
                ''              => ''
            ];
        }

        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecords,
            "itotalCompleted"  => $totalCompleted,
            "aaData" => $tasks
        );

        return $response;
    }

    /**
     * Insert a new task into the users table
     * @param type $data['task_title']
     * @param type $data['task_status']
     * @param type $data['task_estimate']
     * @param type $data['task_started']
     * @param type $data['task_ended']
     * @return int id of the inserted user
     */
    public function insertTask($data) {
        $sql = 'INSERT INTO tasks(task_title,task_status,task_estimate,task_started,task_ended) '
                . 'VALUES(:task_title,:task_status,:task_estimate,:task_started,:task_ended)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'task_title'    => $data['task_title'],
            'task_status'   => $data['task_status'],
            'task_estimate' => $data['task_estimate'],
            'task_started'  => $data['task_started'],
            'task_ended'    => $data['task_ended']
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Delete a task by task id
     * @param int $taskId
     * @return int the number of rows deleted
     */
    public function deleteTask($taskId) {
        $sql = 'DELETE FROM tasks WHERE task_id = :task_id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':task_id', $taskId);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Mark a task completed specified by the task_id 
     * @param type $taskId
     * @return bool true if success and falase on failure
     */
    public function completeTask($task_id){
        $sql = "UPDATE tasks SET task_status = 4 WHERE task_id = :task_id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':task_id', $task_id);

        return $stmt->execute();
    }

    /**
     * Update task priority
     * @param type $data['task_priority']
     * @param type $data['task_id']
     * @return bool true if success and false on failure
     */
    public function updateTaskPriority($data){
        $sql = "UPDATE tasks SET task_priority = :task_priority WHERE task_id = :task_id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':task_priority', $data['task_priority']);
        $stmt->bindValue(':task_id', $data['task_id']);

        return $stmt->execute();
    }

    /**
     * Update task assignment
     * @param type $data['user_id']
     * @param type $data['task_id']
     * @return bool true if success and false on failure
     */
    public function updateTaskAssignment($data){
        $sql = "UPDATE tasks SET user_id = :user_id WHERE task_id = :task_id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':user_id', $data['user_id']);
        $stmt->bindValue(':task_id', $data['task_id']);

        return $stmt->execute();
    }

    /**
     * Update task Title and Estimate
     * @param type $data['task_title']
     * @param type $data['task_estimate']
     * @param type $data['task_id']
     * @return bool true if success and false on failure
     */
    public function updateTaskTitle($data){

        // SQL statement to update user's information
        $sql = "UPDATE tasks SET task_title = :task_title, task_estimate = :task_estimate  WHERE task_id = :task_id";
        $stmt = $this->pdo->prepare($sql);

        // passing values to the parameters
        $stmt->bindValue(':task_title', $data['task_title']);
        $stmt->bindValue(':task_estimate', $data['task_estimate']);
        $stmt->bindValue(':task_id', $data['task_id']);

        // execute the update statement
        return $stmt->execute();
    }

    /**
     * Insert Task Status
     * @param type $task_status_name
     * @return bool true if success and false on failure
     */
    public function insertStatus($task_status_name) {
        $sql = 'INSERT INTO task_status(task_status_name)VALUES(:task_status_name)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'task_status_name' => $task_status_name
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Insert Task Priority
     * @param type $priority_name
     * @return bool true if success and false on failure
     */
    public function insertPriority($priority_name) {
        $sql = 'INSERT INTO task_priority(priority_name) VALUES(:priority_name)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'priority_name' => $priority_name
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Get the tasks as an object list
     * @return an array of Task Status objects
     */
    public function getListTaskStatus() {

        $stmt = $this->pdo->query( "SELECT * from task_status" );

        $task_status = [];
        
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $task_status[] = [
                'task_status_id'      => $row['task_status_id'],
                'task_status_name'    => $row['task_status_name'],
            ];
        }

        return $task_status;
    }

    /**
     * Get the tasks as an object list
     * @return an array of Task Priority objects
     */
    public function getListTaskPriority() {
        $stmt = $this->pdo->query( "SELECT * from task_priority" );

        $task_priority = [];
        
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $task_priority[] = [
                'priority_id'      => $row['priority_id'],
                'priority_name'    => $row['priority_name'],
            ];
        }

        return $task_priority;
    }

}

?>