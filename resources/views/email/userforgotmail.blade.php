<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body>
	<p><b>Hi {{$details['name']}}</b></p>
	<p>Seems like you've forgotten your password. Please click on the button below to reset it</p>
    <p><a href="{{ $details['link'] }}"><button class="btn">Reset Password</button></a></p>
    <p>If you did not request a password reset, please ignore this email.</p>
   
    <p>Thanks</p>
    <p><b>The Kappa Team</b></p>
</body>
</html>