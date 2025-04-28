
(async () => {
    "use strict";
    await loadTranslationFile('web', 'user,common');
    loadNotifications();
    function loadNotifications(page = 1) {
        $.ajax({
            url: `/admin/notifications?page=${page}`,
            method: 'GET',
            success: function (response) {
                if(response.count > 0){
                    $('#notification-list').html(response.html);
                    $('#pagination-container').html(renderPagination(response)); 
                    $("#notification_action").removeClass("d-none");
                }else{
                    $('#notification-list').html(`<p class="text-center">${_l('web.user.no_notifications_found')}</p>`);
                    $('#pagination-container').html('');
                    $("#notification_action").addClass("d-none");
                }
            }
        });
    }
    
    $(document).on('click', '.pagination .page-link', function (e) {
        e.preventDefault();
        let page = $(this).data('page');
        if (page) {
            loadNotifications(page);
        }
    });
    
    function renderPagination(data) {
        let html = `
            <nav class="custom-pagination">
                <ul class="pagination justify-content-center align-items-center">
                    <li class="page-item ${data.prev_page_url ? '' : 'disabled'}">
                        <a class="page-link" href="#" data-page="${data.current_page - 1}">
                            <i class="fas fa-arrow-left me-1"></i> ${_l('web.user.prev')}
                        </a>
                    </li>`;
    
        for (let i = 1; i <= data.last_page; i++) {
            html += `
                <li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">
                        ${i}
                    </a>
                </li>`;
        }
    
        html += `
                    <li class="page-item ${data.next_page_url ? '' : 'disabled'}">
                        <a class="page-link" href="#" data-page="${data.current_page + 1}">
                            ${_l('web.user.next')} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </li>
                </ul>
            </nav>`;
    
        return html;
    }
    
    $(document).on('click','#mark-all-read', function(){
        $.ajax({
            type:"POST",
            url : "/admin/mark-all-notifications-as-read",
            data : {
                _token : $('meta[name="csrf-token"]').attr('content')
            },
            dataType : "json",
            success : function(response) {
                if(response.code === 200){
                    showToast('success', response.message);
                    loadNotifications();
                }else{
                    showToast('error', response.message);
                }
            },
            error : function(response) {
                showToast('error', response.message);
            }
        });
    });

    $(document).on("click", ".notificationitem", function () {
        let id = $(this).data('id');
        $.ajax({
            type: "POST",
            url: "/admin/mark-notification-as-read",
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function (response) {
                if (response.status == 'success') {
                    showToast('success', response.message);
                    loadNotifications();
                }else{
                    console.log(response);
                    showToast('error', response.message);
                }
            },
            error: function (response) {
                showToast('error', response.message);
            }
        });
    });
    
    $(document).on("click", ".del_notification", function () {
        let id = $(this).data('id');
        $("#delete_notification .deletebtn").data('id', id);
        $("#delete_notification").modal('show');
    });

    $(document).on('click', '#delete_notification .deletebtn', function () {
        let id = $("#delete_notification .deletebtn").data('id');
        if(id){
            $.ajax({
                type: "POST",
                url: "/admin/delete-notification",
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function (response) {
                    $("#delete_notification").modal('hide');
                    if (response.status == 'success') {
                        showToast('success', response.message);
                        loadNotifications();
                    }else{
                        showToast('error', response.message);
                    }
                    
                },
                error: function (response) {
                    showToast('error', response.message);
                }
            });
        }
    });

    $(document).on('click','#deleteAll', function(){
        $("#deleteAllNotifications").modal('show'); 
    });

    $(document).on('click','.deleteAllNotifications', function(){
        $.ajax({
            type:"POST",
            url : "/admin/delete-all-notifications",
            data : {
                _token : $('meta[name="csrf-token"]').attr('content')
            },
            dataType : "json",
            success : function(response) {
                $("#deleteAllNotifications").modal('hide');
                if(response.code === 200){
                    showToast('success', response.message);
                    loadNotifications();
                }else{
                    showToast('error', response.message);
                }
            },
            error : function(response) {
                showToast('error', response.message);
            }
        }) 
    });
})();