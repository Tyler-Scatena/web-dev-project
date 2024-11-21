<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Calorie Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><span class="highlight">Calorie</span> Tracker</h1>
            </div>
            <nav>
                <ul>
                    <li class="current"><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="add_food.php">Add Food</a></li>
                    <li><a href="search_food.php">Search Food</a></li>
                    <li><a href="logout.php">Logout</a></li>
                    <!-- Add this to the navigation menu in dashboard.php -->
                    <li><a href="calendar.php">Calendar</a></li>

                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
        <div class="form-container">
            <h3>Your Recent Food Entries</h3>
            <?php
            $user_id = $_SESSION["id"];
            $sql = "SELECT * FROM food_entries WHERE user_id = ? ORDER BY entry_date DESC LIMIT 5";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                if(mysqli_stmt_execute($stmt)){
                    $result = mysqli_stmt_get_result($stmt);
                    if(mysqli_num_rows($result) > 0){
                        echo "<table>";
                        echo "<tr><th>Date</th><th>Food</th><th>Calories</th></tr>";
                        while($row = mysqli_fetch_array($result)){
                            echo "<tr>";
                            echo "<td>" . $row['entry_date'] . "</td>";
                            echo "<td>" . $row['food_name'] . "</td>";
                            echo "<td>" . $row['calories'] . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "No food entries found.";
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
            ?>
        </div>
    </div>
</body>
</html>
