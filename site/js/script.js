window.onload = function() {
    //Redirect to ticket when clicking table row
    const table = document.querySelector(".tickets");
    table.onclick = function(e) {
        let ticketId = e.target.parentElement.id;
        ticketId = ticketId.replace('t-', '');
        window.location = '/ticket/view/' + ticketId;
    };
};
