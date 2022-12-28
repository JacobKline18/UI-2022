var loginButton = document.getElementById("submit");
var url = "/v1/login.php";


loginButton.addEventListener('click', async function(event) {
    attemptAdminLogin();
});


function sendData(data, url, httpMethod) {
    return new Promise((resolve, reject) => {
        const XHR = new XMLHttpRequest();
        var returnData;

        // set the proper 'GET' HTTP URL
        XHR.open(httpMethod, url, true);
        console.log("inside sendData function");
        console.log("url inside sendData() is " + url);

        // Define what happens on successful data submission
        XHR.addEventListener('load', (event) => {
            console.log('Yeah! Data sent and response loaded.');
        });

        // Define what happens in case of error
        XHR.addEventListener('error', (event) => {
            alert('Oops! Something went wrong.');
        });

        XHR.onreadystatechange = function () {
            if (XHR.readyState === 4) {
                if (XHR.status == 400) {
                    alert("Please fill in at least one field!");
                }
                if (XHR.status == 200 || XHR.status == 201) {
                    returnData = XHR.responseText;
                    try {returnData = (JSON.parse(XHR.responseText))
                         document.cookie = "sessionName=".concat(returnData["response"]);}
                    catch {
                        console.log("raw Data cannot undergo \"JSON.parse\"")
                    }
                    console.log(XHR.status);
                    console.log(XHR.responseText);
                    console.log("RETURN DATA IS " + returnData);
                    resolve(returnData);
                }
                if (XHR.status == 202) {
                    alert(XHR.responseText);
                }
            }
        };

        XHR.send(data);
})};

function packageFormData(data) {
    return new Promise((resolve, reject) => {
        var formData = new FormData();
        // Push our data into our FormData object
        for (const [name, value] of Object.entries(data)) {
            formData.append(name, value);
            console.log(name + " " + value);
        }

        if (formData) {
            resolve(formData);
        }
        else {
            reject("Something went wrong converting data into FormData object");
        }
    })
};

async function attemptAdminLogin() {
    var userData, loginResult, validSession;
    var loginEmail = document.getElementById("email").value;
    var loginPassword = document.getElementById("password").value;

    userData = await packageFormData({"email" : loginEmail, 
                                      "password" : loginPassword});

    loginResult = await sendData(userData, url, "POST");

    location.assign("http://admin.roadrunnernecklaces.com/adminpage.html");
    
}