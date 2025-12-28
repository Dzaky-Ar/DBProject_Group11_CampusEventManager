<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval</title>
    <link rel="stylesheet" href="approval.css">
</head>
<body>
    <div>Approval </div>
        <div>
            <?php
            require_once "config.php";
            $sql = "SELECT proposalID,description, status,organizer.instansi from 
                    proposal_try inner join organizer on organizer.organizerID=proposal_try.organizerID;";
            if($result=mysqli_query($conn,$sql)){
                if(mysqli_num_rows($result) > 0){
                    
                    echo'<table>';
                        echo '<thead>';
                            echo'<tr class="table1">';
                                echo'<th>Proposal ID</th>';
                                echo'<th>Organizer</th>';
                                echo'<th>Desc</th>';
                                echo'<th>Status</th>';
                            echo'</tr>';
                        echo '</thead>';
                        echo'<tbody>';
                        while($row=mysqli_fetch_array($result)){
                            echo'<tr>';
                            echo '<td>'.$row['proposalID'].'</td>';
                            echo '<td>'.$row['instansi'].'</td>';
                            echo '<td>'.$row['description'].'</td>';
                            echo '<td>'.$row['status'].'</td>';
                            
                            echo'<td>';
                            $i=0;
                            switch($row['status']){
                                case 'pending':
                                    echo'<form method="post"> ';
                                    echo '<button type="submit" name="approve"><a href="approve.php?id='.$row['proposalID'] .'"> approve</a> </button>';
                                    echo '<button type="submit" name="deny"><a href="delete.php?id='.$row['proposalID'] .'"> deny</a></button>';
                                     echo'</form> ';
                                    break;
                                case 'approved':
                                    echo '<a href="">see event page</a>';
                                    break;
                                case 'denied':
                                    echo 'denied';
                                    break;
                            }   
                            echo'</td>';
                            echo'</tr>';
                        }
                        echo'</tbody>';
                    echo'</table>';
                    mysqli_free_result($result);
                } else{
                    echo'No records found';
                }
            }
            ?>
        </div>
    
</body>
</html>