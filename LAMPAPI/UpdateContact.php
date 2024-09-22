<?php
	$inData = getRequestInfo();

	$FirstName = $inData["FirstName"];
	$LastName = $inData["LastName"];
	$Phone = $inData["Phone"];
	$Email = $inData["Email"];


	//TODO: this connection will have to be updated
	$conn = new mysqli("localhost", "", "", "ContactManager");
	if ($conn->connect_error)
	{
		returnWithError( $conn->connect_error );
	}
	else
	{

        	//checks if in testing mode for swaggerhub for the source of userId. Either from session or input from swaggerhub
                $testingMode = isset($inData['testing']) && $inData['testing'] === true;

                if ($testingMode && isset($inData['userId'])) {
                $userId = $inData['userId'];
                } else if (isset($_SESSION['Users']) && isset($_SESSION['Users']['ID'])) {
                $userId = $_SESSION['Users']['ID'];
                }else {
                returnWithError("User ID not available in session or request body");
                }

        	//get ID of contact you want changed
		$stmt = $conn->prepare("select ID from Contacts where FirstName like ? AND UserID=?");
		$name = "%" . $FirstName . "%";
		$stmt->bind_param("ss", $name, $userId);
		$stmt->execute();
		$result = $stmt->get_result();
		$result_row = $result->fetch_assoc();
		$contactId = $result_row['ID'];
        	//alter specfic user with all new info given
        	//in the future could replace individual parts rather then full person
		$stmt = $conn->prepare("UPDATE Contacts SET FirstName = ?, LastName = ?, Phone = ?, Email = ? WHERE ID = ?");
                // Bind the parameters (s = string, i = integer)
                $stmt->bind_param("ssssi", $FirstName, $LastName, $Email, $Phone, $contactId);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		returnWithError('');
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}

	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}

?>
