<!DOCTYPE html>
<html>

<head>

    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            padding: 20px;
        }


        .container {

            background: white;
            padding: 25px;
            max-width: 600px;
            margin: auto;

        }


        table {

            width: 100%;
            border-collapse: collapse;

        }


        td {

            border: 1px solid #ddd;
            padding: 10px;

        }
    </style>

</head>


<body>


    <div class="container">


        <h3>
            New HR Request Submitted
        </h3>


        <p>
            A new HR request has been submitted.
        </p>



        <table>


            <tr>

                <td>
                    <strong>Reference No.</strong>
                </td>

                <td>
                    <?= $reference_no ?>
                </td>

            </tr>



            <tr>

                <td>
                    <strong>Applicant</strong>
                </td>

                <td>
                    <?= $fullname ?>
                </td>

            </tr>



            <tr>

                <td>
                    <strong>Service</strong>
                </td>

                <td>
                    <?= $service ?>
                </td>

            </tr>



            <tr>

                <td>
                    <strong>Date Requested</strong>
                </td>

                <td>
                    <?= $date_requested ?>
                </td>

            </tr>



            <tr>

                <td>
                    <strong>Status</strong>
                </td>

                <td>
                    New Application
                </td>

            </tr>


        </table>


        <br>


        <p>
            Please login to HR Dashboard to process this request.
        </p>


    </div>


</body>

</html>