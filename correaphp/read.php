<?php
require_once 'config.php';


$result = $conn->query("SELECT id, facility_name, sport_type, capacity, created_at FROM facilities ORDER BY id DESC");

if (!$result) {
    die("Could not retrieve facilities from the database.");
}

$facilities = $result->fetch_all(MYSQLI_ASSOC);
$result->free();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facilities</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="read.php">Facility Manager</a>
            <div class="navbar-nav">
                <a class="nav-link active" href="read.php">Facilities</a>
                <a class="nav-link" href="create.php">Add facility</a>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h2 class="mb-0">Registered facilities</h2>
            <a class="btn btn-success" href="create.php">Add facility</a>
        </div>

        <div class="table-responsive bg-white border rounded shadow-sm">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Facility name</th>
                        <th>Sport type</th>
                        <th class="text-end">Capacity</th>
                        <th>Created at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($facilities)): ?>
                        <?php foreach ($facilities as $facility): ?>
                            <tr>
                                <td><?= htmlspecialchars($facility['id']) ?></td>
                                <td><?= htmlspecialchars($facility['facility_name']) ?></td>
                                <td><span class="badge text-bg-warning"><?= htmlspecialchars($facility['sport_type']) ?></span></td>
                                <td class="text-end"><?= htmlspecialchars($facility['capacity']) ?></td>
                                <td><?= htmlspecialchars($facility['created_at']) ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-success" href="update.php?id=<?= $facility['id'] ?>">Edit</a>
                                    <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?= $facility['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No facilities yet. <a href="create.php">Add your first facility</a>.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
