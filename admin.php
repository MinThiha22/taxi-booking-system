<!-- 
  Author: Min Thiha Ko Ko
  ID: 21156028
  network username: sss0276
  Description: This PHP file manages taxi booking searches and assignments by interacting with database and dynamically generating a results table.
-->
<?php
  require_once("../../files/sqlinfo.inc.php");
  $conn = @mysqli_connect($sql_host,$sql_user,$sql_pass,$sql_db);
  if(!$conn){
    echo '
      <p class="text-center bg-red-100 border-2 border-red-400 text-red-700 text-2xl rounded-xl font-bold"> 
        Database Connection Failed! ' . mysqli_connect_error() .
      '</p>';
    exit();
  } 

  // 1. HANDLE ASSIGNMENT
  if (isset($_POST['booking_ref']) && !isset($_POST['bsearch'])) {
    $booking_ref_assign = mysqli_real_escape_string($conn, $_POST['booking_ref']);
    $update_status_query = "
      UPDATE BOOKING
      SET status = 'Assigned'
      WHERE booking_ref = '$booking_ref_assign'
    ";
    $update_result = mysqli_query($conn, $update_status_query);
      
    if($update_result) {
      $affected_rows = mysqli_affected_rows($conn);
      if($affected_rows > 0) {
        echo '<p class="text-center bg-green-100 border-2 border-green-400 text-green-700 text-2xl">Congratulation! Booking request <span class="font-bold">' . htmlspecialchars($booking_ref_assign) . '</span> has been successfully assigned!</p>';
        
        $select_query = "
          SELECT booking_ref, name, phone, suburb, dsuburb, datetime, status 
          FROM BOOKING
          WHERE booking_ref = '$booking_ref_assign'";
        $select_result = mysqli_query($conn, $select_query);
        table_output($select_result);

      } else {
        echo '<p class="bg-red-100 border-2 border-red-400 text-red-700 mb-4">Booking Reference <span class="font-bold">' . htmlspecialchars($booking_ref_assign) . '</span> could not be assigned.';
      }
    } else {
      echo '
        <p class="text-center bg-red-100 border-2 border-red-400 text-red-700 text-2xl rounded-xl font-bold"> 
          Error assigning taxi: ' . mysqli_error($conn) .
        '</p>';
    }
    exit(); 
  }

  // 2. HANDLE SEARCH
  $search = mysqli_real_escape_string($conn, $_POST['bsearch']);
  if(empty($search)){
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);

    $datetime_obj = DateTime::createFromFormat('Y-m-d H:i', "$date $time");
    $datetime = $datetime_obj->format('Y-m-d H:i:s');

    $search_query = "
      SELECT booking_ref, name, phone, suburb, dsuburb, datetime, status 
      FROM BOOKING
      WHERE datetime >= '$datetime' AND datetime < DATE_ADD('$datetime', INTERVAL 2 HOUR) AND
      status = 'unassigned'
      ORDER BY datetime ASC
    ";
  } else {
    $search_query = "
      SELECT booking_ref, name, phone, suburb, dsuburb, datetime, status 
      FROM BOOKING
      WHERE booking_ref = '$search'
    ";
  }
  $result = mysqli_query($conn, $search_query);
  if(!$result) {
    echo '
      <p class="text-center bg-red-100 border-2 border-red-400 text-red-700 text-2xl rounded-xl font-bold"> 
        Error performing search query: ' . mysqli_error($conn) .
      '</p>';
    exit();
  }

  table_output($result);

  // Function to output the results in a table format
  function table_output($result){
    if(mysqli_num_rows($result) > 0){
      echo '<table class="w-full min-w-full border-2 text-center">';
      echo '
        <thead class="bg-sky-200">
          <tr>
            <th>Booking Reference Number</th>
            <th>Customer Name</th>
            <th>Phone</th>
            <th>Pickup Suburb</th>
            <th>Destination Suburb</th>
            <th>Pickup Date and Time</th>
            <th>Status</th>
            <th>Assign</th>
          </tr>
        </thead>';
      while($row = mysqli_fetch_assoc($result)){
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['booking_ref']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
        echo "<td>" . htmlspecialchars($row['suburb']) . "</td>";
        echo "<td>" . htmlspecialchars($row['dsuburb']) . "</td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($row['datetime'])) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        if($row['status'] !== 'unassigned') {
          echo '
          <td class="text-center px-4 py-2">
            <input 
              disabled
              type="button" 
              name="assign" 
              value="Assign" 
              class="assign-btn bg-yellow-600 hover:bg-yellow-700 font-bold text-white font-inter px-4 py-2 rounded-xl transform hover:scale-105 transition-all shadow-lg cursor-not-allowed opacity-50 w-[70%] text-center"
              data-booking-ref="'.htmlspecialchars($row['booking_ref']).'"/>
          </td>
          ';
        } else{
          echo '
          <td class="text-center px-4 py-2">
            <input 
              type="button" 
              name="assign" 
              value="Assign" 
              class="assign-btn bg-yellow-600 hover:bg-yellow-700 font-bold text-white font-inter px-4 py-2 rounded-xl transform hover:scale-105 transition-all shadow-lg cursor-pointer w-[70%] text-center"
              data-booking-ref="'.htmlspecialchars($row['booking_ref']).'"/>
          </td>
          ';
        }
        echo "</tr>";
      }
      echo "</table>";
    } else {
      echo '
        <p class="text-center bg-red-100 border-2 border-red-400 text-red-700 text-2xl rounded-xl font-bold"> 
          No bookings found! 
        </p>';
    } 
  }
  mysqli_close($conn); 

?>