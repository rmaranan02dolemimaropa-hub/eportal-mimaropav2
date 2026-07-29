<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
        }

        h2 {
            color: #10058C;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td,
        table th {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .footer {
            margin-top: 20px;
            font-size: 13px;
            color: #777;
        }
    </style>

</head>


<body>

    <div class="container">


        <h2>
            DOLE MIMAROPA E-Portal
        </h2>


        <p>
            Dear <strong>
                <?= $fullname ?>
            </strong>,
        </p>


        <p>
            Your HR request has been successfully submitted.
        </p>



        <table>

            <tr>
                <th>
                    Reference Number
                </th>

                <td>
                    <?= $reference_no ?>
                </td>

            </tr>


            <tr>
                <th>
                    Service Requested
                </th>

                <td>
                    <?= $service ?>
                </td>

            </tr>


            <tr>
                <th>
                    Date Requested
                </th>

                <td>
                    <?= $date_requested ?>
                </td>

            </tr>


        </table>


        <br>


        <p>
            Please keep your <strong>Reference Number</strong> for tracking your request.
        </p>


        <div class="footer">

            Thank you for using DOLE MIMAROPA E-Portal.

        </div>

        <p style="color:#777;font-size:12px;">
            This is an automated email from DOLE MIMAROPA E-Portal.
            Please do not reply to this email.
        </p>


    </div>

</body>

</html>