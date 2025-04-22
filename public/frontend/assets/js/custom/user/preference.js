(async () => {
    await loadTranslationFile("web", "home, common");

    let isInitialLoad = true;

    $(document).ready(function () {
        $('.custom-select2').select2();
        fetchPreference();
    });

    $(document).on('change', '#language_id', function () {
        if (isInitialLoad) return;
        let formData = {
            language_id: $(this).val()  
        };
        updatePreference(formData);
    });

    $(document).on('change', '#region_id', function () {
        if (isInitialLoad) return;
        let formData = {
            region_id: $(this).val()  
        };
        updatePreference(formData);
    });

    function fetchPreference() {
        $.ajax({
            type:"POST",
            url:"/user/get-preferences",
            dataType: "json",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(resp){
                if (resp.code == 200) {
                    $("#language_id").val(resp.data.language_id).trigger('change');
                    $("#region_id").val(resp.data.region_id).trigger('change');
                    isInitialLoad = false;
                }
            }
        }); 
    }

    function updatePreference(formData = '') {
        $.ajax({
            type:"POST",
            url:"/user/preference/update",
            data: formData,
            dataType: "json",
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(resp){
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                if (resp.code === 200) {
                    showToast('success', resp.message);
                }
            },
            error:function(error){
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                if (error.responseJSON.code === 422) {
                    $.each(error.responseJSON.errors, function(key, val) {
                        $("#" + key).addClass("is-invalid");
                        $("#" + key + "_error").text(val[0]);
                    });
                } else {
                    showToast('error', error.responseJSON.message);
                }
            }
        }); 
    }

}) ();