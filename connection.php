<?php


   $con=mysqli_connect("localhost","root","","hospitalms");
   if (mysqli_connect_error()){
      echo"<script>alert('cannot connect to the database');</script>";
      exit();
   }
?>