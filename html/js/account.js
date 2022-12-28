const firstNameField = document.getElementById("firstName");
const lastNameField = document.getElementById("lastName");
const emailField = document.getElementById("email");

const editFirstName = document.getElementById("newFirstName");
const editLastName = document.getElementById("newLastName");
const editPassword = document.getElementById("newPassword");
const editButton = document.getElementById("editButton");
const deleteButton = document.getElementById("deleteButton");
const closeButton = document.getElementById("closeButton");
const editForm = document.getElementById("editForm");

const deletePopup = document.getElementById("deletePopup");
const noDeleteButton = document.getElementById("noDeleteButton");
const yesDeleteButton = document.getElementById("yesDeletButton");
const logoutButton = document.getElementById("logoutButton");


function setupPrivateUserData(element) {
    var data = 1; 
    element.setPrivate = function ( d ) { data = d; }
    element.getPrivate = function ( ) { return data; }
}

function setEditFormValues(userData) {
    return new Promise((resolve, reject) => {
        for (const [name, value] of Object.entries(userData["response"][0])) {
            if (name == "firstName")
                editFirstName.value = value;
            if (name == "lastName")
                editLastName.value = value;
            if (name == "password")
                editPassword.value = value;
        }
        resolve("");
    });
}

function closeEditForm() {
    editForm.style.display = "none";
}

function isDeletePopupActive() {
    if (deletePopup.style.display == "block")
        return true;
    return false;
}

function isEditFormActive() {
    if (editForm.style.display == "block")
        return true;
    return false;
}

function closeDeletePopup() {
    deletePopup.style.display = "none";
}

async function openEditForm() {
    var editForm = document.getElementById("editForm");
    var userData = editForm.getPrivate();

    await setEditFormValues(userData);

    document.getElementById("editForm").style.display = "block";

}

async function openDeletePopup() {
    closeEditForm();
    deletePopup.style.display = "block";
}

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
                if (httpMethod == "DELETE" && XHR.status == 200) {
                    location.assign("http://roadrunnernecklaces.com/");
                }

                if (XHR.status == 400) {
                    alert("Please fill in at least one field!");
                }
                if (XHR.status == 200 || XHR.status == 201) {
                    returnData = XHR.responseText;
                    try {returnData = (JSON.parse(XHR.responseText))}
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

async function deleteSession(sid) {
    var rawData = "{\"sessionName\" : \"" + sid + "\"}";
    var url = "/v1/logout.php";
    var returnData;
    console.log(rawData);
    returnData = await sendData(rawData, url, "DELETE");
    document.cookie = "sessionName=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    console.log(returnData);
}

function packageRawData(data, array) {
  return new Promise((resolve, reject) => {
      
      var rawData = "{";
      var i = 0;
      // Push our data into our FormData object

      if (array) {
          for (const [name, value] of Object.entries(data["response"][0])) {
              if (name != "email" && name != "isAdmin")
                  if (name == "user_id")
                      rawData += ("\"" + name + "\" : \"" + value + "\",");
                  else {
                      rawData += ("\"" + name + "\" : \"" + array[i] + "\",");
                      i++;
                  }
              //rawData += ("\"" + name + "\" : \"" + value + "\",");
              console.log(name + " " + array[i]);
          }
          rawData = rawData.slice(0, -1);
          rawData += "}";
          rawData = rawData.replace("user_id", "userId");
      }

      if (rawData) {
          resolve(rawData);
      }
      else {
          reject("Something went wrong converting data into FormData object");
      }
  })
}

async function updateUser(userId) {
    var editForm = document.getElementById("editForm");
    var userData, fName, lName, password, rawData, url;
    fName = editFirstName.value;
    lName = editLastName.value;
    password = editPassword.value;
    url = "http://roadrunnernecklaces.com/v1/users.php";

    rawData = await packageRawData(editForm.getPrivate(), [fName, lName, password]);
    console.log(rawData);
    await sendData(rawData, url, "PUT");
    userData = await getUserData(userId);
}

async function deleteUser(rawData, sid) {
    var url = "http://roadrunnernecklaces.com/v1/users.php";
    await sendData(rawData, url, "DELETE");
    await validateSessionId(sid);
}

function validateSessionId(sid) {
    return new Promise((resolve, reject) => {
        const XHR = new XMLHttpRequest();
        XHR.open('GET', '/v1/users.php?sid='+sid, true);

        XHR.addEventListener('error', (event) => {
            console('Something went wrong sending!');
            //alert(XHR.response);
            location.assign("http://roadrunnernecklaces.com/");
            reject("something went wrong sending!");
        });

        XHR.onreadystatechange = function () {
            if (XHR.readyState === 4 && XHR.status == 200) {
                const x = JSON.parse(XHR.response);
                let user_id = x["response"];
                resolve(user_id);
            }
            // need to add a check here for if the uid is not found
            // else {
            //     alert('Something went wrong sending data!');
            //     alert(XHR.response);
            //     //window.location.href = "/";
            // }
        };

        XHR.send(null);
    });
}

function getUserData(user_id) {
    return new Promise((resolve, reject) => {
        const XHR = new XMLHttpRequest();
        XHR.open('GET', '/v1/users.php?user_id='+user_id, true);
    
        XHR.addEventListener('error', (event) => {
            alert('Something went wrong!');
            alert(XHR.response);
            reject("something went wrong!");
            // window.location.href = "/";
        });
    
        XHR.onreadystatechange = function () {
            if (XHR.readyState === 4 && XHR.status == 200) {
                    const y = JSON.parse(XHR.response);
                    // console.log("HERE" + Object.entries(y["response"][0]));
                    // console.log("hello " + XHR.responseText);
                    // const z = JSON.stringify(y.response);
                    // const fn = parseString(z, "firstName");
                    // const ln = parseString(z, "lastName");
                    // const e = parseString(z, "email");
                    firstNameField.innerHTML = y.response["0"].firstName;
                    lastNameField.innerHTML = y.response["0"].lastName;
                    emailField.innerHTML = y.response["0"].email;
                    resolve(JSON.parse(XHR.responseText));
                    // window.location.href = "/loggedInHome.html";
                }
            if (XHR.readyState === 4 && XHR.status == 202) {
                location.assign("http://roadrunnernecklaces.com/");
                reject("something went wrong sending!");
            }
                // else {
                //   alert('Something went wrong!');
                //   alert(XHR.response);
                //   window.location.href = "/";
                // }
            };
    
        XHR.send(null);
    });
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
}

function parseString(string, value) {
    let ca = string.split(',');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      if(c.includes(value)){
        let index = c.indexOf(":");
        return c.substring(index+2, c.length-1);
      }
    }
    return "";
}

async function startValidation() {

    var user_id, userData;
    var editForm = document.getElementById("editForm");
    var updateButton = document.getElementById("updateUserButton");
    closeEditForm();
    closeDeletePopup();

    logoutButton.addEventListener('click', async function(event) {
        await deleteSession(sid);
    });

    deleteButton.addEventListener('click', async function(event) {
        if (isDeletePopupActive())
            closeDeletePopup();
        else {
            openDeletePopup();
            closeEditForm();
        }
    });

    closeButton.addEventListener('click', function(event) {
        closeEditForm();
    });

    noDeleteButton.addEventListener('click', function(event) {
        closeDeletePopup();
    });

    yesDeleteButton.addEventListener('click', async function(event) {
        var rawData = "{ \"userId\" : \"" + user_id + "\" }";
        await deleteUser(rawData, sid);
    });

    editButton.addEventListener('click', async function(event) {
        if (isEditFormActive())
            closeEditForm();
        else {
            openEditForm();
            closeDeletePopup();
        }
    });

    updateButton.addEventListener('click', async function(event) {
        await updateUser(user_id);
        location.assign("http://roadrunnernecklaces.com/");
    });

    console.log("hi");
    // need to add error checking for if the sid is not found
    let sid = await getCookie("sessionName");

    if (sid == "")
        location.assign("http://roadrunnernecklaces.com/");

    user_id = await validateSessionId(sid);

    userData = await getUserData(user_id);

    setupPrivateUserData(editForm);
    editForm.setPrivate(userData);

};

startValidation();