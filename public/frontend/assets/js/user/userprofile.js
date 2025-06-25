/* global $, loadTranslationFile, setTimeout, document, showToast, _l, FormData, window, Image, FileReader, URL */
(async () => {
  "use strict";

  await loadTranslationFile("web", "user,common,home");

  $(document).ready(() => {
    initIntelInput();
    initValidation();
    initSelect2();
    initEvents();
  });

  function initEvents() {
    setTimeout(() => $("#country").trigger("change"), 100);

    $("#country").on("change", function () {
      const id = $(this).val();
      id ? fetchStatesByCountry(id) : (updateStateOptions(), updateCityOptions());
    });

    $("#state").on("change", function () {
      const id = $(this).val();
      id ? fetchCitiesByState(id) : updateCityOptions();
    });
  }

  function updateStateOptions() {
    $("#state").empty().append(`<option value="">${_l("web.common.select")}</option>`);
  }

  function updateCityOptions() {
    $("#city").empty().append(`<option value="">${_l("web.common.select")}</option>`);
  }

  function initSelect2() {
    $(".custom-select2").select2();
  }

  function initValidation() {
    $.validator.addMethod("filesize", (value, el, param) => !el.files.length || el.files[0].size <= param * 1024, "File size must be less than {0} KB.");

    $("#userProfileForm").validate({
      rules: {
        profile_photo: { extension: "jpeg|jpg|png", filesize: 2048 },
        first_name: { required: true, maxlength: 30 },
        last_name: { required: true, maxlength: 30 },
        email: { required: true, email: true },
        user_phone: { required: true, minlength: 10, maxlength: 15 },
        address_line: { required: true, maxlength: 50 },
        country: { required: true },
        state: { required: true },
        city: { required: true },
        postal_code: { required: true, pattern: /^[0-9a-zA-Z]+$/ }
      },
      messages: {
        first_name: { required: _l("web.user.enter_first_name"), maxlength: _l("web.common.maxlength_30") },
        last_name: { required: _l("web.user.enter_last_name"), maxlength: _l("web.common.maxlength_30") },
        email: { required: _l("web.user.enter_email"), email: _l("web.home.valid_email") },
        user_phone: { required: _l("web.user.phone_number_required"), minlength: _l("web.home.phone_number_minlength"), maxlength: _l("web.home.phone_number_maxlength") },
        address_line: { required: _l("web.user.enter_address"), maxlength: _l("web.user.maxlength_50") },
        postal_code: { required: _l("web.home.enter_pincode"), pattern: "Please enter a valid postal code" }
      },
      errorPlacement: (error, element) => $("#" + element.attr("id") + "_error").text(error.text()),
      highlight: (el) => {
        const $el = $(el);
        $el.addClass("is-invalid").removeClass("is-valid");
        if ($el.hasClass("select2-hidden-accessible")) $el.next(".select2-container").addClass("is-invalid").removeClass("is-valid");
      },
      unhighlight: (el) => {
        const $el = $(el);
        $el.removeClass("is-invalid").addClass("is-valid");
        if ($el.hasClass("select2-hidden-accessible")) $el.next(".select2-container").removeClass("is-invalid").addClass("is-valid");
        $("#" + el.id + "_error").text("");
      },
      submitHandler: function (form) {
        const data = new FormData(form);
        data.set("user_phone", $("#international_phone_number").val());
        data.append("_token", $("meta[name='csrf-token']").attr("content"));

        const $btn = $(".btn-primary").text(_l("web.user.plz_wait")).prop("disabled", true);

        $.ajax({
          type: "POST",
          url: "/userprofile",
          data: data,
          processData: false,
          contentType: false,
          success: (resp) => {
            showToast("success", resp.message);
            if (resp.data.profile_image) $(".header_profile_image").attr("src", resp.data.profile_image);
            $btn.text(_l("web.user.save_changes")).prop("disabled", false);
          },
          error: (err) => {
            $btn.text(_l("web.user.save_changes")).prop("disabled", false);
            $(".form-control").removeClass("is-invalid is-valid");
            if (err.responseJSON?.code === 422) {
              $.each(err.responseJSON.errors, (k, v) => {
                $("#" + k).addClass("is-invalid");
                $("#" + k + "_error").text(v[0]);
              });
            } else {
              showToast("error", err.responseJSON?.message || "An error occurred.");
            }
          }
        });
      }
    });
  }

  function initIntelInput() {
    const input = document.querySelector(".user_phone");
    const intl = document.querySelector("#international_phone_number");
    const form = document.querySelector("#userProfileForm");
    if (!input || !form) return;

    const iti = window.intlTelInput(input, {
      utilsScript: `${window.location.origin}/frontend/assets/plugins/intltelinput/js/utils.js`,
      separateDialCode: true,
      placeholderNumberType: "",
      autoPlaceholder: "off"
    });

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const num = iti.getNumber();
      if (num) intl.value = num;
    });
  }

  $("#profile_photo").on("change", function () {
    const file = this.files[0];
    const $preview = $("#profile_photo_preview");
    const $error = $("#profile_photo_error");

    let errMsg = "";
    if (file) {
      const types = ["image/jpeg", "image/png", "image/jpg"];
      if (!types.includes(file.type)) errMsg = "Only jpg, jpeg and png formats are allowed.";
      else if (file.size > 2 * 1024 * 1024) errMsg = "Image size should be less than 2MB.";

      if (errMsg) {
        $error.text(errMsg);
        $preview.attr("src", "").addClass("d-none");
        $(this).valid();
        return;
      }

      const img = new Image();
      const objURL = URL.createObjectURL(file);
      img.onload = () => {
        if (img.width < 180 || img.height < 180) {
          $error.text("Image should be at least 180 x 180 pixels.");
          $preview.attr("src", "").addClass("d-none");
        } else {
          $error.text("");
          const reader = new FileReader();
          reader.onload = (e) => $preview.attr("src", e.target.result).removeClass("d-none");
          reader.readAsDataURL(file);
        }
        URL.revokeObjectURL(objURL);
      };
      img.onerror = () => {
        $error.text("Invalid image file.");
        $preview.attr("src", "").addClass("d-none");
        URL.revokeObjectURL(objURL);
      };
      img.src = objURL;
    } else {
      $error.text("");
      $preview.attr("src", "").addClass("d-none");
    }
    $(this).valid();
  });

  function fetchStatesByCountry(id) {
    $.post("/api/states", { country_id: id }, (res) => {
      if (res.code === 200) {
        const $state = $("#state").empty().append("<option value=''>Select</option>");
        $.each(res.data, (_, item) => $state.append(`<option value="${item.id}">${item.name}</option>`));
        const defaultId = $state.data("default-id");
        if (defaultId) setTimeout(() => $state.val(defaultId).trigger("change"), 100);
        updateCityOptions();
      }
    }).fail(() => showToast("error", "Failed to fetch states."));
  }

  function fetchCitiesByState(id) {
    $.post("/api/cities", { state_id: id }, (res) => {
      if (res.code === 200) {
        const $city = $("#city").empty().append("<option value=''>Select</option>");
        $.each(res.data, (_, item) => $city.append(`<option value="${item.id}">${item.name}</option>`));
        const defaultId = $city.data("default-id");
        if (defaultId) setTimeout(() => $city.val(defaultId).trigger("change"), 100);
        else $city.trigger("change");
      }
    }).fail(() => showToast("error", "Failed to fetch cities."));
  }
})();