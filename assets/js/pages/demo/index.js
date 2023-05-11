require("datatables.net");
require("datatables.net-plugins/sorting/datetime-moment");
require("../../datatables/plugins/dataTables.cellEdit");

let demoIndex = (function() {
  "use strict";
  let init = function() {
    $(document).on("click", ".btn-delete-demo", handleDeletedemoButtonClick);

    let $demoTable = $(".js-demo-table");
    if ($demoTable.length > 0) {
      let demoTable = $demoTable.DataTable({
        language: {
          url: "/i18n",
          info: Translator.trans("common.paginator.info", {
            entity: Translator.trans("demos")
          }),
          infoEmpty: Translator.trans("common.paginator.empty_info", {
            entity: Translator.trans("demos")
          })
        },
        ajax: "/demo/",
        dom: "lrtip",
        columns: [
          {
            data: "url",
            searchable: true,
            orderable: true
          },
          {
            data: "format",
            searchable: true,
            orderable: true
          },
          {
            data: "status",
            searchable: true,
            orderable: true
          },
          {
            data: "video",
            searchable: true,
            orderable: true
          },
          {
            data: "date",
            searchable: true,
            orderable: true
          },
          {
            data: "actions",
            width: "165px",
            className: "align-right nowrap",
            searchable: false,
            orderable: false,
            render: function(data, type, row) {
              let targets = "";

              return (
                targets +
                '<span class="icon-small clone help" onclick="window.copy(document.getElementById(\'url_' +
                row.id +
                '\').href)"><p class="help-inner">' +
                Translator.trans("demo.copy") +
                "</p></span>" +
                '<a href="/demo/' +
                row.id +
                '/edit" class="icon-small edit help" data-value="' +
                row.name +
                '"><p class="help-inner">' +
                Translator.trans("demo.edit_demo") +
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
        filterGlobal(demoTable, $("#search_list_name").val(), false, true);
      });
    }
  };

  function handleDeletedemoButtonClick(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    let popupWrapper = document.querySelector(".popup-wrapper");
    let deleteUrl = evt.currentTarget.getAttribute("href");

    let xhr = new XMLHttpRequest();
    xhr.onload = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        popupWrapper.innerHTML = xhr.response;

        let popupDeleteAdvertiser = document.querySelector(
          ".popup-delete-demo"
        );
        popupWrapper.classList.add("active");
        popupDeleteAdvertiser.classList.add("active");
      }
    };
    xhr.open("GET", deleteUrl, true);
    xhr.send(null);
  }
  $(function() {
    formatDefaultValues($(".format li.selected a").attr("data-value"), 1);
    $("#demo_url").change(setPreviewDemoUrl);
    $("#demo_url").on("keyup", setPreviewDemoUrl);
    $(".url_format li").on("click", function() {
      $("#demo_url_format").val(
        $(this)
          .find("a")
          .attr("data-value")
      );
      setPreviewDemoUrl();
    });
    $(".format li").on("click", function() {
      var cad = $(this)
        .find("a")
        .attr("data-value");
      formatDefaultValues(cad, 2);
    });

    $("#btn-copy-form").on("click", function() {
      copy($("#preview_demo_url").html());
    });

    $("#demo_url").on("keypress", function(event) {
      var regex = new RegExp("^[a-zA-Z0-9_-]+$");
      var key = String.fromCharCode(
        !event.charCode ? event.which : event.charCode
      );
      if (!regex.test(key)) {
        event.preventDefault();
        return false;
      }
    });
    $("#desktop, #mobile").change(function() {
      toogleShowDiv(this);
    });

    $("#video_file").change(function() {
      loadVideo(this);
      console.log("H1");
    });

    $("#demo_video").change(function() {
      loadVideo2(this);
      console.log("H2");
    });

    setPreviewDemoUrl();

    toogleShowDiv($("#desktop"));
    toogleShowDiv($("#mobile"));

    loadVideo2($("#demo_video"));
  });

  function loadVideo2(input) {
    console.log("H3");

    if ($(input).val() != null && $(input).val() != "") {
      console.log($(input).val());
      $("#loadVideo").attr("src", $(input).val());
      $("#loadVideo").removeClass("hidden");
    }
  }

  function loadVideo(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $("#demo_video").val(e.target.result);
        loadVideo2($("#demo_video"));
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  window.loadVideo = loadVideo;

  function toogleShowDiv(x) {
    formatDefaultValues($(".format li.selected a").attr("data-value"), 1);
    if ($(x).is(":checked")) {
      $("." + $(x).attr("id")).removeClass("hidden");
    } else {
      $("." + $(x).attr("id")).addClass("hidden");
    }
  }

  function formatDefaultValues(cad, i) {
    var w = "";
    var h = "";

    switch (cad) {
      case "intext":
        w = "640";
        h = "360";
        break;

      case "slider":
        w = "400";
        h = "225";
        break;
    }
    if (i == 2) {
      $("#demo_width").val(w);
      $("#demo_height").val(h);

      $("#demo_width_mobile").val(w);
      $("#demo_height_mobile").val(h);
    }

    $("#demo_width").attr(
      "placeholder",
      Translator.trans("demo.default") + " " + w
    );
    $("#demo_height").attr(
      "placeholder",
      Translator.trans("demo.default") + " " + h
    );
    $("#demo_width_mobile").attr(
      "placeholder",
      Translator.trans("demo.default") + " " + w
    );
    $("#demo_height_mobile").attr(
      "placeholder",
      Translator.trans("demo.default") + " " + h
    );
  }

  function setPreviewDemoUrl() {
    var host = "http://vidoomy.com/demos/";
    var url_format = $("#demo_url_format").val();
    var url = $("#demo_url").val();

    if (url_format != "demo") {
      url_format += "/";
    } else {
      url_format = "";
    }
    $("#preview_demo_url").html(host + url_format + url);
  }

  function copy(text) {
    var aux = document.createElement("input");
    aux.setAttribute("value", text);
    document.body.appendChild(aux);
    aux.select();
    document.execCommand("copy");
    document.body.removeChild(aux);
    $("#btn-copy-form").html(Translator.trans("demo.copied"));
    showActionModal("success", Translator.trans("demo.copiedUrlMessage"));
  }
  window.copy = copy;

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
    }, 2000);
  }

  return {
    init
  };
})(jQuery);

demoIndex.init();
