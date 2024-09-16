<?php

	$inData = getRequestInfo();

	$contactID = $inData["id"];


  	//TODO: This needs to be updated with the correct connection point
	$conn = new mysqli("localhost", "Sebastian", "123456789", "ContactManager");
	if ($conn->connect_error)
	{
		returnWithError($conn->connect_error);
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

		$stmt = $conn->prepare("DELETE FROM Contacts WHERE ID = ? AND UserID = ?");
		$stmt->bind_param("ii", $contactID, $userID);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		returnWithError("");
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
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}

?>
