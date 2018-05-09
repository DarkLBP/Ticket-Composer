class Request {
    constructor() {
        let path = window.location.pathname;
        if (path.startsWith('/')) {
            path = path.substring(1);
        }
        if (path.endsWith('/')) {
            path = path.substring(0, path.length - 1);
        }
        const chunks = path.split('/');

        this.defaultController = "main";
        this.defaultAction = "index";
        this.controller = this.defaultController;
        this.action = this.defaultAction;
        this.parameters = [];

        if (chunks.length === 1) {
            this.controller = chunks[0];
        } else if (chunks.length > 1) {
            this.controller = chunks[0];
            this.action = chunks[1];
        }
        for (let i = 2; i < chunks.length; i++) {
            this.parameters.push(chunks[i]);
        }
    }

    getAction() {
        return this.action;
    }

    getController() {
        return this.controller;
    }

    getParameters() {
        return this.parameters;
    }

    redirect(controller = this.defaultController, action = this.defaultAction, parameters = []) {
        let url = '/' + controller + '/';
        if (action.length > 0) {
            url +=  action;
        }
        if (parameters.length > 0) {
            for (let i in parameters) {
                url += '/' + parameters[i];
            }
        }
        window.location = url;
    }
}

let request = new Request();

window.onload = function() {
    //Redirect to ticket when clicking table row
    const table = document.querySelector(".tickets");
    table.onclick = function(e) {
        let ticketId = e.target.parentElement.id;
        ticketId = ticketId.replace('t-', '');
        window.location = '/ticket/view/' + ticketId;
    };
};
