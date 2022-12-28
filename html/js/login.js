const submitButton = document.getElementById("submit");
const signupButton = document.getElementById("sign-up");
const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const main = document.getElementById("main");
const createacct = document.getElementById("create-acct")

const signupEmail = document.getElementById("email-signup");
const signupPassword = document.getElementById("password-signup");
const firstName = document.getElementById("firstName");
const lastName = document.getElementById("lastName");
const createacctbtn = document.getElementById("create-acct-btn");

// Need to validate that all params are there for both of these functions
submitButton.addEventListener('click', function(event) {
    sendLoginData({ email : emailInput.value,
                    password : passwordInput.value });
});

createacctbtn.addEventListener('click', async function(event) {
    var newUserData = await sendCreateData({  email : signupEmail.value,
                                          password : signupPassword.value,
                                          firstName : firstName.value,
                                          lastName : lastName.value });
    sendLoginData({ email : newUserData[0],
                    password : newUserData[1] });
});

function sendCreateData(data) {
    return new Promise((resolve, reject) => {
        const FD = new FormData();
        const XHR = new XMLHttpRequest();
        XHR.open('POST', '/v1/users.php', true);
        
        for (const [name, value] of Object.entries(data)) {
            FD.append(name, value);
        }
    
        XHR.addEventListener('error', (event) => {
            alert('Something went wrong!');
            reject('Something went wrong!');
            window.location.href = "/";
        });
    
        XHR.onreadystatechange = function () {
            if (XHR.readyState === 4) {
                if (XHR.status == 201) {
                    resolve([data["email"], data["password"]]);
                }
                else {
                alert('Something went wrong!');
                reject('Something went wrong!');
                window.location.href = "/";
                }
            }};
    
        XHR.send(FD);
    });

}

function sendLoginData(data) {
  const FD = new FormData();
  const XHR = new XMLHttpRequest();
  XHR.open('POST', '/v1/login.php', true);
  
  for (const [name, value] of Object.entries(data)) {
      FD.append(name, value);
  }

  XHR.addEventListener('error', (event) => {
      alert('Something went wrong!');
      window.location.href = "/";
  });

  XHR.onreadystatechange = function () {
      if (XHR.readyState === 4) {
          if (XHR.status == 200) {
              const x = JSON.parse(XHR.response);
              document.cookie = "sessionName=".concat(x["response"]);
              window.location.href = "/";
          }
          else {
            alert('Something went wrong!');
            window.location.href = "/";
          }
      }};

  XHR.send(FD);
}
