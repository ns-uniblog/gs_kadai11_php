<?php
// タイムゾーンを設定
date_default_timezone_set('Asia/Tokyo');

// 前月・次月リンクが押された場合は、GETパラメーターから年月を取得
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    // 今月の年月を表示
    $ym = date('Y-m');
}

// タイムスタンプを作成し、フォーマットをチェックする
$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

// 今日の日付
$today = date('Y-m-j');

// カレンダーのタイトルを作成
$html_title = date('Y年n月', $timestamp);

// 前月・次月の年月を取得
$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));

// 該当月の日数を取得
$day_count = date('t', $timestamp);

// 1日が何曜日か
$youbi = date('w', $timestamp);

// DB接続
include("functions.php");
$pdo= db_connect();

// メモをデータベースから取得
$notes = [];
$sql = "SELECT * FROM uniblog_gs_kadai08_db2 WHERE date BETWEEN :start_date AND :end_date";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':start_date' => $ym . '-01',
    ':end_date' => $ym . '-' . $day_count
]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $notes[$row['date']] = $row['note'];
}

// カレンダー作成の準備
$weeks = [];
$week = '';

// 第1週目：空のセルを追加
$week .= str_repeat('<td></td>', $youbi);

for ($day = 1; $day <= $day_count; $day++, $youbi++) {
    $date = $ym . '-' . str_pad($day, 2, '0', STR_PAD_LEFT); // 日付をゼロ埋め

    if ($today == $date) {
        // 今日の日付の場合は、class="today"をつける
        $week .= '<td class="today">' . $day;
    } else {
        $week .= '<td>' . $day;
    }

    // メモがある場合、表示する
    if (isset($notes[$date])) {
        $week .= '<div>' . htmlspecialchars($notes[$date]) . '</div>';
    }

    $week .= '</td>';

    // 週終わり、または、月終わりの場合
    if ($youbi % 7 == 6 || $day == $day_count) {
        if ($day == $day_count) {
            $week .= str_repeat('<td></td>', 6 - $youbi % 7);
        }
        $weeks[] = '<tr>' . $week . '</tr>';
        $week = '';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>PHPカレンダー</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div>
        <h3>
            <a href="?ym=<?= $prev ?>">&lt;　</a> <!-- &lt; -->
            <span><?= $html_title ?></span>
            <a href="?ym=<?= $next ?>">　&gt;</a><!-- &gt; -->
        </h3>
     <button onclick="location.href='index.php'">今月に戻る</button> <br>
        <table>
            <tr>
                <th>日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
            </tr>
            <?php foreach ($weeks as $week): ?>
                <?= $week; ?>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
