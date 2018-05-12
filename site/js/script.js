window.onload = function() {
    let mvc = new EasyMVC();
    const action = mvc.getAction();
    const controller = mvc.getController();

    //Show js only elements
    const hiddenElements = document.querySelectorAll('.jsHidden');
    for (let i = 0; i < hiddenElements.length; i++) {
        hiddenElements[i].classList.remove('jsHidden');
    }

    //Redirect to ticket when clicking table row
    if (controller === 'panel') {
        if (action === 'tickets') {
            const table = document.querySelector(".tickets");
            if (table != null) {
                table.onclick = function(e) {
                    let ticketId = e.target.parentElement.id;
                    if (ticketId) {
                        ticketId = ticketId.replace('t-', '');
                        EasyMVC.redirect('ticket', 'view', [ticketId]);
                    }
                };
            }
        } else if (action === 'departments') {
            const table = document.querySelector(".departments");
            if (table != null) {
                table.onclick = function(e) {
                    let departmentId = e.target.parentElement.id;
                    if (departmentId) {
                        departmentId = departmentId.replace('d-', '');
                        EasyMVC.redirect('department', 'edit', [departmentId]);
                    }
                }
            }
        } else if (action === "users") {
            const table = document.querySelector(".users");
            if (table != null) {
                table.onclick = function(e) {
                    let userId = e.target.parentElement.id;
                    if (userId) {
                        userId = userId.replace('u-', '');
                        EasyMVC.redirect('user', 'edit', [userId]);
                    }
                }
            }

        }
    } else if (controller === 'ticket') {
        if (action === 'view' || action === 'create') {
            const attachments = document.querySelector('#attachments');
            const addAttachment = document.querySelector('#addAttachment');
            const removeAttachment = document.querySelector('#removeAttachment');
            if (attachments != null) {
                if (addAttachment != null) {
                    addAttachment.onclick = function() {
                        let element = document.createElement('input');
                        element.type = 'file';
                        element.name = 'attachment[]';
                        attachments.appendChild(element);
                    }
                }
                if (removeAttachment != null) {
                    removeAttachment.onclick = function() {
                        if (attachments.childElementCount !== 0) {
                            attachments.removeChild(attachments.children[attachments.childElementCount - 1]);
                        }
                    }
                }
            }
        }
    }

    //Convert dates to current timezone
    const currentDate = new Date();
    const currentTimezoneOffset = currentDate.getTimezoneOffset();
    const serverClientDiff = Math.abs(serverTimezoneOffset) + Math.abs(currentTimezoneOffset);
    const timeElements = document.querySelectorAll('time');
    for (let i = 0; i < timeElements.length; i++){
        let element = timeElements[i];
        let date = new Date(element.innerHTML);
        let newDate = new Date(date.getTime() + serverClientDiff * 60000);
        let year = newDate.getFullYear();
        let month = newDate.getMonth() + 1 < 10 ? "0" + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
        let day = newDate.getDate() < 10 ? "0" + newDate.getDate() : newDate.getDate();
        let hours = newDate.getHours() < 10 ? "0" + newDate.getHours() : newDate.getHours();
        let minutes = newDate.getMinutes() < 10 ? "0" + newDate.getMinutes() : newDate.getMinutes();
        let seconds = newDate.getSeconds() < 10 ? "0" + newDate.getSeconds() : newDate.getSeconds();
        element.innerHTML = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
    }
};
