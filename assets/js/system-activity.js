document.addEventListener("DOMContentLoaded", () => {

    fetch("../api/admin/get-system-activity.php")
        .then(res => res.json())
        .then(data => {

            const body = document.getElementById("activityBody");
            body.innerHTML = "";

            if (data.length === 0) {
                body.innerHTML = `
                    <tr><td colspan="6">No activity found</td></tr>
                `;
                return;
            }

            data.forEach((log, i) => {
                body.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${log.user}</td>
                        <td>${log.role}</td>
                        <td>${log.action}</td>
                        <td class="${log.status}">
                            ${log.status}
                        </td>
                        <td>${log.timestamp}</td>
                    </tr>
                `;
            });
        })
        .catch(err => {
            console.error(err);
            alert("Failed to load activity logs");
        });
});
