<?php
# @author Name: Jamel Boumazouna ,Student Number:x21122768

//not starting a new session but the page needs access to it
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}

// Connect to database
require_once 'includes/db.php';

$userId = $_SESSION['user_id'];
// Variable to store error msg
$msg = '';

// Handle any new task submission from the user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addTask'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (!empty($title)) {
        //prepared statements userd to limit possible injection sites
        $stmt = $pdo->prepare("INSERT INTO list (user_id, title, description) VALUES (:user_id, :title, :description)");
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description
        ]);
        //after adding reload page so no duplication or other potential explotiable error
        header("Location: Home.php");
        exit();

        //error message if its empty field
    } else {
        $msg = "Task title is required.";
    }
}

// Handle task deletion
if (isset($_GET['delete'])) {
    $task_id = (int) $_GET['delete'];
    //prepared statements usesed to limit possible injection sites
    $stmt = $pdo->prepare("DELETE FROM list WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        'id' => $task_id,
        'user_id' => $userId
    ]);
    //after deleting reload page so no potential errors happen
    header("Location: Home.php");
    exit();
}

// Handle task update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task'])) {
    $task_id = (int) $_POST['task_id'];
    $updated_title = trim($_POST['title']);
    $updated_description = trim($_POST['description']);

    if (!empty($updated_title)) {
        $stmt = $pdo->prepare("UPDATE list SET Title = :title, Description = :description WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            'title' => $updated_title,
            'description' => $updated_description,
            'id' => $task_id,
            'user_id' => $userId
        ]);
        //after updating reload page so no duplication happens
        header("Location: Home.php");
        exit();
    } else {
        $msg = "Updated title cannot be empty.";
    }
}

// Fetch user's tasks from the database,using prepared statements for less potential inject
//prepared statements usesed to limit possible injection sites
$stmt = $pdo->prepare("SELECT * FROM list WHERE user_id = :user_id");
$stmt->execute(['user_id' => $userId]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your To-Do List</title>
    <!-- Link to external CSS stylesheet -->
    <link rel="stylesheet" href="Style.css">
</head>
<body>

<div class="login-box">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> </h2>

    <!-- Add Task Form -->
    <form method="post">
        <!-- Display error message if  -->
        <?php if (!empty($msg)) echo "<p class='error'>$msg</p>"; ?>
        <label for="title">Task Title:</label>
        <input type="text" name="title" id="title" placeholder="e.g. Submit project" required>

        <label for="description">Description:</label>
        <input type="text" name="description" id="description" placeholder="(optional)">

        <input type="submit" value="Add Task" name="addTask">
    </form>

    <!-- Task List -->
    <h3>Your Tasks:</h3>
    <ul>
    <?php if ($tasks): ?>
        <?php foreach ($tasks as $task): ?>
            <li>
                <?php if (isset($_GET['edit']) && $_GET['edit'] == $task['id']): ?>
                    <!-- Inline Edit Form -->
                    <form method="post">
                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                        <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                        <input type="text" name="description" value="<?php echo htmlspecialchars($task['description']); ?>">
                        <button type="submit" name="edit_task">Save</button>
                        <a href="Home.php">Cancel</a>
                    </form>
                <?php else: ?>
                    <!-- Task Display -->
                    <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                    <?php if (!empty($task['description'])): ?>
                        - <?php echo htmlspecialchars($task['description']); ?>
                    <?php endif; ?>
                    <!-- Action Buttons -->
                    <a href="Home.php?edit=<?php echo $task['id']; ?>">Edit</a>
                    <a href="Home.php?delete=<?php echo $task['id']; ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>No tasks yet. Add one above!</li>
    <?php endif; ?>
</ul>

    <!-- Logout Button -->
    <form method="post" action="Logout.php">
        <input type="submit" value="Logout">
    </form>
</div>

</body>
</html>