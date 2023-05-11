require("datatables.net");
require("datatables.net-plugins/sorting/datetime-moment");
require("../../datatables/plugins/dataTables.cellEdit");
let advertisersIndex = (function($) {
  "use strict";
  let init = function() {
    $(document).on(
      "click",
      ".btn-delete-advertiser",
      handleDeleteAdvertiserButtonClick
    );

    $(document).on("change", "#create-user", handleCreateUserButtonChange);
    let createUserButton = document.querySelector("#create-user");
    if (createUserButton) {
      setOnlyCreateUserFieldsDisplayStyle(createUserButton.checked);
    }

    let $advertisersTable = $(".js-advertisers-table");
    if ($advertisersTable.length > 0) {
      $advertisersTable.DataTable({
        language: {
          url: "/i18n",
          info: Translator.trans("common.paginator.info", {
            entity: Translator.trans("advertisers")
          }),
          infoEmpty: Translator.trans("common.paginator.empty_info", {
            entity: Translator.trans("advertisers")
          })
        },
        ajax: "/advertiser/",
        dom: "lrtip",
        columns: [
          {
            data: "name",
            searchable: true,
            orderable: true,
            className: "m-hidden"
          },
          { data: "username", searchable: true, orderable: true },
          {
            data: "email",
            searchable: true,
            orderable: true,
            className: "m-hidden"
          },
          {
            data: "actions",
            width: "130px",
            searchable: false,
            orderable: false,
            class: "align-right nowrap",
            render: function(data, type, row) {
              let impersonatingOption = "";

              if (row.hasUser) {
                if (row.isImpersonating) {
                  impersonatingOption =
                    '<a class="icon-small impersonate off help"><p class="help-inner">' +
                    Translator.trans("users.simulate.not_allowed") +
                    "</p></a>\n";
                } else {
                  impersonatingOption =
                    '<a href="/?_switch_user=' +
                    row.username +
                    '" class="icon-small impersonate help"><p class="help-inner">' +
                    Translator.trans("advertisers.simulate") +
                    "</p></a>\n";
                }
              }

              return (
                '<a href="/advertiser/' +
                row.id +
                '/edit" class="icon-small edit help" data-value="' +
                row.name +
                '"><p class="help-inner">' +
                Translator.trans("advertisers.edit_advertiser") +
                "</p></a>\n" +
                impersonatingOption +
                '<a href="/advertiser/' +
                row.id +
                '/delete" class="icon-small delete popup-trigger btn-delete btn-delete-advertiser help" data-value="' +
                row.name +
                '"><p class="help-inner">' +
                Translator.trans("advertisers.delete_advertiser") +
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

  function handleDeleteAdvertiserButtonClick(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    let popupWrapper = document.querySelector(".popup-wrapper");
    let deleteUrl = evt.currentTarget.getAttribute("href");

    let xhr = new XMLHttpRequest();
    xhr.onload = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        popupWrapper.innerHTML = xhr.response;

        let popupDeleteAdvertiser = document.querySelector(
          ".popup-delete-advertiser"
        );
        popupWrapper.classList.add("active");
        popupDeleteAdvertiser.classList.add("active");
      }
    };
    xhr.open("GET", deleteUrl, true);
    xhr.send(null);
  }

  function handleCreateUserButtonChange(evt) {
    setOnlyCreateUserFieldsDisplayStyle(evt.target.checked);
  }

  function setOnlyCreateUserFieldsDisplayStyle(setDisplayBlock) {
    let onlyCreateUserFields = document.querySelector(".js-only-create-user");
    if (setDisplayBlock) {
      onlyCreateUserFields.style.display = "block";
    } else {
      onlyCreateUserFields.style.display = "none";
    }
  }

  return {
    init
  };
})(jQuery);

advertisersIndex.init();
