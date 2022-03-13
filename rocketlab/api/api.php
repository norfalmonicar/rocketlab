<?php

    require '../db/sqliteconnection.php';
    require '../class/users.php';
    require '../class/tasks.php';
    
    $pdo    = (new SQLiteConnection())->connect();

    $method = (isset( $_REQUEST['method'] ) ? $_REQUEST['method'] : '');

    switch ($method) {
        case "getListTask":
            $task   = new Tasks( $pdo );
            $result = $task->getListTask( $_REQUEST );

            echo json_encode($result);
        break;
        case "getListTaskStatus":
          $task   = new Tasks( $pdo );
          $result = $task->getListTaskStatus( );

          echo json_encode($result);
        break;
        case "insertTask":
          $task   = new Tasks( $pdo );
          $result = $task->insertTask( $_REQUEST );

          echo $result;
        break;
        case "deleteTask":
          $task     = new Tasks( $pdo );
          $task_id  = $_REQUEST['task_id'];
          $result   = $task->deleteTask( $task_id );

          echo $result;
        break;
        case "getListTaskPriority":
          $task   = new Tasks( $pdo );
          $result = $task->getListTaskPriority( );

          echo json_encode($result);
        break;
        case "updateTaskPriority":
          $task   = new Tasks( $pdo );
          $result = $task->updateTaskPriority( $_REQUEST );
          echo $result;
        break;
        case "completeTask":
          $task     = new Tasks( $pdo );
          $task_id  = $_REQUEST['task_id'];
          $result   = $task->completeTask( $task_id );
          echo $result;
        break;
        case "getListUsers":
          $users   = new users( $pdo );
          $result  = $users->getListUsers();

          echo json_encode($result);
        break; 
        case "updateTaskAssignment":
          $task   = new Tasks( $pdo );
          $result = $task->updateTaskAssignment( $_REQUEST );
          echo $result;
        break; 
        case "insertUser":
          $users   = new users( $pdo );
          $result  = $users->insertUser( $_REQUEST );
          echo $result;
        break;   
        case "updateTaskTitle":
          $task   = new Tasks( $pdo );
          $result = $task->updateTaskTitle( $_REQUEST );
          echo $result;
        break;
        default:
          echo "Error";
    }
?>