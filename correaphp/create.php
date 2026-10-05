<?php
require_once 'config.php';
 
$message = '';
$messageType = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_name = trim($_POST['facility_name'] ?? '');
    $sport_type = trim($_POST['sport_type'] ?? '');
    $capacity = filter_var($_POST['capacity'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
 
    if (!empty($facility_name) && !empty($sport_type) && $capacity !== false) {
        // Prepared statement using "?" as a structural placeholder
        $stmt = $conn->prepare("INSERT INTO facilities (facility_name, sport_type, capacity) VALUES (?, ?, ?)");
 
        if ($stmt) {
            // Bind variables: "ssi" signifies two strings followed by an integer
            $stmt->bind_param("ssi", $facility_name, $sport_type, $capacity);
 
            if ($stmt->execute()) {
                $message = "Facility added successfully! ID: " . $conn->insert_id;
                $messageType = 'success';
            } elseif ($stmt->errno == 1062) { // MySQL code for Duplicate entry error
                $message = "Error: A facility with that name already exists.";
                $messageType = 'danger';
            } else {
                $message = "An error occurred while saving the facility.";
                $messageType = 'danger';
            }
            $stmt->close();
        } else {
            $message = "An error occurred while saving the facility.";
            $messageType = 'danger';
        }
    } else {
        $message = "Please provide a valid facility name, sport type, and capacity (1 or more).";
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add facility</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="read.php">Facility Manager</a>
            <div class="navbar-nav">
                <a class="nav-link" href="read.php">Facilities</a>
                <a class="nav-link active" href="create.php">Add facility</a>
            </div>
        </div>
    </nav>
 
    <main class="container pb-5">
      <div class="mx-auto" style="max-width: 520px;">
        <h2 class="mb-3 text-center">Add a facility</h2>
 
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>" role="alert"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
 
        <form method="POST" class="card card-body shadow-sm">
            <div class="mb-3">
                <label for="facility_name" class="form-label">Facility name</label>
                <input type="text" class="form-control" id="facility_name" name="facility_name" placeholder="e.g. Main Basketball Court" required>
            </div>
            <div class="mb-3">
                <label for="sport_type" class="form-label">Sport type</label>
                <input type="text" class="form-control" id="sport_type" name="sport_type" placeholder="e.g. Basketball" required>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" placeholder="Number of people" min="1" required>
            </div>
            <div class="d-flex justify-content-center gap-2">
                <button type="submit" class="btn btn-success">Create facility</button>
                <a class="btn btn-outline-secondary" href="read.php">View all</a>
            </div>
        </form>
      </div>
    </main>
</body>
</html>
 