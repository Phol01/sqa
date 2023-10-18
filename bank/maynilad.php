<?php
session_start();
include "login/config.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login/login.php");
    exit();
}


include "login/config.php"; 


$userID = $_SESSION['user_id'];

$sql = "SELECT balance FROM users WHERE user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userID]);
$user = $stmt->fetch();

if ($user) {
    $userBalance = $user['balance'];
} else {

    $userBalance = "N/A"; 
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentAmount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $accountNo = isset($_POST['accountNo']) ? $_POST['accountNo'] : '';
    $accountName = isset($_POST['accountName']) ? $_POST['accountName'] : '';

    if ($userBalance >= $paymentAmount) {
    
        $newBalance = $userBalance - $paymentAmount;

      
        $_SESSION['balance'] = $newBalance;
        

        
        $updateSql = "UPDATE users SET balance = ? WHERE user_id = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$newBalance, $userID]);

        
        $insertSql = "INSERT INTO trx_water (billerID, merchantID, user_id, accNum, amount, accName) VALUES (?, ?, ?, ?, ?, ?)";
        $billerID = '2'; 
        $merchantID = '2'; 

        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([$billerID, $merchantID, $userID, $accountNo, $paymentAmount, $accountName]);

        
        header("Location: nawasa.php");
        exit();
    } else {
        
        $paymentError = "Insufficient balance. Please top up your account.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.min.css">

<style>
  table {
    border-collapse: collapse;
  }

  td {
    
    padding-bottom: 10px;
    text-align: left;
  }
  td:nth-child(2) {
    padding-left: 20px; 
  }
  
  body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Dark overlay */
    z-index: -1; /* Place the overlay behind the content */
}

body {
    font-family: Arial, sans-serif;
    background-image: url('bills_background.jpg'); /* Replace 'bills_background.jpg' with the actual file path */
    background-size: cover; /* Ensure the image covers the entire background */
    background-repeat: no-repeat; /* Prevent the image from repeating */
    margin: 0;
    padding: 0;
}

.container {
    background-color: rgba(255, 255, 255, 1); /* Solid white background */
    border-radius: 10px; /* Optional: Add rounded corners for a smoother blend */
    box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1); /* Optional: Add a shadow for depth */
    padding: 20px;
    width: 90%;
    max-width: 400px;
    margin: 40px auto;
}


  .back-button {
    margin-bottom: 10px;
  }

  .back-button a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: black;
    font-weight: bold;
  }

  .back-button svg {
    width: 20px;
    height: 20px;
    margin-right: 6px;
  }

  .supplier-details {
    background-color: #ffffff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
  }

  .supplier-name {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
  }

  .detail-item {
    font-size: 16px;
    margin-bottom: 10px;
    color: #555;
  }

  .next-button {
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 10px 20px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }

  .next-button:hover {
    background-color: #0056b3;
  }
  
  .supplier-details {
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  }

  .detail-item {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
  }

  .detail-label {
    font-size: 14px;
    color: #555;
    margin-bottom: 5px;
  }

  .detail-input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
  }

  .next-button {
    width: 100%; 
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 10px 16px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .next-button:hover {
    background-color: #0056b3;
  }
  .circle {
    width: 65px; 
    height: 65px; 
    border-radius: 50%;
    overflow: hidden; 
    text-align: center; 
    margin: 0 auto; 
    background-color: #f0f0f0; 
}
.circle img {
    max-width: 100%; 
    height: auto; 
    display: block; 
    margin: 0 auto; 
}

  
</style>
<title>Pay Bills</title>
</head>
<body>
    <div class="container">
        <div class="back-button">
            <a href="waterbill.php">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path d="M0 0h24v24H0z" fill="none"/>
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
              </svg>
              Back to Suppliers
            </a>
          </div>
        <div class="supplier-details">
            <div class="circle">
            <div style="text-align: center;"><img src="https://media.philstar.com/photos/2023/09/27/mayniladlogo-faucet-image-philstar_2023-09-27_23-41-50.jpg" width="65px" height="65px"></div>
            </div>
            <div class="supplier-name" id="merchant"><center>Maynilad</center></div>
            <div class="detail-item">
              <span class="detail-label">Amount I'm paying:</span>
              <input type="text" class="detail-input" id="amount" placeholder="Enter amount" />
            </div>
            <div class="detail-item">
              <span class="detail-label">Account No.:</span>
              <input type="text" class="detail-input" id="accountNo" placeholder="Enter account number" />
            </div>
            <div class="detail-item">
              <span class="detail-label">Account Name:</span>
              <input type="text" class="detail-input" id="accountName" placeholder="Enter account name" />
            </div>
            <div class="detail-item">
              <span class="detail-label">Email:</span>
              <input type="email" class="detail-input" id="email" placeholder="Enter email (Optional)" />
            </div>
            <button class="next-button" id="nextButton">Next</button>

          </div>
    </div>



</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.min.js"></script>
<!-- <script>
      
        document.getElementById("confButton").addEventListener("click", function() {
        
            const amount = parseFloat(document.getElementById("amount").value);
            const accountNo = document.getElementById("accountNo").value;
            const accountName = document.getElementById("accountName").value;
            const email = document.getElementById("email").value;

          
            if (isNaN(amount) || amount <= 0) {
                Swal.fire({
                    title: "Invalid Amount",
                    text: "Please enter a valid positive amount.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                return;
            }

            const remainingBalance = 500 - amount;

            
        });
    </script> -->


<script>
  const nextButton = document.getElementById("nextButton");
  const nameInput = document.getElementById("accountName");
  const accNumInput = document.getElementById("accountNo");
  const amtInput = document.getElementById("amount");
  const mailInput = document.getElementById("email");

  nextButton.addEventListener("click", function() {
      
    
      document.getElementById("nameOut").textContent = nameInput.value;
      document.getElementById("amtOut").textContent = amtInput.value;
      document.getElementById("numOut").textContent = accNumInput.value;
      document.getElementById("mailOut").textContent = mailInput.value;

      
      $('#myModal').modal('show');

       // Store values in session variables
     sessionStorage.setItem("nameOut", nameInput.value);
    sessionStorage.setItem("amtOut", amtInput.value);
    sessionStorage.setItem("numOut", accNumInput.value);
    sessionStorage.setItem("mailOut", mailInput.value);
    sessionStorage.setItem("billOut", billInput.value);
   
  });
</script> 


<script>
   
    // document.getElementById("confButton").addEventListener("click", function() {
        
    //     const amount = parseFloat(document.getElementById("amount").value);
    //     const accountNo = document.getElementById("accountNo").value;
    //     const accountName = document.getElementById("accountName").value;
    //     const email = document.getElementById("email").value;

       
    //     if (isNaN(amount) || amount <= 0) {
    //         Swal.fire({
    //             title: "Invalid Amount",
    //             text: "Please enter a valid positive amount.",
    //             icon: "error",
    //             confirmButtonText: "OK"
    //         });
    //         return;
    //     }

       
    //     $.ajax({
    //         type: "POST",
    //         url: "process_payment_nawasa.php", 
    //         data: {
    //             amount: amount,
    //             accountNo: accountNo,
    //             accountName: accountName,
    //             email: email
    //         },
    //         success: function(response) {
                
    //             if (response === "success") {

    //                 Swal.fire({
    //                     title: "Payment Successful",
    //                     text: "Your payment has been successfully processed.",
    //                     icon: "success",
    //                     confirmButtonText: "OK"
    //                 }).then((result) => {
    //                     if (result.isConfirmed) {
    //                         window.location.href = "receipt.php"; 
    //                     }
    //                 });
    //             } else {
                    
    //                 Swal.fire({
    //                     title: "Payment Failed",
    //                     text: response, 
    //                     icon: "error",
    //                     confirmButtonText: "OK"
    //                 });
    //             }
    //         }
    //     });
    // });
      $(document).ready(function() {
        $("#confButton").click(function() {
            const amount = parseFloat($("#amount").val());
            const accountNo = $("#accountNo").val();
            const accountName = $("#accountName").val();
            const email = $("#email").val();

            if (isNaN(amount) || amount <= 0) {
                Swal.fire({
                    title: "Invalid Amount",
                    text: "Please enter a valid positive amount.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                return;
            }

            $.ajax({
                type: "POST",
                url: "process_payment_nawasa.php",
                data: {
                    amount: amount,
                    accountNo: accountNo,
                    accountName: accountName,
                    email: email
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            title: "Payment Successful",
                            text: "Your payment has been successfully processed.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "receipt.php";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Payment Failed",
                            text: response,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                }
            });
        });
    });
</script>


<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Confirmation</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <table>
            <tr>
              <td>Account Name:</td>
              <td id="nameOut"></td>
            </tr>
            <tr>
              <td>Amount:</td>
              <td id="amtOut"></td>
            </tr>
            <tr>
              <td>Account Number:</td>
              <td id="numOut"></td>
            </tr>
            <tr>
              <td>Email:</td>
              <td id="mailOut"></td>
            </tr>
          </table>
        <p><b>Do you want to proceed?</b></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="confButton">Confirm</button>
      </div>
    </div>
  </div>
</div>
</html>
