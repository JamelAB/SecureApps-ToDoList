<?php
# @author Name: Jamel Boumazouna ,Student Number:x21122768

//not starting a new session but the page needs access to it
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}

// connect to database but insecurely as its a new connection each time instead of using the connection in the includes folder
$pdo = new PDO('sqlite:includes/../database/database.sqlite3');

$userId = $_SESSION['user_id'];
// Variable to store error msg
$msg = '';
// just for reflected
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}


// Handle any new task submission from the user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addTask'])) {
    // remove the trim so the inputs are not sanitisated
    $title = $_POST['title'];
    $description = $_POST['description'];

    if (!empty($title)) {
        //direct sql no prepared statement
        $sql = "INSERT INTO list (user_id, title, description) VALUES ($userId, '$title', '$description')";
        $pdo->exec($sql);

        //after adding reload page so no duplication or other potential explotiable error
        header("Location: Home.php");
        exit();

        //error message if its empty field
    } else {
        $msg = "Task title is required";
    }
}

// Handle task deletion
if (isset($_GET['delete'])) {
    $task_id = (int) $_GET['delete'];

    //same again no prepared statements
    $sql = "DELETE FROM list WHERE id = $task_id AND user_id = $userId";
    $pdo->exec($sql);
    //after deleting reload page so no potential errors happen
    //header("Location: Home.php");
    //exit();
}

// Handle task update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task'])) {
    $task_id = (int) $_POST['task_id'];
    //removed the trim again
    $updated_title = $_POST['title'];
    $updated_description = $_POST['description'];

    if (!empty($updated_title)) {
        //removed prepared statements again
        $sql = "UPDATE list SET title = '$updated_title', description = '$updated_description' WHERE id = $task_id AND user_id = $userId";
        $pdo->exec($sql);

        //after updating reload page so no duplication happens
        //header("Location: Home.php");
        //exit();
    } else {
        $msg = "Updated title cannot be empty";
    }
}

// Fetch user's tasks from the database
//no prepared statements direct sql to database,big no no
$sql = "SELECT * FROM list WHERE user_id = $userId";
$result = $pdo->query($sql);
$tasks = $result->fetchAll(PDO::FETCH_ASSOC);
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
                     //removing all the specialchars below to allow xss
                    <form method="post">
                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                        <input type="text" name="title" value="<?php echo $task['title']; ?>" required>
                        <input type="text" name="description" value="<?php echo $task['description']; ?>">
                        <button type="submit" name="edit_task">Save</button>
                        <a href="Home.php">Cancel</a>
                    </form>
                <?php else: ?>
                    <!-- Task Display -->
                    <strong><?php echo $task['title']; ?></strong>
                    <?php if (!empty($task['description'])): ?>
                        - <?php echo $task['description']; ?>
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