document.addEventListener("DOMContentLoaded", () => {

    // =================================================
    // Product Gallery
    // =================================================

    const mainImage =
        document.getElementById("productMainImage");

    const thumbnails =
        document.querySelectorAll(".product-thumbnail");


    thumbnails.forEach((thumbnail) => {

        thumbnail.addEventListener("click", () => {

            const image =
                thumbnail.dataset.image;

            if (!mainImage || !image) {
                return;
            }

            mainImage.src = image;


            thumbnails.forEach((item) => {
                item.classList.remove("active");
            });


            thumbnail.classList.add("active");

        });

    });


    // =================================================
    // Quantity
    // =================================================

    const quantityInput =
        document.getElementById("productQuantity");

    const minusButton =
        document.getElementById("quantityMinus");

    const plusButton =
        document.getElementById("quantityPlus");


    if (
        quantityInput &&
        minusButton &&
        plusButton
    ) {

        minusButton.addEventListener("click", () => {

            let value =
                parseInt(quantityInput.value) || 1;

            value--;

            if (value < 1) {
                value = 1;
            }

            quantityInput.value = value;

        });


        plusButton.addEventListener("click", () => {

            let value =
                parseInt(quantityInput.value) || 1;

            const max =
                parseInt(quantityInput.max) || 999;

            value++;

            if (value > max) {
                value = max;
            }

            quantityInput.value = value;

        });

    }

});


// =====================================================
// Review Form
// =====================================================

const reviewForm =
    document.getElementById("reviewForm");

const reviewMessage =
    document.getElementById("reviewFormMessage");


if (reviewForm) {

    const translations = JSON.parse(
        reviewForm.dataset.translations || "{}"
    );


    reviewForm.addEventListener(
        "submit",
        async (event) => {

            event.preventDefault();


            const submitButton =
                reviewForm.querySelector(
                    ".review-submit"
                );


            const formData =
                new FormData(reviewForm);


            submitButton.disabled = true;


            try {

                const response = await fetch(
                    "api/products/review.php",
                    {
                        method: "POST",
                        body: formData
                    }
                );


                const data =
                    await response.json();


                const message =
                    translations[data.message_key]
                    || translations.error
                    || "Error";


                reviewMessage.textContent =
                    message;


                reviewMessage.className =
                    data.success
                        ? "review-form-message success"
                        : "review-form-message error";


                if (data.success) {

                    reviewForm.reset();

                }

            } catch (error) {

                reviewMessage.textContent =
                    translations.error
                    || "Error";

                reviewMessage.className =
                    "review-form-message error";

            } finally {

                submitButton.disabled = false;

            }

        }
    );

}