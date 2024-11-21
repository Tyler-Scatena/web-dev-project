<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include 'config.php';

function getMonthCalories($user_id, $year, $month) {
    global $conn;
    $sql = "SELECT DAY(entry_date) as day, SUM(calories) as total_calories 
            FROM food_entries 
            WHERE user_id = ? AND YEAR(entry_date) = ? AND MONTH(entry_date) = ?
            GROUP BY DAY(entry_date)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $user_id, $year, $month);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $calories = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $calories[$row['day']] = $row['total_calories'];
    }
    return $calories;
}

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$calories = getMonthCalories($_SESSION['id'], $year, $month);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calorie Tracker Calendar</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .today {
            background-color: #e6f3ff;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><span class="highlight">Calorie</span> Tracker</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="add_food.php">Add Food</a></li>
                    <li class="current"><a href="calendar.php">Calendar</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Calorie Tracker Calendar</h2>
        <div class="form-container">
            <?php
            $monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
            echo "<h3>$monthName $year</h3>";
            
            $daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
            $firstDay = date('N', mktime(0, 0, 0, $month, 1, $year));
            
            echo "<table>";
            echo "<tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr>";
            
            $dayCount = 1;
            echo "<tr>";
            for ($i = 1; $i < $firstDay; $i++) {
                echo "<td></td>";
            }
            for ($i = $firstDay; $i <= 7; $i++) {
                $class = ($dayCount == date('j') && $month == date('n') && $year == date('Y')) ? 'today' : '';
                $caloriesForDay = isset($calories[$dayCount]) ? $calories[$dayCount] : 0;
                echo "<td class='$class'>$dayCount<br>$caloriesForDay cal</td>";
                $dayCount++;
            }
            echo "</tr>";
            
            while ($dayCount <= $daysInMonth) {
                echo "<tr>";
                for ($i = 1; $i <= 7 && $dayCount <= $daysInMonth; $i++) {
                    $class = ($dayCount == date('j') && $month == date('n') && $year == date('Y')) ? 'today' : '';
                    $caloriesForDay = isset($calories[$dayCount]) ? $calories[$dayCount] : 0;
                    echo "<td class='$class'>$dayCount<br>$caloriesForDay cal</td>";
                    $dayCount++;
                }
                echo "</tr>";
            }
            echo "</table>";
            ?>
        </div>
    </div>
</body>
</html>
