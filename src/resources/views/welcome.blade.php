<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mpesa Payment</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(120deg, #84fab0, #8fd3f4);
            font-family: 'Roboto', sans-serif;
        }

        .branding {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }

        .branding h1 {
            margin: 10px 0;
            font-size: 22px;
            color: #555;
        }

        .branding a {
            text-decoration: none;
            color: #6A82FB;
        }

        .branding p {
            margin: 5px 0;
            font-size: 14px;
            color: #777;
        }

        .container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0px 6px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 360px;
            position: relative;
        }

        .container img {
            width: 120px;
            height: auto;
            margin-bottom: 1rem;
            background: #ffffff;
            padding: 0.5rem;
        }

        .container h1 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-container {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 25px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }

        .input-container .prefix {
            padding: 0.8rem;
            background: #f4f4f4;
            border-right: 1px solid #ddd;
            border-radius: 25px 0 0 25px;
            color: #333;
            font-size: 1rem;
            font-weight: 500;
        }

        .input-container input {
            flex: 1;
            padding: 0.8rem;
            border: none;
            font-size: 1rem;
            color: #333;
            background: #ffffff;
            border-radius: 0 25px 25px 0;
            transition: 0.3s;
        }

        .input-container input:focus {
            outline: none;
            border-color: #6A82FB;
            transform: scale(1.02);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
        }

        .form-group label {
            position: absolute;
            top: -1.2rem;
            left: 1rem;
            font-size: 0.9rem;
            color: #6A82FB;
            background: #ffffff;
            padding: 0 0.5rem;
            border-radius: 25px;
            font-weight: 500;
        }

        .submit-btn {
            padding: 0.8rem;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            color: #ffffff;
            background: linear-gradient(45deg, #FC5C7D, #6A82FB);
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }

        .submit-btn:hover {
            transform: scale(1.05);
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.15);
        }

        /* FOOTER */
        .footer {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            --webkit-backdrop-filter: blur(10px);
            bottom: 0;
            height: 12px;
            width: 100%;
            position: fixed;
            z-index: 1000;
        }

        .footer a,
        .footer p {
            color: white;
            text-decoration: none;
            padding-right: 20px;
            font-size: 10px;
            padding: 5px;
        }

        .footer a:hover {
            color: blue;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
            max-width: 500px;
            margin: 0 auto 20px auto;
        }

        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }

        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
    </style>
</head>

<body>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="container">
        <div class="branding">
            <h1>Service by <a href="https://www.alloysamasakha.com/" target="_blank">EutopiaTech</a></h1>
            <p>All programming solutions</p>
        </div>

        <h1>Make a Payment</h1>
        <form id="payment-form" action="{{ url('/payments/initiatepush') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-container">
                    <div class="prefix">254</div>
                    <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
                </div>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <div class="input-container">
                    <div class="prefix">KSh</div>
                    <input type="number" id="amount" name="amount" placeholder="Enter the amount" required>
                </div>
            </div>
            <button type="submit" class="submit-btn">Pay Now</button>
        </form>
    </div>

    <footer class="footer">
        <p>&copy; 2024 EutopiaTech. All rights reserved.</p>
        <p>Contact us at: <a href="mailto:eutopiatech@gmail.com">eutopiatech@gmail.com</a></p>
    </footer>

    <script>
        document.getElementById('payment-form').addEventListener('submit', function(event) {
            var phoneInput = document.getElementById('phone');
            var phoneNumber = phoneInput.value;

            // Ensure the phone number has the '254' prefix and remove leading zero if present
            if (!phoneNumber.startsWith('254')) {
                phoneInput.value = '254' + phoneNumber.replace(/^0/, '');
            }
        });
    </script>
</body>

</html>
