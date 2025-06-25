/* global loadTranslationFile, document, showToast, setTimeout, FormData, _l, jQuery, mqtt*/

(function ($) {
    "use strict";

    (async () => {
        await loadTranslationFile("web", "user,common");

        const customerId = $("#messageinput").data("senderid");
        listenMqttForNewMessages(customerId);
        fetchMessages();
    })();

    const userId = $("#messageinput").data("receiverid");
    let offset = "";
    let isLoading = false;
    let lastOffset = "";

    /**
     * Fetch messages from server.
     * @param {boolean} initial - Whether this is the initial fetch.
     * @param {boolean} reset - Whether to reset offsets.
     */
    async function fetchMessages(initial = true, reset = false) {
        if (isLoading || offset === null) {
            return;
        }
        isLoading = true;

        if (reset) {
            offset = "";
            lastOffset = "";
        }

        $.ajax({
            url: "/user/fetch-messages",
            type: "POST",
            data: {
                user_id: userId,
                offset: offset,
                reset: reset,
                last_offset: lastOffset,
                _token: $("meta[name='csrf-token']").attr("content")
            },
            beforeSend: () => {
                if (initial) {
                    $("#messagearea").html("");
                }
            },
            success: (response) => {
                if (response.code === 200 && response.messages.length > 0) {
                    updateMessageView(response.messages, initial);
                    offset = response.next_offset;
                    lastOffset = response.last_offset;
                }

                if (response.last_message) {
                    $(".user-last-chat").text(response.last_message.message);
                    $(".last-chat-time").text(response.last_message.created_at);
                }

                if (offset === null) {
                    $("#messagebody").off("scroll");
                }
                isLoading = false;
            },
            error: () => {
                isLoading = false;
            }
        });
    }

    function updateMessageView(messages, initial) {
        const messageContainer = $("#messagebody");
        const messageArea = $("#messageArea");
        const existingMessages = new Set();

        $(".message-card").each(function () {
            existingMessages.add($(this).data("message-id"));
        });

        const newMessages = messages.filter((msg) => !existingMessages.has(msg.id));
        const html = newMessages.map(createMessageCard).join("");

        if (initial) {
            messageArea.html(html);
            scrollToBottom(messageContainer);
        } else {
            const oldScrollHeight = messageContainer[0].scrollHeight;
            messageArea.prepend(html);
            adjustScrollPosition(messageContainer, oldScrollHeight);
        }

        setTimeout(() => {
            $(".message-card").addClass("loaded");
        }, 10);
    }

    function scrollToBottom(container) {
        setTimeout(() => {
            container.scrollTop(container[0].scrollHeight);
        }, 10);
    }

    function adjustScrollPosition(container, oldScrollHeight) {
        setTimeout(() => {
            const newScrollHeight = container[0].scrollHeight;
            container.scrollTop(newScrollHeight - oldScrollHeight);
        }, 50);
    }

    $("#messagebody").on("scroll", function () {
        if ($(this).scrollTop() === 0 && !isLoading) {
            fetchMessages(false);
        }
    });

    function listenMqttForNewMessages(customerId) {
        const topic = `dreamsrent/to_user/${customerId}`;

        if (typeof mqtt === "undefined") {
            showToast("error", "MQTT not connected! Please refresh the page.");
            return;
        }

        const client = mqtt.connect("wss://broker.emqx.io:8084/mqtt", {
            clientId: `client_${Math.random().toString(16).substr(2, 8)}`,
            clean: true,
            reconnectPeriod: 1000,
            connectTimeout: 5000
        });

        client.on("connect", () => {
            client.subscribe(topic, { qos: 1 });
        });

        client.on("message", () => {
            offset = "";
            fetchMessages(true, true);
        });
    }

    $(document).on("keydown", "#messageinput", function (e) {
        if (e.keyCode === 13) {
            $("#sendmsg").trigger("click");
        }
    });

    $(document).on("click", "#sendmsg", function () {
        const $messageInput = $("#messageinput");
        const message = $messageInput.val().trim();
        const senderId = $messageInput.data("senderid");
        const receiverId = $messageInput.data("receiverid");
        const topic = `dreamsrent/to_user/${receiverId}`;
        const file = $("#fileupload")[0].files[0];

        if (!message && !file) {
            showToast("error", _l("web.user.type_message_or_file"));
            return;
        }

        const formData = new FormData();
        formData.append("message", message);
        formData.append("sender_id", senderId);
        formData.append("receiver_id", receiverId);
        formData.append("topic", topic);
        formData.append("messageType", file ? "file" : "text");
        if (file) {
            formData.append("file", file);
        }
        formData.append("_token", $("meta[name='csrf-token']").attr("content"));

        $.ajax({
            url: "/user/send-message",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: () => {
                $messageInput.val("").prop("disabled", true);
                $("#sendmsg").prop("disabled", true);
            },
            success: () => {
                offset = "";
                lastOffset = "";
                fetchMessages(true, true);
            },
            complete: () => {
                $messageInput.prop("disabled", false);
                $("#sendmsg").prop("disabled", false);
                $("#fileupload").val("");
                $("#messageinput").val("");
                $(".selected_file").text("").addClass("d-none");
            },
            error: () => {
                offset = "";
                lastOffset = "";
                fetchMessages(true, true);
            }
        });
    });

    function createMessageCard(message) {
        const isRightAligned = message.alignment === "right";
        const messageContent = message.message_type === "text"
            ? message.message
            : `<a href="${message.file_path}" target="_blank"><i class="fa fa-link"></i> ${message.message}</a>`;

        return `
            <li class="notify-block ${isRightAligned ? "sent" : "received"} d-flex">
                ${!isRightAligned ? `
                    <div class="avatar flex-shrink-0">
                        <img src="${message.admin_avatar}" alt="User Image" class="avatar-img rounded-circle">
                    </div>` : ""}
                <div class="media-body flex-grow-1">
                    <div class="msg-box">
                        <div>
                            <p>${messageContent}</p>
                            <ul class="chat-msg-info">
                                <li>
                                    <div class="chat-time">
                                        <span>${message.time}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </li>`;
    }

    $(document).on("change", "#fileupload", function () {
        const fileName = this.files.length > 0 ? this.files[0].name : "";
        const displayName = fileName.length > 20 ? `${fileName.substring(0, 20)}...` : fileName;

        if (!fileName) {
            $(".selected_file").text("").addClass("d-none");
        } else {
            $(".selected_file").removeClass("d-none").text(displayName);
        }
    });
})(jQuery);