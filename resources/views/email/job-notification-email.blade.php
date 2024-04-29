<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Jo Notofication Email</title>
</head>
<body>


<h1>Hello {{$mailData['employer']->name}}</h1>
<p>Job Title: {{$mailData['job']->title}}</p>

<p>Employer Details:</p>
<p>Name: {{$mailData['user']->name}}:</p>
<p>Email" {{$mailData['user']->email}}:</p>
<p>Mobile No :{{$mailData['user']->mobile}}</p>
</body>
</html>