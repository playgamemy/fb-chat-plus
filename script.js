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
    $(".fbcp-openChat").click(function (event) {
      event.preventDefault();
      FB.CustomerChat.showDialog();
      console.log("chat opened");
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
  if (autoOpen) {
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

    if (autoOpenbyDelay) {
      $(document).ready(function () {
        FB.Event.subscribe(
          "customerchat.load",
          //delay openChat
          setTimeout(function () {
            //check autoOpen condition again
            if (autoOpen) {
              openConversation();
            }
          }, openDelay)
        );
      });
    }
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
      jQuery("#fb-root iframe").effect("shake");
    }, 500);
  };

  //subscribe to scroll event
  jQuery(function ($) {
    $(window).scroll(function () {
      if ((autoOpenChat == true) & $("#scroll-to").length) {
        var hT = $("#scroll-to").offset().top,
          hH = $("#scroll-to").outerHeight(),
          wH = $(window).height(),
          wS = $(this).scrollTop();
        if (wS > hT + hH - wH) {
          FB.CustomerChat.showDialog();
        }
      }
    });
  });
})(jQuery);
