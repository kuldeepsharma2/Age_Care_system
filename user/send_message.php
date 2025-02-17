<?php
session_start();
include '../database.php';

// Restrict access to users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Fetch staff/admin for messaging
$stmt = $pdo->query("SELECT id, full_name FROM users WHERE role IN ('staff', 'admin')");
$recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle message sending
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receiverId = $_POST['receiver_id'];
    $message = $_POST['message'];
    $senderId = $_SESSION['user_id'];
    $replyTo = isset($_POST['reply_to']) ? $_POST['reply_to'] : null;

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_content, reply_to) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$senderId, $receiverId, $message, $replyTo])) {
        $_SESSION['success'] = "Message sent!";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Message failed!";
    }
}
?>

<?php include '../includes/header_user.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Send a Message</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Select Recipient</label>
            <select name="receiver_id" class="form-control" required>
                <?php foreach ($recipients as $recipient): ?>
                <option value="<?php echo $recipient['id']; ?>"><?php echo $recipient['full_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Message</label>
            <textarea name="message" class="form-control" required></textarea>
        </div>
        <input type="hidden" name="reply_to" value="<?php echo isset($_GET['reply_to']) ? $_GET['reply_to'] : ''; ?>">
        <button type="submit" class="btn btn-success w-100">Send Message</button>
    </form>

</div>
</body>

</html>