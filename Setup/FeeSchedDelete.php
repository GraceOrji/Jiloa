<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>

<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST['id'])) && ($_POST['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM fee WHERE id=%s",
                       GetSQLValueString($_GET['id'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($deleteSQL, $swmisconn) or die(mysql_error());

  $deleteGoTo = "FeeSchedule.php";
//  if (isset($_SERVER['QUERY_STRING'])) {
//    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
//    $deleteGoTo .= $_SERVER['QUERY_STRING'];
//  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$colname_feesched = "-1";
if (isset($_GET['id'])) {
  $colname_feesched = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_swmisconn, $swmisconn);
$query_feesched = sprintf("SELECT id, dept, `section`, name, unit, descr, fee, entryby, entrydt FROM fee WHERE id = %s", $colname_feesched);
$feesched = mysql_query($query_feesched, $swmisconn) or die(mysql_error());
$row_feesched = mysql_fetch_assoc($feesched);
$totalRows_feesched = mysql_num_rows($feesched);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Edit Fee Schedule</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
</head>

<body>


<table width="50%" align="center">
  <tr>
    <td><form id="form1" name="form1" method="post" action="">
      <table width="100%"  bgcolor="#FBD0D7">
        <tr>
          <td>&nbsp;</td>
          <td class="subtitlebk">Delete Fee Item </td>
        </tr>

        <tr>
          <td class="BlackBold_14"><div align="right">Department:</div></td>
          <td><input name="Dept" type="text" id="Dept" readlonly="readonly" value="<?php echo $row_feesched['dept']; ?>" /></td>
        </tr>
        <tr>
          <td class="BlackBold_14"><div align="right">Section:</div></td>
          <td><input name="section" type="text" id="section" readlonly="readonly" value="<?php echo $row_feesched['section']; ?>" /></td>
        </tr>
        <tr>
          <td class="BlackBold_14"><div align="right">Name:</div></td>
          <td><input name="name" type="text" id="name" readlonly="readonly" value="<?php echo $row_feesched['name']; ?>" /></td>
        </tr>
        <tr>
          <td class="BlackBold_14"><div align="right">Unit:</div></td>
          <td><input name="unit" type="text" id="unit" readlonly="readonly" value="<?php echo $row_feesched['unit']; ?>" /></td>
        </tr>
        <tr>
          <td class="BlackBold_14"><div align="right">Description:</div></td>
          <td><textarea name="descr" id="descr" readlonly="readonly"><?php echo $row_feesched['descr']; ?></textarea></td>
        </tr>
        <tr>
          <td class="BlackBold_14"><div align="right">Fee (Naira):</div></td>
          <td><input name="fee" type="text" id="fee" readlonly="readonly" value="<?php echo $row_feesched['fee']; ?>" /></td>
        </tr>
        <tr>
          <td><input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
            <input name="entrydt" type="hidden" id="entrydt" value="<?php echo date("Y-m-d H:i:s"); ?>" />
			<input name="id" type="hidden" value="<?php echo $row_feesched['id']; ?>" /></td>

         <td><p>
            <input type="submit" name="Submit" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="Delete Fee Item" />
          </p></td>
        </tr>
      </table>
        </form>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($feesched);

?>
