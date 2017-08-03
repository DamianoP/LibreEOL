<html>
<head>
    <style>textarea {
            border: 1px solid #999999;
            width: 100%;
            margin: 5px 0;
            padding: 3px;
        }
    </style>
</head>
<div>Enter the text to be converted, then press the button corresponding to the code you want to achieve
<br/><br/><br/><br/>
<form action="converter.php" name="orderform" method="post">
    <input type="submit" name="Submit" id="submit" value="JavaScript" />
    <input type="submit" name="Submit" id="submit" value="PHP" />
</div>
<table width="100%">
<tr>
<td>
    <textarea name="xml" rows="100" cols="40"><?php
            if(isset($_POST["xml"])){
                echo trim($_POST["xml"]);
            }
        ?></textarea>

</td>
<td><textarea name="result" rows="100" cols="40"><?php
    if(isset($_POST["xml"]) && isset($_POST["Submit"]) && (strcmp($_POST["Submit"],"JavaScript")==0)){
        $j=0;$i=0;
        for($i=0;$i<strlen($_POST["xml"]);$i++){
            if($_POST["xml"][$i]=="<"){
                if(isset($_POST["xml"][$i+9]) && isset($_POST["xml"][$i+10]) && (strcmp($_POST["xml"][$i+9],'"')==0)){
                    if(
                        (strcmp($_POST["xml"][$i+1],'t')==0) &&
                        (strcmp($_POST["xml"][$i+2],'e')==0) &&
                        (strcmp($_POST["xml"][$i+3],'x')==0) &&
                        (strcmp($_POST["xml"][$i+4],'t')==0)
                    ){
                        echo "var ";
                       for($j=$i+10;isset($_POST["xml"][$j]) && (strcmp($_POST["xml"][$j],'"')!=0);$j++){
                            echo $_POST["xml"][$j];

                       }
                        $j++;$j++;
                        echo '="';
                        for(;isset($_POST["xml"][$j]) &&(strcmp($_POST["xml"][$j],'<')!=0);$j++){
                            echo $_POST["xml"][$j];
                        }
                        echo '";';
                        echo "\r\n";
                    }
                }
            }
        }
    }
    if(isset($_POST["xml"]) && isset($_POST["Submit"]) && (strcmp($_POST["Submit"],"PHP")==0)){
        $j=0;$i=0;
        echo "<?php\r\n";
        for($i=0;$i<strlen($_POST["xml"]);$i++){
            if($_POST["xml"][$i]=="<"){
                if(isset($_POST["xml"][$i+9]) && isset($_POST["xml"][$i+10]) && (strcmp($_POST["xml"][$i+9],'"')==0)){
                    if(
                        (strcmp($_POST["xml"][$i+1],'t')==0) &&
                        (strcmp($_POST["xml"][$i+2],'e')==0) &&
                        (strcmp($_POST["xml"][$i+3],'x')==0) &&
                        (strcmp($_POST["xml"][$i+4],'t')==0)
                    ){
                        echo "define('";
                        for($j=$i+10;isset($_POST["xml"][$j]) && (strcmp($_POST["xml"][$j],'"')!=0);$j++){
                            echo $_POST["xml"][$j];

                        }
                        $j++;$j++;
                        echo "' ,";
                        echo '"';
                        for(;isset($_POST["xml"][$j]) &&(strcmp($_POST["xml"][$j],'<')!=0);$j++){
                            echo $_POST["xml"][$j];
                        }
                        echo '");';
                        echo "\r\n";
                    }
                }
            }
        }
    }
?></textarea>
</td>
</tr>

</table>
</form>
</html>