<?php
/**
 * 로또 데이터베이스 설치 및 동기화 관리 페이지
 * 
 * 이 페이지에서:
 * 1. 테이블 생성
 * 2. 당첨번호 데이터 동기화
 * 3. 판매점/당첨점 데이터 동기화
 */

$sub_menu = '990100';
include_once('./_common.php');

// 관리자 권한 체크
if ($is_admin != 'super') {
    alert_close('최고관리자만 접근 가능합니다.');
}

// 라이브러리 로드
include_once(G5_LIB_PATH . '/lotto_draw.lib.php');
$store_lib = G5_LIB_PATH . '/lotto_store.lib.php';
$store_lib_loaded = file_exists($store_lib);
if ($store_lib_loaded) {
    include_once($store_lib);
}

$g5['title'] = '로또 DB 설치 및 동기화';
include_once('./admin.head.php');

// AJAX 처리
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    $result = ['success' => false, 'message' => '', 'data' => []];
    
    try {
        switch ($action) {
            case 'check_tables':
                // 테이블 존재 여부 확인
                $tables = [
                    'g5_lotto_draw' => false,
                    'g5_lotto_credit' => false,
                    'g5_lotto_credit_log' => false,
                    'g5_lotto_store' => false,
                    'g5_lotto_store_win' => false,
                    'g5_lotto_review' => false,
                    'g5_lotto_ai_recommend' => false,
                ];
                
                foreach ($tables as $table => &$exists) {
                    $res = sql_query("SHOW TABLES LIKE '{$table}'", false);
                    $exists = ($res && sql_num_rows($res) > 0);
                }
                
                $result['success'] = true;
                $result['data'] = $tables;
                break;
                
            case 'create_tables':
                // 모든 테이블 생성
                $sqls = [];
                
                // g5_lotto_draw
                $sqls[] = "CREATE TABLE IF NOT EXISTS `g5_lotto_draw` (
                    `draw_no` int(11) NOT NULL COMMENT '회차',
                    `draw_date` date NOT NULL COMMENT '추첨일',
                    `n1` tinyint(2) NOT NULL,
                    `n2` tinyint(2) NOT NULL,
                    `n3` tinyint(2) NOT NULL,
                    `n4` tinyint(2) NOT NULL,
                    `n5` tinyint(2) NOT NULL,
                    `n6` tinyint(2) NOT NULL,
                    `bonus` tinyint(2) NOT NULL COMMENT '보너스번호',
                    `total_sales` bigint(20) DEFAULT 0 COMMENT '총 판매금액',
                    `first_winners` int(11) DEFAULT 0 COMMENT '1등 당첨자수',
                    `first_prize_each` bigint(20) DEFAULT 0 COMMENT '1등 1인당 당첨금',
                    `first_prize_total` bigint(20) DEFAULT 0 COMMENT '1등 총 당첨금',
                    `second_winners` int(11) DEFAULT 0 COMMENT '2등 당첨자수',
                    `second_prize_each` bigint(20) DEFAULT 0 COMMENT '2등 1인당 당첨금',
                    `third_winners` int(11) DEFAULT 0 COMMENT '3등 당첨자수',
                    `third_prize_each` bigint(20) DEFAULT 0 COMMENT '3등 1인당 당첨금',
                    `fourth_winners` int(11) DEFAULT 0,
                    `fifth_winners` int(11) DEFAULT 0,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`draw_no`),
                    KEY `idx_date` (`draw_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='로또 회차별 당첨번호'";
                
                // g5_lotto_credit
                $sqls[] = "CREATE TABLE IF NOT EXISTS `g5_lotto_credit` (
                    `mb_id` varchar(20) NOT NULL,
                    `free_uses` int(11) DEFAULT 2 COMMENT '무료 분석 횟수',
                    `credit_balance` int(11) DEFAULT 0 COMMENT '유료 크레딧 잔액',
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`mb_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='회원별 크레딧'";
                
                // g5_lotto_credit_log
                $sqls[] = "CREATE TABLE IF NOT EXISTS `g5_lotto_credit_log` (
                    `log_id` int(11) NOT NULL AUTO_INCREMENT,
                    `mb_id` varchar(20) NOT NULL,
                    `log_type` enum('use','charge','bonus','refund') NOT NULL,
                    `amount` int(11) NOT NULL,
                    `balance_after` int(11) DEFAULT 0,
                    `description` varchar(200) DEFAULT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`log_id`),
                    KEY `idx_mb_id` (`mb_id`),
                    KEY `idx_created` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='크레딧 사용/충전 로그'";
                
                // g5_lotto_store
                $sqls[] = "CREATE TABLE IF NOT EXISTS `g5_lotto_store` (
                    `store_id` int(11) NOT NULL AUTO_INCREMENT,
                    `store_name` varchar(100) NOT NULL COMMENT '판매점명',
                    `address` varchar(255) NOT NULL COMMENT '주소',
                    `region1` varchar(20) DEFAULT NULL COMMENT '시/도',
                    `region2` varchar(50) DEFAULT NULL COMMENT '시/군/구',
                    `phone` varchar(20) DEFAULT NULL,
                    `lat` decimal(10,7) DEFAULT NULL COMMENT '위도',
                    `lng` decimal(10,7) DEFAULT NULL COMMENT '경도',
                    `wins_1st` int(11) DEFAULT 0 COMMENT '1등 당첨 횟수',
                    `wins_2nd` int(11) DEFAULT 0 COMMENT '2등 당첨 횟수',
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`store_id`),
                    KEY `idx_name` (`store_name`),
                    KEY `idx_region` (`region1`, `region2`),
                    KEY `idx_wins` (`wins_1st` DESC)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='로또 판매점'";
                
                // g5_lotto_store_win
                $sqls[] = "CREATE TABLE IF NOT EXISTS `g5_lotto_store_win` (
                    `win_id` int(11) NOT NULL AUTO_INCREMENT,
                    `draw_no` int(11) NOT NULL COMMENT '회차',
                    `store_id` int(11) NOT NULL COMMENT '판매점 ID',
                    `rank` tinyint(1) NOT NULL COMMENT '등수 (1, 2)',
                    `win_type` varchar(10) DEFAULT 'auto' COMMENT '자동/수동/반자동',
                    `prize_amount` bigint(20) DEFAULT 0,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`win_id`),
                    UNIQUE KEY `uk_draw_store_rank` (`draw_no`, `store_id`, `rank`),
                    KEY `idx_draw_no` (`draw_no`),
                    KEY `idx_store_id` (`store_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='당첨점 기록'";
                
                // g5_lotto_review
                $sqls[] = "CREATE TABLE IF NOT EXISTS `g5_lotto_review` (
                    `review_id` int(11) NOT NULL AUTO_INCREMENT,
                    `mb_id` varchar(20) NOT NULL,
                    `rating` tinyint(1) DEFAULT 5,
                    `content` text,
                    `is_visible` tinyint(1) DEFAULT 1,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`review_id`),
                    KEY `idx_mb_id` (`mb_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='사용자 리뷰'";
                
                // g5_lotto_ai_recommend
                $sqls[] = "CREATE TABLE IF NOT EXISTS `g5_lotto_ai_recommend` (
                    `rec_id` int(11) NOT NULL AUTO_INCREMENT,
                    `mb_id` varchar(20) NOT NULL,
                    `target_draw` int(11) NOT NULL COMMENT '대상 회차',
                    `numbers` varchar(50) NOT NULL COMMENT '추천번호 (1,2,3,4,5,6)',
                    `algorithm` varchar(50) DEFAULT NULL COMMENT '사용 알고리즘',
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`rec_id`),
                    KEY `idx_mb_id` (`mb_id`),
                    KEY `idx_draw` (`target_draw`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='AI 추천번호'";
                
                $created = 0;
                $errors = [];
                
                foreach ($sqls as $sql) {
                    $res = sql_query($sql, false);
                    if ($res) {
                        $created++;
                    } else {
                        $errors[] = sql_error();
                    }
                }
                
                $result['success'] = true;
                $result['message'] = "{$created}개 테이블 생성 완료";
                if (!empty($errors)) {
                    $result['message'] .= " (오류: " . count($errors) . "개)";
                }
                break;
                
            case 'sync_draws':
                // 당첨번호 동기화
                $start = (int)($_POST['start'] ?? 1);
                $end = (int)($_POST['end'] ?? 10);
                $synced = 0;
                $errors_list = [];
                
                for ($round = $start; $round <= $end; $round++) {
                    $error = '';
                    $data = li_get_lotto_api_json($round, $error);
                    
                    if ($data && isset($data['drwtNo1'])) {
                        $draw_no = (int)$data['drwNo'];
                        $draw_date = $data['drwNoDate'] ?? date('Y-m-d');
                        
                        $sql = "REPLACE INTO g5_lotto_draw SET 
                            draw_no = {$draw_no},
                            draw_date = '{$draw_date}',
                            n1 = {$data['drwtNo1']},
                            n2 = {$data['drwtNo2']},
                            n3 = {$data['drwtNo3']},
                            n4 = {$data['drwtNo4']},
                            n5 = {$data['drwtNo5']},
                            n6 = {$data['drwtNo6']},
                            bonus = {$data['bnusNo']},
                            total_sales = " . ((int)($data['totSellamnt'] ?? 0)) . ",
                            first_winners = " . ((int)($data['firstPrzwnerCo'] ?? 0)) . ",
                            first_prize_each = " . ((int)($data['firstWinamnt'] ?? 0)) . ",
                            first_prize_total = " . ((int)($data['firstAccumamnt'] ?? 0)) . "
                        ";
                        
                        if (sql_query($sql, false)) {
                            $synced++;
                        }
                    } else {
                        if ($error) $errors_list[] = "{$round}회: {$error}";
                    }
                    
                    // API 부하 방지
                    usleep(100000); // 0.1초
                }
                
                $result['success'] = true;
                $result['message'] = "{$synced}개 회차 동기화 완료";
                $result['data'] = ['synced' => $synced, 'errors' => $errors_list];
                break;
                
            case 'sync_stores':
                // 당첨점 동기화 (단일 회차)
                if (!$store_lib_loaded) {
                    throw new Exception('lotto_store.lib.php가 없습니다.');
                }
                
                $round = (int)($_POST['round'] ?? 0);
                if ($round < 1) {
                    throw new Exception('회차를 지정해주세요.');
                }
                
                $saved = li_sync_draw_winning_stores($round);
                
                $result['success'] = true;
                $result['message'] = "{$round}회: 1등 {$saved['first']}개, 2등 {$saved['second']}개 저장";
                $result['data'] = $saved;
                break;
                
            case 'get_stats':
                // 현재 DB 통계
                $stats = [];
                
                $row = sql_fetch("SELECT COUNT(*) AS cnt, MAX(draw_no) AS max_round FROM g5_lotto_draw");
                $stats['draws'] = (int)$row['cnt'];
                $stats['max_round'] = (int)$row['max_round'];
                
                $row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_store");
                $stats['stores'] = (int)($row['cnt'] ?? 0);
                
                $row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_store_win WHERE rank = 1");
                $stats['first_wins'] = (int)($row['cnt'] ?? 0);
                
                $row = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_credit");
                $stats['members'] = (int)($row['cnt'] ?? 0);
                
                $result['success'] = true;
                $result['data'] = $stats;
                break;
        }
    } catch (Exception $e) {
        $result['message'] = $e->getMessage();
    }
    
    echo json_encode($result);
    exit;
}

// 현재 통계
$draw_count = sql_fetch("SELECT COUNT(*) AS cnt, MAX(draw_no) AS max_round FROM g5_lotto_draw");
$store_count = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_store");
$win_count = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_lotto_store_win WHERE rank = 1");
?>

<style>
.setup-container { max-width: 1000px; margin: 20px auto; }
.setup-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; overflow: hidden; }
.setup-card-header { background: #343a40; color: #fff; padding: 15px 20px; font-weight: 600; }
.setup-card-body { padding: 20px; }
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px; }
.stat-item { background: #f8f9fa; border-radius: 8px; padding: 15px; text-align: center; }
.stat-value { font-size: 2rem; font-weight: 700; color: #007bff; }
.stat-label { color: #6c757d; font-size: 0.9rem; }
.table-status { margin-bottom: 20px; }
.table-status .badge { margin-right: 5px; padding: 5px 10px; }
.btn-action { margin: 5px; }
.log-area { background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 0.85rem; max-height: 300px; overflow-y: auto; }
.log-area .success { color: #4ec9b0; }
.log-area .error { color: #f14c4c; }
.log-area .info { color: #569cd6; }
.progress-container { margin: 15px 0; }
.sync-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 15px; }
.sync-form input { width: 100px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
.sync-form label { font-weight: 500; }
</style>

<div class="setup-container">
    <h2>🗄️ 로또 데이터베이스 설치 및 동기화</h2>
    <p class="text-muted">테이블 생성, 당첨번호 수집, 판매점 데이터 동기화를 수행합니다.</p>
    
    <!-- 현재 통계 -->
    <div class="setup-card">
        <div class="setup-card-header">📊 현재 DB 통계</div>
        <div class="setup-card-body">
            <div class="stat-grid">
                <div class="stat-item">
                    <div class="stat-value" id="stat-draws"><?= number_format($draw_count['cnt'] ?? 0) ?></div>
                    <div class="stat-label">회차 데이터</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="stat-max-round"><?= number_format($draw_count['max_round'] ?? 0) ?></div>
                    <div class="stat-label">최신 회차</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="stat-stores"><?= number_format($store_count['cnt'] ?? 0) ?></div>
                    <div class="stat-label">판매점</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="stat-wins"><?= number_format($win_count['cnt'] ?? 0) ?></div>
                    <div class="stat-label">1등 당첨 기록</div>
                </div>
            </div>
            <button class="btn btn-secondary" onclick="refreshStats()">🔄 통계 새로고침</button>
        </div>
    </div>
    
    <!-- 테이블 상태 -->
    <div class="setup-card">
        <div class="setup-card-header">📋 테이블 상태</div>
        <div class="setup-card-body">
            <div class="table-status" id="table-status">
                테이블 상태를 확인하세요...
            </div>
            <button class="btn btn-info btn-action" onclick="checkTables()">🔍 테이블 확인</button>
            <button class="btn btn-success btn-action" onclick="createTables()">➕ 테이블 생성</button>
        </div>
    </div>
    
    <!-- 당첨번호 동기화 -->
    <div class="setup-card">
        <div class="setup-card-header">🎱 당첨번호 동기화 (동행복권 API)</div>
        <div class="setup-card-body">
            <div class="sync-form">
                <label>시작 회차:</label>
                <input type="number" id="draw-start" value="1" min="1">
                <label>끝 회차:</label>
                <input type="number" id="draw-end" value="100" min="1">
                <button class="btn btn-primary" onclick="syncDraws()">🚀 동기화 시작</button>
                <button class="btn btn-outline-primary" onclick="syncDraws(true)">📥 전체 동기화 (1~최신)</button>
            </div>
            <div class="progress-container" id="draw-progress" style="display: none;">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                </div>
                <small class="text-muted" id="draw-progress-text">준비 중...</small>
            </div>
        </div>
    </div>
    
    <!-- 당첨점 동기화 -->
    <div class="setup-card">
        <div class="setup-card-header">🏪 당첨점 동기화 (판매점 데이터)</div>
        <div class="setup-card-body">
            <div class="sync-form">
                <label>시작 회차:</label>
                <input type="number" id="store-start" value="1" min="1">
                <label>끝 회차:</label>
                <input type="number" id="store-end" value="100" min="1">
                <button class="btn btn-warning" onclick="syncStores()">🏪 당첨점 동기화</button>
            </div>
            <p class="text-muted small">※ 당첨점 동기화는 시간이 오래 걸립니다. (회차당 약 1초)</p>
        </div>
    </div>
    
    <!-- 로그 -->
    <div class="setup-card">
        <div class="setup-card-header">📝 실행 로그</div>
        <div class="setup-card-body">
            <div class="log-area" id="log-area">
                <div class="info">[시작] 로또 DB 설치 관리자</div>
            </div>
            <button class="btn btn-secondary btn-sm mt-2" onclick="clearLog()">🗑️ 로그 지우기</button>
        </div>
    </div>
</div>

<script>
function log(message, type = 'info') {
    const area = document.getElementById('log-area');
    const time = new Date().toLocaleTimeString();
    area.innerHTML += `<div class="${type}">[${time}] ${message}</div>`;
    area.scrollTop = area.scrollHeight;
}

function clearLog() {
    document.getElementById('log-area').innerHTML = '<div class="info">[시작] 로그 초기화됨</div>';
}

async function api(action, data = {}) {
    const formData = new FormData();
    formData.append('action', action);
    for (const key in data) {
        formData.append(key, data[key]);
    }
    
    const response = await fetch('', { method: 'POST', body: formData });
    return await response.json();
}

async function checkTables() {
    log('테이블 상태 확인 중...', 'info');
    const result = await api('check_tables');
    
    if (result.success) {
        let html = '';
        for (const [table, exists] of Object.entries(result.data)) {
            const badge = exists 
                ? `<span class="badge badge-success">✓ 존재</span>` 
                : `<span class="badge badge-danger">✗ 없음</span>`;
            html += `${badge} ${table} &nbsp;&nbsp;`;
        }
        document.getElementById('table-status').innerHTML = html;
        log('테이블 상태 확인 완료', 'success');
    } else {
        log('테이블 확인 실패: ' + result.message, 'error');
    }
}

async function createTables() {
    if (!confirm('모든 테이블을 생성하시겠습니까?')) return;
    
    log('테이블 생성 중...', 'info');
    const result = await api('create_tables');
    
    if (result.success) {
        log(result.message, 'success');
        checkTables();
    } else {
        log('테이블 생성 실패: ' + result.message, 'error');
    }
}

async function refreshStats() {
    const result = await api('get_stats');
    if (result.success) {
        document.getElementById('stat-draws').textContent = result.data.draws.toLocaleString();
        document.getElementById('stat-max-round').textContent = result.data.max_round.toLocaleString();
        document.getElementById('stat-stores').textContent = result.data.stores.toLocaleString();
        document.getElementById('stat-wins').textContent = result.data.first_wins.toLocaleString();
        log('통계 새로고침 완료', 'success');
    }
}

async function syncDraws(fullSync = false) {
    let start = parseInt(document.getElementById('draw-start').value) || 1;
    let end = parseInt(document.getElementById('draw-end').value) || 100;
    
    if (fullSync) {
        start = 1;
        // 최신 회차 추정 (현재 날짜 기준)
        const now = new Date();
        const base = new Date('2002-12-07'); // 1회차 추첨일
        const weeks = Math.floor((now - base) / (7 * 24 * 60 * 60 * 1000));
        end = weeks + 1;
        
        if (!confirm(`1회부터 약 ${end}회까지 전체 동기화하시겠습니까?\n약 ${Math.ceil(end / 10)}분 소요됩니다.`)) return;
    }
    
    const progress = document.getElementById('draw-progress');
    const progressBar = progress.querySelector('.progress-bar');
    const progressText = document.getElementById('draw-progress-text');
    
    progress.style.display = 'block';
    log(`당첨번호 동기화 시작: ${start}회 ~ ${end}회`, 'info');
    
    const batchSize = 50; // 50개씩 배치
    let synced = 0;
    let errors = 0;
    
    for (let batchStart = start; batchStart <= end; batchStart += batchSize) {
        const batchEnd = Math.min(batchStart + batchSize - 1, end);
        
        progressText.textContent = `${batchStart}회 ~ ${batchEnd}회 동기화 중...`;
        const pct = ((batchStart - start) / (end - start + 1)) * 100;
        progressBar.style.width = pct + '%';
        
        const result = await api('sync_draws', { start: batchStart, end: batchEnd });
        
        if (result.success) {
            synced += result.data.synced;
            if (result.data.errors) errors += result.data.errors.length;
            log(`${batchStart}~${batchEnd}회: ${result.data.synced}개 완료`, 'success');
        } else {
            log(`${batchStart}~${batchEnd}회 오류: ${result.message}`, 'error');
        }
        
        // 잠시 대기
        await new Promise(r => setTimeout(r, 500));
    }
    
    progressBar.style.width = '100%';
    progressText.textContent = `완료! ${synced}개 동기화, ${errors}개 오류`;
    log(`당첨번호 동기화 완료: ${synced}개 성공, ${errors}개 오류`, 'success');
    refreshStats();
}

async function syncStores() {
    const start = parseInt(document.getElementById('store-start').value) || 1;
    const end = parseInt(document.getElementById('store-end').value) || 100;
    
    if (!confirm(`${start}회부터 ${end}회까지 당첨점을 동기화하시겠습니까?\n약 ${end - start + 1}초 이상 소요됩니다.`)) return;
    
    log(`당첨점 동기화 시작: ${start}회 ~ ${end}회`, 'info');
    
    let synced = 0;
    let total1st = 0;
    let total2nd = 0;
    
    for (let round = start; round <= end; round++) {
        const result = await api('sync_stores', { round: round });
        
        if (result.success) {
            synced++;
            total1st += result.data.first || 0;
            total2nd += result.data.second || 0;
            log(`${round}회: 1등 ${result.data.first}개, 2등 ${result.data.second}개`, 'success');
        } else {
            log(`${round}회 오류: ${result.message}`, 'error');
        }
        
        // API 부하 방지
        await new Promise(r => setTimeout(r, 600));
    }
    
    log(`당첨점 동기화 완료: ${synced}회차, 1등 ${total1st}개, 2등 ${total2nd}개`, 'success');
    refreshStats();
}

// 초기화
checkTables();
</script>

<?php
include_once('./admin.tail.php');
