require("datatables.net");
require("datatables.net-plugins/sorting/datetime-moment");
require("../../datatables/plugins/dataTables.cellEdit");
let agencyIndex = (function($) {
  "use strict";
  let init = function() {
    $(document).on(
      "click",
      ".js-add-account-manager",
      handleAddAccountManagerButtonClick
    );
    $(document).on(
      "click",
      ".js-delete-manager",
      handleDeleteManagerButtonClick
    );
    $(document).on(
      "click",
      ".btn-archive-agency",
      handleArchiveAgencyButtonClick
    );

    let $agenciesTable = $(".js-agencies-table");
    if ($agenciesTable.length > 0) {
      let agencyTable = $agenciesTable.DataTable({
        language: {
          url: "/i18n",
          info: Translator.trans("common.paginator.info", {
            entity: Translator.trans("agencies")
          }),
          infoEmpty: Translator.trans("common.paginator.empty_info", {
            entity: Translator.trans("agencies")
          })
        },
        ajax: "/agency/",
        dom: "lrtip",
        columns: [
          { data: "name", searchable: true, orderable: true },
          { data: "type", searchable: true, orderable: true },
          {
            data: "salesManager",
            searchable: true,
            orderable: true,
            className: "m-hidden"
          },
          {
            data: "countries",
            className: "m-hidden",
            searchable: true,
            orderable: true,
            render: function(data) {
              let snippet = " <ul>";
              for (let i = 0; i < data.length; i++) {
                snippet = snippet + "<li>" + data[i] + "</li>";
              }

              snippet = snippet + "</ul>";
              return snippet;
            }
          },
          {
            data: "actions",
            width: "85px",
            searchable: false,
            orderable: false,
            className: 'nowrap',
            render: function(data, type, row) {
              let snippet = "";

              if (row.isAdmin) {
                snippet =
                  snippet +
                  '<a href="/agency/' +
                  row.id +
                  '/edit" class="icon-small edit help" data-value="' +
                  row.name +
                  '"><p class="help-inner">' +
                  Translator.trans("agencies.edit_agency") +
                  "</p></a>\n" +
                  '<a href="/agency/' +
                  row.id +
                  '/archive" class="icon-small delete popup-trigger btn-delete btn-archive-agency help" data-value="' +
                  row.name +
                  '"><p class="help-inner">' +
                  Translator.trans("agencies.archive_agency") +
                  "</p></a>";
              } else {
                snippet =
                  snippet +
                  '<a href="/agency/' +
                  row.id +
                  '" class="icon-small edit help" data-value="' +
                  row.name +
                  '"><p class="help-inner">' +
                  Translator.trans("agencies.show_agency") +
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
        filterGlobal(agencyTable, $("#search_list_name").val(), false, true);
      });
    }
  };

  function makeId(length) {
    let result = "";
    let characters =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    let charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
  }

  function handleAddAccountManagerButtonClick() {
    let template =
      '<div style="margin-bottom: 20px;" class="js_agency_manager_container"><hr/>' +
      '<div id="agency_account_manager___name__">' +
      '    <div class="form-field">' +
      '        <label for="agency_account_manager___name___name" class="required">' +
      Translator.trans("common.name.label") +
      "</label>" +
      '        <input type="text" id="agency_account_manager___name___name" name="agency[account_manager][__name__][name]" required="required" placeholder="' +
      Translator.trans("common.name.placeholder") +
      '" />' +
      "    </div>" +
      '    <div class="form-field">' +
      '        <label for="agency_account_manager___name___email" class="required">' +
      Translator.trans("common.email.label") +
      "</label>" +
      '        <input type="email" id="agency_account_manager___name___email" name="agency[account_manager][__name__][email]" required="required" placeholder="' +
      Translator.trans("common.email.placeholder") +
      '" />' +
      "    </div>" +
      '    <div class="form-field">' +
      '        <label for="agency_account_manager___name___phone" class="required">' +
      Translator.trans("common.phone.label") +
      "</label>" +
      '        <input type="text" id="agency_account_manager___name___phone" name="agency[account_manager][__name__][phone]" required="required" placeholder="' +
      Translator.trans("common.phone.placeholder") +
      '" />' +
      "    </div>" +
      '    <div class="form-field" style="text-align: right">' +
      '        <button data-parent="agency_account_manager___name__" type="button" id="agency_account_manager___name___delete" name="agency[account_manager][__name__][delete]" class="btn btn-square btn-blue js-delete-manager" formnovalidate="formnovalidate">' +
      Translator.trans("common.delete_manager.label") +
      "</button>" +
      "    </div>" +
      "</div></div>";

    let container = $(".js-managers-container");
    let name = makeId(10);
    let newWidget = template.replace(/__name__/g, name);

    let newElem = $(newWidget);
    newElem.appendTo(container);
  }

  function handleDeleteManagerButtonClick(evt) {
    $(evt.currentTarget)
      .parents(".js_agency_manager_container")
      .fadeOut("slow", function() {
        $(this).remove();
      });
  }

  function handleArchiveAgencyButtonClick(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    let popupWrapper = document.querySelector(".popup-wrapper");
    let deleteUrl = evt.currentTarget.getAttribute("href");

    let xhr = new XMLHttpRequest();
    xhr.onload = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        popupWrapper.innerHTML = xhr.response;

        let popupArchiveAgency = document.querySelector(
          ".popup-archive-agency"
        );
        popupWrapper.classList.add("active");
        popupArchiveAgency.classList.add("active");
      }
    };
    xhr.open("GET", deleteUrl, true);
    xhr.send(null);
  }

  return {
    init
  };
})(jQuery);

agencyIndex.init();
