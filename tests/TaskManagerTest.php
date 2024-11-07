<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/TaskManager.php';
use PDO;

class TaskManagerTest extends TestCase
{
    protected $pdo;
    protected $taskManager;

    // Set up a mock PDO object and TaskManager instance
    protected function setUp(): void
    {
        $this->pdo = new PDO('mysql:host=sql12.freesqldatabase.com;dbname=sql12743168', 'sql12743168', 'TnRn5yDYvX');
        $this->taskManager = new TaskManager($this->pdo);
    }

    // Test adding a task
    public function testAddTask()
    {
        $task = "Test Task";

        // Add task and assert it was successfully added
        $result = $this->taskManager->addTask($task);

        $this->assertTrue($result, "Task was not added successfully.");
    }

    // Test deleting a task
    public function testDeleteTask()
    {
        // Assuming task ID 1 exists in the database
        $taskId = 1;

        // Delete task and assert it was successfully deleted
        $result = $this->taskManager->deleteTask($taskId);

        $this->assertTrue($result, "Task was not deleted successfully.");
    }

    // Test updating task status to "completed"
    public function testUpdateTaskStatus()
    {
        // Assuming task ID 1 exists in the database
        $taskId = 1;
        $status = 'completed';

        // Update task status and assert it was updated
        $result = $this->taskManager->updateTaskStatus($taskId, $status);

        $this->assertTrue($result, "Task status was not updated successfully.");
    }

    // Test filtering tasks by status (e.g., 'pending' or 'completed')
    public function testFilterTasksByStatus()
    {
        // Assuming we have a method to get tasks filtered by status
        $status = 'pending';

        $tasks = $this->taskManager->getTasksByStatus($status);

        // Check if all tasks are 'pending'
        foreach ($tasks as $task) {
            $this->assertEquals($status, $task['status'], "Task status should be '$status'.");
        }
    }

    // Clean up after each test
    protected function tearDown(): void
    {
        // Close the database connection
        $this->pdo = null;
    }
}
?>

