



$(function () {
    // INIT
    setMenuHeight();


    // RESIZE
    $(window).resize(function () {
        setMenuHeight();
    });

    function setMenuHeight() {
        $('.menu-panel').css({
            height: window.innerHeight
        });
    }
});

let URL = {
    userOperation: 'portal'
}

let API = {
    config
}
