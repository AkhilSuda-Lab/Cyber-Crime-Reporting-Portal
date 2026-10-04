function toggleMenu() {

    const navLinks = document.getElementById("navLinks");
    if (navLinks) {
        navLinks.classList.toggle("active");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll("#navLinks a");
    navLinks.forEach(function (link) {

        link.addEventListener("click", function () {
            const menu = document.getElementById("navLinks");
            if (menu) {
                menu.classList.remove("active");
            }
        });
    });
});