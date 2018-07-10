<html>
  <body style="background-color: black">
		<h1 style="color: white; font-size: 80px; vertical-align: middle;">
        <?php
        include_once "../src/ORACLEKanojoX.php";
        
        $kanojo = new ORACLEKanojoX();
        $kanojo->host = "172.25.39.186";
        $kanojo->user_name = "AYESA";
        $kanojo->password = "4y3542017";
        $kanojo->port = "1630";
        $kanojo->db_name = "DBDWHQA";
        $conn = $kanojo->connect();
        if ($conn) {
            $msg = "Connected to ORACLE";
            // Do some transactions to ORACLE
            oci_close($conn);
        } else
            $msg = $kanojo->error;
        echo $msg;
        ?>
        </h1>
  </body>
</html>