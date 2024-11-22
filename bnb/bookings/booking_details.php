<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Booking Details</title>
</head>
    <body>
        <?php
        include "config.php";

        $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

        if (mysqli_connect_errno()) {
            echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
            // Stop processing the page further
            exit;
        }

        // Check if ID exists
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'];
            if (empty($id) or !is_numeric($id)) {
                echo "<h2>Invalid Booking ID</h2>";
                exit;
            }
        }

        $query = "SELECT bookingID, room.roomname, 
        booking.checkin_date, booking.checkout_date, booking.contact_number, booking.booking_extras, booking.booking_review
                FROM booking
                INNER JOIN room ON booking.roomID = room.roomID WHERE bookingID=" . $id;
        $result = mysqli_query($DBC, $query);
        $rowcount = mysqli_num_rows($result);
        ?>

        <!-- We can add a menu bar here to go back -->
        <h1>Booking Details View</h1>
        <h2>
            <a href="listbookings.php">Return to the ticket listing</a>
            <a href="/bnb/">Return to the main page</a>
        </h2> 
    
        <?php
        
        if ($rowcount > 0) {
            echo "<fieldset><legend>Booking Detail #$id</legend><br/>";
            $row = mysqli_fetch_assoc($result);

            // Replace empty values with "Nothing"
            $bookingExtras = !empty($row['booking_extras']) ? $row['booking_extras'] : "Nothing";
            $bookingReview = !empty($row['booking_review']) ? $row['booking_review'] : "Nothing";

            echo "<dt>Room name: </dt><dd>" . $row['roomname'] . "</dd><br>" . PHP_EOL;
            echo "<dt>Check-In Date: </dt><dd>" . $row['checkin_date'] . "</dd><br>" . PHP_EOL;
            echo "<dt>Check-Out date: </dt><dd>" . $row['checkout_date'] . "</dd><br>" . PHP_EOL;
            echo "<dt>Contact Number: </dt><dd>" . $row['contact_number'] . "</dd><br>" . PHP_EOL;
            echo "<dt>Extras: </dt><dd>" . $bookingExtras . "</dd><br>" . PHP_EOL;
            echo "<dt>Room Review: </dt><dd>" . $bookingReview . "</dd><br>" . PHP_EOL;
            echo "</fieldset>" . PHP_EOL;
        } else {
            echo "<h2>No booking found! Possibly deleted!</h2>";
        }
        mysqli_free_result($result);
        mysqli_close($DBC);
        ?>
    </body>
</html>
