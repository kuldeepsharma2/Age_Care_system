<?php
session_start();
include '../database.php';

// Restrict access to staff/admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../login.php");
    exit();
}

// Fetch messages received
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT m.*, u.full_name AS sender_name 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id 
    WHERE m.receiver_id = ? 
    ORDER BY m.created_at DESC
");
$stmt->execute([$userId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header_admin.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Messages</h2>
    <ul class="list-group mt-3">
        <?php foreach ($messages as $message): ?>
        <li class="list-group-item">
            <strong><?php echo $message['sender_name']; ?>:</strong>
            <p><?php echo $message['message_content']; ?></p>
            <a href="send_message.php?reply_to=<?php echo $message['id']; ?>" class="btn btn-sm btn-primary">Reply</a>

            <!-- Display Replies -->
            <?php
                    $replyStmt = $pdo->prepare("SELECT m.*, u.full_name AS sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.reply_to = ?");
                    $replyStmt->execute([$message['id']]);
                    $replies = $replyStmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

            <?php if (!empty($replies)): ?>
            <ul class="list-group mt-2">
                <?php foreach ($replies as $reply): ?>
                <li class="list-group-item">
                    <strong><?php echo $reply['sender_name']; ?> (Reply):</strong>
                    <p><?php echo $reply['message_content']; ?></p>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>

</div>
</body>

</html>