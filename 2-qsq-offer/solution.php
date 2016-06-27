<?php

#PSR-2 warning ignored

#输入数据生成器
$M = 5;
$N = 5;
$seats = [];
for ($i = 0; $i < $M; $i++) {
    $seats[$i] = [];
    for ($j = 0; $j < $N; $j++) {
        $seats[$i][$j] = mt_rand(0, 30);
    }
}

//*

#一组样例数据

$seats = [
    [  8,  2, 11, 24,  7],
    [ 23,  8, 24, 29, 29],
    [ 19, 29,  8,  4, 23],
    [ 17,  3, 23, 19, 19],
    [  9, 12, 19, 22, 17],
];
// */

#说明：
#
#从第一排任选一个座位，向后走的所有可能性，实际上就是展开一棵三叉树。
#
#树是一种典型的递归数据结构，可以很容易地用这个公式来得出通过这棵树可以获得的最大"积分"：
#
#    maxPoints($node) = $node->value + max(maxPoints($node->child[0]), maxPoints($node->child[1]), maxPoints($node->child[2]))
#
#    (递归的终止条件是到达了最后一行)
#
#第一排有N个座位，只要比较N棵三叉树的结果，取最大的即可。
#
#但是这棵树上节点的数量级是 3^M ,如果逐一计算的话，运算量太大，不可行（不信试试看 M = 30, N = 10 XD）
#
#通过观察可以发现，这棵树的节点绝大部分都是重叠的，所以可以缓存中间结果来避免重复计算。

class Solution
{
    protected $seats;
    protected $M;
    protected $N;
    protected $cache;

    public function __construct($seats, $M, $N)
    {
        $this->seats = $seats;
        $this->M = $M;
        $this->N = $N;
        $this->cache = [];
    }

    public function run()
    {
        $best = 0;
        for ($i = 1; $i < $this->N; $i++) {
            if ($this->maxPoints(0, $i) > $this->maxPoints(0, $best)) {
                $best = $i;
            }
        }
        printf("Best result: %d\n", $this->maxPoints(0, $best));
        for ($i = 0, $j = $best; $i < $this->M; $i++) {
            printf("choose seats[%d][%d] with %d\n", $i, $j, $this->seats[$i][$j]);
            $j = $this->cache[$i][$j]['next'];
        }
    }

    protected function maxPoints($i, $j)
    {
        if ($i == $this->M) {
            return 0;
        }
        #var_dump($i, $j); fgets(STDIN);
        if (!isset($this->cache[$i][$j])) {
            $options = [$j];
            if ($j > 0) {
                $options[] = $j - 1;
            }
            if ($j < $this->N - 1) {
                $options[] = $j + 1;
            }
            $next = -1;
            foreach ($options as $k) {
                if ($next < 0 or $this->maxPoints($i + 1, $k) > $this->maxPoints($i + 1, $next)) {
                    $next = $k;
                }
            }
            $this->cache[$i][$j] = [
                'max'   => $this->seats[$i][$j] + $this->maxPoints($i + 1, $next),
                'next'  => $next
            ];
        }
        return $this->cache[$i][$j]['max'];
    }
}

$s = new Solution($seats, $M, $N);
$s->run();

#注：树本身是个递归的数据结构，因此使用递归的形式来实现比较直观，有兴趣的小伙伴也可以试着用递推的方式来实现。
