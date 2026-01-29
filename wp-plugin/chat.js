document.addEventListener("DOMContentLoaded", () => {
  const apiUrl = aiChat.apiUrl;
  const clientId = aiChat.clientId; // The business identity
  const messagesDiv = document.getElementById("ai-chat-messages");
  const input = document.getElementById("ai-chat-input");
  const sendBtn = document.getElementById("ai-chat-send");
  const wrapper = document.getElementById("ai-chat-wrapper");
  const container = document.getElementById("ai-chat-container");
  const toggleBtn = document.getElementById("ai-chat-toggle");
  const closeBtn = document.getElementById("ai-chat-close");

  // Toggle Visibility
  function toggleChat() {
    container.classList.toggle('hidden');
    if (!container.classList.contains('hidden')) {
      input.focus();
    }
  }

  function closeChat() {
    container.classList.add('hidden');
  }

  toggleBtn.addEventListener('click', toggleChat);
  closeBtn.addEventListener('click', closeChat);

  // Message Helper
  function appendMessage(sender, text) {
    const msg = document.createElement("div");
    msg.className = `chat-message ${sender}`;
    // Basic markdown-ish formatting (optional, can be improved)
    msg.textContent = text;
    messagesDiv.appendChild(msg);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
  }

  // Initial Greeting
  if (!sessionStorage.getItem("greeted")) {
    appendMessage("ai", "Hello! How can I assist you today?");
    sessionStorage.setItem("greeted", "true");
  }

  // Send Logic
  async function sendQuestion() {
    const question = input.value.trim();
    if (!question) return;

    if (!clientId) {
      appendMessage("ai", "⚠️ This chat widget is not configured properly (Missing Client ID).");
      return;
    }

    appendMessage("user", question);
    input.value = "";
    appendMessage("ai", "..."); // Typing indicator

    try {
      const res = await fetch(`${apiUrl}/ask`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          q_text: question,
          message_group_id: clientId, // Using Business Email as message_group_id
        }),
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.detail || "Error");
      }

      // Remove typing indicator if it's the last child
      removeTypingIndicator();

      // Start polling
      appendMessage("ai", "⏳ Thinking...");
      pollAnswer(data.question_id);

    } catch (err) {
      removeTypingIndicator();
      console.error(err);
      appendMessage("ai", "❌ Sorry, something went wrong.");
    }
  }

  function removeTypingIndicator() {
    // Logic assumes '...' or 'Thinking...' are the indicators. 
    // A robust way knows the specific element ID. For now simple check:
    const last = messagesDiv.lastElementChild;
    if (last && (last.textContent === "..." || last.textContent === "⏳ Thinking...")) {
      last.remove();
    }
  }

  async function pollAnswer(qid) {
    let answered = false;
    const interval = setInterval(async () => {
      try {
        const res = await fetch(`${apiUrl}/get_answer/${qid}`);
        if (!res.ok) return;

        const data = await res.json();
        if (data.status === "answered") {
          removeTypingIndicator();
          appendMessage("ai", data.answer);
          clearInterval(interval);
          answered = true;
        }
      } catch (err) {
        // Silent fail
      }
    }, 2000); // Poll every 2s

    // Timeout after 60s
    setTimeout(() => {
      if (!answered) {
        clearInterval(interval);
        removeTypingIndicator();
        appendMessage("ai", "⌛ Response timed out. Please try again later.");
      }
    }, 60000);
  }

  sendBtn.addEventListener("click", sendQuestion);
  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") sendQuestion();
  });
});
