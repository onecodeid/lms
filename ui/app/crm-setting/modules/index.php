<?php

http_response_code(403);
die('<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>403 Forbidden - Oops!</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background-color: #f4f4f4;
		}
		.container {
			max-width: 600px;
			margin: 0 auto;
			padding: 50px;
			background-color: #fff;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
			text-align: center;
		}
		h1 {
			font-size: 36px;
			margin-top: 0;
			margin-bottom: 20px;
			color: #e74c3c;
		}
		p {
			font-size: 18px;
			margin-top: 0;
			margin-bottom: 30px;
		}
		.error-image {
			max-width: 200px;
			margin: 0 auto 30px;
			display: block;
		}
		.link-button {
			display: inline-block;
			padding: 10px 20px;
			background-color: #3498db;
			color: #fff;
			text-decoration: none;
			border-radius: 5px;
			transition: all 0.3s ease-in-out;
		}
		.link-button:hover {
			background-color: #2980b9;
			transform: scale(1.05);
		}
	</style>
</head>
<body>
	<div class="container">
		<img class="error-image" src="https://i.imgur.com/z9TaY2v.png" alt="Cute Cat">
		<h1>403 Forbidden - Oops!</h1>
		<p>We are sorry, but you do not have permission to access this page. Maybe you should ask our cute cat for help?</p>
		<a class="link-button" href="../">Back to Homepage</a>
	</div>
</body>
</html>
');
?>