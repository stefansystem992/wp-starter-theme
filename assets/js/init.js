// JS Object size method
Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

// PHP like empty() function
function empty(v){
    return !v || typeof v == 'undefined' || ( typeof v == 'string' && v.replace(/[0| ]+/gm,'')==='' )	|| ( typeof v == 'object' && !Object.size(v) );
}

(function ($) {
    // run in view animations
    fadeIn();

    $(window).scroll(function () {
        // run in view animations
        fadeIn();
    });

    // fire Swiper Ready event
    if (typeof Swiper !== 'undefined') {
        document.dispatchEvent(new Event("SwiperReady"));
    }

    // smooth scroll to anchor point
    $('a[href^="#"]').on('click', function (event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 40
            }, 1000);
        }
    });

    // run animations for elements in the viewport
    function fadeIn() {
        var winHeight = $(window).height();
        var bodyScroll = $(document).scrollTop();
        var calcHeight = bodyScroll + winHeight;

        $(".fadein-wrap").each(function (index, el) {
            if (
                $(this).offset().top < calcHeight &&
                $(this).offset().top + $(this).height() > bodyScroll
            ) {
                if (!$(this).hasClass("in-view")) {
                    $(this).addClass("in-view");
                }
            }
        });
    }
}(jQuery));
