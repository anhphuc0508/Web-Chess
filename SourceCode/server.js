const io = require('socket.io')(3000, { cors: { origin: "*" } });
const mysql = require('mysql2');

const db = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'chess_db'
});

let waitingPlayers = [];
const activeMatches = {};

const onlineUsers = new Map();

function closeMatch(matchId) {
    db.query('UPDATE matches SET status = "finished" WHERE id = ?', [matchId]);
    if (activeMatches[matchId]) delete activeMatches[matchId];
}

io.on('connection', (socket) => {

    socket.on('find-match', (data) => {
        waitingPlayers.push({ socketId: socket.id, userId: data.userId, username: data.username, elo: data.elo });
        if (waitingPlayers.length >= 2) {
            const p1 = waitingPlayers.shift();
            const p2 = waitingPlayers.shift();
            const matchId = `room_${Date.now()}`;

            db.query('INSERT INTO matches (id, white_id, black_id, move_history, status) VALUES (?, ?, ?, ?, "playing")',
                [matchId, p1.userId, p2.userId, JSON.stringify([])]);

            io.to(p1.socketId).emit('match-found', { matchId: matchId, color: 'w', opponentName: p2.username, opponentElo: p2.elo });
            io.to(p2.socketId).emit('match-found', { matchId: matchId, color: 'b', opponentName: p1.username, opponentElo: p1.elo });
        }
    });

    socket.on('join-room', (matchId) => {
        socket.join(matchId);
        if (!activeMatches[matchId]) {
            db.query('SELECT * FROM matches WHERE id = ? AND status = "playing"', [matchId], (err, results) => {
                if (results && results.length > 0) {
                    activeMatches[matchId] = {
                        history: JSON.parse(results[0].move_history || "[]"),
                        players: 1
                    };
                    socket.emit('sync-board', activeMatches[matchId].history);
                }
            });
        } else {
            activeMatches[matchId].players++;
            if (activeMatches[matchId].timer) clearTimeout(activeMatches[matchId].timer);
            socket.to(matchId).emit('opponent-reconnected');
            socket.emit('sync-board', activeMatches[matchId].history);
        }
    });

    socket.on('send-move', (data) => {
        if (activeMatches[data.matchId]) {
            activeMatches[data.matchId].history.push(data.move);
            db.query('UPDATE matches SET move_history = ? WHERE id = ?',
                [JSON.stringify(activeMatches[data.matchId].history), data.matchId]);
        }
        socket.to(data.matchId).emit('receive-move', data.move);
    });

    socket.on('match-finished', (matchId) => {
        closeMatch(matchId);
    });

    socket.on('resign', (matchId) => {
        socket.to(matchId).emit('opponent-resigned');
        closeMatch(matchId);
    });
    socket.on('offer-draw', (matchId) => { socket.to(matchId).emit('draw-offered'); });
    socket.on('accept-draw', (matchId) => {
        socket.to(matchId).emit('draw-accepted');
        closeMatch(matchId);
    });
    socket.on('decline-draw', (matchId) => { socket.to(matchId).emit('draw-declined'); });

    socket.on('disconnecting', () => {
        socket.rooms.forEach(room => {
            if (room !== socket.id) {
                if (activeMatches[room]) {
                    activeMatches[room].players--;
                    socket.to(room).emit('opponent-disconnected-temp');

                    activeMatches[room].timer = setTimeout(() => {
                        if (activeMatches[room] && activeMatches[room].players < 2) {
                            socket.to(room).emit('opponent-abandoned');
                            closeMatch(room);
                        }
                    }, 60000);
                }
            }
        });
    });

    socket.on('user-online', (userId) => {
        onlineUsers.set(userId.toString(), socket.id);
    });

    socket.on('send-challenge', (data) => {
        const receiverSocketId = onlineUsers.get(data.receiverId.toString());

        if (receiverSocketId) {
            io.to(receiverSocketId).emit('receive-challenge', data);
            socket.emit('challenge-sent-success');
        } else {
            socket.emit('challenge-error', 'Người chơi này đang offline hoặc không trong sảnh!');
        }
    });

    socket.on('accept-challenge', (data) => {
        const senderSocketId = onlineUsers.get(data.senderId.toString());

        db.query(
            'INSERT INTO matches (id, white_id, black_id, move_history, status) VALUES (?, ?, ?, ?, "playing")',
            [data.roomId, data.senderId, data.receiverId, JSON.stringify([])],
            (err, results) => {
                if (err) {
                    console.error("Lỗi khi tạo trận thách đấu:", err);
                    socket.emit('challenge-error', 'Không thể tạo trận đấu, vui lòng thử lại!');
                    return;
                }

                if (senderSocketId) {
                    io.to(senderSocketId).emit('challenge-accepted', data);
                }


                io.to(socket.id).emit('challenge-receiver-ready', data);
            }
        );
    });

    socket.on('reject-challenge', (data) => {
        const senderSocketId = onlineUsers.get(data.senderId.toString());
        if (senderSocketId) {
            io.to(senderSocketId).emit('challenge-rejected', data);
        }
    });

    socket.on('disconnect', () => {

        waitingPlayers = waitingPlayers.filter(p => p.socketId !== socket.id);

        for (let [userId, sId] of onlineUsers.entries()) {
            if (sId === socket.id) {
                onlineUsers.delete(userId);
                break;
            }
        }
    });
});