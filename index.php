<?php
include 'db.php';

// Add a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    // Input validation
    $task = trim($_POST['task']);
    if (empty($task) || strlen($task) > 255) {
        echo "Task cannot be empty and must be less than 255 characters.";
        exit;
    }

    // Prepare and insert task
    $stmt = $pdo->prepare("INSERT INTO tasks (task, status) VALUES (:task, 'pending')");
    $stmt->execute(['task' => $task]);
    header("Location: index.php");
    exit;
}

// Update task status based on AJAX request
if (isset($_POST['task_id']) && isset($_POST['status'])) {
    $taskId = filter_var($_POST['task_id'], FILTER_VALIDATE_INT);
    if ($taskId === false) {
        echo "Invalid task ID.";
        exit;
    }

    $newStatus = $_POST['status'] === 'true' ? 'completed' : 'pending';
    $stmt = $pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $newStatus, 'id' => $taskId]);
    exit;
}

// Handle task deletion
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
    if ($id === false) {
        echo "Invalid task ID.";
        exit;
    }

    // Delete task
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: index.php");
    exit;
}

// Handle filtering with pagination
$filter = $_GET['filter'] ?? 'all';
$limit = 10; // Limit the number of tasks shown per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM tasks";
if ($filter === 'pending') {
    $query .= " WHERE status = 'pending'";
} elseif ($filter === 'completed') {
    $query .= " WHERE status = 'completed'";
}
$query .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total count of tasks for pagination
$totalTasksQuery = "SELECT COUNT(*) FROM tasks";
$totalStmt = $pdo->prepare($totalTasksQuery);
$totalStmt->execute();
$totalTasks = $totalStmt->fetchColumn();
$totalPages = ceil($totalTasks / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>

        <!-- Add Task Form -->
        <form method="POST" action="index.php">
            <input type="text" name="task" placeholder="Enter new task" required>
            <button type="submit">Add Task</button>
        </form>

        <!-- Filter Options -->
        <div class="filters">
            <a href="index.php?filter=all" class="<?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
            <a href="index.php?filter=pending" class="<?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending</a>
            <a href="index.php?filter=completed" class="<?php echo $filter === 'completed' ? 'active' : ''; ?>">Completed</a>
        </div>

        <!-- Display Tasks -->
        <ul class="task-list">
            <?php foreach ($tasks as $task): ?>
                <li class="<?php echo htmlspecialchars($task['status']); ?>">
                    <input type="checkbox" 
                           class="status-checkbox" 
                           data-task-id="<?php echo htmlspecialchars($task['id']); ?>" 
                           <?php echo $task['status'] === 'completed' ? 'checked' : ''; ?>>
                    <span class="task-text <?php echo htmlspecialchars($task['status']); ?>">
                        <?php echo htmlspecialchars($task['task']); ?>
                    </span>
                    <!-- Delete button -->
                    <a href="index.php?delete=<?php echo htmlspecialchars($task['id']); ?>" class="delete-btn">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="index.php?filter=<?php echo $filter; ?>&page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <script>
        // JavaScript to handle checkbox status update
        document.querySelectorAll('.status-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskId = this.getAttribute('data-task-id');
                const status = this.checked ? 'true' : 'false'; // true for completed, false for pending

                // Send AJAX request to update task status
                fetch('index.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `task_id=${taskId}&status=${status}`
                }).then(response => response.text())
                  .then(data => {
                      console.log('Status updated:', data);
                  })
                  .catch(error => console.error('Error updating status:', error));
            });
        });
    </script>
</body>
</html>
