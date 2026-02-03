<!DOCTYPE html>
<html>
<head>
    <title>New Service Ticket</title>
</head>
<body>
    <h2>New Service Ticket Submitted</h2>
    <p><strong>House Owner ID:</strong> {{ $ticket->house_owner_id }}</p>
    <p><strong>Title:</strong> {{ $ticket->title }}</p>
    <p><strong>Description:</strong></p>
    <p>{{ $ticket->description }}</p>
</body>
</html>
