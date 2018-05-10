window.onload = function() {
    let mvc = new EasyMVC();
    //Redirect to ticket when clicking table row
    if (mvc.getAction() === 'tickets' && mvc.getController() === 'panel') {
        const table = document.querySelector(".tickets");
        table.onclick = function(e) {
            let ticketId = e.target.parentElement.id;
            ticketId = ticketId.replace('t-', '');
            EasyMVC.redirect('ticket', 'view', [ticketId]);
        };
    }
};
