<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}

// Get user role
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get channels and messages
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    
    // Get channels
    $stmt = $conn->query("SELECT * FROM channels");
    $channels = $stmt->fetchAll();
    
    // Get selected channel (default: General Discussion)
    $selected_channel_id = isset($_GET['channel']) ? (int)$_GET['channel'] : 1;
    
    // Get messages for selected channel
    $stmt = $conn->prepare("
        SELECT m.*, u.name as sender_name 
        FROM messages m 
        JOIN users u ON m.sender_id = u.user_id 
        WHERE channel_id = ? 
        ORDER BY sent_at DESC 
        LIMIT 50
    ");
    $stmt->execute([$selected_channel_id]);
    $messages = $stmt->fetchAll();
    
    // Get message reaction counts
    $reaction_counts = [];
    $stmt = $conn->prepare("
        SELECT message_id, reaction_type, COUNT(*) as count 
        FROM reactions 
        GROUP BY message_id, reaction_type
    ");
    $stmt->execute();
    $reactions = $stmt->fetchAll();
    
    foreach($reactions as $react) {
        $message_id = $react['message_id'];
        $type = $react['reaction_type'];
        if(!isset($reaction_counts[$message_id])) {
            $reaction_counts[$message_id] = [];
        }
        $reaction_counts[$message_id][$type] = $react['count'];
    }
    
    // Get if current user has reacted to each message
    $user_reactions = [];
    $stmt = $conn->prepare("
        SELECT message_id, reaction_type 
        FROM reactions 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user_reacts = $stmt->fetchAll();
    
    foreach($user_reacts as $react) {
        $message_id = $react['message_id'];
        $type = $react['reaction_type'];
        if(!isset($user_reactions[$message_id])) {
            $user_reactions[$message_id] = [];
        }
        $user_reactions[$message_id][$type] = true;
    }
    
    // Get selected channel name
    $stmt = $conn->prepare("SELECT name FROM channels WHERE channel_id = ?");
    $stmt->execute([$selected_channel_id]);
    $channel_name = $stmt->fetchColumn();
} catch(PDOException $e){
    die("Database error: " . $e->getMessage());
}
?>
        <div class="dashboard-content">
            <div class="chat-container">
                <!-- Sidebar -->
                <div class="chat-sidebar">
                    <h3>University Connect</h3>
                    <div class="search-bar">
                        <input type="text" placeholder="Search channels...">
                    </div>
                    <div class="channel-list">
                        <div class="channel-section">
                            <h4>General</h4>
                            <?php if($is_admin): ?>
                            <span class="add-channel" data-section="general">+</span>
                            <?php endif; ?>
                        </div>
                        <ul>
                            <?php foreach($channels as $chan): 
                                if(strpos($chan['name'], 'Department') === false && 
                                   strpos($chan['name'], 'Introduction') === false && 
                                   strpos($chan['name'], 'Calculus') === false):
                            ?>
                                <li class="<?= $chan['channel_id'] == $selected_channel_id ? 'active' : '' ?>">
                                    <a href="?channel=<?= $chan['channel_id'] ?>">
                                        <?= $chan['name'] ?>
                                    </a>
                                </li>
                            <?php endif; endforeach; ?>
                        </ul>
                        
                        <div class="channel-section">
                            <h4>Departments</h4>
                            <?php if($is_admin): ?>
                            <span class="add-channel" data-section="departments">+</span>
                            <?php endif; ?>
                        </div>
                        <ul>
                            <?php foreach($channels as $chan): 
                                if(strpos($chan['name'], 'Department') !== false):
                            ?>
                                <li class="<?= $chan['channel_id'] == $selected_channel_id ? 'active' : '' ?>">
                                    <a href="?channel=<?= $chan['channel_id'] ?>">
                                        <?= $chan['name'] ?>
                                    </a>
                                </li>
                            <?php endif; endforeach; ?>
                        </ul>
                        
                        <div class="channel-section">
                            <h4>Courses</h4>
                            <?php if($is_admin): ?>
                            <span class="add-channel" data-section="courses">+</span>
                            <?php endif; ?>
                        </div>
                        <ul>
                            <?php foreach($channels as $chan): 
                                if(strpos($chan['name'], 'Introduction') !== false || 
                                   strpos($chan['name'], 'Calculus') !== false):
                            ?>
                                <li class="<?= $chan['channel_id'] == $selected_channel_id ? 'active' : '' ?>">
                                    <a href="?channel=<?= $chan['channel_id'] ?>">
                                        <?= $chan['name'] ?>
                                    </a>
                                </li>
                            <?php endif; endforeach; ?>
                        </ul>
                        
                        <div class="channel-section">
                            <h4>Private Channels</h4>
                            <?php if($is_admin): ?>
                            <span class="add-channel" data-section="private">+</span>
                            <?php endif; ?>
                        </div>
                        <ul>
                            <?php 
                            // Get private channels (is_public = 0)
                            $private_channels = [];
                            foreach($channels as $chan) {
                                if(isset($chan['is_public']) && $chan['is_public'] == 0) {
                                    $private_channels[] = $chan;
                                }
                            }
                            ?>
                            
                            <?php if(count($private_channels) === 0): ?>
                                <li>No private channels</li>
                            <?php else: ?>
                                <?php foreach($private_channels as $chan): 
                                    $active = $chan['channel_id'] == $selected_channel_id ? 'active' : '';
                                ?>
                                    <li class="<?= $active ?>">
                                        <a href="?channel=<?= $chan['channel_id'] ?>">
                                            <?= $chan['name'] ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        
                        <h4>Online Now (4)</h4>
                        <ul>
                            <li>Dr. Maria Garcia</li>
                            <li>Dr. Sarah Smith</li>
                            <li>Emma Davis</li>
                            <li>Mike Wilson</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Chat Area -->
                <div class="chat-main">
                    <div class="chat-header">
                        <h2><?= $channel_name ?? 'Select a Channel' ?></h2>
                        <p><?= $channel_name ? 'University-wide discussion' : '' ?></p>
                    </div>
                    <div class="chat-messages">
                        <?php if(count($messages) === 0): ?>
                            <p>No messages in this channel yet</p>
                        <?php else: ?>
                            <?php foreach(array_reverse($messages) as $msg): ?>
                                <div class="message" data-message-id="<?= $msg['message_id'] ?>">
                                    <div class="message-header">
                                        <span class="sender"><?= $msg['sender_name'] ?></span>
                                        <span class="timestamp">
                                            <?= date('g:i A', strtotime($msg['sent_at'])) ?>
                                        </span>
                                    </div>
                                    <div class="message-content">
                                        <?= htmlspecialchars($msg['content']) ?>
                                    </div>
                                    <div class="message-reactions">
                                        <!-- Like Button -->
                                        <button class="reaction-btn" data-reaction-type="like">
                                            👍 
                                            <span class="count">
                                                <?= $reaction_counts[$msg['message_id']]['like'] ?? 0 ?>
                                            </span>
                                            <?php if(isset($user_reactions[$msg['message_id']]['like'])): ?>
                                                <span class="user-reacted">✓</span>
                                            <?php endif; ?>
                                        </button>
                                        
                                        <!-- Clap Button -->
                                        <button class="reaction-btn" data-reaction-type="clap">
                                            👏 
                                            <span class="count">
                                                <?= $reaction_counts[$msg['message_id']]['clap'] ?? 0 ?>
                                            </span>
                                            <?php if(isset($user_reactions[$msg['message_id']]['clap'])): ?>
                                                <span class="user-reacted">✓</span>
                                            <?php endif; ?>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <!-- Message Input Form -->
                    <form action="send_message.php" method="POST" class="chat-input">
                        <input type="hidden" name="channel_id" value="<?= $selected_channel_id ?>">
                        <input type="text" name="content" placeholder="Type your message..." required>
                        <button type="submit" class="send-btn">Send</button>
                    </form>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
        
        <!-- Channel Creation Modals (Admin only) -->
        <?php if($is_admin): ?>
        <!-- General Channel Creation Modal -->
        <div id="create-channel-modal-general" class="modal">
            <div class="modal-content">
                <span class="close" data-modal="create-channel-modal-general">&times;</span>
                <h2>Create New General Channel</h2>
                <form class="create-channel-form" data-section="general">
                    <div class="form-group">
                        <label for="channel-name">Channel Name</label>
                        <input type="text" id="channel-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="channel-description">Description</label>
                        <textarea id="channel-description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_public" checked> Public Channel
                        </label>
                    </div>
                    <button type="submit" class="btn">Create Channel</button>
                </form>
            </div>
        </div>
        
        <!-- Departments Channel Creation Modal -->
        <div id="create-channel-modal-departments" class="modal">
            <div class="modal-content">
                <span class="close" data-modal="create-channel-modal-departments">&times;</span>
                <h2>Create New Department Channel</h2>
                <form class="create-channel-form" data-section="departments">
                    <div class="form-group">
                        <label for="channel-name">Channel Name</label>
                        <input type="text" id="channel-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="channel-description">Description</label>
                        <textarea id="channel-description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_public" checked> Public Channel
                        </label>
                    </div>
                    <button type="submit" class="btn">Create Channel</button>
                </form>
            </div>
        </div>
        
        <!-- Courses Channel Creation Modal -->
        <div id="create-channel-modal-courses" class="modal">
            <div class="modal-content">
                <span class="close" data-modal="create-channel-modal-courses">&times;</span>
                <h2>Create New Course Channel</h2>
                <form class="create-channel-form" data-section="courses">
                    <div class="form-group">
                        <label for="channel-name">Channel Name</label>
                        <input type="text" id="channel-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="channel-description">Description</label>
                        <textarea id="channel-description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_public" checked> Public Channel
                        </label>
                    </div>
                    <button type="submit" class="btn">Create Channel</button>
                </form>
            </div>
        </div>
        
        <!-- Private Channel Creation Modal -->
        <div id="create-channel-modal-private" class="modal">
            <div class="modal-content">
                <span class="close" data-modal="create-channel-modal-private">&times;</span>
                <h2>Create New Private Channel</h2>
                <form class="create-channel-form" data-section="private">
                    <div class="form-group">
                        <label for="channel-name">Channel Name</label>
                        <input type="text" id="channel-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="channel-description">Description</label>
                        <textarea id="channel-description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_public" checked> Public Channel
                        </label>
                    </div>
                    <button type="submit" class="btn">Create Channel</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add click handlers to reaction buttons
            document.querySelectorAll('.reaction-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const messageId = this.closest('.message').dataset.messageId;
                    const reactionType = this.dataset.reactionType;
                    
                    // Save reference to button for use in callback
                    const button = this;
                    
                    // Send AJAX request
                    fetch('handle_reaction.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `message_id=${messageId}&reaction_type=${reactionType}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Update count
                            const countSpan = button.querySelector('.count');
                            countSpan.textContent = data.count;
                            
                            // Toggle user-reacted indicator
                            const reactedSpan = button.querySelector('.user-reacted');
                            if(reactedSpan) {
                                reactedSpan.remove();
                            } else {
                                button.innerHTML += '<span class="user-reacted">✓</span>';
                            }
                        }
                    });
                });
            });
            
            // Add channel creation code for admins
            <?php if($is_admin): ?>
            // Get all add channel buttons
            document.querySelectorAll('.add-channel').forEach(btn => {
                btn.addEventListener('click', function() {
                    const section = this.dataset.section;
                    const modalId = `create-channel-modal-${section}`;
                    const modal = document.getElementById(modalId);
                    if(modal) {
                        modal.style.display = "block";
                    }
                });
            });
            
            // Close modals
            document.querySelectorAll('.close').forEach(closeBtn => {
                closeBtn.addEventListener('click', function() {
                    const modalId = this.dataset.modal;
                    const modal = document.getElementById(modalId);
                    if(modal) {
                        modal.style.display = "none";
                    }
                });
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                document.querySelectorAll('.modal').forEach(modal => {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                });
            });
            
            // Handle form submissions
            document.querySelectorAll('.create-channel-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Get form data
                    const formData = new FormData(this);
                    const section = this.dataset.section;
                    
                    // Send to server
                    fetch('create_channel.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Refresh page to show new channel
                            window.location.reload();
                        } else {
                            alert('Error creating channel: ' + data.error);
                        }
                    });
                });
            });
            <?php endif; ?>
        });
        </script>