<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Booking</title>
</head>

<body>
    <?php
    include "config.php";
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error:Unable to connect to MySql." . mysqli_connect_error();
        exit; //stop processing the page further.
    }

    function cleanInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    //check if id exists
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
            echo "<h2>Invalid Booking ID</h2>"; //simple error feedback
            exit;
        } 
    }

    //delete booking
    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
        $error = 0;
        $msg = "Error:";

        //we try to convert to number - intval function(return to the integer of a variable)
        if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
        
            //code here
            $id = cleanInput($_POST['id']); 

        } else {
            //code here

            $error++; //bump the error flag
            $msg .= 'Invalid Booking ID'; //append error message
            $id = 0;  

        }

        if ($error == 0 and $id > 0) {
           //code here
            $query = "DELETE FROM booking WHERE bookingID=?"; 
            $stmt = mysqli_prepare($DBC, $query); // Prepare the query
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Your Booking is now deleted</h2>";  
        
        } else {
            echo "<h5>$msg</h5>" . PHP_EOL;
        }
    }

    $query = "SELECT 
    booking.bookingID, room.roomname, customer.firstname, customer.lastname, booking.checkin_date, booking.checkout_date, booking.booking_extras, booking.booking_review 
    FROM booking 
    INNER JOIN room ON booking.roomID = room.roomID 
    INNER JOIN customer ON booking.customerID = customer.customerID 
    WHERE booking.bookingID=" . $id;

    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);
    ?>

    <!-- We can add a menu bar here to go back -->
    <h1>Booking preview before deletion</h1>
    <h2>
        <a href="listbookings.php">[Return to the Bookings Listing]</a>
        <a href="/bnb/">[Return to the Main Page]</a>
    </h2>
    <?php
    if ($rowcount > 0) {
        
        echo "<fieldset><legend>Booking Details #$id</legend><dl>";
        $row = mysqli_fetch_assoc($result);
        $id = $row['bookingID'];

        echo "<dt>Room Name: </dt><dd>" . $row['roomname'] . "</dd>" . PHP_EOL;
        echo "<dt>Customer Name: </dt><dd>" . $row['firstname'] . " " . $row['lastname'] . "</dd>" . PHP_EOL;
        echo "<dt>Check-In Date: </dt><dd>" . $row['checkin_date'] . "</dd>" . PHP_EOL;
        echo "<dt>Check-Out Date: </dt><dd>" . $row['checkout_date'] . "</dd>" . PHP_EOL;
        echo "<dt>Extras: </dt><dd>" . $row['booking_extras'] . "</dd>" . PHP_EOL;
        echo "<dt>Review: </dt><dd>" . $row['booking_review'] . "</dd>" . PHP_EOL;
        echo '</dl></fieldset>' . PHP_EOL;

    ?>

        <form method="POST" action="deletebooking.php">
            <h3>Are you sure you want to delete this booking?</h3>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="submit" name="submit" value="Delete">
            <a href="listbookings.php">Cancel</a>
        </form>

    <?php
    } else echo "<h5>No Booking found! Possibly already deleted!</h5>";
    mysqli_free_result($result);
    mysqli_close($DBC);
    ?>
</body>

</html>