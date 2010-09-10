<html>

<head><title>
<?php

$var="db";
$val = isset($_GET[$var]) ? "$_GET[$var]" : "No db selected";

$var="sortby";
$sortby = isset($_GET[$var]) ? $_GET[$var] : "keys.key";
$validkeys = array('multikey','added_by', 'added_at', 'fact', 'totalcount');
if (! in_array($sortby, $validkeys)){
    $sortby = "multikey";
}

$var="sortorder";
$sortorder = isset($_GET[$var]) ? $_GET[$var] : "ASC";
$validorders = array("ASC","DESC");
if (! in_array($sortorder, $validorders)){
    $sortorder = "ASC";
}



echo "Factoids display for: $val";

?>
</title>

<style>
<!--
  table.factoiddisplay { border: 1px solid gray; border-collapse: collapse; 
    margin-left: 50px; margin-right: 50px; }
  p.keylist {margin-left: 50px; margin-right: 50px; }
  .factoiddisplay td { border: 1px solid gray; padding: 10px; }
  .factoiddisplay th { border: 1px solid gray; padding: 10px; background-color: #d3d7cf; }
  tr.even { background-color: #dbdfff; }
  h2 { text-align: center; }
-->
</style>

</head>

<body>

<h2>
<?php

if (preg_match("/^[a-zA-Z\._\-#]*$/", $val)){
    echo "Factoids display for: $val";
}
else {
    echo "Invalid characters in db name";
    $val = "No db selected";
}

?>
</h2>

<p>[<a href="/">home</a>]</p>

<h3>Brief key list</h3>

<p class="keylist">
<?php

// factoids db format:
// table keys: id=int, key=text, locked=bool
// table factoids: id=int, key_id=int, added_by=text, added_at=timestamp, fact=text

if ($val !== "No db selected" && $db = new PDO('sqlite:' . './factoids/' . $val)) {
    $query = $db->Query('SELECT key FROM keys ORDER BY key ASC');
    if ($query == false) {
        echo "No factoids found" . "\n";
    }
    else {
        while ($entry = $query->fetch(PDO::FETCH_BOTH)) {
            $keycontent = preg_replace('/>/', '&gt;', preg_replace('/</', '&lt;', $entry['key']));
            echo '<a href="#' . $keycontent . '">' . $keycontent . '</a>  ';
        }
    }
} else {
    die($err);
}

?>
</p>

<h3>Detailed key info</h3>

<table class="factoiddisplay">
<tr>
<th>#</th>

<?php
$sortorders = array('multikey' => 'ASC', 'added_by' => 'ASC', 'added_at' => 'ASC', 'fact' => 'ASC', 'totalcount' => 'ASC');
if ($sortorder == 'ASC') {
  $sortorders[$sortby] = 'DESC';
}
echo '  <th><a href="viewfactoids.php?db=' . urlencode($val) . '&sortby=multikey&sortorder=' . $sortorders['multikey'] . '">key</a></th>';
echo '  <th><a href="viewfactoids.php?db=' . urlencode($val) . '&sortby=fact&sortorder=' . $sortorders['fact'] . '">fact</a></th>';
echo '  <th><a href="viewfactoids.php?db=' . urlencode($val) . '&sortby=totalcount&sortorder=' . $sortorders['totalcount'] . '">usage count</a></th>';
echo '  <th><a href="viewfactoids.php?db=' . urlencode($val) . '&sortby=added_by&sortorder=' . $sortorders['added_by'] . '">added by</a></th>';
echo '  <th><a href="viewfactoids.php?db=' . urlencode($val) . '&sortby=added_at&sortorder=' . $sortorders['added_at'] . '">added at</a></th>';
?>
</tr>

<?php

function group_concat_step(&$context, $rownumber, $string) 
{
    if (is_null($context)){
        $context = $string;
    }
    else {
        $context = $context . ',' . $string;
    }
    return $context;
}

function group_concat_finalize(&$context, $rownumber) 
{
    return $context;
}


// factoids db format: 
// table keys: id=int, key=text, locked=bool
// table factoids: id=int, key_id=int, added_by=text, added_at=timestamp, fact=text

if ($val !== "No db selected" && $db = new PDO('sqlite:' . './factoids/' . $val)) {
    $db->sqliteCreateAggregate('group_concat', 'group_concat_step', 'group_concat_finalize');
    $query = $db->Query('SELECT group_concat(keys.key) as multikey, group_concat(relations.usage_count) as multicount, sum(relations.usage_count) as totalcount, factoids.added_by as added_by, factoids.added_at as added_at, factoids.fact as fact FROM factoids, keys, relations WHERE relations.key_id=keys.id and relations.fact_id=factoids.id GROUP BY factoids.id ORDER BY ' . $sortby . ' ' . $sortorder );
    if ($query == false) {
        echo "<tr><td>No factoids found</td></tr>" . "\n";
    } 
    else {
        $color = 1;
        //$resultrow = 0;
        //$results = $query->fetchAll(PDO::FETCH_BOTH);
        while ($entry = $query->fetch(PDO::FETCH_BOTH)) {
            if ($color % 2 == 1){
                echo '<tr class="odd">' . "\n"; 
            }
            else {
                echo '<tr class="even">' . "\n";
            }
            echo '  <td>' . $color . '</td>' . "\n";
            $color = $color + 1;
            $keycontent = preg_replace('/>/', '&gt;', preg_replace('/</', '&lt;', $entry['multikey']));
            $keycontent = split(",", $keycontent);
            $countcontent = split(",", $entry['multicount']);
            echo '  <td>' . "\n" . '<ul>';
            for ($i = 0; $i < count($keycontent); $i++) {
                echo '<li><a name="' . $keycontent[$i] . '">' . $keycontent[$i] . '</a> (' . $countcontent[$i] . ') </li>' . "\n";
            }
            echo '</ul>' . '  </td>' . "\n";
            echo '  <td>' . preg_replace('/(https?:[^\s]+)/', '<a href="$1">$1</a>', preg_replace('/>/', '&gt;', preg_replace('/</', '&lt;', $entry['fact']))) . '</td>' . "\n";
            echo '  <td>' . $entry['totalcount'] . '</td>' . "\n";
            echo '  <td>' . preg_replace('/>/', '&gt;', preg_replace('/</', '&lt;', $entry['added_by'])) . '</td>' . "\n"; 
            echo '  <td>' . gmdate('Y-m-d|H:i:s|e', $entry['added_at']) . '</td>' . "\n";
            echo '</tr>' . "\n";
        }
    }
} else {
    die($err);
}

?>
</table>

<p>[<a href="/">home</a>]</p>

</body>
</html>
