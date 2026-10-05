<?php
require_once 'config.php';

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    die("Invalid or missing Facility ID.");
}


$stmt = $conn->prepare("SELECT facility_name, sport_type, capacity FROM facilities WHERE id = ?");
if (!$stmt) {
    die("Database error occurred.");
}

$stmt->bind_param("i", $id); // "i" signifies integer data type
$stmt->execute();

$result = $stmt->get_result();
$facility = $result->fetch_assoc();
$stmt->close();

if (!$facility) {
    die("Facility record not found.");
}

$message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_name = trim($_POST['facility_name'] ?? '');
    $sport_type = trim($_POST['sport_type'] ?? '');
    $capacity = filter_var($_POST['capacity'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if (!empty($facility_name) && !empty($sport_type) && $capacity !== false) {
        $stmt = $conn->prepare("UPDATE facilities SET facility_name = ?, sport_type = ?, capacity = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param("ssii", $facility_name, $sport_type, $capacity, $id);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: read.php");
                exit;
            } elseif ($stmt->errno == 1062) {
                $message = "Error: A facility with that name already exists.";
            } else {
                $message = "Failed to update record.";
            }
            $stmt->close();
        } else {
            $message = "Failed to update record.";
        }
    } else {
        $message = "Please provide a valid facility name, sport type, and capacity (1 or more).";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit facility</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand navbar-dark bg-success mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="read.php">Facility Manager</a>
            <div class="navbar-nav">
                <a class="nav-link" href="read.php">Facilities</a>
                <a class="nav-link" href="create.php">Add facility</a>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        <h2 class="mb-3">Edit facility</h2>

        <?php if ($message): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" class="card card-body shadow-sm" style="max-width: 520px;">
            <div class="mb-3">
                <label for="facility_name" class="form-label">Facility name</label>
                <input type="text" class="form-control" id="facility_name" name="facility_name" value="<?= htmlspecialchars($facility['facility_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="sport_type" class="form-label">Sport type</label>
                <input type="text" class="form-control" id="sport_type" name="sport_type" value="<?= htmlspecialchars($facility['sport_type']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" value="<?= htmlspecialchars($facility['capacity']) ?>" min="1" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Update facility</button>
                <a class="btn btn-outline-secondary" href="read.php">Cancel</a>
            </div>
        </form>
    </main>
</body>
</html>
