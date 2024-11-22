<?php
include "config.php"; 

$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit;
}

// Retrieve input 
$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];

$fromDate = mysqli_real_escape_string($DBC, $fromDate);
$toDate = mysqli_real_escape_string($DBC, $toDate);

// Query to get available rooms
$query = "
    SELECT roomID, roomname, roomtype, beds 
    FROM room 
    WHERE roomID NOT IN (
        SELECT roomID 
        FROM booking 
        WHERE (checkin_date <='$toDate' AND checkout_date >= '$fromDate')
    )
";
$result = mysqli_query($DBC, $query);

// Check if any rooms are available
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['roomID']}</td>
                <td>{$row['roomname']}</td>
                <td>{$row['roomtype']}</td>
                <td>{$row['beds']}</td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No rooms available for the selected dates.</td></tr>";
}

mysqli_free_result($result);
mysqli_close($DBC);
?>
