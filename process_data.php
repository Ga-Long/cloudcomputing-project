<?php
include "../inc/dbinfo.inc";

// Connect to MySQL and select the database
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$database = mysqli_select_db($connection, DB_DATABASE);

// Ensure that the WeeksContent table exists
VerifyWeeksContentTable($connection, DB_DATABASE);

// If form is submitted, add a row to the WeeksContent table
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $week = htmlentities($_POST['week']);
    $content = htmlentities($_POST['content']);

    if (strlen($week) && strlen($content)) {
        AddWeekContent($connection, $week, $content);
    }
}

// Display table data
$result = mysqli_query($connection, "SELECT * FROM WeeksContent");

echo "<h2>Week Contents</h2>";
echo "<table border='1' cellpadding='2' cellspacing='2'>";
echo "<tr><td>Week</td><td>Content</td></tr>";

while ($query_data = mysqli_fetch_row($result)) {
    echo "<tr><td>", $query_data[0], "</td><td>", $query_data[1], "</td></tr>";
}

echo "</table>";

// Clean up
mysqli_free_result($result);
mysqli_close($connection);

// Add a week and content to the table
function AddWeekContent($connection, $week, $content) {
    $w = mysqli_real_escape_string($connection, $week);
    $c = mysqli_real_escape_string($connection, $content);

    $query = "INSERT INTO `WeeksContent` (`Week`, `Content`) VALUES ('$w', '$c');";

    if (!mysqli_query($connection, $query)) {
        echo("<p>Error adding week content data.</p>");
    }
}

// Check whether the table exists and, if not, create it
function VerifyWeeksContentTable($connection, $dbName) {
    if (!TableExists("WeeksContent", $connection, $dbName)) {
        $query = "CREATE TABLE `WeeksContent` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `Week` varchar(45) DEFAULT NULL,
            `Content` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`ID`),
            UNIQUE KEY `ID_UNIQUE` (`ID`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";

        if (!mysqli_query($connection, $query)) {
            echo("<p>Error creating table.</p>");
        }
    }
}

// Check for the existence of a table
function TableExists($tableName, $connection, $dbName) {
    $t = mysqli_real_escape_string($connection, $tableName);
    $d = mysqli_real_escape_string($connection, $dbName);

    $checktable = mysqli_query($connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

    if (mysqli_num_rows($checktable) > 0) {
        return true;
    }

    return false;
}
?>
