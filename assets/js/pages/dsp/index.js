require("datatables.net");
require("datatables.net-plugins/sorting/datetime-moment");
require("../../datatables/plugins/dataTables.cellEdit");
let dspIndex = (function($) {
  "use strict";
  let init = function() {
    $(document).on("click", ".btn-delete-dsp", handleDeleteDspButtonClick);

    let $dspsTable = $(".js-dsps-table");
    if ($dspsTable.length > 0) {
      $dspsTable.DataTable({
        language: {
          url: "/i18n",
          info: Translator.trans("common.paginator.info", {
            entity: Translator.trans("dsps")
          }),
          infoEmpty: Translator.trans("common.paginator.empty_info", {
            entity: Translator.trans("dsps")
          })
        },
        ajax: "/dsp/",
        dom: "lrtip",
        columns: [
          { data: "name", searchable: true, orderable: true },
          {
            data: "actions",
            width: "85px",
            searchable: false,
            orderable: false,
            className: 'nowrap',
            render: function(data, type, row) {
              return (
                '<a href="/dsp/' +
                row.id +
                '/edit" class="icon-small edit help" data-value="' +
                row.name +
                '"><p class="help-inner">' +
                Translator.trans("dsps.edit_dsp") +
                "</p></a>\n" +
                '<a href="/dsp/' +
                row.id +
                '" class="icon-small delete popup-trigger btn-delete btn-delete-dsp help" data-value="' +
                row.name +
                '"><p class="help-inner">' +
                Translator.trans("dsps.delete_dsp") +
                "</p></a>"
              );
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
    }
  };

  function handleDeleteDspButtonClick(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    let popupWrapper = document.querySelector(".popup-wrapper");
    let deleteUrl = evt.currentTarget.getAttribute("href");

    let xhr = new XMLHttpRequest();
    xhr.onload = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        popupWrapper.innerHTML = xhr.response;

        let popupDeleteDsp = document.querySelector(".popup-delete-dsp");
        popupWrapper.classList.add("active");
        popupDeleteDsp.classList.add("active");
      }
    };
    xhr.open("GET", deleteUrl, true);
    xhr.send(null);
  }

  return {
    init
  };
})(jQuery);

dspIndex.init();
