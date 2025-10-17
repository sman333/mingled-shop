<?
if (!in_array ($_GET["page"]??"Home", ["Home", "profile"]))
    if (!($usr[$FormFields["permissions"][$_GET["page"]??""]??""]??0))
        exit ("<script> alert (`Access denied! Contact System Administrator for this permission.`); location=`$PATH"."Admin/Login`; </script>");
?>