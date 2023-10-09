window.addEventListener("DOMContentLoaded", () => {
    let dataChanged = false;

    document.querySelectorAll("form input,textarea").forEach((element) => {
        element.addEventListener("change", () => {
            dataChanged = true;
        });
    });

    document.querySelectorAll("form").forEach((element) => {
        element.addEventListener("submit", () => {
            dataChanged = false;
        });
    });

    window.addEventListener("beforeunload", (event) => {
        if (dataChanged) {
            event.preventDefault();
        }
    });
});