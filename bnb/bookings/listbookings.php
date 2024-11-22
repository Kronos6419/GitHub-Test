<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Bookings</title>
</head>

<body>
    <?php
    include "checksession.php";
    include "config.php";
    loginStatus(); 
    checkUser();


    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error:Unable to connect to MySql." . mysqli_connect_error();
        exit; //stop processing the page further.
    }

    //prepare a query and send it to the server

    $query = 'SELECT booking.bookingID, room.roomname, booking.checkin_date, booking.checkout_date, customer.firstname, customer.lastname  
    FROM room, booking, customer 
    WHERE booking.roomID = room.roomID and booking.customerID = customer.customerID
    ORDER BY bookingID';
    
    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);
    ?>

    <h1>Bookings Listing</h1>
    <h2>
        <?php  if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']  ==1){?>

        <a href="makeabooking.php">[Make A Booking]</a>           
        <?php } ?>
            <a href="/bnb/">[Return to main page]</a>
    </h2>

    <table border="1">
        <thead>
            <tr>
            <th>Booking (room, dates)</th>
                <th>Customer</th>
                <th>Action</th>
            </tr>
        </thead>

        <!-- .PHP_EOL can be "\n"
    represents the endline character for the current system -->
        <?php
        if ($rowcount > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['bookingID'];
                
                echo '<tr><td>' . $row['roomname'] .', '. $row['checkin_date'] .', '. $row['checkout_date'] . '</td>';
                echo '<td>' . $row['firstname'] .', '. $row['lastname'] . '</td>';

                echo '<td><a href="booking_details.php?id='.$id.'">[View Booking]</a>';
                echo '<a href="edit_booking.php?id='.$id.'">[Edit Booking ]</a>';
                echo '<a href="edit_review.php?id='.$id.'">[Edit Review]</a>';
                echo '<a href="booking_del.php?id='.$id.'">[Delete Booking]</a></td>';

                // if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] ==1){
                // }

                echo '</tr>' . PHP_EOL;
            }
        } else echo "<h2>No Rooms found!</h2>";
        echo "</table>";
        
        mysqli_free_result($result); //free any memory used by the query
        mysqli_close($DBC);
        ?>

</body>
</html>