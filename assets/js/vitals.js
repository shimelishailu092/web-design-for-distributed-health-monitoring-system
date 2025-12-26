document.addEventListener("DOMContentLoaded", function() {

    const form = document.getElementById("vitalsForm");
    const msg = document.getElementById("msg");

    form.addEventListener("submit", function(e){
        e.preventDefault();

        msg.textContent = "Submitting...";

        const formData = new FormData(form);

        fetch("../api/patient/manual_vitals.php", {
            method: "POST",
            body: formData,
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                msg.style.color = "green";
                msg.textContent = data.message + (data.alert ? " Alert Level: " + data.alert : "");
                form.reset();
            } else {
                msg.style.color = "red";
                msg.textContent = data.message;
                console.log("Received:", data.received);
            }
        })
        .catch(err => {
            msg.style.color = "red";
            msg.textContent = "Request failed";
            console.error(err);
        });
    });

});
