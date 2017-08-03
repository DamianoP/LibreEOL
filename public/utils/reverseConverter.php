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
<div>Insert the PHP text to be converted, then press the button corresponding to the code you want to achieve
<br/><br/><br/><br/>
<form action="reverseConverter.php" name="orderform" method="post">
    <input type="submit" name="Submit" id="submit" value="Convert" />
</div>
<table width="100%">
<tr>
<td>
    <textarea name="php" rows="100" cols="40"><?php
            if(isset($_POST["php"])){
                echo trim($_POST["php"]);
            }
        ?></textarea>

</td>
<td><textarea name="result" rows="100" cols="40"><?php
    if(isset($_POST["php"]) && isset($_POST["Submit"]) && (strcmp($_POST["Submit"],"Convert")==0)){
        $j=0;$i=0;
        for($i=0;$i<strlen($_POST["php"]);$i++){
            if($_POST["php"][$i]=="d"){
                if(isset($_POST["php"][$i+9]) && isset($_POST["php"][$i+8]) && (strcmp($_POST["php"][$i+7],"'")==0)){
                    if(
                        (strcmp($_POST["php"][$i+1],'e')==0) &&
                        (strcmp($_POST["php"][$i+2],'f')==0) &&
                        (strcmp($_POST["php"][$i+3],'i')==0) &&
                        (strcmp($_POST["php"][$i+4],'n')==0) &&
                        (strcmp($_POST["php"][$i+5],'e')==0) &&
                        (strcmp($_POST["php"][$i+6],'(')==0)
                    ){
                        echo '<text id="';
                       for($j=$i+8;isset($_POST["php"][$j]) && (strcmp($_POST["php"][$j],"'")!=0);$j++){
                            echo $_POST["php"][$j];

                       }
                        echo '">';
                        for(;isset($_POST["php"][$j]) &&(strcmp($_POST["php"][$j],'"')!=0);$j++) {

                        }
                        $j++;
                        for(;isset($_POST["php"][$j]) &&(strcmp($_POST["php"][$j],'"')!=0);$j++){
                            echo $_POST["php"][$j];
                        }
                        echo '</text>';
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