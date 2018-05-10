window.onload = function() {
    let mvc = new EasyMVC();
    const action = mvc.getAction();
    const controller = mvc.getController();
    //Redirect to ticket when clicking table row
    if (controller === 'panel') {
        if (action === 'tickets') {
            const table = document.querySelector(".tickets");
            table.onclick = function(e) {
                let ticketId = e.target.parentElement.id;
                ticketId = ticketId.replace('t-', '');
                EasyMVC.redirect('ticket', 'view', [ticketId]);
            };
        } else if (action === 'departments') {
            const table = document.querySelector(".departments");
            table.onclick = function(e) {
                let departmentId = e.target.parentElement.id;
                departmentId = departmentId.replace('d-', '');
                EasyMVC.redirect('department', 'edit', [departmentId]);
            }
        } else if (action === "users") {
            const table = document.querySelector(".users");
            table.onclick = function(e) {
                let userId = e.target.parentElement.id;
                userId = userId.replace('u-', '');
                EasyMVC.redirect('user', 'edit', [userId]);
            }
        }

    }
};
