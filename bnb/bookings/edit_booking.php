<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit booking</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd'
            });
            $(function() {
                checkin_date = $("#checkin_date").datepicker();
                checkout_date = $("#checkout_date").datepicker();

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
<?php
include "config.php";
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

if (mysqli_connect_errno()) {
    echo "Error:Unable to connect to MySQL." . mysqli_connect_error();
    exit;
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

//on submit check if empty or not string and is submitted by POST
if(isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] =='Update')){
    
    $roomID = cleanInput($_POST['roomname']);
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $contact_number = cleanInput($_POST['contact_number']);
    $booking_extras = cleanInput($_POST['booking_extras']);
    $booking_review = cleanInput($_POST['booking_review']);
    $id= cleanInput($_POST['id']);

    $upd = "UPDATE booking SET roomID=?,checkin_date=?,checkout_date=?,contact_number=?,booking_extras=?,booking_review=?
    WHERE bookingID=?";
    $stmt = mysqli_prepare($DBC,$upd); //prepare the query
    mysqli_stmt_bind_param($stmt,'isssssi', $roomID, $checkin_date, $checkout_date, $contact_number, $booking_extras, $booking_review, $id); 
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);    
    echo "<h2>Booking updated successfully.</h2>";  
}

$query ="SELECT booking.bookingID, room.roomID, room.roomname,
booking.checkin_date, booking.checkout_date, booking.contact_number, booking.booking_extras, booking.booking_review
FROM booking
INNER JOIN room ON booking.roomID = room.roomID WHERE booking.bookingID=" .$id;

$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

?>

    <body>
        <h1>Update ticket</h1>
        <h2>
            <a href="listbookings.php">[Return to the tickets listing]</a>
            <a href="/bnb/">[Return to main page]</a>
        </h2>
        <div>
            <form action="edit_booking.php" method="POST">
                <p>
                    <label for="roomname">Rooms:</label>
                    <select name="roomname" id="roomname">
                        <?php
                            if (isset($roomsResult)) {
                                while ($room = mysqli_fetch_assoc($roomsResult)) {
                                    $selected = $room['roomID'] == $row['roomID'] ? 'selected' : '';
                                    echo "<option value='{$room['roomID']}' $selected>{$room['roomname']}</option>";
                                }
                            }
                            if($rowcount > 0){
                                $row = mysqli_fetch_assoc($result);
                                
                            ?>
                            <option value="<?php echo $row['roomID']; ?>">
                            <?php
                            echo $row['roomname'] . " "
                                ?>

                            </option>
                        <?php
                            }else echo "<option>No Rooms founds</option>";
                        ?>
                    

                    </select>
                </p>

                <p>
                    <input type="hidden" name="id" value="<?php echo $id;?>" >
                </p>

                <p>
                    <label for="checkin_date">Check-in Date:</label>
                    <input type="text" id="checkin_date" name="checkin_date" required 
                    value="<?php echo $row['checkin_date'];?>" >
                </p>
                <p>
                    <label for="checkout_date">Check-out Date</label>
                    <input type="text" id="checkout_date" name="checkout_date" required
                    value="<?php echo $row['checkout_date'];?>" >
                </p>
                <p>
                    <label for="contact_number">Contact Number:</label>
                    <input type="text" id="contact_number" name="contact_number"
                    value="<?php echo $row['contact_number'];?>" >
                </p>
                <p>
                    <label for="booking_extras">Booking Extras:</label>
                    <input type="text" id="booking_extras" name="booking_extras"
                    value="<?php echo $row['booking_extras'];?>" >
                </p>
                <p>
                    <label for="booking_review">Booking Reviews:</label>
                    <input type="text" id="booking_review" name="booking_review"
                    value="<?php echo $row['booking_review'];?>" >
                </p>
                <input type="submit" name="submit" value="Update">
            </form>
        </div>

        <?php
        mysqli_free_result($result);
        mysqli_close($DBC);
        ?>
    </body>

</html>