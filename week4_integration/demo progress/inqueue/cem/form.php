<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal</title>
</head>
<body>
    <div>
        
        <form action="connect.php" method="post" enctype="multipart/form-data" style="width: 150px;">
            <h1>Proposal</h1>
            <div>
                <?php
                $previous = explode("/",strtolower($_SERVER["HTTP_REFERER"]));
                if($previous[4]=="form.php"){
                    echo "Proposal Submitted";
                };

                ?>
            </div>
            <div style="width: 150px;">
                <input type="text" required name="organizer" placeholder="organizer">
                <input type="text" required name="proposal" placeholder="deskripsi">
            </div>            
            <input type="file" required name="file" accept=".pdf">
            <button type="submit">Submit</button>
        </form>
    </div>
    <div>
        <button><a href="approval.php">Check Approval</a></button>
    </div>
</body>
</html>