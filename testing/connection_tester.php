<html>
  <body style="background-color: black">
		<h1 style="color: white; font-size: 80px; vertical-align: middle;">
        <?php
        include_once("../KanojoX.php");
        $kanojo = new KanojoX();
        $kanojo->host = "10.0.0.3";
        $kanojo->user_name = "riviera";
        $kanojo->password = "r4cks";
        $conn = $kanojo->create_connection();
        if ($conn){
            $msg = "Connected to ORACLE";
            // Do some transactions to ORACLE
            oci_close();
        }
        else
            $msg = $kanojo->error;
        echo $msg;
        ?>
        </h1>
  </body>
</html>