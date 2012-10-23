<?php 

$insert_registrant = "
INSERT INTO registrants (
		firstname,
		lastname,
		email,
		password,
		affiliation,
		region,
		type
	) VALUES (
		'{$firstname}', 
		'{$lastname}', 
		'{$email}', 
		'{$password['hash']}', 
		'{$affiliation}', 
		'{$region}', 
		'{$type}'
	)
";

?>