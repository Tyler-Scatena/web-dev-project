<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
include 'config.php';

$api_key = "DbaHIXcYrd1dKhPFvUvAbaqmjZI63lL6QCHLgVYn"; // Replace with your actual USDA FoodData Central API key
$search_results = [];
$error_message = "";
$success_message = "";

function searchUSDAFood($query, $api_key) {
    $url = "https://api.nal.usda.gov/fdc/v1/foods/search?api_key=" . $api_key . "&query=" . urlencode($query);
    $response = file_get_contents($url);
    return json_decode($response, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['usda_search'])) {
        $search_query = $_POST['usda_query'];
        $search_results = searchUSDAFood($search_query, $api_key);
    } elseif (isset($_POST['add_food'])) {
        $food_name = mysqli_real_escape_string($conn, $_POST['food_name']);
        $calories = mysqli_real_escape_string($conn, $_POST['calories']);
        $entry_date = date('Y-m-d');
        $user_id = $_SESSION["id"];

        $sql = "INSERT INTO food_entries (user_id, food_name, calories, entry_date) VALUES (?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "isis", $user_id, $food_name, $calories, $entry_date);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Food entry added successfully.";
            } else {
                $error_message = "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Food - Calorie Tracker</title>
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
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li class="current"><a href="add_food.php">Add Food</a></li>
                    <li><a href="search_food.php">Search Food</a></li>
                    <li><a href="calendar.php">Calendar</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Add Food Entry</h2>
        <div class="form-container">
            <?php 
            if(!empty($success_message)){
                echo '<div class="alert alert-success">' . $success_message . '</div>';
            }
            if(!empty($error_message)){
                echo '<div class="alert alert-danger">' . $error_message . '</div>';
            }
            ?>
            <h3>Search USDA FoodData Central</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div>
                    <label>Search for food</label>
                    <input type="text" name="usda_query" required>
                </div>
                <div>
                    <input type="submit" name="usda_search" class="btn" value="Search USDA">
                </div>
            </form>

            <?php if (!empty($search_results) && isset($search_results['foods'])): ?>
                <h3>Search Results</h3>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <select name="food_name">
                        <?php foreach ($search_results['foods'] as $food): ?>
                            <option value="<?php echo htmlspecialchars($food['description']); ?>" data-calories="<?php echo htmlspecialchars($food['foodNutrients'][3]['value']); ?>">
                                <?php echo htmlspecialchars($food['description']); ?> (<?php echo htmlspecialchars($food['foodNutrients'][3]['value']); ?> kcal)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="calories" id="selected_calories">
                    <input type="submit" name="add_food" class="btn" value="Add Selected Food">
                </form>
            <?php endif; ?>

            <h3>Manual Entry</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div>
                    <label>Food Name</label>
                    <input type="text" name="food_name" required>
                </div>
                <div>
                    <label>Calories</label>
                    <input type="number" name="calories" required>
                </div>
                <div>
                    <input type="submit" name="add_food" class="btn" value="Add Food">
                </div>
            </form>
        </div>
    </div>

    <script>
    document.querySelector('select[name="food_name"]').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        document.getElementById('selected_calories').value = selectedOption.dataset.calories;
    });
    </script>
</body>
</html>
