<?php

/*

(1)

#求两条线路的一次换乘站点
select distinct(stop) from subway
    where stop in (select stop from subway where line = 2)
      and stop in (select stop from subway where line = 9);

#求两个站点的一次换乘站点
select distinct(stop) from subway
    where stop in 
        (select stop from subway where line in
            (select line from subway where stop = '淞虹路'))
      and stop in
        (select stop from subway where line in
            (select line from subway where stop = '漕河泾开发区'));

 */

/*
 * (2) 先导出为 subway.csv
 */

$lines = []; # [$stop, $sequence];

#读取所有线路的站点
$fsubway = fopen("subway.csv", "r");
while ($row = fgetcsv($fsubway)) {
    list($line, $stop, $sequence) = $row;
    if (!array_key_exists($line, $lines)) {
        $lines[$line] = [];
    }
    $lines[$line][] = ['name' => $stop, 'sequence' => $sequence];
}

$g = []; #short for graph, 用[邻接表]表示的图

foreach ($lines as $line => $stops) {
    #按线路排序
    usort($stops, function ($stop1, $stop2) {
        return $stop1['sequence'] - $stop2['sequence'];
    });
    #记录每个站点的相邻站点
    foreach ($stops as $idx => $stop) {
        $stop_name = $stop['name'];
        if (!array_key_exists($stop_name, $g)) {
            $g[$stop_name] = ['visited' => false, 'next' => [], "path_prev" => null];
        }
        $prev = $idx - 1;
        if ($prev >= 0) {
            $prev_stop = $stops[$prev];
            $g[$stop_name]['next'][$prev_stop['name']] = 1;
        }
        $next = $idx + 1;
        if ($next < count($stops)) {
            $next_stop = $stops[$next];
            $g[$stop_name]['next'][$next_stop['name']] = 1;
        }
    }
}

$src  = '淞虹路';
$dest = '漕河泾开发区';

#使用BFS（广度优先搜索）算法寻路

$queue = new SplQueue();
$queue->enqueue($dest);
$found = false;
while (!$found and !$queue->isEmpty()) {
    $current_stop = $queue->dequeue();
    $g[$current_stop]['visited'] = true;
    foreach ($g[$current_stop]['next'] as $next_stop => $ignored_value) {
        if ($g[$next_stop]['visited']) {
            continue;
        }
        $g[$next_stop]['path_prev'] = $current_stop;
        if ($next_stop == $src) {
            $found = true;
            break;
        }
        $queue->enqueue($next_stop);
    }
}

if ($found) {
    echo "Path found:\n";
    for ($stop = $src; $stop != null; $stop = $g[$stop]['path_prev']) {
        echo $stop, " => ";
    }
    echo "[到达]\n";
} else {
    echo "Path not found\n";
}
