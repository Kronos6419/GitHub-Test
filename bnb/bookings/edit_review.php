<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit/Add Room Review</title>
</head>
<body>
    <?php
    include "config.php";

    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
        exit;
    }

    function cleanInput($data) {
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

    if (isset($_POST['submit']) && !empty($_POST['submit']) && ($_POST['submit'] == 'Update')) {
        $review = cleanInput($_POST['review']);
        $id = cleanInput($_POST['id']);
        $query = "UPDATE booking SET booking_review=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC, $query);
        mysqli_stmt_bind_param($stmt, 'si', $review, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h5>Review updated successfully!</h5>";
    }

    $query = "SELECT booking.bookingID, booking.booking_review, customer.firstname, customer.lastname
            FROM booking 
            INNER JOIN customer ON booking.customerID = customer.customerID
            WHERE bookingID=" . $id;
    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);
    ?>

    <h1>Edit/Add Room Review</h1>
    <h2>
        <a href="listbookings.php">[Return to the booking listing]</a>
        <a href="/bnb/">[Return to the main page]</a>
    </h2>
    <div>
        <?php
        if ($rowcount > 0) {
            $row = mysqli_fetch_assoc($result);
            $review = !empty($row['booking_review']) ? $row['booking_review'] : "Nothing";
            ?>
            <p>Review made by <?php echo $row['firstname'] . ' ' . $row['lastname']; ?></p>

            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <label for="review">Room review:</label>
                <textarea id="review" name="review" rows="4" cols="50"><?php echo $review; ?></textarea>
                <br><br>
                <input type="submit" name="submit" value="Update">
            </form>
            <?php
        } else {
            echo "<h5>No Rooms found!</h5>";
        }
        mysqli_free_result($result);
        mysqli_close($DBC);
        ?>
    </div>
</body>
</html>
