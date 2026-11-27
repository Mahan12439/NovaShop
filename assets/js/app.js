document.addEventListener("DOMContentLoaded", () => {

    const mobileMenuButton =
        document.getElementById("mobileMenuButton");

    const mobileMenu =
        document.getElementById("mobileMenu");


    if (!mobileMenuButton || !mobileMenu) {
        return;
    }


    mobileMenuButton.addEventListener("click", () => {

        const isOpen =
            mobileMenu.style.display === "block";


        mobileMenu.style.display =
            isOpen ? "none" : "block";

    });

});





// =====================================================
// Hero Slider
// =====================================================

const heroSlider = document.getElementById("heroSlider");

if (heroSlider) {

    const slides =
        heroSlider.querySelectorAll(".hero-slide");

    const dots =
        heroSlider.querySelectorAll(".hero-dot");

    const nextButton =
        document.getElementById("heroNext");

    const prevButton =
        document.getElementById("heroPrev");

    let currentSlide = 0;

    let autoSlide;


    function showSlide(index) {

        if (!slides.length) {
            return;
        }

        if (index >= slides.length) {
            index = 0;
        }

        if (index < 0) {
            index = slides.length - 1;
        }

        currentSlide = index;


        slides.forEach((slide, i) => {

            slide.classList.toggle(
                "active",
                i === currentSlide
            );

        });


        dots.forEach((dot, i) => {

            dot.classList.toggle(
                "active",
                i === currentSlide
            );

        });

    }


    function startAutoSlide() {

        clearInterval(autoSlide);

        if (slides.length <= 1) {
            return;
        }

        autoSlide = setInterval(() => {

            showSlide(currentSlide + 1);

        }, 5000);

    }


    if (nextButton) {

        nextButton.addEventListener(
            "click",
            () => {

                showSlide(currentSlide + 1);

                startAutoSlide();

            }
        );

    }


    if (prevButton) {

        prevButton.addEventListener(
            "click",
            () => {

                showSlide(currentSlide - 1);

                startAutoSlide();

            }
        );

    }


    dots.forEach((dot) => {

        dot.addEventListener(
            "click",
            () => {

                showSlide(
                    Number(
                        dot.dataset.slide
                    )
                );

                startAutoSlide();

            }
        );

    });


    showSlide(0);

    startAutoSlide();
}