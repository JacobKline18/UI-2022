var confirmOrderButton = document.getElementById("confirmOrderBtn");
//console.log("hiiiiiii");
confirmOrderButton.addEventListener('click', async function(event) {
    console.log("HELLO?");
    check_all();
});

async function check_all(){
    check_cc_firstName();
    check_cc_lastName();
    check_cc();
    check_cvc();
    check_date();
    check_street();
    check_city();
    check_state();
    if(check_cc_firstName()){
        if(check_cc_lastName()){
            if(check_cc()){
                if(check_cvc()){
                    if(check_date()){
                        if(check_street()){
                            if(check_city()){
                                if(check_state()){
                                    /* Post Order */
                                    // Declare Vars
                                    let ids = sessionStorage.ids;
                                    let quantities = sessionStorage.quantities;
                                    let prices = sessionStorage.prices;
                                    let ccFirstName = document.getElementById("cc-firstName").value;
                                    let ccLastName = document.getElementById("cc-lastName").value;
                                    let cc = document.getElementById("cc").value;
                                    let cvc = document.getElementById("cc-cvc").value;
                                    let date = document.getElementById("cc-date").value;
                                    let street = document.getElementById("street").value;
                                    let city = document.getElementById("city").value;
                                    let state = document.getElementById("state").value;
                                    let total = document.getElementById("total").value;
  
                                    /* POST */
                                    let success = await createShippingOrder(ccFirstName, ccLastName, cc, cvc, date, street, city, state, total);
  
                                    if (success) {
                                      window.alert("Success!\n");
                                    }
                                    else {
                                      window.alert("failure to create shipping order\n");
                                    }
                                    return;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
  }
  
  function packageFormData(data) {
    return new Promise((resolve, reject) => {
        formData = new FormData();
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
                    alert("Invalid input!");
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
                    alert("Nothing was found with that search parameter");
                }
            }
        };
  
        XHR.send(data);
  })};
  
  // Changed
  async function createShippingOrder(ccFirstName, ccLastName, sentCC, sentCVC, sentCCDate, sentStreet, sentCity, sentState, sentTotal) {
    var orderList, shippingData, orderPostResult, url;
    url = "/v1/orders.php";
    orderList = ["sample item"];
    shippingData = await packageFormData({orders      : orderList,
                                          ccfirstname : ccFirstName,
                                          cclastname  : ccLastName,
                                          cc          : sentCC,
                                          cvc         : sentCVC,
                                          ccDate      : sentCCDate,
                                          city        : sentCity,
                                          state       : sentState,
                                          street      : sentStreet,
                                          total       : sentTotal});
    orderPostResult = await sendData(shippingData, url, "POST");
    console.log("orderPostResult is " + orderPostResult);
  }
  
  function check_cc_firstName(){
    if (document.getElementById("cc-firstName").value.length>2){
        document.getElementById("cc-firstName").style.borderColor="#5EFF33"; //green
        return true;
    } else {
        document.getElementById("cc-firstName").style.borderColor="#FF3333"; //red
    }
  }
  function check_cc_lastName(){
    if (document.getElementById("cc-lastName").value.length>2){
        document.getElementById("cc-lastName").style.borderColor="#5EFF33"; //green
        return true;
    } else {
        document.getElementById("cc-lastName").style.borderColor="#FF3333"; //red
    }
  }
  function check_cc(){
    if (document.getElementById("cc").value.length==16){
        document.getElementById("cc").style.borderColor="#5EFF33"; //green
        return true;
    } else {
        document.getElementById("cc").style.borderColor="#FF3333"; //red
    }
  }
  function check_cvc(){
    if (document.getElementById("cc-cvc").value.length==3){
        document.getElementById("cc-cvc").style.borderColor="#5EFF33"; //green
        return true;
    } else {
        document.getElementById("cc-cvc").style.borderColor="#FF3333"; //red
    }
  }
  function check_date(){
    if (document.getElementById("cc-date").value.length==7){
        document.getElementById("cc-date").style.borderColor="#5EFF33"; //green
        return true;
    } else {
        document.getElementById("cc-date").style.borderColor="#FF3333"; //red
    }
  }
  function check_street(){
    if (document.getElementById("street").value.length>5){
        document.getElementById("street").style.borderColor="#5EFF33"; //green
        return true;
    } else {
        document.getElementById("street").style.borderColor="#FF3333"; //red
    }
  }
  function check_city(){
    if (document.getElementById("city").value.length>2){
        document.getElementById("city").style.borderColor="#5EFF33"; //green
        return true;
    } else {
        document.getElementById("city").style.borderColor="#FF3333"; //red
    }
  }
  function check_state(){
    if (document.getElementById("state").value.length==2){
        document.getElementById("state").style.borderColor="#5EFF33"; //green
        return true;
    } else {
        document.getElementById("state").style.borderColor="#FF3333"; //red
    }
  }