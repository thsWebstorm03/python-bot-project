"use strict";

var socket = new WebSocket(WEB_SOCKET_URL);

var LogsModule = (function () {
  const initWebSocket = function () {};

  const getAllLogs = function () {
    $.get(BASE_URL + "controller/logsController.php", (res) => {
      $("#tbl-logs tbody").html(res);
    });
  };

  const dateTimeFormat = function(totalMilliseconds) {
    const datetime = new Date(totalMilliseconds);
    return `${datetime.getFullYear()}-${datetime.getMonth() + 1}-${datetime.getDay()} ${datetime.getHours()}:${datetime.getMinutes()}:${datetime.getSeconds()}:${datetime.getMilliseconds()}`;
  }

  const isTableEmpty = function () {
    if (
      $("#tbl-logs tbody tr").length == 0 ||
      ($("#tbl-logs tbody tr").length == 1 &&
        $("#tbl-logs tbody tr").eq(0).find("td").length == 1)
    ) {
      return true;
    }
    return false;
  };
  const updateTable = function (data) {
    // switch (data.type) {
    //   case "new":
    //     const tr =
    //       '<tr class="border border-solid border-gray-300">' +
    //       '<td class="py-[12px] px-[10px]"></td>' +
    //       '<td class="py-[12px] px-[10px]"></td>' +
    //       '<td class="py-[12px] px-[10px]"></td>' +
    //       '<td class="py-[12px] px-[10px]"><pre>Activity history\n</pre></td>' +
    //       "</tr>";
    //     if (isTableEmpty()) {
    //       $("#tbl-logs tbody").html(tr);
    //     } else {
    //       $("#tbl-logs tbody").prepend(tr);
    //     }
    //     break;
    //   case "email":
    //     $("#tbl-logs tbody tr:first td:nth-child(1)").text(data.content);
    //     break;
    //   case "name":
    //     $("#tbl-logs tbody tr:first td:nth-child(2)").text(data.content);
    //     break;
    //   case "passport":
    //     $("#tbl-logs tbody tr:first td:nth-child(3)").text(data.content);
    //     break;
    //   case "log":
    //     $("#tbl-logs tbody tr:first td:nth-child(4) pre").html(
    //       (index, text) => text + data.content
    //     );
    //     break;
    //   default:
    //     break;
    // }
    const tr = '<tr class="border border-solid border-gray-300">' +
      '<td class="py-[12px] px-[10px]"></td>' +
      '<td class="py-[12px] px-[10px]"></td>' +
      '<td class="py-[12px] px-[10px]"></td>' +
      '<td class="py-[12px] px-[10px] relative"></td>' +
      '<td class="py-[12px] px-[10px] text-right"></td>' +
    '</tr>';
    if (isTableEmpty()) {
      $("#tbl-logs tbody").html(tr);
    } else {
      $("#tbl-logs tbody").prepend(tr);
    }
    const $tr = $("#tbl-logs tbody tr:first");
    $tr.find('td:nth-child(1)').text(data.email);
    $tr.find('td:nth-child(2)').text(data.name);
    $tr.find('td:nth-child(3)').text(data.passport);
    const badge = '<span class="absolute px-[5px] py-0 ml-3 bg-red-500 top-0 rounded-md text-white text-[12px]">+ new</span>';
    $tr.find('td:nth-child(4)').html(data.log + badge);
    $tr.find('td:nth-child(5)').text(data.created_at);
  };

  const init = function () {
    initWebSocket();
    getAllLogs();
  };

  return { init, updateTable };
})();

$(document).ready(function () {
  LogsModule.init();

  socket.onopen = () => {
    console.log("WebSocket connection established.");
    // Now you can safely send data
  };
  // Socket listener
  socket.onmessage = function (e) {
    const data = JSON.parse(e.data);
    console.log(data);
    if (data.to === 'browser') {
      toastr.info("New activity log has arrived", "Alert!");
      LogsModule.updateTable(data.row_data);
    }
  };
});
