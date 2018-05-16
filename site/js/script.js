/**
 * Get a cookie
 * @param name Name of the cookie
 * @returns (string) The cookie value
 */
function getCookie(name) {
    name += '=';
    const cookies = document.cookie.split('; ');
    for (let i = 0; i < cookies.length; i++) {
        if (cookies[i].startsWith(name)) {
            return cookies[i].split("=")[1];
        }
    }
    return '';
}

/**
 * Performs an AJAX request
 * @param url Target url
 * @param method Desired request method
 * @param callback Function that will be called when changing request state
 * @param headers Headers to be set before the request is sent
 * @param body Data to be sent
 */
function performAJAXRequest(url, method = "POST", callback = null, headers = {}, body = '') {
    const request = new XMLHttpRequest();
    request.open(method, url, true);
    for (let property in headers) {
        if (headers.hasOwnProperty(property)) {
            request.setRequestHeader(property, headers[property]);
        }
    }
    request.onreadystatechange = callback;
    request.send(body);
}

/**
 * Gets a random int
 * @param min Min value
 * @param max Max value
 * @returns(int) Generated random int
 */
function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Generates a random RGB string
 * @returns {string} Random RGB string
 */
function randomRGB() {
    const r = getRandomInt(0, 255);
    const g = getRandomInt(0, 255);
    const b = getRandomInt(0, 255);
    return "rgb(" + r + "," + g + "," + b + ")";
}

//Wait for page load
window.onload = function () {
    let mvc = new EasyMVC();
    const action = mvc.getAction();
    const controller = mvc.getController();

    //Show js only elements
    $('.jsHidden').removeClass('jsHidden');

    //Do actions depending which controller or action was triggered
    if (controller === 'panel') {
        if (action === 'tickets') {
            //Redirect to ticket when clicking table row
            const table = document.querySelector(".tickets");
            if (table != null) {
                table.onclick = function (e) {
                    let ticketId = e.target.parentElement.id;
                    if (ticketId) {
                        ticketId = ticketId.replace('t-', '');
                        EasyMVC.redirect('ticket', 'view', [ticketId]);
                    }
                };
            }
        } else if (action === 'departments') {
            //Redirect to department when clicking table row
            const table = document.querySelector(".departments");
            if (table != null) {
                table.onclick = function (e) {
                    let departmentId = e.target.parentElement.id;
                    if (departmentId) {
                        departmentId = departmentId.replace('d-', '');
                        EasyMVC.redirect('department', 'edit', [departmentId]);
                    }
                }
            }
        } else if (action === "users") {
            //Redirect to user when clicking table row
            const table = document.querySelector(".users");
            if (table != null) {
                table.onclick = function (e) {
                    let userId = e.target.parentElement.id;
                    if (userId) {
                        userId = userId.replace('u-', '');
                        EasyMVC.redirect('user', 'edit', [userId]);
                    }
                }
            }
        } else if (action === "index") {
            const charts = [];
            const titles = [];
            let currentChart = 0;

            //Create canvas for each chart
            charts.push(document.createElement('canvas'));
            charts.push(document.createElement('canvas'));
            charts.push(document.createElement('canvas'));
            const slideshow = document.querySelector("#chart-slideshow");
            const slideshowTitle = document.querySelector("#slideshow-title");

            //Get statistics through AJAX
            const sessionToken = getCookie('userToken');
            const requestURL = EasyMVC.getURL('api', 'statistics');
            const headers = {
                "Session-Token": sessionToken
            };
            performAJAXRequest(requestURL, 'POST', function () {
                if (this.readyState === 4 && this.status === 200) {
                    const response = JSON.parse(this.responseText);
                    titles.push.apply(titles, response.resultTitles);

                    //Tickets per department stats
                    let stats = response.results[0];
                    let config = {
                        type: 'pie',
                        data: {
                            datasets: [{
                                data: [],
                                backgroundColor: [],
                            }],
                            labels: []
                        },
                        options: {
                            responsive: true
                        }
                    };
                    for (let i = 0; i < stats.length; i++) {
                        config.data.datasets[0].data.push(stats[i].count);
                        config.data.datasets[0].backgroundColor.push(randomRGB());
                        config.data.labels.push(stats[i].name);
                    }
                    new Chart(charts[0].getContext("2d"), config);

                    //Open and closed tickets
                    stats = response.results[1];
                    config = {
                        type: 'pie',
                        data: {
                            datasets: [{
                                data: [
                                    0,
                                    0
                                ],
                                backgroundColor: [
                                    randomRGB(),
                                    randomRGB()
                                ],
                            }],
                            labels: [
                                'Closed',
                                'Open'
                            ]
                        },
                        options: {
                            responsive: true
                        }
                    };
                    for (let i = 0; i < stats.length; i++) {
                        if (stats[i].open === "1") {
                            config.data.datasets[0].data[1] += parseInt(stats[i].count);
                        } else if (stats[i].open === "0") {
                            config.data.datasets[0].data[0] += parseInt(stats[i].count);
                        }
                    }
                    new Chart(charts[1].getContext("2d"), config);

                    //Open and closed tickets per department
                    stats = response.results[2];
                    config = {
                        type: 'bar',
                        data:  {
                            labels: [],
                            datasets: [{
                                label: 'Closed',
                                backgroundColor: randomRGB(),
                                data: []
                            }, {
                                label: 'Open',
                                backgroundColor: randomRGB(),
                                data: []
                            }]

                        },
                        options: {
                            tooltips: {
                                mode: 'index',
                                intersect: false
                            },
                            responsive: true,
                            scales: {
                                xAxes: [{
                                    stacked: true,
                                }],
                                yAxes: [{
                                    stacked: true
                                }]
                            }
                        }
                    };
                    for (let i = 0; i < stats.length; i++) {
                        let index = config.data.labels.indexOf(stats[i].name);
                        if (index === -1) {
                            //Prepare empty data for that department
                            config.data.labels.push(stats[i].name);
                            index = config.data.labels.length - 1;
                            config.data.datasets[1].data[index] = 0;
                            config.data.datasets[0].data[index] = 0;
                        }
                        if (stats[i].open === "1") {
                            config.data.datasets[1].data[index] += parseInt(stats[i].count);
                        } else if (stats[i].open === "0") {
                            config.data.datasets[0].data[index] += parseInt(stats[i].count);
                        }
                    }
                    new Chart(charts[2].getContext("2d"), config);

                    //Set first chart
                    slideshow.appendChild(charts[currentChart]);
                    slideshowTitle.innerHTML = titles[currentChart];

                    //Set automatic slideshow change
                    let applyInterval = true;
                    setInterval(function() {
                        if (!applyInterval) {
                            return;
                        }
                        $("#chart-slideshow canvas").animate({
                            opacity: 0,
                            left: "-=100%"
                        }, 1000, function() {
                            slideshow.removeChild(charts[currentChart]);
                            currentChart++;
                            if (currentChart > charts.length - 1) {
                                currentChart = 0;
                            }
                            slideshow.appendChild(charts[currentChart]);
                            slideshowTitle.innerHTML = titles[currentChart];
                            $("#chart-slideshow canvas")
                                .css('left', '100%')
                                .animate({
                                    opacity: 1,
                                    left: ""
                                }, 1000);
                        });
                    }, 5000);

                    //Avoid slide change when hovering the slideshow
                    $('#chart-slideshow').hover(function() {
                        applyInterval = false;
                    }, function() {
                        applyInterval = true;
                    })
                }
            }, headers);

        }
    } else if (controller === 'ticket') {
        if (action === 'view' || action === 'create') {
            //Add and remove attachments
            const attachments = document.querySelector('#attachments');
            const addAttachment = document.querySelector('#addAttachment');
            const removeAttachment = document.querySelector('#removeAttachment');
            if (attachments != null) {
                if (addAttachment != null) {
                    addAttachment.onclick = function () {
                        let element = document.createElement('input');
                        element.type = 'file';
                        element.name = 'attachment[]';
                        attachments.appendChild(element);
                    }
                }
                if (removeAttachment != null) {
                    removeAttachment.onclick = function () {
                        if (attachments.childElementCount !== 0) {
                            attachments.removeChild(attachments.children[attachments.childElementCount - 1]);
                        }
                    }
                }
            }
        }
    } else if (controller === 'user') {
        if (action === 'register') {
            const emailField = document.querySelector('#email');
            const nameField = document.querySelector('#name');
            const surnameField = document.querySelector('#surname');
            const emailRegexp = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            const invalidName = /[^A-Za-z ]/g;
            const sessionToken = getCookie('userToken');
            const requestURL = EasyMVC.getURL('api', 'email');
            const headers = {
                "Session-Token": sessionToken,
                'Content-Type': 'application/x-www-form-urlencoded'
            };
            if (emailField != null) {
                emailField.oninput = function() {
                    //Validate email
                    const test = emailRegexp.test(this.value.toLowerCase());
                    if (test) {
                        emailField.setCustomValidity('');
                        performAJAXRequest(requestURL, 'POST', function () {
                            if (this.readyState === 4 && this.status === 200) {
                                const response = JSON.parse(this.responseText);
                                if (response.hasOwnProperty('error')) {
                                    emailField.setCustomValidity(response.error);
                                } else {
                                    emailField.setCustomValidity('');
                                }
                            }
                        }, headers, 'email=' + this.value);
                    } else {
                        emailField.setCustomValidity('Invalid email');
                    }
                }
            }
            if (nameField != null) {
                //Validate name
                nameField.oninput = function() {
                    const test = invalidName.test(this.value);
                    if (test) {
                        nameField.setCustomValidity('Invalid name');
                    } else {
                        nameField.setCustomValidity('');
                    }
                }
            }
            if (surnameField != null) {
                //Validate surname
                surnameField.oninput = function() {
                    const test = invalidName.test(this.value);
                    if (test) {
                        surnameField.setCustomValidity('Surname name');
                    } else {
                        surnameField.setCustomValidity('');
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
    for (let i = 0; i < timeElements.length; i++) {
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
