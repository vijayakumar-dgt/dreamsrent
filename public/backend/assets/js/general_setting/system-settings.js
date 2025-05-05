(async () => {
    "use strict";
    await loadTranslationFile('admin', 'general_settings,common');
    const permissions = await loadUserPermissions();

    DbBackUpTable();

function DbBackUpTable() {
    $.ajax({
        url: '/admin/settings/system-backup/list',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            let tableBody = $("#system-backup-list");
            tableBody.empty();
            if(response.data.length === 0){
                tableBody.append(`
                    <tr>
                        <td colspan="4">
                            <p class="text-gray-9 text-center">${_l('admin.common.empty_table')}</p>
                        </td>
                    </tr>
                `);
            }
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
                                    <li>
                                        <a class="dropdown-item rounded-1" href="${backup.download_url}" download>
                                            <i class="ti ti-download me-1"></i>${_l('admin.common.download')}
                                        </a>
                                    </li>
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
        complete: function () {
            $(".table-loader").hide();
            $(".label-loader, .input-loader").hide();
            $(".real-label, .real-table, .real-data, .table-footer").removeClass("d-none");
        },
        error: function(error) {
            showToast('error', _l('admin.general_settings.retrive_error'));
        }
    });
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

})();


function restoreBackup(filename) {
    alert("Restore function for " + filename + " will be implemented here.");
}

function deleteSystemBackup(id){
    $("#delete_id").val(id);
}

