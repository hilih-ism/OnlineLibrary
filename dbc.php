<?php
$hostname = "localhost";
$username = "root";
$password = "";


$con = mysqli_connect($hostname, $username, $password);
if ($con === false) {
	die("ERROR: could not connect. " . mysqli_connect_error());
} else {
	echo "Connected successfully <br>";
}


$db = "CREATE DATABASE IF NOT EXISTS ip";
mysqli_query($con, $db);
mysqli_select_db($con, 'ip');


$tbl = "CREATE TABLE IF NOT EXISTS Book (
	ISBN INT NOT NULL PRIMARY KEY,
	Name VARCHAR(50) NOT NULL,
	Author VARCHAR(50) NOT NULL,
	Genre VARCHAR(50) NOT NULL,
	PublishDate DATE NOT NULL
)";
if (mysqli_query($con, $tbl) === true) {
	echo "Table created successfully.";
} else {
	echo "ERROR: was not able to execute $tbl. " . mysqli_error($con);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	
	$form = "INSERT INTO Book (ISBN, Name, Author, Genre, PublishDate) VALUES (?, ?, ?, ?, ?)";
	$stmt = mysqli_prepare($con, $form);

	if ($stmt === false) {
		die("ERROR: could not prepare statement. " . mysqli_error($con));
	}

	
	mysqli_stmt_bind_param($stmt, "issss", $_POST['isbn'], $_POST['name'], $_POST['author'], $_POST['genre'], $_POST['pdate']);

	
	if (mysqli_stmt_execute($stmt)) {
		$message = "Record inserted successfully.";
	} else {
		$message = "Error inserting record: " . mysqli_error($con);
	}

	
	mysqli_stmt_close($stmt);
	
	if(isset($_POST['updatebtn'])){
		
		$updateQuery="UPDATE Book SET Name=?, Author=?,Genre=?,PublishDate=? WHERE ISBN=?";
		$stm = mysqli_prepare($con,$updateQuery);
		
		if ($stm === false) {
		die("ERROR: could not prepare statement. " . mysqli_error($con));
	}
		mysqli_stmt_bind_param($stm,"ssssi",$_POST['name'],$_POST['author'],$_POST['genre'],$_POST['pdate'],$_POST['isbn']);
		if (mysqli_stmt_execute($stm)) {
        $message = "Record updated successfully.";
    } else {
        $message = "Error updating record: " . mysqli_error($con);
    }

    mysqli_stmt_close($stm);
    mysqli_close($con);
	}
	
	if(isset($_POST['deletebtn'])){
		$isbn=$_POST['isbn'];
		$deleteQuery="DELETE FROM Book WHERE ISBN=?";
		$sm = mysqli_prepare($con, $deleteQuery);
    mysqli_stmt_bind_param($sm, "i", $isbn);
    if (mysqli_stmt_execute($sm)) {
        $message = "Record deleted successfully.";
    } else {
        $message = "Error deleting record: " . mysqli_error($con);
    }
    mysqli_stmt_close($sm);
    mysqli_close($con);
	}
	
	if(isset($_POST['fetchbtn'])){
		$isbn=$_POST['isbn'];
		$fetchQuery="SELECT * FROM Book WHERE ISBN=?";
		$s = mysqli_prepare($con,$fetchQuery);
		mysqli_stmt_bind_param($s,"i",$isbn);
		if(mysqli_stmt_execute($s)){
			$result = mysqli_stmt_get_result($s);
			$row = mysqli_fetch_assoc($result);
			if($row){
				echo "<br>",$row['ISBN'],"<br>";
				echo $row['Name'],"<br>";
				echo $row['Author'],"<br>";
				echo $row['Genre'],"<br>";
				echo $row['PublishDate'];
			}
		}
		else{
			 $message = "Error fetching record: " . mysqli_error($con);
		}
		 mysqli_stmt_close($s);
    mysqli_close($con);
	}
}
?>
