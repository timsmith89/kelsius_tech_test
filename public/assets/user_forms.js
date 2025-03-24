document.addEventListener("DOMContentLoaded", function () {
    // Show/Hide Password
    const togglePassword = document.getElementById("togglePassword");
    const passwordField = document.getElementById("password");

    if (togglePassword && passwordField) {
        togglePassword.addEventListener("click", function () {
            if (passwordField.type === "password") {
                passwordField.type = "text";
                togglePassword.textContent = "Hide Password";
            } else {
                passwordField.type = "password";
                togglePassword.textContent = "Show Password";
            }
        });
    }

    const profileForm = document.getElementById("update-profile");

    if (profileForm) {
        profileForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const formData = new FormData(profileForm);

            fetch("profile.php", {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
            .then(response => response.json())
            .then(data => {
                console.log("Server Response:", data);

                if (data.redirect) {
                    const messageContainer = document.createElement("p");
                    messageContainer.textContent = "Profile updated successfully.";
                    messageContainer.style.color = "green";
                    profileForm.appendChild(messageContainer);

                    // Remove existing messages
                    const existingMessage = document.querySelector(".form-container p");
                    if (existingMessage) {
                        existingMessage.remove();
                    }

                    // ➕ Append message and reload after 3 seconds
                    profileForm.appendChild(messageContainer);

                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 3000);
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        });
    }
});