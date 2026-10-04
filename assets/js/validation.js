document.addEventListener("DOMContentLoaded", function () {

    const registerForm =
        document.getElementById("registerForm");

    if (!registerForm) {
        return;
    }

    registerForm.addEventListener("submit", function (event) {

        const mobile=document.getElementById("mobile").value.trim();
        const password=document.getElementById("password").value;
        const confirmPassword=document.getElementById("confirm_password").value;

        if (!/^[0-9]{10}$/.test(mobile)) {
            alert("Please enter a valid 10-digit mobile number.");
            event.preventDefault();
            return;
        }

        if (password.length < 6) {
            alert("Password must contain at least 6 characters.");
            event.preventDefault();
            return;
        }

        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            event.preventDefault();
            return;
        }
    });
});