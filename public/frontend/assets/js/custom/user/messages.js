
(async () => {
    await loadTranslationFile('web', 'user,common');
    let adminId = $("#messageinput").data('receiverid');
    let customerId = $("#messageinput").data('senderid');
    let topicName = 'dreamsrent_' + adminId;
    listenMqttForNewMessages(customerId);
    fetchMessages();
})();

let userId = $("#messageinput").data('receiverid');
let offset = "";
let isLoading = false;
let last_offset = "";
async function fetchMessages(initial = true,reset = false) {
    if (isLoading || offset === null) return;
    isLoading = true;
    if(reset){
        offset = "";
        last_offset = "";
    }
    $.ajax({
        url: '/user/fetch-messages',
        type: 'POST',
        data: {
            'user_id': userId,
            'offset': offset,
            'reset' : reset,
            'last_offset': last_offset,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            if (initial) {
                $("#messagearea").html('');
            }
        },
        success: function (response) {
            if (response.code === 200 && response.messages.length > 0) {
                let messageContainer = $("#messagebody");
                let messageArea = $("#messageArea");
                let existingMessages = new Set();

                $(".message-card").each(function () {
                    existingMessages.add($(this).data("message-id"));
                });

                let newMessages = response.messages.filter(msg => !existingMessages.has(msg.id));
                let html = newMessages.map(message => createMessageCard(message)).join('');
                if (initial) {
                    messageArea.html(html);
                    setTimeout(() => {
                        messageContainer.scrollTop(messageContainer[0].scrollHeight);
                    }, 10);
                } else {
                    let oldScrollHeight = messageContainer[0].scrollHeight;
                    messageArea.prepend(html);
                    setTimeout(() => {
                        let newScrollHeight = messageContainer[0].scrollHeight;
                        messageContainer.scrollTop(newScrollHeight - oldScrollHeight);
                    }, 50);
                }
                
                offset = response.next_offset;
                last_offset = response.last_offset;
            }
            setTimeout(() => {
                $(".message-card").addClass("loaded");
            }, 10);
            if(response.last_message){
                $(".user-last-chat").html(response.last_message.message);
                $(".last-chat-time").html(response.last_message.created_at);
            }
            if (offset === null) {
                $("#messagebody").off("scroll");
            }

            isLoading = false;
        }
    });
}

$("#messagebody").on("scroll", function () {
    if ($(this).scrollTop() === 0 && !isLoading) {
        fetchMessages(false);
    }
});




function listenMqttForNewMessages(customerId) {
    const  topic = 'dreamsrent/admin_to_customer/' + customerId;
    if(typeof mqtt === 'undefined'){
        showToast('error', 'MQTT not connected!, Please refresh the page');
        return;
    }
    const client = mqtt.connect('wss://broker.emqx.io:8084/mqtt', {
        clientId: 'client_' + Math.random().toString(16).substr(2, 8),
        clean: true,
        reconnectPeriod: 1000,
        connectTimeout: 5000,
      });
      
      

    client.on('connect', function () {
        console.log('Connected to MQTT broker');
        client.subscribe(topic, { qos: 1 }, (err) => {
            if (err) {
                console.error('Subscription error:', err);
            } else {
                console.log('Subscribed to topic:', topic);
            }
        });
    });

    client.on('message', function (receivedTopic, message) {
        const msgString = message.toString();
        console.log('Received message on topic', receivedTopic, ':', msgString);
        offset = "";
        fetchMessages(true, true);
    });

    client.on('error', function (err) {
        console.error('MQTT error:', err);
    });

    client.on('close', function () {
        console.log('MQTT connection closed');
    });
}
//if enter key pressed send message
$(document).on('keydown', '#messageinput', function (e) {
    if (e.keyCode === 13) {
        $("#sendmsg").trigger('click');
    }
})
$(document).on('click', '#sendmsg', function () {
    const $messageInput = $("#messageinput");
    const message = $messageInput.val().trim();
    const senderId = $messageInput.data('senderid');
    const receiverId = $messageInput.data('receiverid');
    const topic = `dreamsrent/customer_to_admin/${senderId}`;
    const file = $("#fileupload")[0].files[0]; // Correct way to get file

    if (message.length === 0 && !file) {
        showToast('error', _l('web.user.type_message_or_file'));
        return;
    }

    let formData = new FormData();
    formData.append('message', message);
    formData.append('sender_id', senderId);
    formData.append('receiver_id', receiverId);
    formData.append('topic', topic);
    formData.append('messageType', file ? 'file' : 'text');
    if (file) formData.append('file', file);
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: "/user/send-message",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $messageInput.val('').prop('disabled', true);
            $("#sendmsg").prop('disabled', true);
        },
        success: function () {
            fetchMessages(true, true);
        },
        complete: function () {
            $messageInput.prop('disabled', false);
            $("#sendmsg").prop('disabled', false);
            $("#fileupload").val('');
            $("#messageinput").val('');
            $(".selected_file").text('');
            $(".selected_file").addClass('d-none');
        },
        error: function (xhr) {
            fetchMessages(true, true);
        }
    });
});


function createMessageCard(message) {
    let html = '';
    if(message.alignment === 'right'){
        html = `<li class="notify-block sent d-flex">
                    <div class="media-body flex-grow-1">
                        <div class="msg-box">
                            <div>
                                <p>${message.message_type == 'text' ? message.message : `<a href="${message.file_path}"><i class="fa fa-link"></i>  ${message.message}</a>`}</p>
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
    }else{
         html = `<li class="notify-block received d-flex">
                    <div class="avatar flex-shrink-0">
                        <img src="${message.admin_avatar}" alt="User Image" class="avatar-img rounded-circle">
                    </div>
                    <div class="media-body flex-grow-1">
                        <div class="msg-box">
                            <div>
                                <p>${message.message_type == 'text' ? message.message : `<a href="${message.file_path}" target="_blank"><i class="fa fa-link"></i>  ${message.message}</a>`}</p>
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
    
    return html;
    
}

$(document).on('change','#fileupload', function(){
    //read and show filename of selected file
    let fileName = this.files[0].name;
    if(this.files.length == 0){
        $(".selected_file").text('');
        $(".selected_file").addClass('d-none');
        return;
    }
    if(fileName.length > 20){
        fileName = fileName.substring(0, 20) + '...';
    }
    $(".selected_file").removeClass('d-none');
    $(".selected_file").text(fileName);
})