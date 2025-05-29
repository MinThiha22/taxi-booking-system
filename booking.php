<!-- 
  Author: Min Thiha Ko Ko
  ID: 21156028
  network username: sss0276
  Description: This PHP file create Booking table and insert new booking data into the database, generating a unique booking reference number.
-->

<?php
  require_once("../../files/sqlinfo.inc.php");
  $conn = @mysqli_connect($sql_host,$sql_user,$sql_pass,$sql_db);
  if(!$conn){
    echo '
      <p class="text-center bg-red-100 border-2 border-red-400 text-red-700 text-2xl rounded-xl font-bold"> 
        Database Connection Failed! ' . mysqli_connect_error() .
      '</p>';
  } else{
    $name = mysqli_escape_string($conn, $_POST['cname']);
    $phone = mysqli_escape_string($conn, $_POST['phone']);
    $unit_no = mysqli_escape_string($conn, $_POST['unumber']);
    $street_no = mysqli_escape_string($conn, $_POST['snumber']);
    $street_name = mysqli_escape_string($conn, $_POST['stname']);
    $suburb = mysqli_escape_string($conn, $_POST['sbname']);
    $dsuburb = mysqli_escape_string($conn, $_POST['dsbname']);
    $date = mysqli_escape_string($conn, $_POST['date']);
    $time = mysqli_escape_string($conn, $_POST['time']);
    
    // Create BOOKING table if it does not exist
    $create_query = "
      CREATE TABLE IF NOT EXISTS BOOKING (
      booking_ref VARCHAR(8) NOT NULL PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      phone VARCHAR(12) NOT NULL,
      unit_no VARCHAR(10),
      street_no VARCHAR(10) NOT NULL,
      street_name VARCHAR(100) NOT NULL,
      suburb VARCHAR(100),
      dsuburb VARCHAR(100),
      datetime DATETIME NOT NULL,
      status VARCHAR(20) NOT NULL
      )";

    $result = mysqli_query($conn, $create_query);
    if(!$result) {
      echo '
        <p class="text-center bg-red-100 border-2 border-red-400 text-red-700 text-2xl rounded-xl font-bold"> 
          Error creating table: ' . mysqli_error($conn) .
        '</p>';
      exit();
    }

    //  Generate new booking reference
    $ref_query = "SELECT booking_ref FROM BOOKING ORDER BY booking_ref DESC LIMIT 1";
    $ref_result = mysqli_query($conn, $ref_query);
    
    if ($ref_result && mysqli_num_rows($ref_result) > 0) {
      $last_booking = mysqli_fetch_assoc($ref_result)['booking_ref'];
      $last_number = intval(substr($last_booking, 3));
      $new_number = $last_number + 1;
    } else {
      $new_number = 1;
    }

    $booking_ref = "BRN" . str_pad($new_number, 5, "0", STR_PAD_LEFT);
    $datetime_obj = DateTime::createFromFormat('d/m/Y H:i', "$date $time");
    $datetime = $datetime_obj->format('Y-m-d H:i:s');
    $status = "unassigned";

    // Insert new booking into the BOOKING table
    $insert_query = "
      INSERT INTO BOOKING (
        booking_ref, name, phone, unit_no, street_no, street_name, suburb, dsuburb, datetime, status
      ) VALUES ( '$booking_ref', '$name', '$phone', '$unit', '$street_no','$street_name', '$suburb', '$dsuburb', '$datetime', '$status'
    )";
    $insert_result = mysqli_query($conn, $insert_query);
    if(!$insert_result) {
      echo '
        <p class="text-center bg-red-100 border-2 border-red-400 text-red-700 text-2xl rounded-xl font-bold"> 
          Error inserting booking: ' . mysqli_error($conn) .
        '</p>';
      
    } else {
      echo '<p class="font-bold text-2xl font-lilita text-center">Thank you for booking!</p>';
      booking_output("Booking reference number: ", $booking_ref);
      booking_output("Pickup Time: ",$time);
      booking_output("Pickup Date: ",$date);
    }
  }
  function booking_output($label, $value){
    echo '
      <div class="flex justify-between items-center font-inter">
        <p>' . $label . '</p>
        <p class="font-bold">' . $value . '</p> 
      </div>
    ';
  }
  mysqli_close($conn); 
?>
