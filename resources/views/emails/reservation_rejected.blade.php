<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reservation Rejected</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #fafafa;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            padding: 25px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #c0392b;
            margin-bottom: 20px;
        }

        p {
            line-height: 1.6;
        }

        ul {
            padding-left: 20px;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }

        .proof-image {
            margin-top: 10px;
            border-radius: 6px;
            max-width: 100%;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Reservation Rejected</h2>

        ```
        <p>Dear {{ $reservation->customer->name ?? 'Valued Customer' }},</p>

        <p>We regret to inform you that your reservation has been rejected due to an issue with your advance payment.
        </p>

        <p><strong>Reason:</strong> The amount you paid does not match the required advance payment.</p>

        <p>Here are the details of your reservation:</p>

        <ul>
            <li><strong>Date &amp; Time:</strong>
                {{ $reservation->started_at ? $reservation->started_at->format('F d, Y h:i A') : 'N/A' }}</li>
        </ul>

        @if ($reservation->payment)
            <p><strong>Advance Payment:</strong> ₱{{ number_format($reservation->payment->advance_payment, 2) }}</p>

            ```
            @if ($reservation->payment->payment_proof)
                <p><strong>Payment Proof:</strong></p>
                <img src="{{ url('/file-serve/payment_proofs/' . basename($reservation->payment->payment_proof)) }}"
                    alt="Payment Proof" class="proof-image">
            @endif
            ```

        @endif


        <p>If you would like to make another reservation or need assistance, please contact us.</p>

        <div class="footer">
            <p>Thanks,<br><strong>Jeongol Izakaya Team</strong></p>
        </div>
    </div>
    ```

</body>

</html>