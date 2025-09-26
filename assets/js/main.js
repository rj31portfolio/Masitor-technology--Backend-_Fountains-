/*-----------------------------------------------------------------------------------

    Theme Name: Metary - Industry & Factory HTML Template
    Description: Industry & Factory HTML Template
    Author: Website Layout
    Version: 1.0
            
    ---------------------------------- */

! function(e) {
    "use strict";
    var a = e(window);

    function o() {
        var o, t, s, i, l;
        o = e(".full-screen"), t = a.height(), o.css("min-height", t), s = e("header").height(), i = e(".screen-height"), l = a.height() - s, i.css("height", l)
    }
    e("#preloader").fadeOut("normall", function() {
        e(this).remove()
    }), a.on("scroll", function() {
        var o = a.scrollTop(),
            t = e(".navbar-brand img"),
            s = e(".navbar-brand.logodefault img");
        o <= 50 ? (e("header").removeClass("scrollHeader").addClass("fixedHeader"), t.attr("src", "assets/img/logos/logo-inner.png"), s.attr("src", "assets/img/logos/logo.png")) : (e("header").removeClass("fixedHeader").addClass("scrollHeader"), t.attr("src", "assets/img/logos/logo.png"), s.attr("src", "assets/img/logos/logo.png"))
    }), (() => {
        let a = getComputedStyle(document.documentElement),
            o = a.getPropertyValue("--primary-color").trim(),
            t = a.getPropertyValue("--secondary-color").trim(),
            s = () => {
                let a = document.documentElement.scrollTop,
                    s = document.documentElement.scrollHeight - document.documentElement.clientHeight,
                    i = Math.round(a / s * 100),
                    l = e(".scroll-top-percentage");
                l.css("background", `conic-gradient(${o} ${i}%, ${t} ${i}%)`), a > 100 ? l.addClass("active") : l.removeClass("active"), i < 96 ? e("#scroll-value").text(`${i}%`) : e("#scroll-value").html('<i class="fa-solid fa-angle-up"></i>')
            };
        window.onscroll = s, window.onload = s, e(".scroll-top-percentage").on("click", function e() {
            document.documentElement.scrollTo({
                top: 0,
                behavior: "smooth"
            })
        })
    })(), e(".parallax,.bg-img").each(function(a) {
        e(this).attr("data-background") && e(this).css("background-image", "url(" + e(this).data("background") + ")")
    }), e(".story-video").magnificPopup({
        delegate: ".video",
        type: "iframe"
    }), new WOW({
        boxClass: "wow",
        animateClass: "animated",
        offset: 0,
        mobile: !1,
        live: !0
    }).init(), a.resize(function(e) {
        setTimeout(function() {
            o()
        }, 500), e.preventDefault()
    }), o(), e(document).ready(function() {
        e(".testimonial-carousel1").owlCarousel({
            loop: !0,
            responsiveClass: !0,
            nav: !1,
            dots: !0,
            margin: 0,
            autoplay: !0,
            thumbs: !0,
            thumbsPrerendered: !0,
            autoplayTimeout: 5e3,
            smartSpeed: 800,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1e3: {
                    items: 1
                }
            }
        }), e(".testimonial-carousel2").owlCarousel({
            loop: !0,
            responsiveClass: !0,
            autoplay: !0,
            smartSpeed: 1500,
            nav: !0,
            navText: ["<i class='ti-arrow-left'></i>", "<i class='ti-arrow-right'></i>"],
            dots: !1,
            center: !1,
            margin: 50,
            responsive: {
                0: {
                    items: 1,
                    margin: 0,
                    nav: !1
                },
                768: {
                    items: 1,
                    nav: !1
                },
                992: {
                    items: 1
                },
                1200: {
                    items: 1
                }
            }
        }), e(".history-carousel").owlCarousel({
            loop: !0,
            responsiveClass: !0,
            autoplay: !0,
            autoplayTimeout: 5e3,
            smartSpeed: 1500,
            nav: !1,
            dots: !1,
            center: !1,
            margin: 0,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 1
                },
                768: {
                    items: 2
                },
                992: {
                    items: 3
                }
            }
        }), e(".services-carousel-one").owlCarousel({
            loop: !0,
            responsiveClass: !0,
            autoplay: !0,
            smartSpeed: 1500,
            nav: !0,
            navText: ["<i class='ti-angle-left'></i>", "<i class='ti-angle-right'></i>"],
            dots: !1,
            center: !1,
            margin: 40,
            responsive: {
                0: {
                    items: 1,
                    nav: !1
                },
                576: {
                    items: 1,
                    nav: !1
                },
                768: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 3
                }
            }
        }), e(".portfolio-carousel-one").owlCarousel({
            loop: !0,
            responsiveClass: !0,
            autoplay: !0,
            center: !1,
            autoplayTimeout: 5e3,
            smartSpeed: 1500,
            nav: !0,
            navText: ["<i class='ti-arrow-left'></i>", "<i class='ti-arrow-right'></i>"],
            dots: !1,
            margin: 30,
            responsive: {
                0: {
                    items: 1,
                    nav: !1
                },
                576: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 3
                }
            }
        }), e(".clients-carousel-one").owlCarousel({
            loop: !0,
            responsiveClass: !0,
            autoplay: !0,
            autoplayTimeout: 5e3,
            smartSpeed: 1500,
            nav: !1,
            dots: !1,
            center: !1,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 4
                },
                1200: {
                    items: 5
                }
            }
        }), e(".slider-fade1").owlCarousel({
            items: 1,
            loop: !0,
            dots: !1,
            margin: 0,
            nav: !0,
            navText: ["<i class='ti-arrow-left'></i>", "<i class='ti-arrow-right'></i>"],
            autoplay: !0,
            smartSpeed: 1500,
            mouseDrag: !1,
            animateIn: "fadeIn",
            animateOut: "fadeOut",
            responsive: {
                0: {
                    items: 1,
                    nav: !1
                },
                576: {
                    items: 1,
                    nav: !1
                },
                768: {
                    items: 1,
                    nav: !1
                },
                992: {
                    items: 1,
                    nav: !0
                }
            }
        }), e(".owl-carousel").owlCarousel({
            items: 1,
            loop: !0,
            dots: !1,
            margin: 0,
            autoplay: !0,
            smartSpeed: 500
        }), e(".slider-fade1").on("changed.owl.carousel", function(a) {
            var o = a.item.index - 2;
            e("h1").removeClass("animated fadeInUp"), e("p").removeClass("animated fadeInUp"), e("a").removeClass("animated fadeInUp"), e(".owl-item").not(".cloned").eq(o).find("h1").addClass("animated fadeInUp"), e(".owl-item").not(".cloned").eq(o).find("p").addClass("animated fadeInUp"), e(".owl-item").not(".cloned").eq(o).find("a").addClass("animated fadeInUp")
        }), e(".countup").counterUp({
            delay: 25,
            time: 2e3
        }), e(".countdown").countdown({
            date: "01 May 2028 00:01:00",
            format: "on"
        }), e(".current-year").text(new Date().getFullYear())
    }), e(document).ready(function() {
        e(".wlt-btn, .wlt-overlay-bg").on("click", function() {
            e(".wlt-overlay-bg").hasClass("active") ? e(".wlt-overlay-bg").animate({
                opacity: "0"
            }, 500, function() {
                e(".wlt-overlay-bg").removeClass("active").hide()
            }) : e(".wlt-overlay-bg").addClass("active").show().animate({
                opacity: "1"
            }, 500, function() {}), e(".wlt-sidebar-main").toggleClass("active")
        }), e(".wlt-sidebar-inner").scrollbar()
    }), a.on("load", function() {
        e(".portfolio-gallery").lightGallery(), e(".portfolio-link").on("click", e => {
            e.stopPropagation()
        })
    })
}(jQuery);