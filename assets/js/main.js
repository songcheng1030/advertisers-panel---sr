require("./reports");

const Mark = require("mark.js");
const showActionModal = require("./components/showActionModal");

let main = (function($) {
  "use strict";

  toggleNavbar();

  if (navigator.userAgent.toLowerCase().indexOf("firefox") > -1) {
    Array.from(document.querySelectorAll("input[type=date]")).map(o =>
      o.setAttribute("readonly", "true")
    );
  }

  window.filterGlobal = function filterGlobal(
    dataTable,
    filter,
    useRegex = false,
    useSmart = true
  ) {
    let search = dataTable.search(filter, useRegex, useSmart);
    search.draw();
    const instance = new Mark(document.querySelector("table tbody"));

    instance.unmark();
    instance.mark(filter);
  };

  let init = function() {
    $(document).on("click", ".navbar a", pushStateHandler);
    $(document).on(
      "click",
      ".dropdown:not(.b-dropdown), .slide-panel",
      openDropdown
    );
    $(document).on("click", ".panel-header .toggle", toggleDropdowns);
    $(document).on(
      "click",
      ".dropdown:not(.b-dropdown).form li a",
      updateValueOnChange
    );
    $(document).on(
      "click",
      ".dropdown:not(.b-dropdown):not(.user) a",
      openDropdownMenus
    );
    $(document).on("click", ".popup-trigger", openPopup);
    $(document).on(
      "change",
      ".checkboxes.tags [type=checkbox]",
      handleCheckbox
    );
    $(document).on("click", ".js-btn-cropper", handleCropperButtonClick);
    $(document).on(
      "click",
      ".popup-wrapper, .popup-wrapper .close-btn",
      closePopup
    );
    $(document).on("input", ".search-wrapper input", showSearchDropdown);
    $(document).on("click", ".js-button", handleSaveButtonClick);
    $(document).on("keyup", ".js-search-countries", handleSearchCountriesInput);
    $(document).on("keyup", ".js-search-agencies", handleSearchAgenciesInput);
    $(document).on(
      "keyup",
      ".js-create-advertiser-inline",
      handleSearchAdvertisersInput
    );

    // Colapse sidebar on click
    $(document).on("click", ".burger", colapseSidebar);
    // Cerrar dropdowns & slide-panels cuando se pincha fuera
    $(document).on("click", closeDropdown);
  };

  /**
   * handle the checkboxes on page load
   */
  handleCheckbox.call($(".checkboxes.tags [type=checkbox]").get(0));

  function handleCheckbox() {
    let $this = $(this),
      container = $this.closest(".slide-panel"),
      checks = container.find('input[type="checkbox"]');

    container.find(".select-all").prop("checked", function() {
      return (
        ($(this).prop("checked") && $this.prop("checked")) ||
        container.find(":checked:not(.select-all)").length ===
          container.find("[type=checkbox]:not(.select-all)").length
      );
    });
    if (container.find("button .tag").length === 0) {
      container.find("button").text("");
    }

    container.find("button .tag").remove();

    if (container.find(".select-all").prop("checked")) {
      container.find("button").text(Translator.trans("common.all"));
    } else {
      for (let i = 0; i < checks.length; i++) {
        if ($(checks[i]).prop("checked")) {
          let template =
            '<span class="tag" data-tag="' +
            $(checks[i]).val() +
            '">' +
            $(checks[i])
              .next("label")
              .find(".js-country")
              .text() +
            '<span class="close"></span></span>';
          container.find("button").append(template);
        }
      }
    }

    if (container.find(":checked").length === 0) {
      container.find("button").text(Translator.trans("common.select"));
    }
  }

  function removeCheckboxTag(target) {
    let container = $(target).closest(".slide-panel"),
      value = $(target)
        .closest(".tag")
        .attr("data-tag");

    $(target)
      .closest(".tag")
      .remove();
    container.find('input[value="' + value + '"]').prop("checked", false);
    if (container.find(":checked").length === 0) {
      container
        .find("button")
        .text(Translator.trans("common.select_at_least_one"));
    }
  }

  function handleCropperButtonClick(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    let popupWrapper = document.querySelector(".popup-wrapper");
    let popupCropper = document.querySelector(".popup-cropper");
    popupWrapper.classList.add("active");
    popupCropper.classList.add("active");
  }

  function initProfilePictureCropper() {
    $(document).ready(function() {
      const Croppie = require("croppie/croppie");
      var $uploadCrop;
      let width = 300;
      let height = 300;
      let $upload = $("#upload");

      if (window.innerWidth < 768) {
        width = 200;
        height = 200;
      }

      window.setTimeout(function() {
        $uploadCrop = new Croppie(
          document.getElementById("cropper-profile-picture"),
          {
            enableExif: true,
            viewport: {
              width: width,
              height: height,
              type: "circle"
            },
            boundary: {
              width: width,
              height: height
            }
          }
        );
      }, 500);

      function readFile(input) {
        if (input.files && input.files[0]) {
          const reader = new FileReader();

          reader.onload = function(e) {
            $uploadCrop.bind({
              url: e.target.result
            });
          };

          reader.readAsDataURL(input.files[0]);
        }
      }

      $upload.on("change", function() {
        readFile(this);
      });

      $("#cropper-submit").on("click", function(evt) {
        evt.preventDefault();
        $uploadCrop
          .result({
            type: "base64",
            size: "viewport"
          })
          .then(function(resp) {
            $("#profile_picture").val(resp);
            $("#profile-picture").css("background-image", "url(" + resp + ")");
            $("#header-profile-picture").css(
              "background-image",
              "url(" + resp + ")"
            );
            $("#cropper-close").trigger("click");
            showActionModal("warning");
          });
      });

      $upload.on("click", function(evt) {
        evt.stopPropagation();
      });

      $("#cropper-upload").on("click", function(evt) {
        evt.preventDefault();
        evt.stopPropagation();
        $("#upload").trigger("click");
      });
    });
  }

  function handleSearchCountriesInput() {
    let value = $(this)
      .val()
      .toLowerCase();
    $(".js-countries li:not(.js-search-item)")
      .show()
      .filter(function() {
        return (
          $(this)
            .find(".js-country")
            .text()
            .toLowerCase()
            .trim()
            .indexOf(value) === -1
        );
      })
      .hide();
  }

  function handleSearchAgenciesInput() {
    let value = $(this)
      .val()
      .toLowerCase();
    $(".js-agencies li:not(.js-search-item)")
      .show()
      .filter(function() {
        return (
          $(this)
            .find("a")
            .text()
            .toLowerCase()
            .trim()
            .indexOf(value) === -1
        );
      })
      .hide();
  }

  function handleSearchAdvertisersInput() {
    let value = $(this)
      .val()
      .toLowerCase();
    $(".js_campaign_advertiser li:not(.ajax-input)")
      .show()
      .filter(function() {
        return (
          $(this)
            .find("a")
            .text()
            .toLowerCase()
            .trim()
            .indexOf(value) === -1
        );
      })
      .hide();
  }

  function pushStateHandler(e) {
    if (e.ctrlKey || e.metaKey) {
      return;
    }
    if (!$(this).hasClass("popup-trigger")) {
      $(this)
        .parent()
        .toggleClass("active");
    }

    if (
      !$(this)
        .parent()
        .hasClass("has-sub")
    ) {
      toggleNavbar();
    }
  }

  // Hide bar for small screens
  function toggleNavbar() {
    if ($("body").hasClass("collapsed")) return;

    if ($(window).width() <= 1024) {
      $("body").addClass("navbar-collapse");
    } else {
      $("body").removeClass("navbar-collapse");
    }
  }

  function colapseSidebar() {
    $("body")
      .toggleClass("navbar-collapse")
      .toggleClass("collapsed", $("body").hasClass("navbar-collapse"));
  }

  // Abrir panel
  function openDropdown(e) {
    e.stopPropagation();

    // if ($(this).closest('.nested').length = 0) {
    // Cerramos el resto de dropdowns y slide-menus
    $(".open")
      .not(this)
      .removeClass("open");
    // }

    if (
      $(e.target).hasClass("search-input") ||
      $(e.target).hasClass("search")
    ) {
      return;
    }

    if ($(e.target).hasClass("close")) {
      removeCheckboxTag(e.target);
      return;
    }

    // Despliega el menú
    $(this).toggleClass("open");

    if (
      e.currentTarget.parentElement.querySelector("#campaign_advertiser") !==
        null ||
      e.currentTarget.parentElement.querySelector("#campaign_dsp") !== null
    ) {
      setTimeout(function() {
        e.currentTarget.parentElement
          .querySelector(".dropdown-menu")
          .querySelector("input")
          .focus();
      }, 50);
    }
  }

  // Dropdown componentes
  function toggleDropdowns() {
    let closest_panel = $(this).closest(".panel");
    if (closest_panel.hasClass("panel-help") && $(this).hasClass("max")) {
      $(".panel-body").slideUp("fast");
      $(".panel-header .toggle").addClass("max");
      $(this).removeClass("max");
    } else {
      $(this).toggleClass("max");
    }
    closest_panel.find(".panel-body").slideToggle("fast");
  }

  function updateValueOnChange() {
    let selectedOption = $(this);
    let selectedValue = selectedOption.attr("data-value");
    let parent = selectedOption.parents(".dropdown:not(.b-dropdown).form");
    let hiddenInput = parent.children('input[type="hidden"]');

    hiddenInput.val(selectedValue);
    hiddenInput.trigger("change");
  }

  // Desplegar menus y ocultar los no activos
  function openDropdownMenus(e) {
    e.preventDefault();
    if ($(this).hasClass("back-btn")) {
      $(this)
        .closest(".nested")
        .toggleClass("open")
        .siblings()
        .removeClass("open");
      $(".nested").show();
      return false;
    }
    if (
      $(this)
        .parent()
        .hasClass("nested")
    ) {
      $(this)
        .parent()
        .toggleClass("open")
        .siblings()
        .removeClass("open");
      $(".nested:not(.open)").hide();
      return false;
    }
    var $button = $(this).closest(
      '.dropdown:not(.b-dropdown):not(".fsp-month")'
    );
    // Marcar el nuevo seleccionado
    $(this)
      .closest('.dropdown:not(".fsp-month"):not(.b-dropdown)')
      .find(".selected")
      .removeClass("selected");
    $(this)
      .parent()
      .toggleClass("selected");
    // Actualizar el valor
    $button.find("button").html($(this).html());
    $button.val($(this).data("value"));
  }

  function openPopup(e) {
    e.stopPropagation();
    e.preventDefault();
  }

  function handleSaveButtonClick(evt) {
    evt.preventDefault();
    evt.stopPropagation();
    let $btnSave = $(this);
    let $form = $btnSave.parents("form");
    $btnSave.attr("disabled", true).addClass("disabled");
    $form.submit();
  }

  function closePopup(e) {
    if (e.target === this) {
      forceClosePopup();
    }
  }

  function forceClosePopup() {
    $(".popup-wrapper, .popup-container").removeClass("active");
  }

  function showSearchDropdown() {
    if ($(this).val().length > 2) {
      $(this)
        .closest(".search-wrapper")
        .addClass("open");
    }
    return false;
  }

  // Cerrar dropdowns & slide-panels cuando se pincha fuera
  function closeDropdown(e) {
    if (e.target.getAttribute("href") === "#") {
      e.preventDefault();
    }

    $(
      ".dropdown:not(.b-dropdown).open, .slide-panel.open, .notifications-dropdown.open, .search-wrapper.open input"
    ).each(function() {
      if (e.target !== this) $(".open").removeClass("open");
    });

    if (!e.target.closest(".filter-dropdown")) {
      $(".filter-dropdown").removeClass("open");
    }
  }

  function showRequestedActionModal() {
    setTimeout(function() {
      if ($("#action-modal-trigger") !== null) {
        showActionModal($("#action-modal-trigger").data("modal"));
      }
    }, 1000);
  }

  function handleExpandablePanels() {
    let expandablePanels = document.querySelectorAll(".js-expandable-panel");
    expandablePanels.forEach(panel => {
      let expandableButton = panel.querySelector(".panel-header button");
      let panelBody = panel.querySelector(".panel-body");
      let formFieldsWithError = panel.querySelectorAll(".form-field.error");

      if (
        formFieldsWithError.length > 0 &&
        expandableButton.classList.contains("max")
      ) {
        expandableButton.classList.remove("max");
        panelBody.style.display = "block";
      }
    });
  }

  $(document).ready(function() {
    showRequestedActionModal();
    initProfilePictureCropper();
    handleExpandablePanels();
  });

  return {
    init
  };
})(jQuery);

main.init();
