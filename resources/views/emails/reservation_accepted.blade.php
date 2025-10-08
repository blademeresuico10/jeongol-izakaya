<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reservation Confirmed!</title>
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
            color: black;
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
    </style>
</head>

<body>
    <div class="container">
        <h2>Reservation Confirmed</h2>

        ```
        <p>Dear {{ $reservation->customer->name ?? 'Valued Customer' }},</p>

        <p>Your reservation has been confirmed! Here are the details:</p>

        <ul>
            <li><strong>Date &amp; Time:</strong>
                {{ $reservation->started_at ? $reservation->started_at->format('F d, Y h:i A') : 'To be confirmed' }}
            </li>
            <li><strong>Table:</strong> {{ $reservation->table->table_number ?? 'N/A' }}</li>

        </ul>

        <p>We look forward to welcoming you at <strong>Jeongol Izakaya Hotpot &amp; Grill</strong>.</p>

        <p>If you have any questions, please contact us.</p>

        <div class="footer">
            <p>Thanks,<br><strong>Jeongol Izakaya Team</strong></p>
        </div>
    </div>
    ```

</body>

</html>