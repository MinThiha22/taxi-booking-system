<!-- 
  Author: Min Thiha Ko Ko
  ID: 21156028
  network username: sss0276
  Description: This PHP file drops the BOOKING table from the database.
-->
<?php
  require_once('../../files/sqlinfo.inc.php');
  $conn = @mysqli_connect($sql_host,
    $sql_user,
    $sql_pass,
    $sql_db
  );
  if(!$conn){
    echo '
      <p class="text-center bg-red-100 border-2 border-red-400 text-red-700 text-2xl rounded-xl font-bold"> 
        Database Connection Failed! ' . mysqli_connect_error() .
      '</p>';
    exit();
  }

  $drop = mysqli_escape_string($conn, $_POST['drop']);
  if(!empty($drop)){
    $drop_query = "DROP TABLE IF EXISTS BOOKING";
    $result = mysqli_query($conn, $drop_query);
    if($result){
      echo '
        <p class="text-center bg-green-100 border-2 border-green-400 text-green-700 text-2xl">
          BOOKING table is dropped successfully!
        </p>';
    } else {
      echo '
        <p class="text-center bg-red-100 border-2 border-red-400 text-red-700 text-2xl rounded-xl font-bold"> 
          Error dropping BOOKING table: ' . mysqli_error($conn) .
        '</p>';
    }
  }
  mysqli_close($conn);
?>
