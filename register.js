document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("registerForm");

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        document.getElementById("nameError").textContent = "";
        document.getElementById("emailError").textContent = "";
        document.getElementById("dobError").textContent = "";
        document.getElementById("passwordError").textContent = "";

        const name = document.getElementById("regName").value.trim();
        const email = document.getElementById("regEmail").value.trim();
        const dob = document.getElementById("regDob").value;
        const password = document.getElementById("regPassword").value.trim();

        let isValid = true;
        if (name === "") { 
            document.getElementById("nameError").textContent = "Full name is required!"; 
            isValid = false; 
        }

        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (email === "") { 
            document.getElementById("emailError").textContent = "Email field is required!"; 
            isValid = false; 
        } else if (!emailPattern.test(email)) {
            document.getElementById("emailError").textContent = "Sariyana Email format-il (example@domain.com) type seiyungal!"; 
            isValid = false; 
        }

        if (dob === "") { 
            document.getElementById("dobError").textContent = "Please select your date of birth!"; 
            isValid = false; 
        }

        // Password Check
        if (password === "") { 
            document.getElementById("passwordError").textContent = "Password configuration is required!"; 
            isValid = false; 
        }

        if (!isValid) return;
        fetch('register_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ fullname: name, email: email, dob: dob, password: password })
        })
        .then(res => {
            if (!res.ok) throw new Error('Network response failure');
            return res.json();
        })
        .then(data => {
            if (data.success) {
                alert("Registration successful! Redirecting to login page...");
                window.location.href = 'index.html';
            } else {
                if(data.field === "email") {
                    document.getElementById("emailError").textContent = data.message;
                } else if(data.field === "password") {
                    document.getElementById("passwordError").textContent = data.message;
                } else {
                    document.getElementById("passwordError").textContent = data.message; // Default fallback error under password
                }
            }
        })
        .catch(err => {
            console.error("Error creating record:", err);
            document.getElementById("passwordError").textContent = "Connection error or register backend is down!";
        });
    });
});