require("datatables.net");
require("datatables.net-plugins/sorting/datetime-moment");
require("../../datatables/plugins/dataTables.cellEdit");
let campaignIndex = (function($) {
  "use strict";
  const ACTION_MODAL_SHOW_TIME = 4000;
  let init = function() {
    $(document).on("click", ".js-create-agency", handleCreateAgencyButtonClick);
    $(document).on(
      "submit",
      ".js-add-agency-popup form",
      handleAddAgencyFormSubmit
    );
    $(document).on(
      "click",
      ".btn-archive-campaign",
      handleCampaignOptionButtonsClick
    );
    $(document).on("change", "#campaign_agency", handleCampaignAgencyChange);
    $(document).on(
      "keypress",
      ".js-create-advertiser-inline",
      handleCreateAdvertiserInline
    );
    $(document).on("keypress", ".js-create-dsp-inline", handleCreateDspInline);
    $(document).on(
      "click",
      ".btn-pause-campaign",
      handleCampaignOptionButtonsClick
    );
    $(document).on(
      "click",
      ".btn-activate-campaign",
      handleCampaignOptionButtonsClick
    );

    let $campaignSspInput = $("#campaign_ssp");
    $(document).on("click", ".js_campaign_ssp li a", changeCampaignSspOptions);
    showCampaignSspFields.call(null, $campaignSspInput.val());

    let $campaignTypeInput = $("#campaign_type");
    let $campaignTypeId = $("#campaign_typeId");
    $(document).on(
      "click",
      ".js_campaign_type li a",
      changeCampaignTypeOptions
    );
    showCampaignTypeFields.call(null, $campaignTypeInput.val());
    if ($campaignTypeId.length > 0) {
      showCampaignTypeFields.call(null, $campaignTypeId.val());
    }

    let $campaignCostTypeInput = $("#campaign_costType");
    $(document).on(
      "click",
      ".js_campaign_costType li a",
      changeCampaignCostTypeOptions
    );
    showCampaignCostTypeFields.call(null, $campaignCostTypeInput.val());

    let $campaignsTable = $(".js-campaigns-table");
    if ($campaignsTable.length > 0) {
      let campaignTable = $campaignsTable.DataTable({
        language: {
          url: "/i18n",
          info: Translator.trans("common.paginator.info", {
            entity: Translator.trans("campaigns")
          }),
          infoEmpty: Translator.trans("common.paginator.empty_info", {
            entity: Translator.trans("campaigns")
          })
        },
        ajax: "/campaign/",
        dom: "lrtip",
        columns: [
          {
            data: "name",
            searchable: true,
            orderable: true,
            className: "ellipsis-name-agency",
            render: function(data, type, row) {
              return "<div>" + data + "</div>";
            }
          },
          {
            data: "deal_id",
            searchable: true,
            orderable: true,
            className: "m-hidden"
          },
          {
            data: "agency",
            searchable: true,
            orderable: true,
            className: "m-hidden"
          },
          {
            data: "status",
            searchable: true,
            orderable: true,
            className: "m-hidden"
          },
          {
            data: "actions",
            width: "160px",
            searchable: false,
            className: "",
            orderable: false,
            render: function(data, type, row) {
              let snippet = "";

              if (row.isAdmin) {
                snippet =
                  snippet +
                  '<a href="/campaign/' +
                  row.id +
                  '/clone" class="icon-small clone help" data-value="' +
                  row.name +
                  '"><p class="help-inner">' +
                  Translator.trans("campaigns.clone_campaign") +
                  "</p></a>\n" +
                  '<a href="/campaign/' +
                  row.id +
                  '/edit" class="icon-small edit help" data-value="' +
                  row.name +
                  '"><p class="help-inner">' +
                  Translator.trans("campaigns.edit_campaign") +
                  "</p></a>\n";

                if (row.isActive) {
                  snippet =
                    snippet +
                    '<a href="/campaign/' +
                    row.id +
                    '/pause-confirmation" class="icon-small pause popup-trigger btn-pause btn-pause-campaign help" data-action="pause" data-value="' +
                    row.name +
                    '"><p class="help-inner">' +
                    Translator.trans("campaigns.pause_campaign") +
                    "</p></a>\n";
                } else {
                  snippet =
                    snippet +
                    '<a href="/campaign/' +
                    row.id +
                    '/activate-confirmation" class="icon-small activate popup-trigger btn-pause btn-activate-campaign help" data-action="activate" data-value="' +
                    row.name +
                    '"><p class="help-inner">' +
                    Translator.trans("campaigns.activate_campaign") +
                    "</p></a>\n";
                }

                snippet =
                  snippet +
                  '<a href="/campaign/' +
                  row.id +
                  '/archive" class="icon-small delete popup-trigger btn-delete btn-archive-campaign help" data-action="archive" data-value="' +
                  row.name +
                  '"><p class="help-inner">' +
                  Translator.trans("campaigns.archive_campaign") +
                  "</p></a>";
              } else {
                snippet =
                  snippet +
                  '<a href="/campaign/' +
                  row.id +
                  '" class="icon-small edit help" data-value="' +
                  row.name +
                  '"><p class="help-inner">' +
                  Translator.trans("campaigns.show_campaign") +
                  "</p></a>";
              }

              return snippet;
            }
          }
        ],
        order: [[0, "asc"]],
        bLengthChange: false,
        bSearchable: true,
        bFilter: true,
        searching: true,
        pageLength: 50,
        paging: true,
        processing: true,
        responsive: true,
        pagingType: "simple_numbers",
        bInfo: true,
        deferRender: true,
        drawCallback: function() {
          let $pagination = $(this)
            .closest(".dataTables_wrapper")
            .find(".dataTables_paginate");
          let $info = $(this)
            .closest(".dataTables_wrapper")
            .find(".dataTables_info");
          $pagination.toggle(this.api().page.info().pages > 1);
          $info.toggle(this.api().page.info().pages > 1);
        }
      });

      $("#search_list_name").on("keyup", function() {
        filterGlobal(campaignTable, $("#search_list_name").val(), false, true);
      });
    }
  };

  function changeCampaignTypeOptions() {
    let $selectedCampaignType = $(this);
    let selectedCampaignTypeId = $selectedCampaignType.attr("data-value");

    if (selectedCampaignTypeId) {
      showCampaignTypeFields(selectedCampaignTypeId);
    }
  }

  function changeCampaignCostTypeOptions() {
    let $selectedCampaignCostType = $(this);
    let selectedCampaignCostTypeId = $selectedCampaignCostType.attr(
      "data-value"
    );

    if (selectedCampaignCostTypeId) {
      showCampaignCostTypeFields(selectedCampaignCostTypeId);
    }
  }

  function changeCampaignSspOptions() {
    let selectedCampaignSspId = this.dataset.value;

    if (selectedCampaignSspId) {
      showCampaignSspFields(selectedCampaignSspId);
    }
  }

  function showCampaignTypeFields(campaignTypeValue) {
    if (campaignTypeValue === undefined) {
      campaignTypeValue = -1;
    }

    let $dealFields = $(".js-only-deal");
    let $campaignFields = $(".js-only-campaign");
    if (parseInt(campaignTypeValue) === 1) {
      $dealFields.show();
      $campaignFields.hide();
    } else if (parseInt(campaignTypeValue) === 2) {
      $campaignFields.show();
      $dealFields.hide();
    } else {
      $dealFields.hide();
      $campaignFields.hide();
    }
  }

  function showCampaignCostTypeFields(campaignCostTypeId) {
    if (campaignCostTypeId === undefined) {
      return;
    }

    let cpmField = document.querySelector(".js-cpm");
    let cpvField = document.querySelector(".js-cpv");

    if (parseInt(campaignCostTypeId) === 1) {
      cpmField.style.display = "block";
      cpvField.style.display = "none";
    } else {
      cpmField.style.display = "none";
      cpvField.style.display = "block";
    }
  }

  function showCampaignSspFields(campaignSspId) {
    if (campaignSspId === undefined || campaignSspId === "") {
      return;
    }

    let $lkqdFields = $(".js-only-lkqd");
    let $dealFields = $(".js-only-deal");
    if (parseInt(campaignSspId) === 4) {
      $lkqdFields.show();
      $dealFields.hide();
    } else {
      $lkqdFields.hide();
      $dealFields.show();
    }
  }

  function handleCreateAgencyButtonClick(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    let popupWrapper = document.querySelector(".popup-wrapper");

    let xhr = new XMLHttpRequest();
    xhr.onload = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        popupWrapper.innerHTML = xhr.response;
        popupWrapper.classList.add("active");
        $("#agency_ajax_save").removeAttr("type");
        $("#agency_ajax_save").on("click", function() {
          handleAddAgencyFormSubmit();
        });
      }
    };
    xhr.open("GET", "/agency/new-ajax", true);
    xhr.send(null);
  }

  function handleAddAgencyFormSubmit() {
    clearErrors();
    let formSerialize = $(this).serialize();

    $.post(
      "/agency/new-ajax",
      formSerialize,
      function(r) {
        if (r.status === "success") {
          addNewOptionToDropdown(r, "agency");
          closePopup();
          $("#campaign_agency").trigger("change");
        } else {
          showActionModal("error");
          showErrors(r.errors, r.form);
        }
      },
      "JSON"
    );
    return false;
  }
  window.handleAddAgencyFormSubmit = handleAddAgencyFormSubmit;

  function closePopup() {
    $(".popup-wrapper, .popup-container").removeClass("active");
  }

  function clearErrors() {
    $(".popup-body .form-field.error")
      .removeClass("error")
      .children(".js-error-list")
      .remove();
  }

  /**
   * Show a modal with a message
   * @param modal type of modal (success, error, warning)
   * @param message translation key
   */
  function showActionModal(modal, message = null) {
    let $actionModal = $(".action-modals." + modal);
    $actionModal.addClass("active");

    if (message) {
      $actionModal
        .find(".js-action-modal-message")
        .text(Translator.trans(message));
    } else {
      $actionModal
        .find(".js-action-modal-message")
        .text(Translator.trans("action_modal." + modal));
    }

    setTimeout(function() {
      $(".action-modals").removeClass("active");
    }, ACTION_MODAL_SHOW_TIME);
  }

  function showErrors(errors, form = null) {
    let formName = form == null ? "form" : form;
    for (let inputId in errors) {
      let $errorList = $('<ul class="js-error-list"></ul>');
      let $errorItem = $("<li></li>").text(errors[inputId]);
      $errorList.append($errorItem);

      $('input[name*="' + form + "[" + inputId + ']"]')
        .parents(".form-field")
        .addClass("error")
        .append($errorList);
    }
    $(".js-button")
      .attr("disabled", false)
      .removeClass("disabled");
  }

  function handleCampaignOptionButtonsClick(evt) {
    evt.preventDefault();
    let popupWrapper = document.querySelector(".popup-wrapper");
    let button = evt.currentTarget;
    let url = button.getAttribute("href");
    let action = button.dataset.action;

    let xhr = new XMLHttpRequest();
    xhr.onload = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        popupWrapper.innerHTML = xhr.response;

        let popupArchiveCampaign = document.querySelector(
          `.popup-${action}-campaign`
        );
        popupWrapper.classList.add("active");
        popupArchiveCampaign.classList.add("active");
      }
    };
    xhr.open("GET", url, true);
    xhr.send(null);
  }

  function handleCampaignAgencyChange(evt) {
    let agencyId = evt.target.value;
    let xhr = new XMLHttpRequest();
    xhr.onload = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        let agencyRebate = JSON.parse(xhr.response);
        $("#campaign_rebate").val(agencyRebate["rebate"]);
      }
    };
    xhr.open("GET", `/agency/${agencyId}/rebate`, true);
    xhr.send(null);
  }

  function handleCreateDspInline(evt) {
    if (evt.keyCode === 13) {
      evt.preventDefault();
      let dspInput = evt.currentTarget;
      dspInput.disabled = true;
      dspInput.parentElement.classList.add("disabled");
      let payload = {
        name: evt.currentTarget.value
      };
      let xhr = new XMLHttpRequest();
      xhr.onload = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          let dsp = JSON.parse(xhr.response);

          addNewOptionToDropdown(dsp, "dsp", dspInput);
        }
      };
      xhr.open("POST", `/dsp/new-ajax`, true);
      xhr.setRequestHeader("Content-type", "application/json; charset=utf-8");
      xhr.send(JSON.stringify(payload));
    }
  }

  function handleCreateAdvertiserInline(evt) {
    if (evt.keyCode === 13) {
      evt.preventDefault();
      let advertiserInput = evt.currentTarget;
      advertiserInput.disabled = true;
      advertiserInput.parentElement.classList.add("disabled");
      let payload = {
        name: evt.currentTarget.value
      };
      let xhr = new XMLHttpRequest();
      xhr.onload = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          let advertiser = JSON.parse(xhr.response);

          addNewOptionToDropdown(advertiser, "advertiser", advertiserInput);
        }
      };
      xhr.open("POST", `/advertiser/new-ajax`, true);
      xhr.setRequestHeader("Content-type", "application/json; charset=utf-8");
      xhr.send(JSON.stringify(payload));
    }
  }

  /**
   * Function to add a new option in a dropdown
   * @param field
   * @param type
   * @param input
   */
  function addNewOptionToDropdown(field, type, input = null) {
    let link = document.createElement("a");
    let listElement = document.createElement("li");
    let campaignDropdown = document.querySelector(`.js_campaign_${type}`);

    link.setAttribute("href", "#");
    link.dataset.value = field.id;
    link.innerText = field.name;
    listElement.classList.add("selected");

    listElement.append(link);

    campaignDropdown.children.forEach(function(child) {
      child.classList.remove("selected");
    });
    campaignDropdown.parentElement.querySelector("button").innerText =
      field.name;
    campaignDropdown.parentElement.querySelector(`#campaign_${type}`).value =
      field.id;

    if (input) {
      campaignDropdown.parentElement.classList.remove("open");
      input.parentElement.classList.remove("disabled");
      input.disabled = false;
      input.value = "";
    }

    campaignDropdown.append(listElement);
  }

  return {
    init
  };
})(jQuery);

campaignIndex.init();
