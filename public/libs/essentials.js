/**
 * File: script.js
 * User: Masterplan
 * Date: 3/28/13
 * Time: 10:17 AM
 * Essential functions for all system pages
 */

ajaxSeparator = "_-^SEPARATOR^-_";
editing = new Array(); // Memory of opened NicEdit panels
maxDifficulty = 3;
emails = new Array(); // List of all student's email
limits = {
  subjectName: "50",
  subjectDesc: "255",
  topicName: "50",
  topicDesc: "255",
  settingsName: "50",
  settingsDesc: "255",
  examName: "50",
  examDesc: "255",
  roomName: "50",
  roomDesc: "255",
};

/**
 *  @descr  Function for charsCounter enabling
 */
function enableCharsCounter(fieldID, limitsName) {
  $("#" + fieldID)
    .on("focus", function () {
      updateCharsCounter("#" + fieldID, limits[limitsName]);
      $("#" + fieldID + "Chars").show();
    })
    .on("keyup", function () {
      updateCharsCounter("#" + fieldID, limits[limitsName]);
    })
    .on("blur", function () {
      if (!$("#" + fieldID + "Chars").hasClass("overlimit")) {
        $("#" + fieldID + "Chars").hide();
      }
    });
}

/**
 *  @name   newLightbox
 *  @descr  Creates a Modal Panel
 *  @param  panel                   DOM Element         Element to transform in a Modal Panel
 *  @param  lightboxOptions         Array               Array of lightbox_me options
 */
function newLightbox(panel, lightboxOptions) {
  var defaultOptions = {
    closeClick: false,
    closeEsc: false,
    destroyOnClose: true,
    zIndex: 950,
    appearEffect: "slideDown",
    overlaySpeed: 1,
    modalCSS: { top: "40px" },
  };
  var options = $.extend({}, defaultOptions, lightboxOptions);

  $(panel).lightbox_me(options);
  //    $("body").addClass("noscroll");
}

/**
 *  @name   showSuccessMessage
 *  @descr  Shows green success Modal Panel
 *  @param  message         String|DOM Element          Content to add in Success Modal Panel
 */
function showSuccessMessage(message) {
  $("#modalSuccess p").html(message);
  $("#modalSuccess").lightbox_me({
    closeClick: false,
    closeEsc: false,
    destroyOnClose: false,
    appearEffect: "slideDown",
    showOverlay: false,
    modalCSS: { top: "0px" },
    onLoad: function () {
      setTimeout(function () {
        $("#modalError").trigger("close");
        $("#modalSuccess").slideUp({
          complete: function () {
            $("#modalSuccess").trigger("close");
          },
        });
      }, 1500);
    },
  });
}

/**
 *  @name   showErrorMessage
 *  @descr  Shows red error Modal Panel
 *  @param  message         String|Dom Element          Content to add in Error Modal Panel
 */
function showErrorMessage(message) {
  $("#modalError p").html(ttError + ": " + message);
  $("#modalError").lightbox_me({
    closeClick: true,
    closeEsc: true,
    destroyOnClose: false,
    appearEffect: "slideDown",
    showOverlay: false,
    closeSelector: ".lbmClose",
    modalCSS: { top: "0px" },
  });
}

/**
 *  @name   closeLightbox
 *  @descr  Closes requested Modal Panel
 *  @param  panel           DOM Element             Panel to close
 */
function closeLightbox(panel) {
  panel.slideUp({
    complete: function () {
      panel.trigger("close");
      $("body").removeClass("noscroll");
    },
  });
}

/**
 *  @descr  Function for graphical dropdown's update
 */
function updateDropdown(selected) {
  var dropdown = selected.closest("dl");
  dropdown.children("dt").children("span").toggleClass("clicked");
  var text = selected.html();
  selected.parent().parent().prev().children("span").html(text);
  selected.parent().hide();
}

/**
 *  @descr  Function for menu effects
 */
var opened = false;
$(function () {
  $("ul.topnav li a.trigger").mouseenter(function () {
    if (opened) {
      $(this).parent().find("ul.subnav").slideDown("fast").show();
      $(this)
        .parent()
        .hover(
          function () {},
          function () {
            $(this).parent().find("ul.subnav").slideUp("fast");
          }
        );
    }
  });
  $("ul.topnav li a.trigger").click(function () {
    if (!opened) {
      opened = true;
      $(this).parent().find("ul.subnav").slideDown("fast").show();
      $(this)
        .parent()
        .hover(
          function () {},
          function () {
            $(this).parent().find("ul.subnav").slideUp("fast");
          }
        );
    } else {
      $(this).parent().find("ul.subnav").slideUp("fast").show();
      opened = false;
    }
  });
  $("ul.subnav li").click(function () {
    if (opened) {
      $(this).parent().slideUp("normal").show();
      opened = false;
    }
  });
});

/**
 *  @descr  Function for dropdwown system language
 */
$(function () {
  $(".dropdownSystemLanguage dt").on("click", function () {
    $(this).children("span").toggleClass("clicked");
    $(this).next().children("ul").slideToggle(200);
  });

  $(".dropdownSystemLanguage dd ul li").on("click", function () {
    var idLang = $(this).find("span.value").text();
    $.ajax({
      url: "index.php?page=admin/updateprofile",
      type: "post",
      data: {
        lang: idLang,
      },
      success: function (data, status) {
        if (status == "success") {
          if (data == "ACK") {
            //alert(data);
            window.location.reload();
          } else {
            //alert(data);
            errorDialog(ttError, data);
          }
        }
      },
      error: function (request, status, error) {
        alert("jQuery AJAX request error:".error);
      },
    });
  });

  // Close all dropdowns when click out of it
  // Maybe too heavy for system... IMPROVE
  $(document).on("click", function (e) {
    var $clicked = $(e.target);
    if (!$clicked.parents().hasClass("dropdownSystemLanguage")) {
      $(".dropdownSystemLanguage dd ul").slideUp(200);
      $(".dropdownSystemLanguage dt span").removeClass("clicked");
    }
  });
});

/**
 *  @descr  Define attributes and functions for classes
 */
$(function () {
  // read class (for readonly input tag)
  $(".readonly").attr("disabled", "");

  // inactive class (prevent click)
  $(".inactive").click(function (event) {
    return false;
  });
});

function printBoxHelpMessage(message) {
  var style = "";
  if (message.indexOf("<br/>") == -1) style = "line-height:22px;";
  return (
    '<div class="boxHelpMessage clearer">' +
    '    <div class="left"><img src="themes/default/images/help.png" /></div>' +
    '    <div class="left" style="' +
    style +
    '">' +
    message +
    "</div>" +
    "</div>"
  );
}
function printBoxLogMessage(message) {
  var style = "";
  if (message.indexOf("<br/>") == -1) style = "line-height:22px;";
  return (
    '<div class="boxHelpMessage clearer">' +
    '    <div class="left"><img src="themes/default/images/log.png" /></div>' +
    '    <div class="left" style="' +
    style +
    '">' +
    message +
    "</div>" +
    "</div>"
  );
}

/**
 *  @descr  Make writable element/s
 */
function makeWritable(elements) {
  $(elements)
    .removeClass("readonly")
    .addClass("writable")
    .removeAttr("disabled");
}

/**
 *  @descr  Make element/s readonly
 */
function makeReadonly(elements) {
  $(elements).removeClass("writable").addClass("readonly").attr("disabled", "");
}

/**
 *  @descr  Update charsCounter in field
 */
function updateCharsCounter(element, max) {
  var chars = max - $(element).val().length;
  if (chars < 0) {
    $(element + "Chars")
      .text(chars)
      .addClass("overlimit");
  } else {
    $(element + "Chars")
      .text(chars)
      .removeClass("overlimit");
  }
}

/**
 *  @descr  Create a confirm dialog
 */
$(function () {
  if ($("#dialogConfirm").length > 0) {
    $("#dialogConfirm").dialog({
      autoOpen: false,
      draggable: false,
      resizable: false,
      width: 400,
      height: "auto",
      modal: true,
      closeOnEscape: false,
      position: ["center", 50],
      buttons: {
        No: function () {
          $(this).dialog("close");
          confirmCallback(
            $(this).data("callback"),
            $(this).data("params"),
            false
          );
        },
        Yes: function () {
          $(this).dialog("close");
          confirmCallback(
            $(this).data("callback"),
            $(this).data("params"),
            true
          );
        },
      },
    });
  }
});

/**
 *  @descr  Create an error dialog
 */
$(function () {
  if ($("#dialogError").length > 0) {
    $("#dialogError").dialog({
      autoOpen: false,
      draggable: false,
      resizable: false,
      width: 400,
      height: "auto",
      modal: true,
      closeOnEscape: true,
      position: ["center", 50],
      buttons: {
        Ok: function () {
          $(this).dialog("close");
        },
      },
    });
    $("#dialogError")
      .find(".ui-button-text")
      .keypress(function (e) {
        if (
          (e.which && e.which == 13) ||
          (e.keyCode && e.keyCode == 13) || // enter
          (e.which && e.which == 27) ||
          (e.keyCode && e.keyCode == 27) // esc
        ) {
          $("#dialogError").dialog("close");
          return false;
        }
      });
  }
});

/**
 *  @descr  Set confirm dialog parameters and show it
 */
function confirmDialog(title, message, callback, params) {
  $("#dialogConfirm p").html(message);
  $("#dialogConfirm")
    .data("callback", callback)
    .data("params", params)
    .dialog("option", "title", title)
    .dialog("open");
  $(".ui-dialog").css("background", "url('" + imageDir + "confirmDialog.png')");
}

/**
 *  @descr  Execute the callback function (if authorized)
 */
function confirmCallback(callback, params, value) {
  if (value) callback(params);
}

/**
 *  @descr  Set error dialog parameters and show it
 */
function errorDialog(title, message) {
  $("#dialogError p").html(message);
  $("#dialogError").dialog("option", "title", title).dialog("open");
  $(".ui-dialog").css("background", "url('" + imageDir + "errorDialog.png')");
}

/**
 *  @descr  Show error message
 */
function showError(div, error) {
  $("#" + div).text(error);
  $("#" + div)
    .slideDown({ duration: 200 })
    .show(200)
    .delay(5000)
    .slideUp(200);
}

/**
 *  @descr  Show message
 */
function showMessage(div, message) {
  $("#" + div).text(message);
  $("#" + div)
    .slideDown({ duration: 200 })
    .show(200)
    .delay(1000)
    .slideUp(200);
}

/**
 *  @descr  Check if typed email addres is valid
 */
function isValidEmailAddress(emailAddress) {
  var pattern = new RegExp(
    /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i
  );
  return pattern.test(emailAddress);
}

function truncate(data, width) {
  if (data != null)
    return (
      '<div class="ellipsis" style="width :' +
      width +
      '; text-overflow:ellipsis;">' +
      data +
      "</div>"
    );
  else return "";
}

function scrollToRow(datatable, row) {
  $(datatable.table().body())
    .closest("div.dataTables_scrollBody")
    .scrollTo(row);
}

function helpjs() {
  $("#dialogError p").html(ttHelpDefaultDescription);
  $("#dialogError").dialog("option", "title", ttHelpDefault).dialog("open");
  $(".ui-dialog").css("background", "url('" + imageDir + "helpDialog.png')");
}
