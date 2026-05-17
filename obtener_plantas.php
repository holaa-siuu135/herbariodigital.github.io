<?php
require_once 'config.php';
$result = $conn->query("SELECT id, nombre_comun, nombre_cientifico, familia, usos, imagen_path FROM plantas ORDER BY id DESC");
$plantas = [];
while ($row = $result->fetch_assoc()) {
    $plantas[] = $row;
}
header('Content-Type: application/json');
echo json_encode($plantas);
?>