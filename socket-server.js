import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';
import cors from 'cors';

const app = express();
app.use(cors());

const server = createServer(app);
const io = new Server(server, {
  cors: {
    origin: "*", // Allow all origins in development
    methods: ["GET", "POST"]
  }
});

// Store active rooms and their members
const rooms = {};

io.on('connection', (socket) => {
  console.log('A user connected:', socket.id);

  // Handle joining a specific command room
  socket.on('join-room', (roomId) => {
    console.log(`User ${socket.id} joined room ${roomId}`);
    socket.join(roomId);
    
    if (!rooms[roomId]) {
      rooms[roomId] = { users: [] };
    }
    rooms[roomId].users.push(socket.id);
  });

  // Handle location updates and broadcast to room members
  socket.on('location-update', (data) => {
    console.log(`Location update in room ${data.roomId}:`, data);
    // Broadcast to everyone in the room except the sender
    socket.to(data.roomId).emit('location-update', data);
  });

  // Handle disconnection
  socket.on('disconnect', () => {
    console.log('User disconnected:', socket.id);
    // Remove user from all rooms they were in
    Object.keys(rooms).forEach(roomId => {
      rooms[roomId].users = rooms[roomId].users.filter(id => id !== socket.id);
      // Clean up empty rooms
      if (rooms[roomId].users.length === 0) {
        delete rooms[roomId];
      }
    });
  });
});

// Serve a simple status page
app.get('/', (req, res) => {
  res.send('CoDelivery Socket.io Server is running');
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
  console.log(`Socket.io server running on port ${PORT}`);
});
