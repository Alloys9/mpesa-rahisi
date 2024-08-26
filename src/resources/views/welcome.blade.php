<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STK Push Payment</title>
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

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            font-size: 1rem;
            color: #333;
            background: #fff;
            transition: 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #6A82FB;
            transform: scale(1.02);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
        }

        .form-group label {
            position: absolute;
            top: -1rem;
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
    </style>
</head>

<body>
    <div class="container">
        <img src="{{ asset('images/mpesa.png') }}" alt="Payment Image">
        <h1>Make a Payment</h1>
        <form action="{{ url('/payments/initiatepush') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" placeholder="Enter the amount" required>
            </div>
            <button type="submit" class="submit-btn">Pay Now</button>
        </form>
    </div>

</body>

</html>
