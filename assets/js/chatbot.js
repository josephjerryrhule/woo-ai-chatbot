jQuery(document).ready(function ($) {
  const chatbot = {
    init: function () {
      this.chatWidget = $("#woo-ai-chatbot");
      this.messageList = this.chatWidget.find(".chat-messages");
      this.input = this.chatWidget.find("input");
      this.sendButton = this.chatWidget.find('button[type="submit"]');
      this.minimizeButton = this.chatWidget.find(".minimize-btn");

      this.bindEvents();
    },

    bindEvents: function () {
      this.sendButton.on("click", () => this.sendMessage());
      this.input.on("keypress", (e) => {
        if (e.which === 13) this.sendMessage();
      });
      this.minimizeButton.on("click", () => this.toggleChat());
    },

    sendMessage: function () {
      const message = this.input.val().trim();
      if (!message) return;

      // Add user message to chat
      this.addMessage(message, "user");
      this.input.val("").focus();

      // Send to server via GET request with query parameters
      $.ajax({
        url: wooAIChatbot.rest_url + "chat", // Ensure the correct endpoint
        method: "GET", // Use GET method instead of POST
        headers: {
          "X-WP-Nonce": wooAIChatbot.nonce,
        },
        data: { message: message }, // Send message as a query parameter
        success: (response) => this.handleResponse(response),
        error: () => this.handleError(),
      });
    },

    handleResponse: function (response) {
      let content = "";

      if (response.error) {
        content = response.error;
      } else {
        content = response.content; // Assume content is a simple string message
      }

      this.addMessage(content, "bot");
    },

    handleError: function () {
      this.addMessage(
        "Sorry, I encountered an error. Please try again later.",
        "bot"
      );
    },

    addMessage: function (content, sender) {
      const messageDiv = $("<div>")
        .addClass("message")
        .addClass(sender)
        .html(content);

      this.messageList.append(messageDiv);
      this.messageList.scrollTop(this.messageList[0].scrollHeight);
    },

    toggleChat: function () {
      this.chatWidget.toggleClass("minimized");
    },
  };

  chatbot.init();
});
