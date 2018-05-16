<?php

include_once 'dbConnection.php';
if (isset($_POST['winConfirm'])) {
    include 'mailer.php';

    $mail->addAddress($_POST['sellerMail'], $_POST['sellerName']);
    $mail->Subject = 'Your auction has been successfully completed!';
    $mail->Body = $_POST['buyerName'] . ' has won your auction for ' . $_POST['winningBid'] . '. The corresponding amount of money will be paid into your account.';

    
    if (!$mail->send()) {
        
    } else {
        
    }


    $mail->clearAddresses();



    $mail->addAddress($_POST['buyerMail'], $_POST['username']);

    $mail->Subject = 'You have succesfully won an auction';

    $mail->Body = 'You have won the ' . $_POST['itemlabel'] . ' auction! The corresponding amount of money will be deducted from your account';

    if (!$mail->send()) {
        
    } else {
        
    }

    $id = $_POST['auction_id'];
    $updatesql = "UPDATE Auction
                                        SET win_confirmed=1
                                        WHERE auction_id=:auctionID";
    //                                
    $stmt = $db->prepare($updatesql);
    $stmt->bindParam(':auctionID', $id);
    $stmt->execute();
    header('location: bidsauctions.php');
}

if (isset($_POST['stopAuction']) and is_numeric($_POST['stopAuction'])) {
    $now = new DateTime();
    $time = $now->format("Y-m-d H:i:s");
    $id = $_POST['stopAuction'];

    $updatesql = "UPDATE Auction
                                SET end_time=:nowtime
                                WHERE auction_id=:auctionID";
    $stmt = $db->prepare($updatesql);
    $stmt->bindParam(':nowtime', $time);
    $stmt->bindParam(':auctionID', $id);
    $stmt->execute();
    header('location: bidsauctions.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>online bidding system</title>

    
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <script src="js/jquery.js"></script>
    
    <script src="js/bootstrap.min.js"></script>

    <script src="clockCode/countdown.js"></script>
    <script>
        $('#confirmwin').on('click', function () {
            var $el = $(this),
                textNode = this.lastChild;
            $el.find('span').toggleClass('glyphicon-fire glyphicon-road');
            textNode.nodeValue = ($el.hasClass('showArchived') ? 'Confirm Win' : 'Win Confirmed');
            $el.toggleClass('showArchived');
        });
    </script>

</head>

<body>


<?php
include 'nav.php';
?>

<div class="container" style="padding-top:50px">
    <div class="row" style="padding-top:50px">
        <div class="col-sm-12 col-md-10 col-lg-12">
            
            <table class="table table-hover">
                
                <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-center">Bid Info</th>
                    <?php
                    if ($_SESSION['role_id'] == 2) {
                        echo '<th class="text-center">Your Reserve Price</th>';
                    } else if ($_SESSION['role_id'] == 1) {
                        echo '<th class="text-center">Your Last Bid</th>';
                    }
                    ?>
                    <?php
                    if ($_SESSION['role_id'] == 2) {
                        echo '<th class="text-center">Current Bid</th>';
                    } else if ($_SESSION['role_id'] == 1) {
                        echo '<th class="text-center">Current Price</th>';
                    }
                    ?>
                   
                </tr>
                </thead>
                
                <tbody>
                <?php
                $userid = $_SESSION['user_id'];
                
                if ($_SESSION['role_id'] == 1) {
                    $sql = "SELECT a.auction_id,a.reserve_price, a.viewings, i.label,i.item_picture,max(b.bid_price) as bid_price,u.first_name, u.username, b.user_id, a.user_id AS sellerID, u.email, a.end_time, a.current_bid FROM Bids b
                            INNER JOIN Auction a ON a.auction_id = b.auction_id
                            INNER JOIN Users u ON u.user_id = a.user_id
                            INNER JOIN Item i ON a.item_id = i.item_id WHERE b.user_id = $userid
                            GROUP BY b.auction_id ORDER BY a.end_time DESC";
                }
                
                if ($_SESSION['role_id'] == 2) {

                    $sql = "SELECT * FROM Auction a
                            INNER JOIN Users u ON a.user_id = u.user_id
                            INNER JOIN Item i ON a.item_id = i.item_id WHERE a.user_id = $userid
                            ORDER BY a.end_time DESC";
                }
                try {
                    $data = $db->query($sql);
                    $data->setFetchMode(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo 'ERROR: ' . $e->getMessage();
                }
                ?>
                <?php while ($bidauction = $data->fetch()): ?>
                    <tr style="vertical-align">
                        <td class="col-sm-12 col-md-4">
                            <div class="media">
                                <a class="thumbnail pull-left" href="#"> <img class="media-object"
                                                                              src="<?php
                                                                              echo $bidauction['item_picture'];
                                                                              ?>"
                                                                              style="width: 72px; height: 72px;"> </a>
                                <div class="media-body">
                                    <h4 class="media-heading">
                                        <a href="productpage.php?auct=<?php echo $bidauction['auction_id']; ?>">
                                            <?php
                                            echo htmlspecialchars($bidauction['label']);
                                            ?></a></h4>
                                    <?php
                                    if ($_SESSION['role_id'] == 1) {
                                        ?>
                                        <h5 class="media-heading"> Sold By: <a
                                                href="profile.php?user=<?php echo $bidauction['sellerID']; ?>"><?php
                                                echo htmlspecialchars($bidauction['username'])
                                                ?></a></h5>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    $auctionID = $bidauction['auction_id'];
                                    $bidSQL = 'SELECT u.user_id, u.username, u.email, u.first_name, u.last_name, a.win_confirmed FROM
                                            Bids b, Users u, Auction a WHERE b.user_id =u.user_id AND b.auction_id =:auctionID AND a.auction_id =:auctionID ORDER BY b.bid_price DESC LIMIT 1';
                                    $bidSQL = $db->prepare($bidSQL);
                                    $bidSQL->bindParam(':auctionID', $auctionID);
                                    $bidSQL->execute();
                                    $result = $bidSQL->fetch();
                                    $enddt = strtotime($bidauction['end_time']);
                                    ?>
                                   
                                    
                                    
                                </div>
                            </div>
                        </td>
                        <td class="col-sm-2 col-md-2"><strong></strong>
                            <h5 class="media-heading"> Number of Bids: <?php
                                $numsql = "SELECT count(b.bid_id) as bidcount FROM Bids b
                            WHERE auction_id=$auctionID GROUP BY auction_id ";
                                try {
                                    $numdata = $db->query($numsql);
                                    $numdata->setFetchMode(PDO::FETCH_ASSOC);
                                    $numbids = $numdata->fetch();
                                } catch (PDOException $e) {
                                    echo 'ERROR: ' . $e->getMessage();
                                }
                                echo htmlspecialchars($numbids['bidcount']);
                                ?></h5>
                            <h5 class="media-heading"> Highest Bidder: <a
                                    href="profile.php?user=<?php echo $result['user_id']; ?>"><?php
                                    echo htmlspecialchars($result['username'])
                                    ?></a></h5>
                            <h5 class="media-heading"> Viewings: <?php
                                echo htmlspecialchars($bidauction['viewings'])
                                ?></h5>
                        </td>
                        <td class="col-sm-2 col-md-2 text-center"><strong><?php
                                if ($_SESSION['role_id'] == 1) {
                                    echo htmlspecialchars($bidauction['bid_price']);

                                }
                                if ($_SESSION['role_id'] == 2) {
                                    echo htmlspecialchars($bidauction['reserve_price']);
                                }
                                ?></strong></td>
                        <td class="col-sm-2 col-md-2 text-center"><strong><?php

                                echo htmlspecialchars($bidauction['current_bid']);
                                ?>
                               
                               <?php
                               ?>
                               
                            </strong>

                        </td>
                       

                            <?php
                            if ($_SESSION['role_id'] == 1 && $enddt < time() && $bidauction['bid_price'] >= $bidauction['current_bid']
                                && $bidauction['current_bid'] > $bidauction['reserve_price']) {
                                if ($result['win_confirmed'] == 1) {
                                    ?>
                            
                                    <?php
                                }
                                else {
                                    ?>

                                   
                                    <?php
                                }
                                ?>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td>  </td>
                    <td>  </td>
                    <td>  </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    
    $('#stopauction').on('click', function () {
        var $el = $(this),
            textNode = this.lastChild;
        $el.find('span').toggleClass('glyphicon-fire glyphicon-road');
        textNode.nodeValue = ($el.hasClass('stopAuction') ? 'Stop Auction' : 'Auction Stopped');
        $el.toggleClass('stopAuction');
    });



</script>
</body>
</html>