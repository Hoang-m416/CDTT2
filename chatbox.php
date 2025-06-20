
<!-- Nút mở hộp chat -->
<button id="chatToggleBtn" class="chat-toggle-btn">💬 Nhắn tin với admin</button>

<!-- Hộp chat -->
<div id="chatBox" class="chat-box">
    <div class="chat-header">
        <strong>Hỗ trợ khách hàng</strong>
        <button id="closeChat" class="close-chat">&times;</button>
    </div>
    <div id="chatMessages" class="chat-messages"></div>
    <form id="chatForm" class="chat-form">
        <textarea id="chatMessage" name="message" placeholder="Nhập tin nhắn..."></textarea>
        <button type="submit">Gửi</button>
    </form>
</div>


<script>
    const toggleBtn = document.getElementById("chatToggleBtn");
    const chatBox = document.getElementById("chatBox");
    const chatMessages = document.getElementById("chatMessages");
    const closeBtn = document.getElementById("closeChat");

    let intervalId = null;

    toggleBtn.onclick = () => {
        chatBox.style.display = "flex";
        loadMessages(); // chỉ tải khi mở chat
        if (!intervalId) {
            intervalId = setInterval(loadMessages, 5000); // bắt đầu cập nhật mỗi 5s
        }
    };

    closeBtn.onclick = () => {
        chatBox.style.display = "none";
        clearInterval(intervalId);
        intervalId = null;
    };

    document.getElementById("chatForm").onsubmit = async function (e) {
        e.preventDefault();
        const msg = document.getElementById("chatMessage").value.trim();
        if (msg === "") return;
        const formData = new FormData();
        formData.append("message", msg);
        await fetch("send_message.php", {
            method: "POST",
            body: formData
        });
        document.getElementById("chatMessage").value = "";
        loadMessages();
    };

    async function loadMessages() {
        const res = await fetch("get_messages.php");
        const data = await res.json();
        chatMessages.innerHTML = "";
        data.forEach(msg => {
            const p = document.createElement("p");
            p.className = msg.sender === "admin" ? "admin" : "user";
            p.innerText = msg.message;
            p.style.margin = "5px 0";
            p.style.padding = "8px";
            p.style.borderRadius = "10px";
            p.style.maxWidth = "80%";
            p.style.wordBreak = "break-word";
            if (msg.sender === "admin") {
                p.style.background = "#f1f0f0";
                p.style.alignSelf = "flex-start";
            } else {
                p.style.background = "#dcf8c6";
                p.style.alignSelf = "flex-end";
            }
            chatMessages.appendChild(p);
        });
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
</script>
<style>
    /* Nút mở chat */
.chat-toggle-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    background-color: #007bff;
    color: white;
    border: none;
    width: 200px;
    height: 50px;
    padding: 12px 0;
    border-radius: 30px;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    cursor: pointer;
    transition: background 0.3s;
}
.chat-toggle-btn:hover {
    background-color: #0056b3;
}

/* Hộp chat */
.chat-box {
    display: none;
    flex-direction: column;
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 320px;
    height: 450px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    overflow: hidden;
    z-index: 1000;
    font-family: 'Segoe UI', sans-serif;
}

/* Header chat */
.chat-header {
    background-color: #007bff;
    color: white;
    padding: 12px 16px;
    font-size: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Nút đóng */
.close-chat {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}

/* Khu vực tin nhắn */
.chat-messages {
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    background-color: #f9f9f9;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* Tin nhắn */
.chat-messages .user {
    align-self: flex-end;
    background-color: #dcf8c6;
    padding: 10px;
    border-radius: 12px 12px 0 12px;
    max-width: 75%;
    word-break: break-word;
}
.chat-messages .admin {
    align-self: flex-start;
    background-color: #e4e6eb;
    padding: 10px;
    border-radius: 12px 12px 12px 0;
    max-width: 75%;
    word-break: break-word;
}

/* Form chat */
.chat-form {
    padding: 10px;
    border-top: 1px solid #ddd;
    background-color: #fff;
    display: flex;
    flex-direction: column;
}
.chat-form textarea {
    width: 100%;
    height: 60px;
    padding: 8px;
    border-radius: 8px;
    border: 1px solid #ccc;
    resize: none;
    font-size: 14px;
}
.chat-form button {
    margin-top: 8px;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    font-size: 15px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
}
.chat-form button:hover {
    background-color: #0056b3;
}
</style>