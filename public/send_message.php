<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('../includes/db.php');

$sender_id = intval($_SESSION['user_id']); // Logged-in user ID as integer

// Fetch all users for the dropdown except the logged-in user
$recipients = [];
$query = "SELECT User_ID, Email FROM user WHERE User_ID != $sender_id";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $recipients[] = $row;
}

// Check if replying to a message
$recipient_id = isset($_GET['reply_to']) ? $_GET['reply_to'] : (isset($_POST['recipient']) ? $_POST['recipient'] : '');
$original_message_id = isset($_GET['message_id']) ? $_GET['message_id'] : (isset($_POST['original_message_id']) ? $_POST['original_message_id'] : '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['message'];
    $timestamp = date('Y-m-d H:i:s');

    // Insert message into the database
    $query = "INSERT INTO message (Sender_ID, Recipient_ID, Content, Timestamp, is_read) 
              VALUES ('$sender_id', '$recipient_id', '$content', '$timestamp', 0)";
    if ($conn->query($query) === TRUE) {
        $message_id = $conn->insert_id;

        // Handle file upload (multiple attachments)
        if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
            $upload_dir = '../uploads/';

            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['attachments']['name'][$key];
                $file_tmp = $_FILES['attachments']['tmp_name'][$key];
                $file_type = $_FILES['attachments']['type'][$key];

                $file_path = $upload_dir . basename($file_name);

                if (move_uploaded_file($file_tmp, $file_path)) {
                    $query = "INSERT INTO attachment (File_Name, File_Type, Message_ID) 
                              VALUES ('$file_name', '$file_type', '$message_id')";
                    $conn->query($query);
                }
            }
        }

        if (!empty($original_message_id)) {
            $conn->query("UPDATE message SET is_read = 1 WHERE Message_ID = $original_message_id");
        }

        $_SESSION['flash_success'] = "Message sent successfully!";  // Set flash message
        header("Location: send_message.php"); // Redirect to same page
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Message</title>
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        .drop-area {
            width: 100%;
            height: 150px;
            border: 2px dashed #ccc;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 18px;
            color: #888;
            position: relative;
            cursor: pointer;
        }
        .drop-area.dragover {
            background-color: #f7f7f7;
            border-color: #5cb85c;
        }
        .file-input {
            display: none;
        }
        .file-list {
            margin-top: 10px;
            font-size: 14px;
            color: #333;
        }
        .file-list span {
            display: inline-block;
            margin: 5px 10px 0 0;
            background-color: #e1e1e1;
            padding: 5px 10px;
            border-radius: 5px;
            position: relative;
        }
        .file-list button {
            margin-left: 8px;
            background: transparent;
            border: none;
            color: red;
            font-size: 14px;
            cursor: pointer;
        }

        .flash-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #d6e9c6;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
<header>
    <h1>Send Message</h1>
</header>
<main>
    <?php
    // Display flash message if set
    if (isset($_SESSION['flash_success'])) {
        echo '<p class="flash-message">' . $_SESSION['flash_success'] . '</p>';
        unset($_SESSION['flash_success']);  // Clear the message after displaying it
    }
    ?>
    
    <form method="POST" action="send_message.php<?php echo (isset($_GET['reply_to']) && isset($_GET['message_id'])) ? '?reply_to=' . $_GET['reply_to'] . '&message_id=' . $_GET['message_id'] : ''; ?>" enctype="multipart/form-data">
        <label for="message">Message Content:</label>
        <textarea name="message" id="message" rows="5" required></textarea><br>

        <?php if (!empty($original_message_id)): ?>
            <input type="hidden" name="original_message_id" value="<?php echo $original_message_id; ?>">
        <?php endif; ?>

        <label for="recipient">Select Recipient:</label>
        <select name="recipient" id="recipient" required>
            <?php foreach ($recipients as $recipient): ?>
                <option value="<?php echo $recipient['User_ID']; ?>"
                    <?php echo ($recipient['User_ID'] == $recipient_id) ? 'selected' : ''; ?>>
                    <?php echo $recipient['Email']; ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="attachments">Attach Files:</label>
        <div class="drop-area" id="drop-area">
            Drag & Drop Files Here or Click to Select
            <input type="file" name="attachments[]" id="attachments" class="file-input" multiple>
        </div>
        <div class="file-list" id="file-list"></div><br>

        <button type="submit">Send Message</button>
    </form>
</main>

<script>
window.onload = function() {
    const flashMessage = document.querySelector('.flash-message');
    if (flashMessage) {
        setTimeout(function() {
            flashMessage.style.display = 'none';
        }, 5000); // 5000 milliseconds = 5 seconds
    }
};

const dropArea = document.getElementById('drop-area');
const fileInput = document.getElementById('attachments');
const fileList = document.getElementById('file-list');

let selectedFiles = [];

dropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropArea.classList.add('dragover');
});

dropArea.addEventListener('dragleave', () => {
    dropArea.classList.remove('dragover');
});

dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.classList.remove('dragover');
    handleFiles(e.dataTransfer.files);
});

dropArea.addEventListener('click', () => {
    fileInput.click();
});

fileInput.addEventListener('change', (e) => {
    handleFiles(e.target.files);
    fileInput.value = ''; // reset input to allow same file again
});

function handleFiles(files) {
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
            selectedFiles.push(file);
        }
    }
    updateFileInput();
    displayFileNames();
}

function updateFileInput() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });
    fileInput.files = dataTransfer.files;
}

function displayFileNames() {
    fileList.innerHTML = '';
    selectedFiles.forEach((file, index) => {
        const span = document.createElement('span');
        span.textContent = file.name;

        const removeBtn = document.createElement('button');
        removeBtn.textContent = '✖';
        removeBtn.title = 'Remove file';

        removeBtn.addEventListener('click', () => {
            selectedFiles.splice(index, 1);
            updateFileInput();
            displayFileNames();
        });

        span.appendChild(removeBtn);
        fileList.appendChild(span);
    });
}

document.querySelector('form').addEventListener('submit', (e) => {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });
    fileInput.files = dataTransfer.files;
});
</script>
</body>
</html>
