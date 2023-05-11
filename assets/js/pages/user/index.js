require("datatables.net");
require("datatables.net-plugins/sorting/datetime-moment");
require("../../datatables/plugins/dataTables.cellEdit");
let userIndex = (function($) {
  "use strict";
  let init = function() {
    $(document).on("click", ".btn-delete-user", handleDeleteUserButtonClick);

    let $rolesInput = $("#user_roles");
    let $parentRolesInputDiv = $rolesInput.parent();

    $parentRolesInputDiv.on("click", ".dropdown-menu li a", changeRolesOptions);
    showRoleFields.call(null, $rolesInput.val());

    let $userTable = $(".js-user-table");
    if ($userTable.length > 0) {
      let userTable = $userTable.DataTable({
        language: {
          url: "/i18n",
          info: Translator.trans("common.paginator.info", {
            entity: Translator.trans("users")
          }),
          infoEmpty: Translator.trans("common.paginator.empty_info", {
            entity: Translator.trans("users")
          })
        },
        ajax: "/user/",
        dom: "lrtip",
        columns: [
          { data: "username", searchable: true, orderable: true },
          {
            data: "name",
            searchable: true,
            orderable: true,
            className: "m-hidden"
          },
          {
            data: "email",
            searchable: true,
            orderable: true,
            className: "m-hidden"
          },
          { data: "role", searchable: true, orderable: true },
          {
            data: "actions",
            width: "165px",
            className: "align-right nowrap",
            searchable: false,
            orderable: false,
            render: function(data, type, row) {
              let impersonatingOption,
                targets = "";
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
                  Translator.trans("users.simulate") +
                  "</p></a>\n";
              }

              if (!row.isAdmin && !row.isCampaignViewer) {
                targets =
                  '<a href="/target/' +
                  row.id +
                  '" class="icon-small target help"><p class="help-inner">' +
                  Translator.trans("targets") +
                  "</p></a>\n";
              }

              return (
                targets +
                '<a href="/user/' +
                row.id +
                '/edit" class="icon-small edit help" data-value="' +
                row.name +
                '"><p class="help-inner">' +
                Translator.trans("users.edit_user") +
                "</p></a>\n" +
                impersonatingOption +
                '<a href="/user/' +
                row.id +
                '" class="icon-small delete popup-trigger btn-delete btn-delete-user help" data-value="' +
                row.name +
                '"><p class="help-inner">' +
                Translator.trans("users.delete_user") +
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

      $("#search_list_name").on("keyup", function() {
        filterGlobal(userTable, $("#search_list_name").val(), false, true);
      });
    }
  };

  function changeRolesOptions() {
    let $selectedRole = $(this);
    let selectedRoleId = $selectedRole.attr("data-value");

    if (selectedRoleId) {
      showRoleFields(selectedRoleId);
    }
  }

  function showRoleFields(roleId) {
    if (roleId === undefined) {
      return;
    }

    let $onlySalesManager = $(".js-only-sales-manager");
    if (roleId === "ROLE_SALES_MANAGER") {
      $onlySalesManager.show();
    } else {
      $onlySalesManager.hide();
    }

    let $onlyManagers = $(".js-only-managers");
    if (
      roleId === "ROLE_SALES_MANAGER" ||
      roleId === "ROLE_SALES_MANAGER_HEAD"
    ) {
      $onlyManagers.show();
    } else {
      $onlyManagers.hide();
    }

    let $onlyCampaignViewers = $(".js-only-campaign-viewer");
    if (roleId === "ROLE_CAMPAIGN_VIEWER") {
      $onlyCampaignViewers.show();
    } else {
      $onlyCampaignViewers.hide();
    }
  }

  function handleDeleteUserButtonClick(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    let popupWrapper = document.querySelector(".popup-wrapper");
    let deleteUrl = evt.currentTarget.getAttribute("href");

    let xhr = new XMLHttpRequest();
    xhr.onload = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        popupWrapper.innerHTML = xhr.response;

        let popupDeleteAdvertiser = document.querySelector(
          ".popup-delete-user"
        );
        popupWrapper.classList.add("active");
        popupDeleteAdvertiser.classList.add("active");
      }
    };
    xhr.open("GET", deleteUrl, true);
    xhr.send(null);
  }

  return {
    init
  };
})(jQuery);

userIndex.init();
