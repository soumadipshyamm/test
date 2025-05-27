How to send Real-Time Notifications with Node.js + React & mongoDB| Bell Icon Using Socket.IO


To implement **notifications using `Socket.IO` with Node.js, ReactJS, MongoDB, and TypeScript**, here’s a full setup including:

---

### ✅ **Project Structure Overview**

```
backend/
├── server.ts                # Express + Socket.IO server
├── sockets/notification.ts # Socket functions
├── models/Notification.ts  # MongoDB model
└── ...                     # MongoDB connection, etc.

frontend/
├── src/
│   ├── socket.ts           # Socket client setup
│   └── Notification.tsx    # Component to receive/display notifications
```

---

## 🧠 1. BACKEND: Node.js + TypeScript + MongoDB + Socket.IO

### 📦 Install Packages

```bash
npm install express socket.io mongoose cors
npm install -D typescript ts-node-dev @types/node @types/express @types/socket.io
```

### 📄 `server.ts`

```ts
import express from "express";
import http from "http";
import { Server } from "socket.io";
import cors from "cors";
import mongoose from "mongoose";
import notificationSocket from "./sockets/notification";

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
  cors: { origin: "*" },
});

app.use(cors());
app.use(express.json());

// Connect to MongoDB
mongoose.connect("mongodb://localhost:27017/socket-notif");

// Initialize Socket.IO listeners
io.on("connection", (socket) => {
  console.log("Client connected:", socket.id);
  notificationSocket(socket, io);
});

server.listen(5000, () => console.log("Server running on http://localhost:5000"));
```

---

### 📄 `models/Notification.ts`

```ts
import mongoose from "mongoose";

const notificationSchema = new mongoose.Schema({
  userId: String,
  message: String,
  read: { type: Boolean, default: false },
  createdAt: { type: Date, default: Date.now },
});

export default mongoose.model("Notification", notificationSchema);
```

---

### 📄 `sockets/notification.ts`

```ts
import { Server, Socket } from "socket.io";
import Notification from "../models/Notification";

const notificationSocket = (socket: Socket, io: Server) => {
  // Send notification
  socket.on("send_notification", async ({ userId, message }) => {
    const notif = await Notification.create({ userId, message });
    io.emit("receive_notification", notif); // or `socket.to(userId).emit()` for private
  });
};

export default notificationSocket;
```

---

## 🧠 2. FRONTEND: React + TypeScript + Socket.IO

### 📦 Install

```bash
npm install socket.io-client
npm install --save-dev @types/socket.io-client
```

---

### 📄 `src/socket.ts`

```ts
import { io } from "socket.io-client";

const socket = io("http://localhost:5000");

export default socket;
```

---

### 📄 `src/Notification.tsx`

```tsx
import React, { useEffect, useState } from "react";
import socket from "./socket";

interface Notification {
  _id: string;
  message: string;
  userId: string;
  read: boolean;
  createdAt: string;
}

const NotificationComponent: React.FC = () => {
  const [notifications, setNotifications] = useState<Notification[]>([]);

  useEffect(() => {
    socket.on("receive_notification", (data: Notification) => {
      setNotifications((prev) => [data, ...prev]);
    });

    return () => {
      socket.off("receive_notification");
    };
  }, []);

  return (
    <div>
      <h2>Notifications</h2>
      <ul>
        {notifications.map((n) => (
          <li key={n._id}>{n.message}</li>
        ))}
      </ul>
    </div>
  );
};

export default NotificationComponent;
```

---

### 📄 To Send Notification (e.g., from admin):

```tsx
const sendNotif = () => {
  socket.emit("send_notification", {
    userId: "123",
    message: "This is a test notification",
  });
};
```

---

## ✅ Summary

| Layer      | Key Part                           |
| ---------- | ---------------------------------- |
| Backend    | `send_notification`, MongoDB save  |
| Frontend   | `receive_notification` listener    |
| DB         | Store notification for persistence |
| TypeScript | Types for safety + autocomplete    |

---

Let me know if you want:

* Private/user-specific notifications
* Notification read/unread toggle
* Toast-style popup notifications in React (e.g., using `react-toastify`)
