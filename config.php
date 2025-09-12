<?php
$servername = "localhost"; // Database server name
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "mini";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . $conn);
}
echo "Connected successfully ";
//error reporting
//echo print and print_r 
//require 
//super globels 
//isset /empaty/is_null
//get and post
//include (file.php) & header ("Loction FILE.PHP")
//inner joim, left join, right join,
//difff primary key unique key indexing 
// sql injection how to prevent in php
//ddl dsl dml
//public protected privet
//self :: $this ->
//csrf,xss  

?>
