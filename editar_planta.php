<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['error' => 'ID inválido']);
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

// Obtener la ruta actual de la imagen
$stmt_img = $conn->prepare("SELECT imagen_path FROM plantas WHERE id = ?");
$stmt_img->bind_param("i", $id);
$stmt_img->execute();
$res_img = $stmt_img->get_result();
$planta = $res_img->fetch_assoc();
$imagen_path = $planta['imagen_path'] ?? 'uploads/default.jpg';
$stmt_img->close();

// Si se sube nueva imagen, eliminar la anterior (si no es la por defecto) y guardar la nueva
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    // Eliminar archivo anterior si no es el default
    if ($imagen_path != 'uploads/default.jpg' && file_exists($imagen_path)) {
        unlink($imagen_path);
    }
    
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombre_archivo = time() . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $nombre_archivo)) {
        $imagen_path = 'uploads/' . $nombre_archivo;
    } else {
        echo json_encode(['error' => 'Error al guardar la nueva imagen']);
        exit;
    }
}

$stmt = $conn->prepare("UPDATE plantas SET nombre_comun=?, nombre_cientifico=?, familia=?, descripcion=?, usos=?, imagen_path=? WHERE id=?");
$stmt->bind_param("ssssssi", $nombre_comun, $nombre_cientifico, $familia, $descripcion, $usos, $imagen_path, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error al actualizar: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
?>