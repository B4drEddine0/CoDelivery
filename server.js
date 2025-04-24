const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();
app.use(cors());

const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: "*", // In production, restrict this to your domain
        methods: ["GET", "POST"]
    },
    connectionStateRecovery: {
        // the backup duration of the sessions and the packets
        maxDisconnectionDuration: 2 * 60 * 1000,
        // whether to skip middlewares upon successful recovery
        skipMiddlewares: true,
    }
});

// Debug middleware to log all events
io.use((socket, next) => {
    console.log(`New connection: ${socket.id}`);
    next();
});

// Store active connections and location updates
const activeCommands = new Map();
const locationCache = new Map();

io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);
    
    // Send connection confirmation
    socket.emit('connected', { socketId: socket.id });
    
    // Join a command room
    socket.on('joinCommand', (data) => {
        const { commandId, userRole } = data;
        const roomName = `command_${commandId}`;
        
        console.log(`Socket ${socket.id} joined command room ${roomName} as ${userRole}`);
        socket.join(roomName);
        
        // Store user role with socket
        socket.data.userRole = userRole;
        socket.data.commandId = commandId;
        
        // Add to active commands
        if (!activeCommands.has(commandId)) {
            activeCommands.set(commandId, new Map());
        }
        activeCommands.get(commandId).set(socket.id, userRole);
        
        // Confirm room join
        socket.emit('joinedCommand', { 
            commandId: commandId,
            roomName: roomName,
            userRole: userRole
        });
        
        // Notify others in the room
        socket.to(roomName).emit('userJoined', {
            socketId: socket.id,
            userRole: userRole,
            timestamp: new Date().toISOString()
        });
        
        // Log active rooms
        console.log('Active commands:', Array.from(activeCommands.keys()));
        console.log(`Command ${commandId} has ${activeCommands.get(commandId).size} connections`);
        
        // Send the latest location updates if available
        if (locationCache.has(commandId)) {
            const cachedData = locationCache.get(commandId);
            
            // Send livreur location to client
            if (userRole === 'client' && cachedData.livreur) {
                socket.emit('locationUpdate', {
                    userRole: 'livreur',
                    ...cachedData.livreur,
                    cached: true
                });
            }
            
            // Send client location to livreur
            if (userRole === 'livreur' && cachedData.client) {
                socket.emit('locationUpdate', {
                    userRole: 'client',
                    ...cachedData.client,
                    cached: true
                });
            }
        }
    });
    
    // Handle test messages
    socket.on('test', (data) => {
        console.log('Received test message:', data);
        socket.emit('testResponse', { received: true, data: data });
    });
    
    // Handle location updates
    socket.on('locationUpdate', (data) => {
        const { commandId, latitude, longitude, userRole } = data;
        
        // Validate data
        if (!commandId || !latitude || !longitude || !userRole) {
            console.error('Invalid location update data:', data);
            return;
        }
        
        console.log(`Location update from ${userRole} for command ${commandId}: ${latitude}, ${longitude}`);
        
        // Store in cache
        if (!locationCache.has(commandId)) {
            locationCache.set(commandId, {});
        }
        
        // Update cache for this role
        locationCache.get(commandId)[userRole] = {
            commandId,
            latitude,
            longitude,
            timestamp: new Date().toISOString()
        };
        
        const roomName = `command_${commandId}`;
        
        // Get room size
        const room = io.sockets.adapter.rooms.get(roomName);
        const roomSize = room ? room.size : 0;
        
        console.log(`Broadcasting ${userRole} location update to ${roomSize} clients in room ${roomName}`);
        
        // Broadcast to all clients in the room
        socket.to(roomName).emit('locationUpdate', {
            commandId,
            latitude,
            longitude,
            userRole,
            timestamp: new Date().toISOString()
        });
    });
    
    // Handle client messages
    socket.on('sendMessage', (data) => {
        const { commandId, message, userRole } = data;
        
        if (!commandId || !message || !userRole) {
            console.error('Invalid message data:', data);
            return;
        }
        
        const roomName = `command_${commandId}`;
        console.log(`Message from ${userRole} in room ${roomName}: ${message}`);
        
        // Broadcast to all clients in the room
        io.to(roomName).emit('newMessage', {
            commandId,
            message,
            userRole,
            timestamp: new Date().toISOString(),
            socketId: socket.id
        });
    });
    
    // Handle disconnection
    socket.on('disconnect', () => {
        console.log('User disconnected:', socket.id);
        
        // Get command ID and user role from socket data
        const { commandId, userRole } = socket.data;
        
        if (commandId && activeCommands.has(commandId)) {
            // Remove from active commands
            activeCommands.get(commandId).delete(socket.id);
            console.log(`Removed socket ${socket.id} (${userRole}) from command ${commandId}`);
            
            // Notify others in the room
            const roomName = `command_${commandId}`;
            socket.to(roomName).emit('userLeft', {
                socketId: socket.id,
                userRole,
                timestamp: new Date().toISOString()
            });
            
            // Clean up empty commands
            if (activeCommands.get(commandId).size === 0) {
                activeCommands.delete(commandId);
                locationCache.delete(commandId);
                console.log(`Removed empty command ${commandId} and its cache`);
            }
        }
    });
});

// Add a simple status endpoint
app.get('/', (req, res) => {
    // Return active connections and rooms information
    const activeRooms = [];
    activeCommands.forEach((sockets, commandId) => {
        activeRooms.push({
            commandId: commandId,
            connections: Array.from(sockets)
        });
    });
    
    res.json({
        status: 'CoDelivery Socket.IO Server is running',
        connections: io.engine.clientsCount,
        activeCommands: activeRooms
    });
});

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        connections: io.engine.clientsCount,
        activeCommands: Array.from(activeCommands.keys()),
        uptime: process.uptime()
    });
});

// Stats endpoint
app.get('/stats', (req, res) => {
    const stats = {
        connections: io.engine.clientsCount,
        activeCommands: {}
    };
    
    // Get detailed stats for each command
    for (const [commandId, sockets] of activeCommands.entries()) {
        const roles = {};
        sockets.forEach((role, socketId) => {
            roles[role] = (roles[role] || 0) + 1;
        });
        
        stats.activeCommands[commandId] = {
            total: sockets.size,
            roles
        };
    }
    
    res.json(stats);
});

// Start the server
const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
    console.log(`Socket.IO server running on port ${PORT}`);
    console.log(`Server status: http://localhost:${PORT}`);
});
