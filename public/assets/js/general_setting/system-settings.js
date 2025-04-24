(async () => {
    await loadTranslationFile('admin', 'general_settings,common');
    const permissions = await loadUserPermissions();

    DbBackUpTable();

function DbBackUpTable() {
    $(document).ready(function() {
        $.ajax({
            url: '/admin/settings/system-backup/list',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                let tableBody = $("#system-backup-list");
                tableBody.empty(); 
<<<<<<< Updated upstream
                if(response.data.length === 0){
                    tableBody.append(`
                        <tr>
                            <td colspan="3">
                                <p class="text-gray-9 text-center">${_l('admin.common.empty_table')}</p>
                            </td>
                        </tr>
                    `);
                }
=======

>>>>>>> Stashed changes
                response.data.forEach(backup => {
                    let row = `
                        <tr>
                            <td>
                                <h6 class="fw-semibold fs-14">
                                    <a href="${backup.download_url}" download>${backup.name}</a>
                                </h6>
                            </td>
                            <td>
                                <p class="text-gray-9">${backup.created_on}</p>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                    ${ hasPermission(permissions, 'other_settings', 'edit') ? 

                                        `<li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);" onclick="restoreBackup('${backup.name}')">
                                                <i class="ti ti-restore me-1"></i>${_l('admin.general_settings.restore')}
                                            </a>
                                        </li>`:''}
                                        ${ hasPermission(permissions, 'other_settings', 'delete') ? 

                                        `<li>
                                            <a class="dropdown-item rounded-1" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_backup" onclick="deleteSystemBackup(${backup.id})">
                                                <i class="ti ti-trash me-1"></i>${_l('admin.general_settings.delete')}
                                            </a>
                                        </li>`:''}
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            },
            error: function(error) {
                console.error("Error fetching backups:", error);
            }
        });
    });

}
    
})();


function restoreBackup(filename) {
    alert("Restore function for " + filename + " will be implemented here.");
}

function deleteSystemBackup(id){
    $("#delete_id").val(id);
}

$("#deleteSystemDbBackup").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url:"/admin/settings/backups/delete",
        type:"POST",
        data: {
            id: $('#delete_id').val()
        },
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if(response.code === 200){
                showToast('success', response.message);
                $("#delete_backup").modal('hide');
                DbBackUpTable();
            }
        },
        error: function(res) {
            if (res.responseJSON.code === 500) {
                showToast('error', res.responseJSON.message);
            } else {
                showToast('error', _l('admin.general_settings.retrive_error'));
            }
        }
        
    });
});

