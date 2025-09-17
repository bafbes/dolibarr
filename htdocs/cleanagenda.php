<?php


require __DIR__ . '/master.inc.php';

$res = $db->query("
select a.id
from llx_actioncomm as a
left join llx_actioncomm_extrafields as ex on ex.fk_object = a.id
where efficy_id is not null and HOUR(datep) = 0 and datep < now() and fk_soc is not null and a.percent >= 0
order by datep DESC
");
if (!$res) {
    exit();
}
$toDel = [];
$i = 0;
while ($obj = $db->fetch_object($res)) {
    $toDel[] = $obj->id;
    $i++;
    if ($i >= 500) {
        $idToDel = implode(',', $toDel);
        $db->query("DELETE FROM llx_actioncomm where id in ({$idToDel})");
        $i = 0;
        $toDel = [];
    }
}
