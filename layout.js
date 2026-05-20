async function loadComponent(id, file) {
    const response = await fetch(file);
    const data = await response.text();

    document.getElementById(id).innerHTML = data;

    // AFTER nav loads
    if (id === "nav-placeholder") {

        highlightCurrentPage();

        // Re-run nav functionality
        if (typeof initializeNav === "function") {
            initializeNav();
        }
    }
}

function highlightCurrentPage() {

    let currentPage = window.location.pathname.split("/").pop();

    // Default homepage
    if (currentPage === "") {
        currentPage = "index.html";
    }

    const navLinks = document.querySelectorAll(".menu a");

    navLinks.forEach(link => {

        const linkPage = link.getAttribute("href");

        if (linkPage === currentPage) {
            link.classList.add("current-page");
        }

    });
}

loadComponent("nav-placeholder", "nav.html");
loadComponent("footer-placeholder", "footer.html");