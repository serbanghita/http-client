<?php
$page = isset($_GET['page']) ? $_GET['page'] : null;

$result = new \stdClass();
$result->id = rand(0, 1000);
$result->page = $page;

$response = new \stdClass();
$response->result = $result;
$response->error = null;
$response->id = rand(0, 10);


header('Content-type: application/json');
exit(json_encode($response));