document.addEventListener("DOMContentLoaded", () => {

    const toggle =
        document.getElementById("adminMobileToggle");

    const sidebar =
        document.getElementById("adminSidebar");

    const overlay =
        document.getElementById("adminOverlay");


    if (!toggle || !sidebar || !overlay) {
        return;
    }


    // همیشه هنگام باز شدن صفحه سایدبار بسته باشد
    sidebar.classList.remove("open");
    overlay.classList.remove("active");


    function openSidebar() {

        sidebar.classList.add("open");
        overlay.classList.add("active");

    }


    function closeSidebar() {

        sidebar.classList.remove("open");
        overlay.classList.remove("active");

    }


    toggle.addEventListener("click", (event) => {

        event.preventDefault();
        event.stopPropagation();

        if (sidebar.classList.contains("open")) {

            closeSidebar();

        } else {

            openSidebar();

        }

    });


    overlay.addEventListener(
        "click",
        closeSidebar
    );


    // با کلیک روی لینک‌های سایدبار بسته شود
    document
        .querySelectorAll(".admin-sidebar .admin-nav-link")
        .forEach((link) => {

            link.addEventListener(
                "click",
                closeSidebar
            );

        });


    // با تغییر زبان، سایدبار حتماً بسته بماند
    document
        .querySelectorAll(".admin-language a")
        .forEach((link) => {

            link.addEventListener(
                "click",
                closeSidebar
            );

        });


    // با تغییر اندازه صفحه
    window.addEventListener("resize", () => {

        if (window.innerWidth > 900) {
            closeSidebar();
        }

    });

});

document
    .querySelectorAll("[data-language-switch]")
    .forEach((link) => {

        link.addEventListener("click", () => {

            closeSidebar();

        });

    });