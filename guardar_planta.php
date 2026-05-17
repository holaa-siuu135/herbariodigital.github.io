<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$nombre_comun = trim($_POST['nombre_comun'] ?? '');
$nombre_cientifico = trim($_POST['nombre_cientifico'] ?? '');
$familia = trim($_POST['familia'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$usos = trim($_POST['usos'] ?? '');

if (empty($nombre_comun) || empty($nombre_cientifico)) {
    echo json_encode(['error' => 'Nombre común y científico son obligatorios']);
    exit;
}

// Subir imagen si se envió
$imagen_path = 'uploads/default.jpg';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombre_archivo = time() . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $nombre_archivo)) {
        $imagen_path = 'uploads/' . $nombre_archivo;
    } else {
        echo json_encode(['error' => 'Error al guardar la imagen']);
        exit;
    }
}

$stmt = $conn->prepare("INSERT INTO plantas (nombre_comun, nombre_cientifico, familia, descripcion, usos, imagen_path) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $nombre_comun, $nombre_cientifico, $familia, $descripcion, $usos, $imagen_path);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['error' => 'Error al guardar: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
?>