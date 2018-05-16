<?php

require_once 'dbConnection.php';

if (isset($_POST['submit'])) {

    $currentBid = $_POST['current_bid'];
    $newBid = $_POST['new_bid'];
    $auctionID = $_POST['auction_id'];
    $userID = $_POST['user_id'];
    $label = $_POST['item_label'];

    if ($newBid > $currentBid) {
        $time = new DateTime();
        $formatTime = $time->format("Y-m-d H:i:s");

        
        $bidsql = 'INSERT INTO Bids (user_id, auction_id, bid_price, bid_time)
        VALUES (:userID, :auctionID, :newBid, :bidTime)';
        $bidupdate = $db->prepare($bidsql);
        $bidupdate->bindParam(':userID', $userID);
        $bidupdate->bindParam(':auctionID', $auctionID);
        $bidupdate->bindParam(':newBid', $newBid);
        $bidupdate->bindParam(':bidTime', $formatTime);
        $bidupdate->execute();

        
        $sql = 'UPDATE Auction SET current_bid=:newBid WHERE auction_id=:auctionID';
        $response = $db->prepare($sql);
        $response->bindParam(':newBid', $newBid);
        $response->bindParam(':auctionID', $auctionID);
        $response->execute();

        
        $previousSQL = 'SELECT user_id, bid_price FROM ebay_clone.Bids WHERE auction_id = :auctionID ORDER BY bid_price DESC LIMIT 1, 1';
        $previousSTMT = $db->prepare($previousSQL);
        $previousSTMT->bindParam(':auctionID', $auctionID);
        $previousSTMT->execute();

        if ($previousUser = $previousSTMT->fetch()) {

            
            $watchSQL = 'SELECT * FROM Watch WHERE user_id = :previousUser AND auction_id = :auctionID';
            $watchSTMT = $db->prepare($watchSQL);
            $watchSTMT->bindParam(':previousUser', $previousUser['user_id']);
            $watchSTMT->bindParam(':auctionID', $auctionID);
            $watchSTMT->execute();

            
        }
        $message = '';
    } if($newBid <= $currentBid) {
        $message="New bid needs to be higher than the current bid";
    }
}
if (isset($_GET["auct"])) {
    
    require("dbConnection.php");
    $resp = $db->prepare('SELECT * FROM Auction WHERE auction_id = :auction_id');
    $resp->bindParam(':auction_id', $_GET["auct"]);
    $resp->execute();

    if ($resp->rowCount() == 0) {
        echo "Auction does not exist";
    } else {
        $data = $resp->fetch();
        $resp->closeCursor();

        $seller = $db->prepare('SELECT * FROM Users WHERE user_id = :user_id');
        $seller->bindParam(':user_id', $data["user_id"]);
        $seller->execute();

        $seller_data = $seller->fetch();
        $seller->closeCursor();

        $item = $db->prepare('SELECT * FROM Item WHERE item_id = :item_id');
        $item->bindParam(':item_id', $data["item_id"]);
        $item->execute();

        $item_data = $item->fetch();
        $item->closeCursor();

        $category = $db->prepare('SELECT * FROM Category where category_id = :category');
        $category->bindParam(':category', $item_data["category_id"]);
        $category->execute();

        $category_data = $category->fetch();
        $category->closeCursor();

        $new_view = $data['viewings'] + 1;
        $update_statement = 'UPDATE Auction SET viewings=:new WHERE auction_id = :auction';
        $stat = $db->prepare($update_statement);
        $stat->bindParam(":new", $new_view);
        $stat->bindParam(":auction", $data['auction_id']);
        $stat->execute();

    }
} else {
    header("Location: redirection.php");
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

    
    <link href="css/productpage.css" rel="stylesheet">

    
    <script src="js/jquery.js"></script>
    
    <script src="clockCode/countdown.js"></script>
</head>

<body>
<?php
include('nav.php');
?>

<div class="container-fluid2" style="padding-top:50px">
    <div class="content-wrapper" style="padding-top:50px">
        <div class="item-container">
            <div class="container-fluid">
                <div class="product col-md-3 service-image-left">
                    <img id="item-display"
                         src="<?php echo $item_data['item_picture']; ?>"
                         alt="">
                </div>
                <div class="product col-md-9">
                    <div class="product-title"><?php echo $item_data["label"]; ?></div>
                    <div class="product-category"><a href="profile.php?user=<?php echo $seller_data['user_id']; ?>"><?php echo $seller_data["username"]; ?></a></div>
                    <div class="product-category"><?php echo $category_data["category"]; ?></div>
                   
                    <hr>
                    <div class="product-price">Current Bid
                        <br>$ <?php echo $data['current_bid']; ?>
                    </div>
                    <p class="product-stock" id="timeRem"></p>

                    
                    <?php
                    if ($_SESSION['role_id'] == 1)
                    {
                    ?>

                    <div class="row">
                        <div class="col-sm-6" style="padding-left:20px;">
                            <form id="addBid" action="productpage.php?auct=<?php echo $data['auction_id']; ?>" method="post" role="form">
                                <input hidden name="user_id" value="<?php echo $_SESSION['user_id'] ?>"/>
                                <input hidden name="current_bid" value="<?php echo $data['current_bid']; ?>"/>
                                <input hidden name="auction_id" value="<?php echo $data['auction_id']; ?>"/>
                                <input hidden name="item_label" value="<?php echo $item_data['label']; ?>"/>
                                <input type="number" step="0.01" id="bidInput" min="0" name="new_bid"/>
                                <button id="submit" name="submit" class="btn btn-success">Submit Bid</button>
                               
                            </form>
                            <?php
                            if(!empty($message))
                            {
                                echo $message;
                            }
                            ?>
                        </div>
                        <div class="col-sm-6">
                            
                            

                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
               

            </div>
        </div>
    </div>
    <div class="container-fluid2">
        <div class="col-md-12 product-info">
            
            <div id="myTabContent" class="tab-content">
               
                <div class="tab-pane fade" id="service-two">

                    <section class="container">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="info">Start Price</td>
                                <td><?php echo $data["start_price"] . "$"; ?></td>
                            </tr>
                            <tr>
                                <td class="info">Reserve Price</td>
                                <td><?php echo $data["reserve_price"] . "$"; ?></td>
                            </tr>
                            <tr>
                                <td class="info">Start Time</td>
                                <td><?php echo $data["start_time"]; ?></td>
                            </tr>
                            <tr>
                                <td class="info">End Time</td>
                                <td><?php echo $data["end_time"]; ?></td>
                            </tr>
                            <tr>
                                <td class="info">Viewings</td>
                                <td><?php echo $data["viewings"]; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </section>

                </div>
                <div class="tab-pane fade" id="service-three">
                    <section class="container">
                        <?php
                        $bid_history = $db->prepare('SELECT Users.username, Users.user_id, Bids.bid_price FROM Users,Bids WHERE auction_id = :auct AND Bids.user_id = Users.user_id ORDER BY Bids.bid_price DESC LIMIT 10');
                        $bid_history->bindParam(':auct', $data["auction_id"]);
                        $bid_history->execute();

                        if ($bid_history->rowCount() == 0) {
                            echo "<p>You're the first to bid!</p>";
                        } else {
                            while ($res_bid = $bid_history->fetch()) {
                                ?>
                            <p><a href='profile.php?user=<?php echo $res_bid["user_id"]; ?>'><?php echo $res_bid["username"] . " " . $res_bid["bid_price"];?> </a> </p>
                            <?php
                            }
                            $bid_history->closeCursor();
                        }
                        ?>
                    </section>
                </div>
                <script>
                    setClock(<?php
                        $time = new DateTime($data['end_time']);
                        echo '"' . $time->format("Y-m-d\TH:i:s") . '"';
                        ?>, 'productpage.php', 'timeRem');
                </script>
            </div>
            <hr>
        </div>
    </div>

</div>
</div>
</body>

</html>