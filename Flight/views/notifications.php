
<?php
session_start();

// Include the MessageModel
require_once '../models/MessageModel.php';

// Create an instance of the MessageModel
$messageModel = new MessageModel();

// Retrieve messages
$messages = $messageModel->get_messages($_SESSION['id']);
$messages = array_reverse($messages); // Reverse the array to show the latest messages first

if (isset($_POST['send_msg'])) {

    $userId = $_SESSION['id']; // Get the user ID from session
    $messageContent = $_POST['reply_message']; // Get the message content
    $sender=$_POST['sender_id'];


    $messageModel = new MessageModel();
    $date = date('Y-m-d H:i:s');
    $result = $messageModel->Send_Message1($userId, $sender, $messageContent,$date);

    if ($result) {
        $flightStatusMessage = "Message sent successfully!";
        $modalType = 'success';  // Success modal
    } else {
        $flightStatusMessage = "Failed to send message!";
        $modalType = 'danger';  // Error modal
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
            padding: 0;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed; /* Make the background stay fixed */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../images/pexels-ahmedmuntasir-912050.jpg') no-repeat center center/cover; /* Ensure the image covers the viewport */
            background-attachment: fixed; /* Keep the image fixed while scrolling */
            opacity: 25%; /* Adjust the opacity for a subtler background */
            z-index: -1; /* Ensure the image is behind the content */
        }
        .header {
            background: #10465a; /* Dark teal header background */
            color: white;
            padding: 20px 0;
            border-bottom: 2px solid #ddd;
        }

        .header a {
            color: white;
            font-size: 18px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .header a:hover {
            background-color: rgba(255, 255, 255, 0.56);
            color: white;
        }

        .notifications-container {
            margin: 20px;
            padding: 20px;
            background: #ffffff; /* White background for the container */
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .notifications-container h1 {
            color: #10465a; /* Dark teal text for the title */
            margin-bottom: 20px;
        }

        .notifications-list {
            list-style-type: none;
            padding: 0;
        }

        .notification-item {
            background-color: #ffffff; /* White background for individual items */
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .notification-item:hover {
            background-color: #f1f1f1; /* Light gray background on hover */
        }

        .notification-content {
            font-size: 16px;
            color: #555;
        }

        .notification-details {
            display: none;
            font-size: 14px;
            color: #777;
            margin-top: 10px;
        }

        .notification-content p {
            margin: 0;
        }

        .btn-reply {
            background-color: #3498db; /* Blue reply button */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        .btn-reply:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }

        .modal-content {
            background-color: #ffffff; /* White background for the modal */
            border-radius: 8px;
        }

        .modal-header {
            background-color: #10465a; /* Dark teal background for modal header */
            color: white;
            border-bottom: 2px solid #ddd;
        }

        .modal-title {
            font-size: 1.25rem;
        }

        .modal-body {
            padding: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        textarea.form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .btn-primary {
            background-color: #EFB11D; /* Yellow background for the primary button */
            border: none;
            border-radius: 5px;
            color: white;
        }

        .btn-primary:hover {
            background-color: #d8a50d; /* Darker yellow on hover */
        }

        .container {
            background-color: #f8f9fa; /* Light background for the container */
            padding-top: 30px;
        }

        button.btn-secondary {
            background-color: #D6536D; /* Pink color for back button */
            border: none;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        button.btn-secondary:hover {
            background-color: #c24663; /* Darker pink on hover */
        }



    </style>
</head>

<body>

<button class="btn btn-secondary" onclick="history.back()">
    <i class="fas fa-arrow-left"></i>
</button>

<div class="notifications-container">
    <h1>Notifications</h1>
    <?php if (!empty($messages)): ?>
        <ul class="notifications-list">
            <?php foreach ($messages as $message): ?>
                <li class="notification-item" onclick="toggleDetails(<?php echo $message['id']; ?>)">
                    <div class="notification-content">
                        <p><?php echo htmlspecialchars($message['content']); ?></p>
                    </div>
                    <div class="notification-details" id="details-<?php echo $message['id']; ?>">
                        <p><strong>Sender ID:</strong> <?php echo htmlspecialchars($message['sender_id']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($message['date']); ?></p>
                        <button class="btn-reply" data-bs-toggle="modal" data-bs-target="#replyModal" onclick="setMessageId(<?php echo $message['id']; ?>, <?php echo $message['sender_id']; ?>)">Reply</button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No notifications available.</p>
    <?php endif; ?>
</div>

<!-- Modal for reply -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel">Reply to Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="notifications.php">
                    <input type="hidden" id="replyMessageId" name="send_msg" value="1">
                    <input type="hidden" id="senderId" name="sender_id" value="">
                    <div class="mb-3">
                        <label for="replyContent" class="form-label">Your Reply</label>
                        <textarea class="form-control" id="replyContent" name="reply_message" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle the visibility of the message details
    function toggleDetails(messageId) {
        const details = document.getElementById('details-' + messageId);
        if (details.style.display === 'none' || details.style.display === '') {
            details.style.display = 'block';
        } else {
            details.style.display = 'none';
        }
    }

    // Set the message ID and sender ID for the reply modal
    function setMessageId(messageId, senderId) {
        document.getElementById('replyMessageId').value = messageId;
        document.getElementById('senderId').value = senderId;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>