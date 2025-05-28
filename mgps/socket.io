const server = httpServer.listen(PORT, () => {
	console.log("info", `\x1b[33m \x1b[1m Server is running in ${NODE_ENV} mode on port ${PORT} \x1b[0m`);

	// logger.info({ a: 123, v: 456 });
	io.on("connection", (socket: any) => {
		//console.log("info", "new socket user" + socket.id);
		socket.on("approval", (message: any) => {
			socket.broadcast.emit("messageSent", message);
			console.log(message);
		});
		socket.on("send_notification", (message: any) => {
			socket.emit("messageSent", message);
			console.log(message);
			if (!message || !message) {
				console.error("Invalid message payload", message);
				return;
			}
		});

		socket.on("notificationList", async (userId: string) => {		

			const userObjectId = new mongoose.Types.ObjectId(userId);

			const notif = await notifactionModel.find({ member_id: userObjectId }).lean();
			socket.emit("messageSent", notif);
			console.log(notif);
			
		});
	});
});







How to send Real-Time Notifications with Node.js + React & mongoDB| Bell Icon Using Socket.IO



To send **real-time notifications** using **Node.js**, **MongoDB**, **React**, and **Socket.IO**, complete with a **bell icon**, follow this full setup:

---

## 🔧 Tech Stack:

* **Backend**: Node.js + Express + MongoDB + Socket.IO
* **Frontend**: React + TypeScript (or JavaScript)
* **Real-time**: Socket.IO (bi-directional event-based communication)

---

## 📦 Step 1: Backend Setup (Node.js + MongoDB + Socket.IO)

### 1.1 Install Required Packages

```bash
npm install express socket.io mongoose cors
npm install -D typescript ts-node-dev @types/node @types/express @types/socket.io
```

### 1.2 Create `server.ts`

```ts
import express from 'express';
import http from 'http';
import mongoose from 'mongoose';
import cors from 'cors';
import { Server } from 'socket.io';
import Notification from './models/Notification';

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
  cors: { origin: "*" }
});

app.use(cors());
app.use(express.json());

// MongoDB connection
mongoose.connect('mongodb://localhost:27017/notify');

// Handle socket connection
io.on("connection", (socket) => {
  console.log("User connected:", socket.id);

  socket.on("send_notification", async (data) => {
    const notification = await Notification.create(data);
    io.emit("receive_notification", notification);
  });

  socket.on("disconnect", () => {
    console.log("User disconnected:", socket.id);
  });
});

server.listen(5000, () => console.log("Server running on http://localhost:5000"));
```

---

### 1.3 `models/Notification.ts`

```ts
import mongoose from "mongoose";

const notificationSchema = new mongoose.Schema({
  userId: String,
  message: String,
  read: { type: Boolean, default: false },
  createdAt: { type: Date, default: Date.now }
});

export default mongoose.model("Notification", notificationSchema);
```

---

## ⚛️ Step 2: Frontend Setup (React + Socket.IO + Bell Icon)

### 2.1 Install Packages

```bash
npm install socket.io-client react-icons
```

---

### 2.2 `src/socket.ts`

```ts
import { io } from "socket.io-client";
const socket = io("http://localhost:5000");
export default socket;
```

---

### 2.3 `NotificationBell.tsx`

```tsx
import React, { useEffect, useState } from "react";
import socket from "./socket";
import { FaBell } from "react-icons/fa";

interface Notification {
  _id: string;
  message: string;
  userId: string;
  read: boolean;
  createdAt: string;
}

const NotificationBell: React.FC = () => {
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    socket.on("receive_notification", (data: Notification) => {
      setNotifications((prev) => [data, ...prev]);
    });

    return () => {
      socket.off("receive_notification");
    };
  }, []);

  const unreadCount = notifications.filter(n => !n.read).length;

  return (
    <div className="relative">
      <button onClick={() => setOpen(!open)} className="relative text-2xl">
        <FaBell />
        {unreadCount > 0 && (
          <span className="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
            {unreadCount}
          </span>
        )}
      </button>

      {open && (
        <div className="absolute top-8 right-0 bg-white shadow-lg rounded w-72 p-3 z-10">
          <h4 className="font-bold mb-2">Notifications</h4>
          <ul>
            {notifications.map((n) => (
              <li key={n._id} className="text-sm border-b py-1">{n.message}</li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
};

export default NotificationBell;
```

---

### 2.4 Trigger Notification (e.g., from Admin Panel)

```tsx
const sendNotif = () => {
  socket.emit("send_notification", {
    userId: "user123",
    message: "You have a new message!"
  });
};
```

---

## ✅ Final Notes

| Feature           | How                              |
| ----------------- | -------------------------------- |
| Realtime Delivery | `socket.emit()` + `socket.on()`  |
| Bell Icon         | `react-icons` (`FaBell`)         |
| Notification List | Rendered from state              |
| Backend Storage   | MongoDB via Mongoose             |
| Typed Safety      | Typescript interfaces (optional) |

---

### 💡 Optional Features to Add:

* Mark as read/unread
* Fetch past notifications on page load
* Private notifications using `socket.to(userId).emit()`

---

Would you like me to export this as a working GitHub template or help add private notification support with user-specific rooms?
















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
