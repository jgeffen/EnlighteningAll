document.addEventListener("DOMContentLoaded", function () {
  // Variable Defaults
  var ignoreAjax = ["/user/manage/transactions.html"];

  // Set the Defaults for DataTables Initialisation
  $.extend(
    true,
    $.fn.dataTable.defaults,
    {
      lengthMenu: [
        [10, 25, 50, 100, 250, 500, -1],
        [10, 25, 50, 100, 250, 500, "All"],
      ],
      stateSave: true,
      stateSaveCallback: function (settings, data) {
        // Variable Defaults
        var tableFilter = $("#table-filter").find("select");

        // Modify Data
        if (tableFilter.length)
          data = Object.assign(data, { category: tableFilter.val() });
        //console.log("location.pathname");
        //console.log(location.pathname);
        // Save Data
        localStorage.setItem(
          "DataTables_" + settings.sInstance + "_" + location.pathname,
          JSON.stringify(data)
        );
      },
      stateLoadCallback: function (settings) {
        // Load Data
        return JSON.parse(
          localStorage.getItem(
            "DataTables_" + settings.sInstance + "_" + location.pathname
          )
        );
      },
      dom:
        '<"row"<"col-sm-12 col-md-3"l><"col-sm-12 col-md-6 table-filter-container"><"col-sm-12 col-md-3"f>>' +
        '<"row"<"col-12"tr>>' +
        '<"row dataTables_paginate__wrapper"<"col-12 col-md-5"i><"col-12 col-md-7"p>>',
      initComplete: function (settings, json) {
        // Variable Defaults
        var dataTable = $(this).DataTable();
        var localStorageData = dataTable.state.loaded();

        // Bind Search Event to Turn Off Row Reorder
        dataTable.on("search.dt", function () {
          // Check Search
          dataTable.search().length
            ? dataTable.rowReorder.disable()
            : dataTable.rowReorder.enable();
        });

        // Bind Reorder Event to Sort Table
        dataTable.on("row-reorder", function (event, details) {
          // Variable Defaults
          var currentTable = $(event.target);
          var tableOptions = currentTable.data("tableOptions");
          var dataTable = currentTable.DataTable();

          // Check Details
          if (details.length) {
            // Handle Ajax
            $.ajax("/user/sort/" + tableOptions.type, {
              data: {
                rows: $.map(details, function (detail) {
                  return {
                    id: dataTable.row(detail.node).data().id,
                    position: detail.newData,
                  };
                }),
              },
              dataType: "json",
              method: "post",
              async: true,
              beforeSend: dataTable.rowReorder.disable,
              complete: dataTable.rowReorder.enable,
              success: function (response) {
                // Switch Status
                switch (response.status) {
                  case "success":
                    // Reload
                    dataTable.ajax.reload();
                    break;
                  case "error":
                    displayMessage(
                      response.message ||
                        Object.keys(response.errors)
                          .map(function (key) {
                            return response.errors[key];
                          })
                          .join("<br>"),
                      "alert",
                      null
                    );
                    break;
                  default:
                    displayMessage(
                      response.message || "Something went wrong.",
                      "alert"
                    );
                }
              },
            });
          }
        });

        // Check JSON
        if (json instanceof Object) {
          // Check Categories
          if (json.hasOwnProperty("categories") && json.categories) {
            // Variable Defaults
            var inputSelect = $(json.categories.html);
            var defaultSelection =
              inputSelect.find("option:first").attr("value") ||
              Object.keys(json.categories.data)[0] ||
              null;
            var categorySelection =
              localStorageData && localStorageData.hasOwnProperty("category")
                ? localStorageData.category
                : defaultSelection;

            // Extend Filter Data
            $.fn.dataTable.ext.search.push(function (settings, row, index) {
              // Variable Defaults
              var tableFilter = $("#table-filter").find("select");
              var data = dataTable.row(index).data();
              var filter = json.categories.filter;
              var value = tableFilter.val();

              // Show All on Failure
              if (!tableFilter.length || !filter) return true;

              // Check User Filtering
              if (data.hasOwnProperty("user") && data.user !== null) {
                if (/user\.[0-9]+/.test(value))
                  return (
                    data.user.id.toString() === value.match(/user\.([0-9]+)/)[1]
                  );
              }

              // Check Action Filtering
              if (data.hasOwnProperty("action")) {
                if (/action\.[\w\s]+/.test(value))
                  return data.action === value.match(/action\.([\w\s]+)/)[1];
              }

              // Switch Value to Return Filtered
              switch (value) {
                case "default.show_all":
                  return true;
                default:
                  // Check Filter
                  if (data.item.hasOwnProperty(filter)) {
                    // Switch Type of Filter
                    switch (typeof data.item[filter]) {
                      case "number":
                        return (
                          data.item.hasOwnProperty(filter) &&
                          data.item[filter] === parseInt(value)
                        );
                      case "bigint":
                      case "boolean":
                      case "function":
                      case "object":
                      case "string":
                      case "symbol":
                      case "undefined":
                      default:
                        return (
                          data.item.hasOwnProperty(filter) &&
                          data.item[filter] === value
                        );
                    }
                  } else return false;
              }
            });

            // Attach Table Filter
            $(".table-filter-container", dataTable.table().container())
              .append(
                // Bind Change Event to Table Filter
                inputSelect.on("change", "select", dataTable.draw)
              )
              .find("select")
              .val(categorySelection)
              .trigger("change");
          }
        }
      },
    },
    ignoreAjax.indexOf(location.pathname) !== -1
      ? {}
      : {
          ajax: {
            method: "post",
            complete: function (jqXHR) {
              // Set Response
              var response = jqXHR.responseJSON;

              //console.log(response);

              // Switch Status
              switch (response.status) {
                case "success":
                  // Console Message
                  console.info(response.message);
                  break;
                case "error":
                  displayMessage(
                    response.message ||
                      Object.keys(response.errors)
                        .map(function (key) {
                          return response.errors[key];
                        })
                        .join("<br>"),
                    "alert",
                    null
                  );
                  break;
                default:
                  displayMessage(
                    response.message || "Something went wrong.",
                    "alert"
                  );
              }
            },
            async: false,
          },
        }
  );

  // Set the Defaults for Search Pane
  $.extend(true, $.fn.dataTable.SearchPane.classes, {
    buttonGroup: "btn-group",
    buttonSub: "dtsp-buttonSub",
    clear: "dtsp-clear",
    clearAll: "dtsp-clearAll",
    clearButton: "clearButton",
    container: "dtsp-searchPane",
    countButton: "dtsp-countButton",
    disabledButton: "disabled",
    hidden: "dtsp-hidden",
    hide: "dtsp-hide",
    layout: "dtsp-",
    name: "dtsp-name",
    nameButton: "dtsp-nameButton",
    nameCont: "dtsp-nameCont",
    narrow: "col",
    pane: {
      container: "table",
    },
    paneButton: "btn btn-light",
    paneInputButton: "dtsp-paneInputButton",
    pill: "pill badge badge-pill badge-primary",
    search: "form-control search",
    searchCont: "input-group",
    searchIcon: "dtsp-searchIcon",
    searchLabelCont: "input-group-append",
    selected: "dtsp-selected",
    smallGap: "dtsp-smallGap",
    subRow1: "dtsp-subRow1",
    subRow2: "dtsp-subRow2",
    subRowsContainer: "dtsp-subRowsContainer",
    table: "table-sm",
    title: "dtsp-title",
    topRow: "dtsp-topRow",
  });

  // Set the Defaults for Search Panes
  $.extend(true, $.fn.dataTable.SearchPanes.classes, {
    clear: "dtsp-clear",
    clearAll: "dtsp-clearAll btn btn-light",
    container: "dtsp-searchPanes",
    disabledButton: "disabled",
    emptyMessage: "dtsp-emptyMessage",
    hide: "dtsp-hidden",
    panes: "dtsp-panes dtsp-panesContainer basic-panes",
    search: "dtsp-search",
    title: "dtsp-title",
    titleRow: "dtsp-titleRow",
  });
});
