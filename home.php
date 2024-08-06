<!DOCTYPE HTML>
<html>

<head>
  <title>Home Automation Switches</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
    integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <link rel="icon" href="data:,">
  <link rel="stylesheet" href="css/home.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .topnav {
      background-color: #333;
      color: white;
      padding: 10px 0;
      width: 100%;
      text-align: center;
    }

    .topnav h3 {
      margin: 0;
      font-size: 1.5rem;
    }

    .content {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-top: 20px;
      width: 100%;
      max-width: 600px;
      background-color: white;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .card {
      width: 100%;
      margin: 10px 0;
      padding: 15px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .card.header {
      background-color: #f7f7f7;
      border-bottom: 1px solid #ddd;
    }

    .card h3 {
      margin: 0;
      font-size: 1.2rem;
    }

    h4.LEDColor {
      display: flex;
      align-items: center;
      font-size: 1rem;
      margin: 10px 0;
    }

    .LEDColor i {
      margin-right: 10px;
      color: #f39c12;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 40px;
      height: 20px;
      margin: 10px 0;
    }

    .switch input {
      display: none;
    }

    .sliderTS {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 20px;
    }

    .sliderTS:before {
      position: absolute;
      content: "";
      height: 16px;
      width: 16px;
      left: 2px;
      bottom: 2px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .sliderTS {
      background-color: #66bb6a;
    }

    input:checked + .sliderTS:before {
      transform: translateX(20px);
    }

    .statusreadColor {
      font-size: 1rem;
      color: #888;
    }
  </style>
</head>

<body>
  <div class="topnav">
    <h3>Home Automation Switches</h3>
  </div>

  <br>

  <!-- __ DISPLAYS MONITORING AND CONTROLLING ____________________________________________________________________________________________ -->
  <div class="content">
    <div class="cards">
      <p class="statusreadColor"><span> </span><span id="ESP32_01_Status_Read_DHT11"></span></p>
    </div>
    <!-- ======================================================================================================= -->

    <!-- == CONTROLLING ======================================================================================== -->
    <div class="card">
      <div class="card header">
        <h3>CONTROLLING</h3>
      </div>

      <!-- Buttons for controlling the LEDs on Slave 2. ************************** -->
      <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> LED 1</h4>
      <label class="switch">
        <input type="checkbox" id="ESP32_01_TogLED_01" onclick="GetTogBtnLEDState('ESP32_01_TogLED_01')">
        <div class="sliderTS"></div>
      </label>
      <h4 class="LEDColor"><i class="fas fa-lightbulb"></i> LED 2</h4>
      <label class="switch">
        <input type="checkbox" id="ESP32_01_TogLED_02" onclick="GetTogBtnLEDState('ESP32_01_TogLED_02')">
        <div class="sliderTS"></div>
      </label>
      <!-- *********************************************************************** -->
    </div>
    <!-- ======================================================================================================= -->

  </div>

  <script>
    //------------------------------------------------------------
    document.getElementById("ESP32_01_Temp").innerHTML = "NN";
    document.getElementById("ESP32_01_Humd").innerHTML = "NN";
    document.getElementById("ESP32_01_Status_Read_DHT11").innerHTML = "NN";
    document.getElementById("ESP32_01_LTRD").innerHTML = "NN";
    //------------------------------------------------------------

    Get_Data("esp32_01");

    setInterval(myTimer, 5000);

    //------------------------------------------------------------
    function myTimer() {
      Get_Data("esp32_01");
    }
    //------------------------------------------------------------

    //------------------------------------------------------------
    function Get_Data(id) {
      if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
      } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const myObj = JSON.parse(this.responseText);
          if (myObj.id == "esp32_01") {
            document.getElementById("ESP32_01_Temp").innerHTML = myObj.temperature;
            document.getElementById("ESP32_01_Humd").innerHTML = myObj.humidity;
            document.getElementById("ESP32_01_Status_Read_DHT11").innerHTML = myObj.status_read_sensor_dht11;
            document.getElementById("ESP32_01_LTRD").innerHTML = "Time : " + myObj.ls_time + " | Date : " + myObj.ls_date + " (dd-mm-yyyy)";
            if (myObj.LED_01 == "ON") {
              document.getElementById("ESP32_01_TogLED_01").checked = true;
            } else if (myObj.LED_01 == "OFF") {
              document.getElementById("ESP32_01_TogLED_01").checked = false;
            }
            if (myObj.LED_02 == "ON") {
              document.getElementById("ESP32_01_TogLED_02").checked = true;
            } else if (myObj.LED_02 == "OFF") {
              document.getElementById("ESP32_01_TogLED_02").checked = false;
            }
          }
        }
      };
      xmlhttp.open("POST", "getdata.php", true);
      xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlhttp.send("id=" + id);
    }
    //------------------------------------------------------------

    //------------------------------------------------------------
    function GetTogBtnLEDState(togbtnid) {
      if (togbtnid == "ESP32_01_TogLED_01") {
        var togbtnchecked = document.getElementById(togbtnid).checked;
        var togbtncheckedsend = "";
        if (togbtnchecked == true) togbtncheckedsend = "ON";
        if (togbtnchecked == false) togbtncheckedsend = "OFF";
        Update_LEDs("esp32_01", "LED_01", togbtncheckedsend);
      }
      if (togbtnid == "ESP32_01_TogLED_02") {
        var togbtnchecked = document.getElementById(togbtnid).checked;
        var togbtncheckedsend = "";
        if (togbtnchecked == true) togbtncheckedsend = "ON";
        if (togbtnchecked == false) togbtncheckedsend = "OFF";
        Update_LEDs("esp32_01", "LED_02", togbtncheckedsend);
      }
    }
    //------------------------------------------------------------

    //------------------------------------------------------------
    function Update_LEDs(id, lednum, ledstate) {
      if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
      } else {
        // code for IE6, IE5
