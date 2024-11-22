<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a ticket</title>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

    <script>
        //insert datepicker jQuery

        $(document).ready(function() {
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd'
            });
            $(function() {
                checkin_date = $("#checkin_date").datepicker()
                checkout_date = $("#checkout_date").datepicker()

                function getDate(element) {
                    var date;
                    try {
                        date = $.datepicker.parseDate(dateFormat, element.value);
                    } catch (error) {
                        date = null;
                    }
                    return date;
                }
            });
        });
        
    </script>
</head>
<body>


<?php
include "checksession.php";
checkUser();
loginStatus(); 
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);



if (mysqli_connect_errno()) {
echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; //stop processing the page further
}


//function to clean input but not validate type and content
function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}


//on submit check if empty or not string and is submitted by POST
if (isset($_POST['submit']) && !empty($_POST['submit']) && ($_POST['submit'] == 'Book')) {
    $room = cleanInput($_POST['rooms']); // Room ID
    $customer = cleanInput($_POST['customers']); // Customer ID
    $checkin = $_POST['checkin_date']; // Check-in date
    $checkout = $_POST['checkout_date']; // Check-out date
    $extras = cleanInput($_POST['booking_extras']); // Booking extras
    $contact_number = cleanInput($_POST['contact_number']); // Customer Contact Number

    $error = 0; // Initialize error count
    $msg = "Error:";

    $checkinDate = new DateTime($checkin);
    $checkoutDate = new DateTime($checkout);

    if ($checkinDate >= $checkoutDate) {
        $error++;
        $msg .= " Check-out Date must be after the Check-In Date.";
        $checkout = ''; // Reset invalid value
    }

    if ($error == 0) {
        $query = "INSERT INTO booking (roomID, customerID, checkin_date, checkout_date, booking_extras, contact_number) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($DBC, $query);
        mysqli_stmt_bind_param($stmt, 'iissss', $room, $customer, $checkin, $checkout, $extras, $contact_number);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo "<h5>Booking submitted successfully.</h5>";
    } else {
        echo "<h5>$msg</h5>" . PHP_EOL;
    }
}

$query = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomID';
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

$query1 = 'SELECT customerID, firstname, lastname FROM customer ORDER BY customerID';
$result1 = mysqli_query($DBC, $query1);
$rowcount1 = mysqli_num_rows($result1);
?>

<h1>Make a Booking</h1>
    <h2>
        <a href='listbookings.php'>[Return to the Bookings listing]</a>
        <a href="/bnb/">[Return to main page]</a>
    </h2>

    <h3>Booking made for test</h3>
    <div>

    <form method="POST">
        <div>
            <label for="rooms">Room: </label>
            <select name="rooms" id="rooms" required>
                <?php
                if ($rowcount > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['roomID']}'>" .
                            "{$row['roomname']} ({$row['roomtype']} - {$row['beds']} beds)</option>";
                    }

                } else {
                    echo "<option>No Rooms found</option>";
                }

                mysqli_free_result($result);
                ?>
            </select>
        </div>

        <br>
        <div>
            <label for="customers">Customer:</label>
            <select name="customers" id="customers" required>
                <?php
                if ($rowcount1 > 0) {
                    while ($row = mysqli_fetch_assoc($result1)) {
                        echo "<option value='{$row['customerID']}'>" .
                            "{$row['firstname']} {$row['lastname']}</option>";
                    }

                } else {
                    echo "<option>No Customers found</option>";
                }

                mysqli_free_result($result1);
                ?>
            </select>
        </div>

        <br>
        <div>
            <label for="checkin_date">Check-in Date:</label>
            <input type="text" id="checkin_date" name="checkin_date" placeholder="yyyy-mm-dd" required>
        </div>

        <br>
        <div>
            <label for="checkout_date">Check-out Date:</label>
            <input type="text" id="checkout_date" name="checkout_date" placeholder="yyyy-mm-dd" required>
        </div>
        <br>
        <div>
        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number"  required>
        </div>
        <br>
        <div>
            <label for="booking_extras">Booking Extras:</label>
            <input type="text" id="booking_extras" name="booking_extras">
        </div>

        <br>
        <div>
            <input type="submit" name="submit" value="Book">
            <a href='listbookings.php'>[Cancel]</a>
        </div>
    </form>

        <hr>

        <h3>Search for Room Availability</h3>
<div>
    <form id="searchForm" method="get">
        <label for="fromDate">Start Date:</label>
        <input type="text" id="fromDate" name="fromDate" placeholder="yyyy-mm-dd" required>
        <label for="toDate">End Date:</label>
        <input type="text" id="toDate" name="toDate" placeholder="yyyy-mm-dd" required>
        <input type="submit" value="Search">
    </form>
</div>
<br>
<div class="row">
    <table id="tblbookings" border="1">
        <thead>
            <tr>
                <th>Room#</th>
                <th>Room Name</th>
                <th>Room Type</th>
                <th>Beds</th>
            </tr>
        </thead>
        <tbody id="result">
            <!-- Results will be dynamically added here -->
        </tbody>
    </table>
</div>

<script>
$(document).ready(function () {
    // Initialize datepickers
    $("#fromDate, #toDate").datepicker({ dateFormat: "yy-mm-dd" });

    // Handle form submission
    $("#searchForm").submit(function (event) {
        event.preventDefault(); // Prevent the default form submission

        var fromDate = $("#fromDate").val();
        var toDate = $("#toDate").val();

        // Validate dates
        if (new Date(fromDate) >= new Date(toDate)) {
            alert("Start Date must be earlier than End Date.");
            return false;
        }

        // Make AJAX request
        $.ajax({
            url: "bookingsearch.php", // Backend script to handle search
            method: "GET",
            data: { fromDate: fromDate, toDate: toDate },
            success: function (response) {
                $("#result").html(response); // Update the table with results
            },
            error: function (xhr, status, error) {
                console.error("Error fetching available rooms:", error);
            }
        });
    });
});
</script>

</body>
</html>