<?php

require('helpers.php');

function api ($path, $db) {
  $response = null;
  $method = $_SERVER['REQUEST_METHOD'];

  if (preg_match('/^\/todos$/', $path)) {
    switch ($method) {
      case 'GET':
        $query = "SELECT * FROM todos;";
        $result = $db->query($query);
        $all_data = $result->fetch_all();
        $response = [];

        foreach ($all_data as $key => $values) {
          $keys = [
            'id',
            'description',
            'completed',
            'createdAt'
          ];
          $combined = array_combine($keys, $values);
          $combined['completed'] = (boolean) $combined['completed'];

          array_push($response, $combined);
        }

        header('Content-Type: application/json');
        break;
      case 'POST':
        $json = file_get_contents('php://input');
        $decoded = json_decode($json);
        $description = $decoded->description;
        $completed = $decoded->completed;
        $created_at = $decoded->createdAt;
        $query = "INSERT INTO todos (
            description,
            completed,
            created_at
          ) VALUES (
            '$description',
            '$completed',
            '$created_at'
          );
        ";

        $db->query($query);

        if ($db->errno)
          http_response_code(500);
        else
          http_response_code(201);

        break;
      default:
        echo 'Method not allowed';
        break;
    }
  } else if (preg_match('/^\/todos\/\d+$/', $path)) {
    $todo_id = explode('/', $path)[2];

    switch ($method) {
      case 'DELETE':
        $query = "DELETE FROM todos WHERE id = '$todo_id';";

        $db->query($query);

        if ($db->errno)
          http_response_code(500);
        else
          http_response_code(200);

        break;
      case 'PUT':
        $json = file_get_contents('php://input');
        $decoded = json_decode($json);
        $description = $decoded->description;
        $completed = (int) $decoded->completed;
        $query = "UPDATE todos
          SET description = '$description',
              completed = '$completed'
          WHERE id = '$todo_id';
        ";

        $db->query($query);

        if ($db->errno)
          http_response_code(500);
        else
          http_response_code(200);

        break;
      default:
        echo 'Method not allowed';
        break;
    }
  } else {
    header('HTTP/1.0 404 Not Found');
  }

  if ($response)
    echo json_encode($response);
}
