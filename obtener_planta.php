<?php
require_once 'config.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) exit(json_encode(['error' => 'ID no proporcionado']));
$stmt = $conn->prepare("SELECT id, nombre_comun, nombre_cientifico, familia, descripcion, usos, imagen_path, created_at FROM plantas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if ($row) {
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'No encontrada']);
}
?>