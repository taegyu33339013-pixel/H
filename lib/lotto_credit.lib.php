<?php
// /lib/lotto_credit.lib.php
if (!defined('_GNUBOARD_')) exit;

if (!isset($g5['lotto_credit_table'])) {
    // 필요하면 common.php 등에서 $g5['lotto_credit_table']를 정의해도 됩니다.
    $g5['lotto_credit_table'] = G5_TABLE_PREFIX . 'lotto_credit';
}

if (!isset($g5['lotto_credit_log_table'])) {
    $g5['lotto_credit_log_table'] = G5_TABLE_PREFIX . 'lotto_credit_log';
}

/**
 * 회원 크레딧 행 조회 (없으면 옵션에 따라 생성)
 *
 * @param string $mb_id
 * @param bool   $create_if_not_exists  true 이면 기본값으로 행 생성
 *
 * @return array
 */
function lotto_get_credit_row($mb_id, $create_if_not_exists = false)
{
    global $g5;

    $mb_id = trim($mb_id);
    if ($mb_id === '') {
        return [
            'mb_id'          => '',
            'free_uses'      => 0,
            'credit_balance' => 0,
        ];
    }

    $tbl = $g5['lotto_credit_table'];
    $mb_id_esc = sql_real_escape_string($mb_id);

    $row = sql_fetch("SELECT * FROM {$tbl} WHERE mb_id = '{$mb_id_esc}'");

    if ($row && isset($row['mb_id'])) {
        // 필드 기본값 보정
        $row['free_uses']      = (int)($row['free_uses'] ?? 0);
        $row['credit_balance'] = (int)($row['credit_balance'] ?? 0);
        return $row;
    }

    if (!$create_if_not_exists) {
        return [
            'mb_id'          => $mb_id,
            'free_uses'      => 0,
            'credit_balance' => 0,
        ];
    }

    // 없으면 기본값으로 생성 (무료 크레딧 없음 - 회원가입 시에만 지급)
    $now = G5_TIME_YMDHIS;
    $sql = "
        INSERT INTO {$tbl}
        SET mb_id          = '{$mb_id_esc}',
            free_uses      = 0,
            credit_balance = 0,
            created_at     = '{$now}',
            updated_at     = '{$now}'
    ";
    sql_query($sql, false);

    $row = sql_fetch("SELECT * FROM {$tbl} WHERE mb_id = '{$mb_id_esc}'");
    if ($row) {
        $row['free_uses']      = (int)($row['free_uses'] ?? 0);
        $row['credit_balance'] = (int)($row['credit_balance'] ?? 0);
        return $row;
    }

    return [
        'mb_id'          => $mb_id,
        'free_uses'      => 0,
        'credit_balance' => 0,
    ];
}

/**
 * 분석 1회 사용 (무료 → 없으면 유료 크레딧에서 차감)
 *
 * @param string $mb_id
 * @param string $memo     로그 메모
 * @param string $ref_key  참조키(회차 등)
 *
 * @return array
 *  - success        bool
 *  - reason         string (실패시)
 *  - used_as        'free'|'paid'
 *  - free_uses      int (남은 무료)
 *  - credit_balance int (남은 유료)
 *  - credit         int (하위호환용 = credit_balance)
 *  - free           int (하위호환용 = free_uses)
 */
function lotto_use_one_analysis($mb_id, $memo = '', $ref_key = '')
{
    global $g5;

    $mb_id = trim($mb_id);
    if ($mb_id === '') {
        return [
            'success'        => false,
            'reason'         => 'INVALID_MEMBER',
            'used_as'        => '',
            'free_uses'      => 0,
            'credit_balance' => 0,
            'free'           => 0,
            'credit'         => 0,
        ];
    }

    $tbl      = $g5['lotto_credit_table'];
    $log_tbl  = $g5['lotto_credit_log_table'];
    $mb_id_esc = sql_real_escape_string($mb_id);

    // 행이 없으면 기본값 생성
    $row  = lotto_get_credit_row($mb_id, true);
    $free = (int)$row['free_uses'];
    $paid = (int)$row['credit_balance'];

    // 무료/유료 둘 다 0이면 실패
    if ($free <= 0 && $paid <= 0) {
        return [
            'success'        => false,
            'reason'         => 'NO_CREDIT',
            'used_as'        => '',
            'free_uses'      => $free,
            'credit_balance' => $paid,
            'free'           => $free,
            'credit'         => $paid,
        ];
    }

    // 어떤 크레딧을 사용할지 결정
    $used_as = 'free';
    if ($free > 0) {
        $free--;
    } else {
        $used_as = 'paid';
        $paid--;
    }

    $now      = G5_TIME_YMDHIS;
    $memo_esc = sql_real_escape_string($memo);
    $ref_esc  = sql_real_escape_string($ref_key);
    $ip_esc   = sql_real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');

    // 트랜잭션 (InnoDB 기준)
    sql_query("BEGIN", false);

    // 1) 메인 테이블 잔액 업데이트
    $update_sql = "
        UPDATE {$tbl}
        SET free_uses      = {$free},
            credit_balance = {$paid},
            updated_at     = '{$now}'
        WHERE mb_id = '{$mb_id_esc}'
    ";
    $ok1 = sql_query($update_sql, false);

    // 2) 로그 테이블 INSERT  (<<<< 여기만 log 스키마에 맞게 변경)
    // change_type : free(무료 사용) / use(유료 사용)
    $change_type    = ($used_as === 'free') ? 'free' : 'use';
    $amount         = -1;
    $before_balance = ($used_as === 'free')
        ? (int)$row['free_uses']
        : (int)$row['credit_balance'];
    $after_balance  = ($used_as === 'free') ? $free : $paid;

    $log_sql = "
        INSERT INTO {$log_tbl}
        SET mb_id          = '{$mb_id_esc}',
            change_type    = '{$change_type}',
            amount         = {$amount},
            before_balance = {$before_balance},
            after_balance  = {$after_balance},
            memo           = '{$memo_esc}',
            ref_key        = '{$ref_esc}',
            ip             = '{$ip_esc}',
            created_at     = '{$now}'
    ";
    $ok2 = sql_query($log_sql, false);

    // 트랜잭션 커밋/롤백
    if ($ok1 && $ok2) {
        sql_query("COMMIT", false);
    } else {
        sql_query("ROLLBACK", false);
        return [
            'success'        => false,
            'reason'         => 'DB_ERROR',
            'used_as'        => '',
            'free_uses'      => (int)$row['free_uses'],
            'credit_balance' => (int)$row['credit_balance'],
            'free'           => (int)$row['free_uses'],
            'credit'         => (int)$row['credit_balance'],
        ];
    }

    // 성공 리턴
    return [
        'success'        => true,
        'reason'         => '',
        'used_as'        => $used_as,   // 'free' or 'paid'
        'free_uses'      => $free,
        'credit_balance' => $paid,
        'free'           => $free,      // JS에서 쓰는 하위호환 키
        'credit'         => $paid,
    ];
}

/**
 * 유료 크레딧 충전 (무통장/PG 공용)
 *
 * @param string $mb_id     회원 아이디
 * @param int    $amount    충전 크레딧 수 (예: 10 = 10회)
 * @param string $memo      관리자/로그 메모
 * @param string $ref_key   주문번호 등 참조키
 * @param string $change_type 'charge' | 'admin_adjust'
 *
 * @return array
 *  - success        bool
 *  - reason         string
 *  - credit_balance int (잔여 유료)
 *  - free_uses      int (잔여 무료)
 */
function lotto_charge_credit($mb_id, $amount, $memo = '', $ref_key = '', $change_type = 'charge')
{
    global $g5;

    $mb_id  = trim($mb_id);
    $amount = (int)$amount;

    if ($mb_id === '' || $amount <= 0) {
        return [
            'success'        => false,
            'reason'         => 'INVALID_PARAM',
            'credit_balance' => 0,
            'free_uses'      => 0,
        ];
    }

    $tbl      = $g5['lotto_credit_table'];
    $log_tbl  = $g5['lotto_credit_log_table'];
    $mb_id_esc = sql_real_escape_string($mb_id);

    // 현재 크레딧 행 (없으면 생성)
    $row = lotto_get_credit_row($mb_id, true);
    $before_paid = (int)$row['credit_balance'];
    $after_paid  = $before_paid + $amount;
    $free_uses   = (int)$row['free_uses'];

    $now       = G5_TIME_YMDHIS;
    $memo_esc  = sql_real_escape_string($memo);
    $ref_esc   = sql_real_escape_string($ref_key);
    $ip_esc    = sql_real_escape_string($_SERVER['REMOTE_ADDR'] ?? '');
    $ct_esc    = sql_real_escape_string($change_type); // 'charge' 또는 'admin_adjust'

    sql_query("BEGIN", false);

    // 1) 메인 크레딧 업데이트
    $up_sql = "
        UPDATE {$tbl}
        SET credit_balance = {$after_paid},
            updated_at     = '{$now}'
        WHERE mb_id = '{$mb_id_esc}'
    ";
    $ok1 = sql_query($up_sql, false);

    // 2) 로그 기록 (유료 크레딧 기준 로그)
    $log_sql = "
        INSERT INTO {$log_tbl}
        SET mb_id          = '{$mb_id_esc}',
            change_type    = '{$ct_esc}',   -- 'charge' / 'admin_adjust'
            amount         = {$amount},     -- 플러스
            before_balance = {$before_paid},
            after_balance  = {$after_paid},
            memo           = '{$memo_esc}',
            ref_key        = '{$ref_esc}',
            ip             = '{$ip_esc}',
            created_at     = '{$now}'
    ";
    $ok2 = sql_query($log_sql, false);

    if ($ok1 && $ok2) {
        sql_query("COMMIT", false);
        return [
            'success'        => true,
            'reason'         => '',
            'credit_balance' => $after_paid,
            'free_uses'      => $free_uses,
        ];
    }

    sql_query("ROLLBACK", false);
    return [
        'success'        => false,
        'reason'         => 'DB_ERROR',
        'credit_balance' => $before_paid,
        'free_uses'      => $free_uses,
    ];
}

/**
 * 신규 회원 가입 시 무료 크레딧 지급 (1회만)
 * 
 * @param string $mb_id 회원 아이디
 * @param string $memo  메모 (기본: '신규 회원 가입 축하')
 * 
 * @return array
 *  - success        bool
 *  - reason         string (실패시)
 *  - free_uses      int (지급 후 무료 크레딧)
 *  - credit_balance int (유료 크레딧)
 */
function lotto_grant_welcome_credit($mb_id, $memo = '신규 회원 가입 축하')
{
    global $g5;

    $mb_id = trim($mb_id);
    if ($mb_id === '') {
        return [
            'success'        => false,
            'reason'         => 'INVALID_MEMBER',
            'free_uses'      => 0,
            'credit_balance' => 0,
        ];
    }

    $tbl = $g5['lotto_credit_table'];
    $log_tbl = $g5['lotto_credit_log_table'];
    $mb_id_esc = sql_real_escape_string($mb_id);

    // 이미 크레딧이 있는지 확인
    $existing = sql_fetch("SELECT * FROM {$tbl} WHERE mb_id = '{$mb_id_esc}'");
    
    if ($existing && isset($existing['mb_id'])) {
        // 이미 크레딧이 있으면 중복 지급 방지
        // 단, 무료 크레딧이 0이고 로그에 welcome 기록이 없으면 지급
        $existing_free = (int)($existing['free_uses'] ?? 0);
        
        if ($existing_free > 0) {
            return [
                'success'        => false,
                'reason'         => 'ALREADY_GRANTED',
                'free_uses'      => $existing_free,
                'credit_balance' => (int)($existing['credit_balance'] ?? 0),
            ];
        }
        
        // welcome 로그 확인
        $welcome_log = sql_fetch("
            SELECT id FROM {$log_tbl} 
            WHERE mb_id = '{$mb_id_esc}' 
            AND change_type = 'welcome' 
            LIMIT 1
        ");
        
        if ($welcome_log) {
            return [
                'success'        => false,
                'reason'         => 'ALREADY_GRANTED',
                'free_uses'      => $existing_free,
                'credit_balance' => (int)($existing['credit_balance'] ?? 0),
            ];
        }
        
        // 무료 크레딧 1회 지급
        $before_free = 0;
        $after_free = 1;
        
        sql_query("BEGIN", false);
        
        $update_sql = "
            UPDATE {$tbl}
            SET free_uses = {$after_free},
                updated_at = '".G5_TIME_YMDHIS."'
            WHERE mb_id = '{$mb_id_esc}'
        ";
        $ok1 = sql_query($update_sql, false);
        
        $log_sql = "
            INSERT INTO {$log_tbl}
            SET mb_id          = '{$mb_id_esc}',
                change_type    = 'welcome',
                amount         = 1,
                before_balance = {$before_free},
                after_balance  = {$after_free},
                memo           = '".sql_real_escape_string($memo)."',
                ref_key        = 'welcome_".date('YmdHis')."',
                ip             = '".sql_real_escape_string($_SERVER['REMOTE_ADDR'] ?? '')."',
                created_at     = '".G5_TIME_YMDHIS."'
        ";
        $ok2 = sql_query($log_sql, false);
        
        if ($ok1 && $ok2) {
            sql_query("COMMIT", false);
            return [
                'success'        => true,
                'reason'         => '',
                'free_uses'      => $after_free,
                'credit_balance' => (int)($existing['credit_balance'] ?? 0),
            ];
        } else {
            sql_query("ROLLBACK", false);
            return [
                'success'        => false,
                'reason'         => 'DB_ERROR',
                'free_uses'      => $before_free,
                'credit_balance' => (int)($existing['credit_balance'] ?? 0),
            ];
        }
    } else {
        // 크레딧 행이 없으면 생성하면서 무료 크레딧 1회 지급
        $now = G5_TIME_YMDHIS;
        
        sql_query("BEGIN", false);
        
        $insert_sql = "
            INSERT INTO {$tbl}
            SET mb_id          = '{$mb_id_esc}',
                free_uses      = 1,
                credit_balance = 0,
                created_at     = '{$now}',
                updated_at     = '{$now}'
        ";
        $ok1 = sql_query($insert_sql, false);
        
        $log_sql = "
            INSERT INTO {$log_tbl}
            SET mb_id          = '{$mb_id_esc}',
                change_type    = 'welcome',
                amount         = 1,
                before_balance = 0,
                after_balance  = 1,
                memo           = '".sql_real_escape_string($memo)."',
                ref_key        = 'welcome_".date('YmdHis')."',
                ip             = '".sql_real_escape_string($_SERVER['REMOTE_ADDR'] ?? '')."',
                created_at     = '{$now}'
        ";
        $ok2 = sql_query($log_sql, false);
        
        if ($ok1 && $ok2) {
            sql_query("COMMIT", false);
            return [
                'success'        => true,
                'reason'         => '',
                'free_uses'      => 1,
                'credit_balance' => 0,
            ];
        } else {
            sql_query("ROLLBACK", false);
            return [
                'success'        => false,
                'reason'         => 'DB_ERROR',
                'free_uses'      => 0,
                'credit_balance' => 0,
            ];
        }
    }
}
