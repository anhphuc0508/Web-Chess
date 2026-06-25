<nav class="sidebar">
    <div class="top-menu">
        <a href="homepage.php" class="sidebar-logo">♟︎CHESS</a>
        <a href="ranking.php" class="nav-item">Xếp hạng</a>
        <a href="history.php" class="nav-item">Lịch sử thi đấu</a>
    </div>

    <div class="bottom-menu" style="position: relative;">
        <a href="#" class="icon-btn" title="Hộp thư" id="btn-mailbox" style="position: relative;">
            <span id="challenge-badge" style="display: none; position: absolute; top: -5px; right: -5px; background: #e63f3f; color: white; font-size: 10px; padding: 2px 5px; border-radius: 50%; font-weight: bold;">0</span>
            <i class="bi bi-envelope-fill" style="color: #888;"></i>
        </a>

        <div id="mailboxPopup" class="custom-popup" style="bottom: 60px; left: 60px; width: 280px; background-color: #262421; padding: 15px; border-radius: 10px; border: 1px solid #3d3b39; box-shadow: 0 5px 15px rgba(0,0,0,0.5);">
            <h6 style="color: #81b64c; border-bottom: 1px solid #3d3b39; padding-bottom: 10px; margin-bottom: 10px;"><i class="bi bi-envelope-open-fill"></i> Lời mời thách đấu</h6>
            <div id="challengeResultArea" style="max-height: 300px; overflow-y: auto;">
                <div class="empty-state text-center text-muted" style="margin-top: 20px; font-size: 13px;">Không có lời mời nào</div>
            </div>
        </div>

        <a href="#" class="icon-btn" title="Bạn bè" id="friend-settings" style="position: relative;">
            <span id="friend-badge" style="display: none; position: absolute; top: -5px; right: -5px; background: red; color: white; font-size: 10px; padding: 2px 5px; border-radius: 50%; font-weight: bold;">0</span>
            <i class="bi bi-people-fill " style="color: #888;"></i>
        </a>

        <div id="friendsPopup" class="custom-popup">
            <div class="popup-tabs">
                <div class="tab active" onclick="switchTab(event, 'friendsTab')">Bạn bè</div>
                <div class="tab" onclick="switchTab(event, 'requestsTab')">Yêu cầu</div>
            </div>
            <div id="friendsTab" class="tab-content active">
                <div class="search-container">
                    <i class="bi bi-search" style="color: #888; font-size: 14px; margin-right: 10px;"></i>
                    <input type="text" placeholder="Tìm kiếm bạn bè" id="friendSearchInput">
                </div>
                <div id="friendsResultArea">
                    <div class="empty-state">
                        <h6>Chơi cờ vua sẽ vui hơn khi chơi cùng bạn bè</h6>
                    </div>
                </div>
            </div>
            <div id="requestsTab" class="tab-content">
                <div class="empty-state" style="margin-top: 60px;">
                    <h4>Không có yêu cầu mới</h4>
                </div>
            </div>
        </div>

        <a href="#" class="icon-btn" title="Cài đặt" id="btn-settings">
            <i class="bi bi-gear-fill" style="color: #888;"></i>
        </a>
        <div id="settingsPopup" class="settings-popup">
            <ul style="list-style: none; padding: 10px; margin: 0;">
                <li><a href="profile.php" style="color: #ccc; text-decoration: none;">Hồ sơ</a></li>
                <li class="divider" style="height: 1px; background: #3d3b39; margin: 5px 0;"></li>
                <li><a href="logout.php" class="text-danger" style="text-decoration: none;">Đăng xuất</a></li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>

<script>
    const btnFriend = document.getElementById('friend-settings');
    const friendPopup = document.getElementById('friendsPopup');
    const btnSettings = document.getElementById('btn-settings');
    const settingsPopup = document.getElementById('settingsPopup');
    const btnMailbox = document.getElementById('btn-mailbox');
    const mailboxPopup = document.getElementById('mailboxPopup');
    const searchInput = document.getElementById('friendSearchInput');
    const resultsArea = document.getElementById('friendsResultArea');

    function setupToggle(btn, popup, others) {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            others.forEach(p => {
                p.classList.remove('show');
                p.style.display = 'none';
            });

            const isVisible = popup.classList.contains('show') || popup.style.display === 'block';
            if (isVisible) {
                popup.classList.remove('show');
                popup.style.display = 'none';
            } else {
                popup.classList.add('show');
                popup.style.display = 'block';
                if (popup.id === 'friendsPopup') loadFriends();
            }
        });
    }


    setupToggle(btnFriend, friendPopup, [settingsPopup, mailboxPopup]);
    setupToggle(btnSettings, settingsPopup, [friendPopup, mailboxPopup]);
    setupToggle(btnMailbox, mailboxPopup, [friendPopup, settingsPopup]);

    window.addEventListener('click', (e) => {
        if (!friendPopup.contains(e.target) && !btnFriend.contains(e.target)) {
            friendPopup.classList.remove('show');
            friendPopup.style.display = 'none';
        }
        if (!settingsPopup.contains(e.target) && !btnSettings.contains(e.target)) {
            settingsPopup.classList.remove('show');
            settingsPopup.style.display = 'none';
        }
        if (!mailboxPopup.contains(e.target) && !btnMailbox.contains(e.target)) {
            mailboxPopup.style.display = 'none';
        }
    });

    function switchTab(event, tabId) {
        document.querySelectorAll('.popup-tabs .tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        event.currentTarget.classList.add('active');
        document.getElementById(tabId).classList.add('active');
        if (tabId === 'requestsTab') loadRequests();
        if (tabId === 'friendsTab') loadFriends();
    }

    function loadFriends() {
        if (searchInput.value.trim().length >= 2) return;
        fetch('../api/api_get_friends.php').then(res => res.json()).then(data => {
            if (data.length === 0) {
                resultsArea.innerHTML = '<div class="empty-state"><h6>Chơi cờ vua sẽ vui hơn khi chơi cùng bạn bè</h6></div>';
                return;
            }
            let html = '<div class="friends-list" style="margin-top: 10px;">';
            data.forEach(friend => {
                html += `
                <div class="user-item d-flex align-items-center justify-content-between p-2 mb-2" style="background:#312e2b; border-radius:5px;">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${friend.avatar || 'default_avatar.png'}" style="width:35px; height:35px; border-radius:50%; object-fit: cover;">
                        <div>
                            <div style="font-size: 14px; font-weight: bold; color:white;">${friend.nickname || friend.username}</div>
                            <div style="font-size: 11px; color: #81b64c;">⭐ ${friend.elo}</div>
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-challenge-trigger" style="color:white; border:none; background-color:#81b64c; width:40px" title="Thách đấu" data-id="${friend.id}">⚔️</button>
                        <button class="btn btn-sm btn-unfriend-trigger" style="color:white; border:none; background-color:#e63f3f; width:40px" title="Hủy kết bạn" data-id="${friend.id}"><i class="bi bi-person-x-fill"></i></button>
                    </div>
                </div>`;
            });
            resultsArea.innerHTML = html + '</div>';
            document.querySelectorAll('.btn-challenge-trigger').forEach(btn => {
                btn.onclick = () => challengeFriend(btn.getAttribute('data-id'));
            });
            document.querySelectorAll('.btn-unfriend-trigger').forEach(btn => {
                btn.onclick = () => {
                    if (confirm('Bạn có chắc chắn muốn hủy kết bạn?')) {
                        fetch('../api/api_unfriend.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'friend_id=' + btn.getAttribute('data-id')
                        }).then(res => res.json()).then(result => {
                            if (result.success) loadFriends();
                            else alert('Có lỗi xảy ra!');
                        });
                    }
                };
            });
        });
    }

    let searchTimeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = searchInput.value.trim();
        if (query.length < 2) {
            loadFriends();
            return;
        }
        searchTimeout = setTimeout(() => {
            fetch('../api/api_search_users.php?query=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        resultsArea.innerHTML = '<div class="empty-state"><h6>Không tìm thấy ai</h6></div>';
                        return;
                    }
                    let html = '<div style="margin-top: 10px;">';
                    data.forEach(user => {
                        html += `
                        <div class="user-item d-flex align-items-center justify-content-between p-2 mb-2" style="background:#312e2b; border-radius:5px;">
                            <div class="d-flex align-items-center gap-2">
                                <img src="${user.avatar || 'default_avatar.png'}" style="width:35px; height:35px; border-radius:50%; object-fit: cover;">
                                <div>
                                    <div style="font-size: 14px; font-weight: bold; color:white;">${user.nickname || user.username}</div>
                                    <div style="font-size: 11px; color: #81b64c;">⭐ ${user.elo}</div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-add-friend" style="color:white; border:none; background-color:#81b64c;" data-id="${user.id}">+ Kết bạn</button>
                        </div>`;
                    });
                    resultsArea.innerHTML = html + '</div>';
                    document.querySelectorAll('.btn-add-friend').forEach(btn => {
                        btn.onclick = () => {
                            fetch('../api/api_add_friend.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'receiver_id=' + btn.getAttribute('data-id')
                            }).then(res => res.json()).then(result => {
                                if (result.success) {
                                    btn.textContent = '✓ Đã gửi';
                                    btn.disabled = true;
                                    btn.style.backgroundColor = '#555';
                                } else {
                                    alert('Không thể gửi lời mời!');
                                }
                            });
                        };
                    });
                });
        }, 300);
    });

    function loadRequests() {
        fetch('../api/api_get_requests.php').then(res => res.json()).then(data => {
            const requestsTab = document.getElementById('requestsTab');
            if (data.length === 0) {
                requestsTab.innerHTML = '<div class="empty-state" style="margin-top: 60px;"><h4>Không có yêu cầu mới</h4></div>';
                return;
            }
            let html = '<div style="margin-top: 10px;">';
            data.forEach(req => {
                html += `
                <div class="user-item d-flex align-items-center justify-content-between p-2 mb-2" style="background:#312e2b; border-radius:5px;">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${req.avatar || 'default_avatar.png'}" style="width:35px; height:35px; border-radius:50%; object-fit: cover;">
                        <div style="font-size: 14px; font-weight: bold; color:white;">${req.nickname || req.username}</div>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-accept-req" style="background-color:#81b64c; color:white; border:none;" data-id="${req.request_id}">✓</button>
                        <button class="btn btn-sm btn-reject-req" style="background-color:#e63f3f; color:white; border:none;" data-id="${req.request_id}">✕</button>
                    </div>
                </div>`;
            });
            requestsTab.innerHTML = html + '</div>';

            document.querySelectorAll('.btn-accept-req').forEach(btn => {
                btn.onclick = () => {
                    fetch('../api/api_handle_request.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'request_id=' + btn.getAttribute('data-id') + '&action=accept'
                    }).then(() => loadRequests());
                };
            });
            document.querySelectorAll('.btn-reject-req').forEach(btn => {
                btn.onclick = () => {
                    fetch('../api/api_handle_request.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'request_id=' + btn.getAttribute('data-id') + '&action=reject'
                    }).then(() => loadRequests());
                };
            });
        });

        fetch('../api/api_get_requests.php').then(res => res.json()).then(data => {
            const badge = document.getElementById('friend-badge');
            if (data.length > 0) {
                badge.style.display = 'block';
                badge.innerText = data.length;
            } else {
                badge.style.display = 'none';
            }
        });
    }

    const appSocket = io('http://localhost:3000');
    const myUserId = String(<?php echo json_encode($_SESSION['user_id']); ?>);
    const myName = <?php echo json_encode($_SESSION['nickname'] ?? $currentUser['nickname'] ?? $currentUser['username'] ?? 'Người chơi'); ?>;
    const myAvatar = <?php echo json_encode($currentUser['avatar'] ?? 'default_avatar.png'); ?>;

    appSocket.emit('user-online', myUserId);

    function challengeFriend(friendId) {
        const roomId = 'room_' + Date.now();
        appSocket.emit('send-challenge', {
            senderId: myUserId,
            senderName: myName,
            senderAvatar: myAvatar,
            receiverId: friendId,
            roomId: roomId
        });
    }

    appSocket.on('challenge-sent-success', () => {
        alert("Đã gửi lời mời!");
    });

    appSocket.on('challenge-error', (msg) => {
        alert("Lỗi: " + msg);
    });

    appSocket.on('receive-challenge', (data) => {
        document.getElementById('challenge-badge').style.display = 'block';
        document.getElementById('challenge-badge').innerText = "1";
        document.getElementById('challengeResultArea').innerHTML = `
            <div class="p-2 mb-2" style="background:#312e2b; border-radius:5px; border-left: 3px solid #81b64c;" id="challenge-item-${data.roomId}">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <img src="${data.senderAvatar}" style="width:30px; height:30px; border-radius:50%;">
                    <div style="font-size: 13px; color: white;"><b>${data.senderName}</b> mời đấu!</div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm w-50" id="accept-btn-${data.roomId}" style="background-color: #81b64c; color: white;">Chấp nhận</button>
                    <button class="btn btn-sm w-50" id="reject-btn-${data.roomId}" style="background-color: #e63f3f; color: white;">Từ chối</button>
                </div>
            </div>`;
        document.getElementById(`accept-btn-${data.roomId}`).onclick = () => {
            appSocket.emit('accept-challenge', {
                senderId: data.senderId,
                roomId: data.roomId,
                receiverId: myUserId,
                receiverName: myName,
                senderName: data.senderName
            });
        };
        document.getElementById(`reject-btn-${data.roomId}`).onclick = () => {
            appSocket.emit('reject-challenge', {
                senderId: data.senderId,
                roomId: data.roomId,
                receiverId: myUserId,
                receiverName: myName
            });
            const item = document.getElementById(`challenge-item-${data.roomId}`);
            if (item) item.remove();
            
            const area = document.getElementById('challengeResultArea');
            if (area.children.length === 0) {
                area.innerHTML = '<div class="empty-state text-center text-muted" style="margin-top: 20px; font-size: 13px;">Không có lời mời nào</div>';
                document.getElementById('challenge-badge').style.display = 'none';
            }
        };
    });

    appSocket.on('challenge-receiver-ready', (data) => {
        window.location.href = `multiplayer.php?id=${data.roomId}&color=b&opponent=${encodeURIComponent(data.senderName)}`;
    });

    appSocket.on('challenge-accepted', (data) => {
        window.location.href = `multiplayer.php?id=${data.roomId}&color=w&opponent=${encodeURIComponent(data.receiverName)}`;
    });

    appSocket.on('challenge-rejected', (data) => {
        alert(data.receiverName + " đã từ chối lời mời thách đấu của bạn.");
    });
</script>
