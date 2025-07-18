<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

$admin_id = $_SESSION['admin_id'];

// Get all customers and staff for recipient list
$recipients_query = "SELECT 'customer' as type, customer_id as id, customer_name as name, customer_email as email FROM rpos_customers 
                     UNION 
                     SELECT 'staff' as type, staff_number as id, staff_name as name, staff_email as email FROM rpos_staff";
$recipients_result = $mysqli->query($recipients_query);

require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php require_once('partials/_sidebar.php'); ?>

    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php require_once('partials/_topnav.php'); ?>

        <!-- Header -->
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;"
            class="header pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body"></div>
            </div>
        </div>

        <!-- Page content -->
        <div class="container-fluid mt--8">
            <div class="row">
                <div class="col-lg-4">
                    <!-- Recipients List -->
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3>Contacts</h3>
                            <div class="input-group mt-2">
                                <input type="text" id="searchContacts" class="form-control"
                                    placeholder="Search contacts...">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" id="recipientsList">
                                <?php while ($recipient = $recipients_result->fetch_object()): ?>
                                    <a href="#" class="list-group-item list-group-item-action recipient-item"
                                        data-recipient-id="<?php echo $recipient->id; ?>"
                                        data-recipient-type="<?php echo $recipient->type; ?>"
                                        data-recipient-name="<?php echo htmlspecialchars($recipient->name); ?>">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($recipient->name); ?></h6>
                                            <small class="text-muted"><?php echo ucfirst($recipient->type); ?></small>
                                        </div>
                                        <small class="text-muted"><?php echo htmlspecialchars($recipient->email); ?></small>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <!-- Chat Container -->
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <h3 id="chatTitle">Select a contact to start messaging</h3>
                        </div>
                        <div class="card-body p-0">
                            <!-- Messages Container -->
                            <div id="messagesContainer" class="p-3"
                                style="height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                                <div class="text-center text-muted mt-5">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>Select a contact to view messages</p>
                                </div>
                            </div>

                            <!-- Message Input -->
                            <div class="p-3 border-top" id="messageInputContainer" style="display: none;">
                                <form id="messageForm">
                                    <input type="hidden" id="currentRecipientId" name="recipient_id">
                                    <input type="hidden" id="currentRecipientType" name="recipient_type">
                                    <div class="input-group">
                                        <input type="text" id="messageText" name="message" class="form-control"
                                            placeholder="Type your message..." required>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane"></i> Send
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php require_once('partials/_footer.php'); ?>
    </div>

    <!-- Argon Scripts -->
    <?php require_once('partials/_scripts.php'); ?>

    <script>
        let currentRecipientId = null;
        let currentRecipientType = null;
        let currentRecipientName = null;

        // Search contacts
        $('#searchContacts').on('input', function () {
            const searchTerm = $(this).val().toLowerCase();
            $('.recipient-item').each(function () {
                const name = $(this).data('recipient-name').toLowerCase();
                const email = $(this).find('small').text().toLowerCase();
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Select recipient
        $('.recipient-item').click(function (e) {
            e.preventDefault();

            // Remove active class from all items
            $('.recipient-item').removeClass('active');

            // Add active class to clicked item
            $(this).addClass('active');

            // Set current recipient
            currentRecipientId = $(this).data('recipient-id');
            currentRecipientType = $(this).data('recipient-type');
            currentRecipientName = $(this).data('recipient-name');

            // Update UI
            $('#chatTitle').text('Chat with ' + currentRecipientName);
            $('#currentRecipientId').val(currentRecipientId);
            $('#currentRecipientType').val(currentRecipientType);
            $('#messageInputContainer').show();

            // Load messages
            loadMessages();

            // Start auto-refresh
            startMessageRefresh();
        });

        // Send message
        $('#messageForm').submit(function (e) {
            e.preventDefault();

            const message = $('#messageText').val().trim();
            if (!message || !currentRecipientId) return;

            // Send message via AJAX
            $.ajax({
                url: '../shared/send_message.php',
                type: 'POST',
                data: {
                    recipient_id: currentRecipientId,
                    recipient_type: currentRecipientType,
                    message: message
                },
                success: function (response) {
                    if (response.success) {
                        $('#messageText').val('');
                        loadMessages();
                    } else {
                        alert('Error sending message: ' + response.message);
                    }
                },
                error: function () {
                    alert('Error sending message. Please try again.');
                }
            });
        });

        // Load messages
        function loadMessages() {
            if (!currentRecipientId) return;

            $.ajax({
                url: '../shared/get_messages.php',
                type: 'GET',
                data: {
                    user_id: '<?php echo $admin_id; ?>',
                    user_type: 'admin',
                    other_id: currentRecipientId,
                    other_type: currentRecipientType
                },
                success: function (response) {
                    if (response.success) {
                        displayMessages(response.messages);
                        markMessagesAsRead();
                    }
                }
            });
        }

        // Display messages
        function displayMessages(messages) {
            const container = $('#messagesContainer');
            container.empty();

            if (messages.length === 0) {
                container.html('<div class="text-center text-muted mt-5"><p>No messages yet. Start the conversation!</p></div>');
                return;
            }

            messages.forEach(function (msg) {
                const isOwn = msg.sender_id === '<?php echo $admin_id; ?>' && msg.sender_type === 'admin';
                const messageHtml = `
                <div class="d-flex ${isOwn ? 'justify-content-end' : 'justify-content-start'} mb-3">
                    <div class="message-bubble ${isOwn ? 'bg-primary text-white' : 'bg-white border'}" 
                         style="max-width: 70%; padding: 10px 15px; border-radius: 18px; word-wrap: break-word;">
                        <div class="message-content">${escapeHtml(msg.content)}</div>
                        <div class="message-time text-${isOwn ? 'light' : 'muted'} small mt-1">
                            ${formatTime(msg.created_at)}
                            ${isOwn ? `<i class="fas fa-${msg.status === 'read' ? 'check-double' : 'check'} ml-1"></i>` : ''}
                        </div>
                    </div>
                </div>
            `;
                container.append(messageHtml);
            });

            // Scroll to bottom
            container.scrollTop(container[0].scrollHeight);
        }

        // Mark messages as read
        function markMessagesAsRead() {
            if (!currentRecipientId) return;

            $.ajax({
                url: '../shared/mark_read.php',
                type: 'POST',
                data: {
                    user_id: '<?php echo $admin_id; ?>',
                    user_type: 'admin',
                    other_id: currentRecipientId,
                    other_type: currentRecipientType
                }
            });
        }

        // Auto-refresh messages
        let refreshInterval;
        function startMessageRefresh() {
            if (refreshInterval) clearInterval(refreshInterval);
            refreshInterval = setInterval(loadMessages, 3000);
        }

        // Utility functions
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;

            if (diff < 86400000) { // Less than 24 hours
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            } else {
                return date.toLocaleDateString();
            }
        }

        // Stop refresh when leaving page
        $(window).on('beforeunload', function () {
            if (refreshInterval) clearInterval(refreshInterval);
        });
    </script>

    <style>
        .message-bubble {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .recipient-item.active {
            background-color: #e3f2fd;
            border-left: 4px solid #007bff;
        }

        #messagesContainer::-webkit-scrollbar {
            width: 6px;
        }

        #messagesContainer::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #messagesContainer::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        #messagesContainer::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</body>

</html>