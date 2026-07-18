document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem('auth_token');
    if (!token) { window.location.href = 'index.html'; return; }

    const profileForm = document.getElementById("profileForm");
    
    fetch('get_profile.php', { 
        headers: { 'Authorization': 'Bearer ' + token } 
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('sidebarName').textContent = data.fullname;
            document.getElementById('sidebarEmail').textContent = data.email;
            
            document.getElementById('profName').value = data.fullname;
            document.getElementById('profDOB').value = data.dob;
            
            if(data.profile_pic) {
                document.getElementById('userAvatar').src = data.profile_pic;
            }
        } else {
            console.error("Error:", data.message);
        }
    })
    .catch(err => console.error("Fetch error:", err));

    document.getElementById("edit-btn").addEventListener("click", () => {
        document.getElementById("profName").disabled = false;
        document.getElementById("profDOB").disabled = false;
        document.getElementById("edit-btn").style.display = "none";
        document.getElementById("save-btn").style.display = "block";
        document.getElementById("cancel-btn").style.display = "block";
        document.getElementById("upload-label").style.display = "flex";
    });
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append("username", document.getElementById("profName").value);
        formData.append("dob", document.getElementById("profDOB").value);
        
        const fileInput = document.getElementById("image-upload");
        if (fileInput.files[0]) {
            formData.append("profile_pic", fileInput.files[0]);
        }

        fetch('update_profile.php', {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert("Profile Updated Successfully!");
                location.reload(); 
            } else {
                alert("Update failed: " + data.message);
            }
        });
    });

    document.getElementById("cancel-btn").addEventListener("click", () => {
        location.reload();
    });


    document.getElementById('logoutBtn').addEventListener('click', () => {
        localStorage.clear();
        window.location.href = 'index.html';
    });
});