/*
* Copyright (C) 2020-present, Concentric Digital Pty Ltd.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 of the License.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*/

//setup variables
var autoOpen = fbcp_variables["autoOpen"],
  autoOpenConversationEnabled = fbcp_variables["autoOpenConversationEnabled"],
  autoOpenConversationOnceOnly = fbcp_variables["autoOpenConversationOnceOnly"],
  autoOpenbyScroll = fbcp_variables["autoOpenbyScroll"],
  autoOpenbyDelay = fbcp_variables["autoOpenbyDelay"],
  shakeConversationEnabled = fbcp_variables["shakeConversationEnabled"],
  openDelay = fbcp_variables["OpenDelay"];

(function ($) {
  // add listener to openChat button click event
  $(document).ready(function () {
    $(".fbcp-open-chat").click(function (event) {
      event.preventDefault();
      openConversation();
    });
  });
  //add listener
  if (autoOpenConversationEnabled) {
    if (autoOpenConversationOnceOnly && Cookies.get("chatOpenedOnce")) {
      autoOpen = false;
    } else {
      autoOpen = true;
      //set cookies if OpenedOnce during session
      window.onload = function () {
        FB.Event.subscribe("customerchat.dialogShow", function () {
          autoOpen = false;
          Cookies.set("chatOpenedOnce", true);
        });
      };
    }
  }
  if (autoOpenbyDelay) {
    setTimeout(function () {
      //check autoOpen condition again
      if (autoOpen) {
        openConversation();
      }
    }, openDelay);
  }

  //listen to scroll, and if scroll past element with id #fbcp-scoll-to, open the chat window
  if (autoOpenbyScroll && $("#scroll-to").length) {
    $(window).scroll(function () {
      (hT = $("#fbcp-scroll-to").offset().top),
        (hH = $("#fbcp-scroll-to").outerHeight()),
        (wH = $(window).height()),
        (wS = $(this).scrollTop());
      //check autoOpen condition again
      if (wS > hT + hH - wH && autoOpen) {
        openConversation;
      }
    });
  }

  //openConversation
  openConversation = () => {
    FB.CustomerChat.showDialog();
    if (shakeConversationEnabled) {
      //shake the conversation if enabled
      shakeConversation();
    }
  };

  //shakeConversation
  shakeConversation = () => {
    setTimeout(function () {
      $("#fb-root iframe").effect("shake");
    }, 500);
  };
})(jQuery);
