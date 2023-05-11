const moment = require("moment");
const showActionModal = require("../../components/showActionModal");
const $ = require("jquery");
window.jQuery = window.$ = $;
require("datatables.net");
require("datatables.net-plugins/sorting/datetime-moment");
require("../../datatables/plugins/dataTables.cellEdit");

let targetIndex = (function($) {
  "use strict";
  let init = function() {
    let pathName = document.location.pathname;
    let userId = pathName.split("/")[2];
    let $targetsTable = $(".js-targets-table");
    let lang = document.querySelector("html").getAttribute("lang");

    if ($targetsTable.length > 0) {
      $.fn.dataTable.moment("MMMM, YYYY", Translator.locale);
      $.fn.dataTable.moment("DD/MM/YYYY");
      let table = $targetsTable.DataTable({
        language: {
          url: "/i18n",
          info: Translator.trans("common.paginator.info", {
            entity: Translator.trans("dsps")
          }),
          infoEmpty: Translator.trans("common.paginator.empty_info", {
            entity: Translator.trans("dsps")
          })
        },
        ajax: `/target/${userId}/`,
        dom: "lrtip",
        columns: [
          { data: "id", searchable: false, orderable: false, visible: false },
          { data: "goal", searchable: false, orderable: false },
          { data: "reached", searchable: true, orderable: true },
          {
            data: "month",
            searchable: true,
            className: "text-capitalize",
            orderable: true,
            render: function(data, type, row) {
              return moment(data, "MM")
                .locale(lang)
                .format("MMMM");
            }
          },
          { data: "year", searchable: true, orderable: true }
        ],
        order: [[4, "desc"]],
        bLengthChange: false,
        bSearchable: true,
        bFilter: true,
        searching: true,
        pageLength: 50,
        paging: true,
        responsive: true,
        processing: true,
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

      table.MakeCellsEditable({
        onUpdate: handleCellUpdate,
        onValidate: handleCellValidation,
        inputCss: "editable-input",
        columns: [1],
        allowNulls: {
          columns: [],
          errorClass: "error"
        },
        confirmationButton: {
          confirmCss: "confirm-button",
          cancelCss: "cancel-button"
        },
        inputTypes: [
          {
            column: 1,
            type: "number",
            options: null
          }
        ]
      });
    }
  };

  function handleCellUpdate(updatedCell, updatedRow) {
    let target = updatedRow.data();
    let xhr = new XMLHttpRequest();
    let payload = new FormData();
    payload.append("goal", target.goal);
    xhr.onload = () => {
      if (xhr.readyState === 4 && xhr.status === 200) {
        showActionModal("success");
      } else {
        showActionModal("error");
      }
    };
    xhr.open("POST", `/target/${target.id}/update`, true);
    xhr.send(payload);
  }

  function handleCellValidation(cell, row, newValue) {
    if (newValue === "") {
      showActionModal("error", "common.field_must_be_completed");
      return false;
    }

    return true;
  }

  return {
    init
  };
})(jQuery);

targetIndex.init();
