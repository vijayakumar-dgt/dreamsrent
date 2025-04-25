let offset = "";
let isLoading = false;
let last_offset = "";
async function fetchMessages(userId,initial = true,reset = false) {
    if (isLoading || offset === null) return;
    isLoading = true;
    if(reset){
        offset = "";
        last_offset = "";
    }
    $.ajax({
        url: '/admin/fetch-messages',
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
                $("#messageArea").html('');
            }
        },
        success: function (response) {
            if (response.code === 200 && response.messages.length > 0) {
                let messageArea = $("#messageArea");
                let messageContainer = $("#messageContainer");
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
                    let oldScrollTop = messageContainer.scrollTop();
                
                    messageArea.prepend(html);
                
                    setTimeout(() => {
                            let newScrollHeight = messageContainer[0].scrollHeight;
                            let scrollOffset = newScrollHeight - oldScrollHeight;
                            messageContainer.scrollTop(newScrollHeight - oldScrollHeight);
                    }, 50);
                }
                
        
                offset = response.next_offset;
                last_offset = response.last_offset;
            }
        
            if (offset === null) {
                $("#messagebody").off("scroll");
            }
        
            isLoading = false;
        }
        
        
        
    });
}

$("#messageContainer").on("scroll", function () {
    if ($(this).scrollTop() === 0 && !isLoading) {
        let userId = $("#chat_avatar").data('userid');
        fetchMessages(userId,false);
    }
});

(async () => {
    await loadTranslationFile('web', 'user,common');
    let adminId = $("#messageinput").data('receiverid');
    listenMqttForNewMessagesFromAllCustomers(adminId);
    //trigger click event for 
    let firstUser = $(".chat-list .userprofile").first();
    firstUser.trigger('click');
})();

function listenMqttForNewMessagesFromAllCustomers(){
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
    const topic  = 'dreamsrent/customer_to_admin/#';
    client.on('connect', function () {
        client.subscribe(topic, { qos: 1 }, (err) => {

        });
    });

    client.on('message', function (receivedTopic, message) {
        let topicParts = receivedTopic.split("/");
        let user_id = topicParts[topicParts.length - 1];
        
        let activeUser = $("#chat_avatar").attr('data-userid');
        if(activeUser != user_id){
            let user = $(".chat-list .userprofile[data-userid='" + user_id + "']");
            user.trigger('click');
        }
        offset = "";
        fetchMessages(user_id, true);
    });

    client.on('error', function (err) {
        
    });

    client.on('close', function () {
        
    });
}


$(document).ready(function () {
    let firstUser = $(".chat-list .userprofile").first();
    if (firstUser.length) {
        let user_id = firstUser.data('userid');
        fetchMessages(user_id);
    }
});
$(document).on('click', '.userprofile', function(){
      let user_id = $(this).data('userid');
      let avatar = $(this).data('avatar');
      let username = $(this).data('username');
      $("#chat_avatar").attr('data-userid',user_id);
      $("#chat_avatar").attr('src',avatar);
      $("#chat_avatar").attr('alt',user_id);
      $(".chat-user-name").text(username);
      offset = "";
      last_offset = "";
      fetchMessages(user_id,true);
});
//if enter key pressed send message
$(document).on('keydown', '#messageinput', function (e) {
    if (e.keyCode === 13) {
        $("#sendmsg").trigger('click');
    }
})
$(document).on('click', '#sendmsg', function () {
    const senderId = $(this).data('senderid');
    const receiverId = $("#chat_avatar").attr('data-userid');
    const message = $("#messageinput").val().trim();
    const topic = `dreamsrent/admin_to_customer/${receiverId}`;
    const file = $("#fileupload")[0].files[0];

    if (!message && !file) {
        showToast('error', 'Please enter a message or select a file.');
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
        url: "/admin/send-message",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#messageinput").val('').prop('disabled', true);
            $("#sendmsg").prop('disabled', true);
        },
        success: function () {
            offset = "";
            last_offset = "";
            fetchMessages(receiverId, true);
        },
        complete: function () {
            $("#sendmsg").prop('disabled', false);
            $("#messageinput").prop('disabled', false);
            $("#fileupload").val('');
            $(".selected_file").text('');
            $(".selected_file").addClass('d-none');
        },
        error: function (xhr, status, error) {
            offset = "";
            last_offset = "";
            fetchMessages(receiverId, true);
        }
    });
});



function createMessageCard(message) {
    let userName = message.is_sender ? message.receiver_username : message.sender_username;
    let avatar   = message.is_sender ? message.receiver_avatar : message.sender_avatar;
    let userid   = message.is_sender ? message.receiver_id : message.sender_id;
    let msgbody  = "";
    if(message.alignment === 'right'){
           msgbody = `<div class="chats">
                            <div class="chat-avatar">
                                <img src="${message.sender_avatar}" class="rounded-circle" alt="image">
                            </div>
                            <div class="chat-content">							
                                <div class="chat-profile-name">
                                    <h6>${message.sender_username}<i class="ti ti-circle-filled fs-7 mx-2"></i><span class="chat-time">${message.time}</span></h6>
                                </div>
                                <div class="chat-info">
                                    <div class="message-content">
                                        ${message.message_type == 'text' ? message.message : `<a href="${message.file_path}" target="_blank"><i class="fa fa-link"></i>  ${message.message}</a>`}
                                    </div> 
                                </div>			
                            </div>
                        </div>`;
    }else{
        msgbody = `<div class="chats chats-right">
                        <div class="chat-content">
                            <div class="chat-profile-name text-end">
                                <h6>${message.sender_username}<i class="ti ti-circle-filled fs-7 mx-2"></i><span class="chat-time">${message.time}</span></h6>                                        
                            </div>
                            <div class="chat-info">   
                                <div class="message-content">
                                   ${message.message_type == 'text' ? message.message : `<a href="${message.file_path}" target="_blank"><i class="fa fa-link"></i>  ${message.message}</a>`}
                                </div>   
                            </div>
                        </div>
                        <div class="chat-avatar">
                            <img src="${message.sender_avatar}" class="rounded-circle dreams_chat" alt="image">
                        </div>
                    </div>`;
    }

    return msgbody;
}

$(document).on('click', '#openFile', function () {
    $("#fileupload").click();
});

$(document).on('change', '#fileupload', function () {
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
});
