function hideOnClickOutside(acctButton, acctMenu) {
    let accButton = $(acctButton)[0];
    let accPopup = $(acctMenu)[0];

    $(document).on("mouseup", function (e) {
        if (!e.target.closest(acctMenu)?.length && $(acctMenu).is(":visible")) {
            accButton.classList.remove("active");
            accPopup.classList.remove("open");
            $(document).off("mouseup");
        }
    });
}

function toggleMenu(acctButton, acctMenu, canBeClickedOutside = true) {
    let accButton = $(acctButton)[0];
    let accPopup = $(acctMenu)[0];

    accButton.classList.toggle("active");
    accPopup.classList.toggle("open");
    if (!canBeClickedOutside) hideOnClickOutside(acctButton, acctMenu);
}

$(function() {
    setTimeout(function () {
        toggleMenu("#head_menu_slider_button", "#head_menu_slider");
    }, 250);

   $("#head_menu_slider_button").on("click", function() {
       toggleMenu("#head_menu_slider_button", "#head_menu_slider");
   });

   $("#head_account").on("click", function() {
       toggleMenu("#head_account", "#head_account_popup", false);
    });
});