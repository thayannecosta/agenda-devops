<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$file = 'agenda.json';

function readData() {
    global $file;
    if (file_exists($file)) {
        $json = file_get_contents($file);
        return json_decode($json, true);
    }
    return [];
}

function writeData($data) {
    global $file;
    $json = json_encode($data);
    file_put_contents($file, $json);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        echo json_encode(readData());
        break;
    case 'POST':
        $data = readData();
        $newEvent = json_decode(file_get_contents('php://input'), true);
        $newEvent['id'] = time(); // Use timestamp as ID
        $data[] = $newEvent;
        writeData($data);
        echo json_encode(['message' => 'Evento adicionado', 'id' => $newEvent['id']]);
        break;
    case 'PUT':
        $data = readData();
        $updatedEvent = json_decode(file_get_contents('php://input'), true);
        $id = $updatedEvent['id'];
        foreach ($data as $key => $event) {
            if ($event['id'] == $id) {
                $data[$key] = $updatedEvent;
                break;
            }
        }
        writeData($data);
        echo json_encode(['message' => 'Evento atualizado']);
        break;
    case 'DELETE':
        $data = readData();
        $id = $_GET['id'];
        foreach ($data as $key => $event) {
            if ($event['id'] == $id) {
                unset($data[$key]);
                break;
            }
        }
        writeData(array_values($data));
        echo json_encode(['message' => 'Evento removido']);
        break;
}
