<?php

//*
#example
$M = 5;
$N = 5;
$seats = [
    [  8,  2, 11, 24,  7],
    [ 23,  8, 24, 29, 29],
    [ 19, 29,  8,  4, 23],
    [ 17,  3, 23, 19, 19],
    [  9, 12, 19, 22, 17],
];
// */

$ans = [];
for ($i = 0; $i < $M; $i += 1) {
    $anwser[$i] = [];
    for ($j = 0; $j < $N; $j += 1) {
        $ans[$i][$j] = [
            'value' => $seats[$i][$j],
            'max'   => 0,   #从这个位置开始往后走最大能获得的积分
            'prev'  => -1,  #从上一排的这个座位走过来
        ];
    }
}

#走到第一排座位 j 能得到的最大积分就是该座位的积分
for ($j = 0; $j < $N; $j += 1) {
    $ans[0][$j]['max'] = $ans[0][$j]['value'];
}

#走到第 i 排第 j 个座位，能得到的最大积分为：
#  ans[$i][$j]['max'] = ans[$i][$j]['value'] + max($ans[$i-1][$j-1 OR $j OR $j+1]['max'])
for ($i = 1; $i < $N; $i += 1) {
    for ($j = 0; $j < $N; $j += 1) {
        $options = [$j];
        if ($j > 0) {
            $options[] = $j - 1;
        }
        if ($j < $N - 1) {
            $options[] = $j + 1;
        }
        $prev = -1;
        foreach ($options as $t) {
            if ($prev < 0 or $ans[$i - 1][$t]['max'] > $ans[$i - 1][$prev]['max']) {
                $prev = $t;
            }
        }
        $ans[$i][$j]['prev'] = $prev;
        $ans[$i][$j]['max']  = $ans[$i][$j]['value'] + $ans[$i - 1][$prev]['max'];
    }
}

#debug: 查看计算结果
for ($i = 0; $i < $M; $i += 1) {
    for ($j = 0; $j < $N; $j += 1) {
        $seat = $ans[$i][$j];
        printf("[%3d, %3d, %3d], ", $seat['value'], $seat['max'], $seat['prev']);
    }
    echo "\n";
}

#最后一排max最大的就是最佳出口
for ($j = 0, $best = 0; $j < $N; $j += 1) {
    if ($ans[$N - 1][$j]['max'] > $ans[$N - 1][$best]['max']) {
        $best = $j;
    }
}

#输出路径
$stack = new SplStack();
printf("best: %d\n", $ans[$N - 1][$best]['max']);
for ($i = $M - 1, $j = $best; $i >= 0; $i -= 1) {
    $output = sprintf("choose [%d][%d] with %d\n", $i, $j, $ans[$i][$j]['value']);
    $stack->push($output);
    $j = $ans[$i][$j]['prev'];
}
while (!$stack->isEmpty()) {
    echo $stack->pop();
}
