import "../style/main.scss";

import "bootstrap";

window.onload = function () {
    let showUrlModal = document.querySelector("#show-url-modal");
    if (showUrlModal !== null) {
        showUrlModal.addEventListener("show.bs.modal", function (event) {
            let button = event.relatedTarget;
            let feedRow = button.closest(".feed-row");
            let feedName = feedRow.getAttribute("data-name");

            document.querySelector("#show-url-modal-feed").textContent = feedName;
            document.querySelector("#show-url-modal-url").value = document.location + "feeds/" + feedName;
        });

        document.querySelector("#show-url-modal-url").addEventListener("focus", function () {
            this.select();
        })

        document.querySelector("#show-url-modal-copy").addEventListener("click", function () {
            let urlInput = document.querySelector("#show-url-modal-url");

            urlInput.select();

            navigator.clipboard.writeText(urlInput.value);
        });
    }

    let removeFeedModal = document.querySelector("#remove-feed-modal");
    if (removeFeedModal !== null) {
        removeFeedModal.addEventListener("show.bs.modal", function (event) {
            let button = event.relatedTarget;
            let feedRow = button.closest(".feed-row");

            document.querySelectorAll(".remove-feed-modal-feed").forEach(function (element) {
                element.textContent = feedRow.getAttribute("data-name");
            });
        });
    }

    let removeFeedModalConfirm = document.querySelector("#remove-feed-modal-confirm");
    if (removeFeedModalConfirm !== null) {
        removeFeedModalConfirm.addEventListener("click", function () {
            let ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function () {
                if (this.readyState !== 4) {
                    return;
                }

                if (this.status === 200) {
                    document.location.reload();
                } else {
                    alert("Delete request failed with error code " + this.status);
                }
            };
            ajax.open("DELETE", "/feeds/" + document.querySelector(".remove-feed-modal-feed").textContent);
            ajax.send();
        });
    }
};