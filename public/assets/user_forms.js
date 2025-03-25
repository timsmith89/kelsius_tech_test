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
                    messageContainer.innerHTML = "<br>Profile updated successfully.";
                    messageContainer.style.color = "green";
                    profileForm.appendChild(messageContainer);

                    // Remove existing messages
                    const existingMessage = document.querySelector(".form-container p");

                    if (existingMessage) {
                        existingMessage.remove();
                    }

                    // Append message and reload after 3 seconds
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

    const manageCommentForm = document.getElementById("manage-comment-form");

    if (manageCommentForm) {
        manageCommentForm.addEventListener("submit", async (event) => {
            // Ensure only form submission triggers this logic
            event.preventDefault();

            // Get the form action and form data
            const formData = new FormData(form);
            const action = formData.get("action");

            // Select the existing success message container
            const successMessage = document.querySelector(".success-message");

            fetch(form.action, {
                method: "POST",
                body: formData
            })
            .then(() => {
                if (successMessage) {
                    // Update the content of the existing success message
                    if (action === "add") {
                        successMessage.textContent = "Comment added successfully!";
                    } else if (action === "edit") {
                        successMessage.textContent = "Comments successfully updated!";
                    } else if (action === "delete") {
                        successMessage.textContent = "Comment successfully deleted!";
                    }

                    // Show the success message
                    successMessage.style.display = "block";

                    // Hide the message after 5 seconds
                    setTimeout(() => {
                        successMessage.style.display = "none";
                    }, 5000);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
            });
        });
    }
});