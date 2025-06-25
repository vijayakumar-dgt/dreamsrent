<<<<<<< Updated upstream
(function ($) {
    "use strict";

    (async () => {
        await loadTranslationFile('web', 'user,common');
        const adminId = $("#messageinput").data('receiverid');
        const customerId = $("#messageinput").data('senderid');
=======
/* global loadTranslationFile, document, showToast, setTimeout, FormData, _l, jQuery, mqtt */

(function ($) {
    "use strict";

    (async function initChat() {
        await loadTranslationFile("web", "user,common");

        const customerId = $("#messageinput").data("senderid");
>>>>>>> Stashed changes
        listenMqttForNewMessages(customerId);
        fetchMessages();
    })();

    const userId = $("#messageinput").data("receiverid");
    let offset = "";
    let isLoading = false;
    let lastOffset = "";

    /**
     * Fetch messages from server.
     * @param {boolean} initial
     * @param {boolean} reset
     */
    async function fetchMessages(initial = true, reset = false) {
        if (isLoading || offset === null) return;

        isLoading = true;

        if (reset) {
            offset = "";
            lastOffset = "";
        }

        $.ajax({
            url: "/user/fetch-messages",
            type: "POST",
            dataType: "json",
            data: {
                user_id: userId,
                offset: offset,
                reset: reset,
                last_offset: lastOffset,
                _token: $("meta[name='csrf-token']").attr("content")
            },
            beforeSend: function () {
                if (initial) $("#messagearea").html("");
            },
            success: function (response) {
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
            },
            complete: function () {
                isLoading = false;
            }
        });
    }

    function updateMessageView(messages, initial) {
        const $container = $("#messagebody");
        const $area = $("#messageArea");
        const existing = new Set();

        $(".message-card").each(function () {
            existing.add($(this).data("message-id"));
        });

        const newMessages = messages.filter(msg => !existing.has(msg.id));
        const html = newMessages.map(createMessageCard).join("");

        if (initial) {
            $area.html(html);
            scrollToBottom($container);
        } else {
            const oldHeight = $container[0].scrollHeight;
            $area.prepend(html);
            adjustScrollPosition($container, oldHeight);
        }

        setTimeout(() => {
            $(".message-card").addClass("loaded");
        }, 10);
    }

    function scrollToBottom($container) {
        setTimeout(() => {
            $container.scrollTop($container[0].scrollHeight);
        }, 10);
    }

    function adjustScrollPosition($container, oldHeight) {
        setTimeout(() => {
            const newHeight = $container[0].scrollHeight;
            $container.scrollTop(newHeight - oldHeight);
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

        client.on("connect", function () {
            client.subscribe(topic, { qos: 1 });
        });

        client.on("message", function () {
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
        const $input = $("#messageinput");
        const message = $input.val().trim();
        const senderId = $input.data("senderid");
        const receiverId = $input.data("receiverid");
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
            beforeSend: function () {
                $input.val("").prop("disabled", true);
                $("#sendmsg").prop("disabled", true);
            },
            success: function () {
                offset = "";
                lastOffset = "";
                fetchMessages(true, true);
            },
            complete: function () {
                $input.prop("disabled", false);
                $("#sendmsg").prop("disabled", false);
                $("#fileupload").val("");
                $input.val("");
                $(".selected_file").text("").addClass("d-none");
            },
            error: function () {
                offset = "";
                lastOffset = "";
                fetchMessages(true, true);
            }
        });
    });

    function createMessageCard(message) {
        const isRight = message.alignment === "right";
        const msgContent =
            message.message_type === "text"
                ? message.message
                : `<a href="${message.file_path}" target="_blank"><i class="fa fa-link"></i> ${message.message}</a>`;

        return `
            <li class="notify-block ${isRight ? "sent" : "received"} d-flex message-card" data-message-id="${message.id}">
                ${!isRight ? `
                    <div class="avatar flex-shrink-0">
                        <img src="${message.admin_avatar}" alt="User Image" class="avatar-img rounded-circle">
                    </div>` : ""}
                <div class="media-body flex-grow-1">
                    <div class="msg-box">
                        <div>
                            <p>${msgContent}</p>
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
        const shortName = fileName.length > 20 ? `${fileName.slice(0, 20)}...` : fileName;

        if (!fileName) {
            $(".selected_file").text("").addClass("d-none");
        } else {
            $(".selected_file").removeClass("d-none").text(shortName);
        }
    });
<<<<<<< Updated upstream
})(jQuery);
=======

})(jQuery);
>>>>>>> Stashed changes
