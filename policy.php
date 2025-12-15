<style>
/**
 * 오늘로또 - 회원가입 약관 동의 스타일
 * 그누보드 회원가입 스킨용
 */

/* ===== 약관 동의 컨테이너 ===== */
.register-form,
.member_register {
  max-width: 720px;
  margin: 0 auto;
  padding: 40px 20px;
}

/* ===== 텍스트 영역 (약관 내용) ===== */
.form-group textarea,
.member_register textarea,
textarea[name*="agree"] {
  width: 100%;
  min-height: 160px;
  padding: 20px;
  background: rgba(15, 23, 42, 0.95);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  color: #94a3b8;
  font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, sans-serif;
  font-size: 0.9rem;
  line-height: 1.8;
  resize: vertical;
  transition: all 0.3s ease;
}

.form-group textarea:focus,
textarea:focus {
  border-color: rgba(0, 255, 204, 0.4);
  box-shadow: 0 0 0 3px rgba(0, 255, 204, 0.1), 0 0 30px rgba(0, 255, 204, 0.1);
  outline: none;
}

/* ===== 개인정보 동의 카드 ===== */
.privacy-agreement-card,
.agree-box,
.form-section,
fieldset,
.tbl_frm01 {
  background: linear-gradient(145deg, 
    rgba(15, 23, 42, 0.9) 0%, 
    rgba(3, 7, 17, 0.95) 100%);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 32px;
  margin-bottom: 24px;
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  box-shadow: 
    0 4px 6px rgba(0, 0, 0, 0.1),
    0 10px 20px rgba(0, 0, 0, 0.15),
    inset 0 1px 0 rgba(255, 255, 255, 0.05);
  position: relative;
  overflow: hidden;
}

/* 카드 상단 글로우 효과 */
.privacy-agreement-card::before,
.agree-box::before,
fieldset::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, 
    transparent, 
    rgba(0, 255, 204, 0.3), 
    rgba(168, 85, 247, 0.2), 
    transparent);
}

/* ===== 카드 헤더 ===== */
.card-header,
.agree-box-header,
legend,
.form-section-title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.card-header h3,
legend,
.form-section-title h3 {
  font-family: 'Outfit', 'Pretendard', sans-serif;
  font-size: 1.25rem;
  font-weight: 700;
  color: #f8fafc;
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 0;
  padding: 0;
  background: none;
  border: none;
  float: none;
  width: auto;
}

.card-header h3::before,
legend::before {
  content: '🔒';
  font-size: 1.1rem;
}

/* 체크박스 라벨 스타일 (헤더 우측) */
.agreement-check,
.agree-check {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.95rem;
  color: #00ffcc;
  font-weight: 600;
  cursor: pointer;
  padding: 10px 18px;
  background: rgba(0, 255, 204, 0.08);
  border: 1px solid rgba(0, 255, 204, 0.2);
  border-radius: 100px;
  transition: all 0.3s ease;
}

.agreement-check:hover,
.agree-check:hover {
  background: rgba(0, 255, 204, 0.12);
  border-color: rgba(0, 255, 204, 0.35);
  transform: translateY(-1px);
}

/* ===== 커스텀 체크박스 ===== */
input[type="checkbox"] {
  appearance: none;
  -webkit-appearance: none;
  width: 22px;
  height: 22px;
  background: rgba(15, 23, 42, 0.8);
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  transition: all 0.25s ease;
  flex-shrink: 0;
}

input[type="checkbox"]:hover {
  border-color: rgba(0, 255, 204, 0.5);
  background: rgba(0, 255, 204, 0.05);
}

input[type="checkbox"]:checked {
  background: linear-gradient(135deg, #00ffcc 0%, #00d4ff 100%);
  border-color: transparent;
  box-shadow: 0 0 15px rgba(0, 255, 204, 0.4);
}

input[type="checkbox"]:checked::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #0f172a;
  font-size: 14px;
  font-weight: 700;
}

input[type="checkbox"]:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 255, 204, 0.2);
}

/* ===== 테이블 스타일 ===== */
.privacy-table,
.tbl_head01,
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 16px;
  overflow: hidden;
  background: rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.06);
}

/* 테이블 헤더 */
.privacy-table thead th,
.tbl_head01 thead th,
table thead th {
  background: linear-gradient(135deg, 
    rgba(0, 255, 204, 0.1) 0%, 
    rgba(168, 85, 247, 0.08) 100%);
  padding: 18px 20px;
  font-family: 'Outfit', 'Pretendard', sans-serif;
  font-size: 0.9rem;
  font-weight: 700;
  color: #f8fafc;
  text-align: left;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  letter-spacing: 0.02em;
}

.privacy-table thead th:first-child,
table thead th:first-child {
  border-top-left-radius: 16px;
}

.privacy-table thead th:last-child,
table thead th:last-child {
  border-top-right-radius: 16px;
}

/* 테이블 바디 */
.privacy-table tbody td,
.tbl_head01 tbody td,
table tbody td {
  padding: 18px 20px;
  font-size: 0.9rem;
  color: #94a3b8;
  line-height: 1.6;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);
  vertical-align: top;
  transition: all 0.2s ease;
}

/* 테이블 행 호버 */
.privacy-table tbody tr:hover td,
table tbody tr:hover td {
  background: rgba(255, 255, 255, 0.02);
  color: #cbd5e1;
}

/* 마지막 행 */
.privacy-table tbody tr:last-child td,
table tbody tr:last-child td {
  border-bottom: none;
}

.privacy-table tbody tr:last-child td:first-child,
table tbody tr:last-child td:first-child {
  border-bottom-left-radius: 16px;
}

.privacy-table tbody tr:last-child td:last-child,
table tbody tr:last-child td:last-child {
  border-bottom-right-radius: 16px;
}

/* 테이블 컬럼 강조 */
.privacy-table td:first-child,
table td:first-child {
  color: #e2e8f0;
  font-weight: 500;
}

/* ===== 개인정보 확인 섹션 ===== */
.personal-info-section,
.member-info,
.form-group {
  background: linear-gradient(145deg, 
    rgba(15, 23, 42, 0.9) 0%, 
    rgba(3, 7, 17, 0.95) 100%);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 32px;
  margin-bottom: 24px;
}

.personal-info-section h3,
.form-group-title {
  font-family: 'Outfit', 'Pretendard', sans-serif;
  font-size: 1.25rem;
  font-weight: 700;
  color: #f8fafc;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.personal-info-section h3::before {
  content: '👤';
}

/* ===== 입력 필드 그룹 ===== */
.input-group,
.form-field {
  margin-bottom: 20px;
}

.input-group label,
.form-field label,
.tbl_frm01 th {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: #94a3b8;
  margin-bottom: 10px;
  letter-spacing: 0.02em;
}

.input-group input,
.input-group select,
.form-field input,
.form-field select,
.tbl_frm01 input[type="text"],
.tbl_frm01 input[type="password"],
.tbl_frm01 input[type="email"],
.tbl_frm01 select {
  width: 100%;
  padding: 16px 20px;
  background: rgba(15, 23, 42, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  color: #f8fafc;
  font-family: 'Pretendard', sans-serif;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.input-group input:hover,
.form-field input:hover,
.tbl_frm01 input:hover {
  border-color: rgba(255, 255, 255, 0.2);
}

.input-group input:focus,
.form-field input:focus,
.tbl_frm01 input:focus {
  border-color: #00ffcc;
  box-shadow: 0 0 0 3px rgba(0, 255, 204, 0.15), 0 0 20px rgba(0, 255, 204, 0.1);
  outline: none;
}

.input-group input::placeholder,
.form-field input::placeholder {
  color: #64748b;
}

/* ===== 필수 항목 표시 ===== */
.required,
.req {
  color: #00ffcc;
  font-weight: 700;
  margin-left: 4px;
}

/* ===== 버튼 스타일 ===== */
.btn-submit,
.btn_submit,
input[type="submit"],
button[type="submit"] {
  width: 100%;
  padding: 18px 32px;
  background: linear-gradient(135deg, #00ffcc 0%, #00d4ff 100%);
  border: none;
  border-radius: 16px;
  font-family: 'Outfit', 'Pretendard', sans-serif;
  font-size: 1.1rem;
  font-weight: 700;
  color: #0f172a;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 0 30px rgba(0, 255, 204, 0.3), 0 0 60px rgba(0, 255, 204, 0.1);
  position: relative;
  overflow: hidden;
  margin-top: 24px;
}

.btn-submit::before,
input[type="submit"]::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(255,255,255,0.3), transparent);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.btn-submit:hover,
input[type="submit"]:hover {
  transform: translateY(-3px) scale(1.01);
  box-shadow: 0 0 50px rgba(0, 255, 204, 0.5), 0 0 100px rgba(0, 255, 204, 0.2);
}

.btn-submit:hover::before,
input[type="submit"]:hover::before {
  opacity: 1;
}

.btn-submit:active,
input[type="submit"]:active {
  transform: translateY(-1px) scale(1);
}

/* 취소 버튼 */
.btn-cancel,
.btn_cancel,
input[type="button"],
button.cancel {
  padding: 16px 28px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  color: #94a3b8;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-right: 12px;
}

.btn-cancel:hover,
input[type="button"]:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.25);
  color: #f8fafc;
}

/* ===== 그누보드 특정 오버라이드 ===== */
.tbl_frm01 {
  background: transparent !important;
  border: none !important;
  padding: 0 !important;
}

.tbl_frm01 tbody th {
  background: transparent;
  padding: 16px 0;
  text-align: left;
  vertical-align: middle;
  width: 140px;
  font-weight: 600;
  color: #94a3b8;
}

.tbl_frm01 tbody td {
  padding: 16px 0;
  background: transparent;
}

.tbl_frm01 tr {
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.tbl_frm01 tr:last-child {
  border-bottom: none;
}

/* ===== 체크박스 라벨 (일반) ===== */
label.checkbox-label,
.chk-label,
.agree_label {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 0.95rem;
  color: #cbd5e1;
  cursor: pointer;
  padding: 12px 16px;
  background: rgba(255, 255, 255, 0.02);
  border-radius: 12px;
  transition: all 0.2s ease;
  margin: 8px 0;
}

label.checkbox-label:hover,
.chk-label:hover,
.agree_label:hover {
  background: rgba(255, 255, 255, 0.04);
}

/* ===== 알림/안내 텍스트 ===== */
.notice-text,
.info-text,
.help-text {
  font-size: 0.85rem;
  color: #64748b;
  margin-top: 8px;
  line-height: 1.6;
}

.warning-text {
  color: #fbbf24;
  display: flex;
  align-items: center;
  gap: 6px;
}

.warning-text::before {
  content: '⚠️';
}

/* ===== 구분선 ===== */
.divider,
hr {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  border: none;
  margin: 28px 0;
}

/* ===== 진행 상태 표시 ===== */
.step-indicator {
  display: flex;
  justify-content: center;
  gap: 16px;
  margin-bottom: 40px;
}

.step {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.85rem;
  color: #64748b;
}

.step.active {
  color: #00ffcc;
}

.step-number {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.8rem;
}

.step.active .step-number {
  background: linear-gradient(135deg, #00ffcc, #00d4ff);
  color: #0f172a;
  box-shadow: 0 0 20px rgba(0, 255, 204, 0.4);
}

.step.completed .step-number {
  background: rgba(0, 255, 204, 0.2);
  color: #00ffcc;
}

.step.completed .step-number::after {
  content: '✓';
}

/* ===== 라디오 버튼 ===== */
input[type="radio"] {
  appearance: none;
  -webkit-appearance: none;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  background: rgba(15, 23, 42, 0.8);
  cursor: pointer;
  position: relative;
  transition: all 0.25s ease;
}

input[type="radio"]:checked {
  border-color: #00ffcc;
  background: rgba(0, 255, 204, 0.1);
}

input[type="radio"]:checked::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #00ffcc;
  box-shadow: 0 0 8px rgba(0, 255, 204, 0.6);
}

/* ===== 모바일 반응형 ===== */
@media (max-width: 768px) {
  .register-form,
  .member_register {
    padding: 24px 16px;
  }

  .privacy-agreement-card,
  .agree-box,
  fieldset {
    padding: 24px 20px;
    border-radius: 20px;
  }

  .card-header,
  legend {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .card-header h3,
  legend {
    font-size: 1.1rem;
  }

  .agreement-check {
    width: 100%;
    justify-content: center;
  }

  .privacy-table,
  table {
    display: block;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .privacy-table thead th,
  table thead th,
  .privacy-table tbody td,
  table tbody td {
    padding: 14px 16px;
    font-size: 0.85rem;
    white-space: nowrap;
  }

  .input-group input,
  .form-field input,
  .tbl_frm01 input {
    padding: 14px 16px;
  }

  .btn-submit,
  input[type="submit"] {
    padding: 16px 24px;
    font-size: 1rem;
  }

  .tbl_frm01 tbody th {
    width: 100px;
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .privacy-agreement-card,
  .agree-box {
    padding: 20px 16px;
    margin-left: -8px;
    margin-right: -8px;
    border-radius: 16px;
  }

  .card-header h3,
  legend {
    font-size: 1rem;
  }

  .tbl_frm01 {
    display: block;
  }

  .tbl_frm01 tbody,
  .tbl_frm01 tr,
  .tbl_frm01 th,
  .tbl_frm01 td {
    display: block;
    width: 100%;
  }

  .tbl_frm01 tbody th {
    padding-bottom: 4px;
    width: 100%;
  }

  .tbl_frm01 tbody td {
    padding-top: 0;
    padding-bottom: 20px;
  }
}

/* ===== 애니메이션 ===== */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.privacy-agreement-card,
.agree-box,
.personal-info-section,
fieldset {
  animation: fadeInUp 0.6s ease forwards;
}

.privacy-agreement-card:nth-child(2),
fieldset:nth-child(2) {
  animation-delay: 0.1s;
}

.privacy-agreement-card:nth-child(3),
fieldset:nth-child(3) {
  animation-delay: 0.2s;
}

/* ===== 스크롤바 ===== */
.privacy-table::-webkit-scrollbar,
table::-webkit-scrollbar {
  height: 6px;
}

.privacy-table::-webkit-scrollbar-track,
table::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 3px;
}

.privacy-table::-webkit-scrollbar-thumb,
table::-webkit-scrollbar-thumb {
  background: rgba(0, 255, 204, 0.3);
  border-radius: 3px;
}

.privacy-table::-webkit-scrollbar-thumb:hover,
table::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 255, 204, 0.5);
}

/* ===== 접근성 - 포커스 상태 ===== */
:focus-visible {
  outline: 2px solid #00ffcc;
  outline-offset: 2px;
}

/* ===== 감소된 모션 지원 ===== */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>회원가입 | 오늘로또</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">

  <!-- Shared Styles -->
  <link rel="stylesheet" href="/styles/shared.css">
  <link rel="stylesheet" href="/styles/register.css">

  <style>
    body {
      min-height: 100vh;
      padding: 40px 20px;
      background: #030711;
      background-image: 
        radial-gradient(ellipse 80% 50% at 50% -20%, rgba(0, 255, 204, 0.12) 0%, transparent 50%),
        radial-gradient(ellipse 60% 40% at 80% 50%, rgba(168, 85, 247, 0.08) 0%, transparent 50%);
    }

    .page-header {
      text-align: center;
      margin-bottom: 48px;
    }

    .page-header h1 {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(1.8rem, 5vw, 2.5rem);
      font-weight: 800;
      margin-bottom: 12px;
    }

    .page-header h1 span {
      background: linear-gradient(135deg, #00ffcc 0%, #00d4ff 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .page-header p {
      color: #94a3b8;
      font-size: 1rem;
    }

    .register-container {
      max-width: 720px;
      margin: 0 auto;
    }

    /* 진행 표시 */
    .progress-steps {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      margin-bottom: 40px;
    }

    .step-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .step-circle {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.08);
      border: 2px solid rgba(255, 255, 255, 0.15);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 0.85rem;
      color: #64748b;
      transition: all 0.3s ease;
    }

    .step-item.active .step-circle {
      background: linear-gradient(135deg, #00ffcc, #00d4ff);
      border-color: transparent;
      color: #0f172a;
      box-shadow: 0 0 25px rgba(0, 255, 204, 0.5);
    }

    .step-item.completed .step-circle {
      background: rgba(0, 255, 204, 0.15);
      border-color: rgba(0, 255, 204, 0.4);
      color: #00ffcc;
    }

    .step-label {
      font-size: 0.85rem;
      color: #64748b;
      font-weight: 500;
    }

    .step-item.active .step-label {
      color: #f8fafc;
    }

    .step-connector {
      width: 40px;
      height: 2px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 1px;
    }

    .step-connector.active {
      background: linear-gradient(90deg, #00ffcc, rgba(0, 255, 204, 0.3));
    }

    /* 카드 아이콘 배지 */
    .card-icon-badge {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, rgba(0, 255, 204, 0.15), rgba(168, 85, 247, 0.1));
      border: 1px solid rgba(0, 255, 204, 0.2);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      flex-shrink: 0;
    }

    /* 동의 체크 버튼 */
    .agree-toggle {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 20px;
      background: rgba(0, 255, 204, 0.08);
      border: 1px solid rgba(0, 255, 204, 0.2);
      border-radius: 100px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 0.9rem;
      font-weight: 600;
      color: #00ffcc;
    }

    .agree-toggle:hover {
      background: rgba(0, 255, 204, 0.12);
      border-color: rgba(0, 255, 204, 0.4);
      transform: translateY(-1px);
    }

    .agree-toggle.checked {
      background: linear-gradient(135deg, #00ffcc, #00d4ff);
      color: #0f172a;
      box-shadow: 0 0 20px rgba(0, 255, 204, 0.4);
    }

    .agree-toggle .check-icon {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      border: 2px solid currentColor;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7rem;
      transition: all 0.3s ease;
    }

    .agree-toggle.checked .check-icon {
      background: #0f172a;
      border-color: #0f172a;
    }

    /* 버튼 그룹 */
    .btn-group {
      display: flex;
      gap: 12px;
      margin-top: 32px;
    }

    .btn-group .btn-secondary {
      flex: 0 0 auto;
      padding: 16px 28px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 14px;
      color: #94a3b8;
      font-family: 'Outfit', sans-serif;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-group .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(255, 255, 255, 0.25);
      color: #f8fafc;
    }

    .btn-group .btn-primary {
      flex: 1;
      padding: 18px 32px;
      background: linear-gradient(135deg, #00ffcc 0%, #00d4ff 100%);
      border: none;
      border-radius: 14px;
      color: #0f172a;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 1.05rem;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 0 30px rgba(0, 255, 204, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-group .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 0 50px rgba(0, 255, 204, 0.5);
    }

    .btn-group .btn-primary:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    /* 푸터 */
    .register-footer {
      text-align: center;
      margin-top: 32px;
      padding-top: 24px;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .register-footer p {
      font-size: 0.85rem;
      color: #64748b;
    }

    .register-footer a {
      color: #00ffcc;
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .register-footer a:hover {
      color: #5cffe8;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <!-- 페이지 헤더 -->
    <div class="page-header">
      <h1><span>오늘로또</span> 회원가입</h1>
      <p>AI 로또 분석 서비스 이용을 위해 약관에 동의해주세요</p>
    </div>

    <!-- 진행 단계 -->
    <div class="progress-steps">
      <div class="step-item active">
        <div class="step-circle">1</div>
        <span class="step-label">약관동의</span>
      </div>
      <div class="step-connector"></div>
      <div class="step-item">
        <div class="step-circle">2</div>
        <span class="step-label">정보입력</span>
      </div>
      <div class="step-connector"></div>
      <div class="step-item">
        <div class="step-circle">3</div>
        <span class="step-label">가입완료</span>
      </div>
    </div>

    <form id="registerForm">
      <!-- 회원가입 약관 -->
      <fieldset>
        <div class="card-header">
          <div style="display: flex; align-items: center; gap: 16px;">
            <div class="card-icon-badge">📋</div>
            <div>
              <h3 style="margin: 0; font-size: 1.15rem;">회원가입 약관</h3>
              <p style="margin: 4px 0 0; font-size: 0.85rem; color: #64748b;">서비스 이용을 위한 필수 약관입니다</p>
            </div>
          </div>
          <label class="agree-toggle" id="termsToggle">
            <span class="check-icon">✓</span>
            <span>동의합니다</span>
            <input type="checkbox" name="agree_terms" style="display: none;">
          </label>
        </div>
        <textarea readonly>해당 홈페이지에 맞는 회원가입약관을 입력합니다.

제 1 조 (목적)
이 약관은 오늘로또(이하 "회사")가 제공하는 AI 로또 분석 서비스(이하 "서비스")의 이용과 관련하여 회사와 회원 간의 권리, 의무 및 책임사항을 규정함을 목적으로 합니다.

제 2 조 (정의)
① "서비스"란 회사가 제공하는 AI 기반 로또 번호 분석 및 추천 서비스를 말합니다.
② "회원"이란 이 약관에 동의하고 회사와 서비스 이용계약을 체결한 자를 말합니다.
③ "크레딧"이란 서비스 이용을 위해 필요한 가상의 포인트를 말합니다.

제 3 조 (약관의 효력 및 변경)
① 이 약관은 서비스 화면에 게시하거나 기타의 방법으로 회원에게 공지함으로써 효력이 발생합니다.
② 회사는 필요한 경우 관련 법령을 위배하지 않는 범위에서 이 약관을 변경할 수 있습니다.</textarea>
      </fieldset>

      <!-- 개인정보 수집 및 이용 -->
      <fieldset>
        <div class="card-header">
          <div style="display: flex; align-items: center; gap: 16px;">
            <div class="card-icon-badge">🔒</div>
            <div>
              <h3 style="margin: 0; font-size: 1.15rem;">개인정보 수집 및 이용</h3>
              <p style="margin: 4px 0 0; font-size: 0.85rem; color: #64748b;">서비스 제공을 위해 필요한 최소 정보만 수집합니다</p>
            </div>
          </div>
          <label class="agree-toggle" id="privacyToggle">
            <span class="check-icon">✓</span>
            <span>동의합니다</span>
            <input type="checkbox" name="agree_privacy" style="display: none;">
          </label>
        </div>

        <table class="privacy-table">
          <thead>
            <tr>
              <th style="width: 35%;">목적</th>
              <th style="width: 40%;">항목</th>
              <th style="width: 25%;">보유기간</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>이용자 식별 및 본인 여부 확인</td>
              <td>아이디, 이름, 비밀번호</td>
              <td>회원 탈퇴 시까지</td>
            </tr>
            <tr>
              <td>고객서비스 이용에 관한 통지,<br>CS 대응을 위한 이용자 식별</td>
              <td>연락처 (이메일, 휴대전화번호)</td>
              <td>회원 탈퇴 시까지</td>
            </tr>
            <tr>
              <td>서비스 이용 기록 관리</td>
              <td>분석 이력, 결제 내역</td>
              <td>관련 법령에 따름</td>
            </tr>
          </tbody>
        </table>
      </fieldset>

      <!-- 개인정보 확인 -->
      <fieldset>
        <div class="card-header" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
          <div style="display: flex; align-items: center; gap: 16px;">
            <div class="card-icon-badge">👤</div>
            <div>
              <h3 style="margin: 0; font-size: 1.15rem;">개인정보 확인</h3>
              <p style="margin: 4px 0 0; font-size: 0.85rem; color: #64748b;">카카오 계정에서 가져온 정보입니다</p>
            </div>
          </div>
        </div>
        
        <div style="margin-top: 24px;">
          <div class="input-group">
            <label>닉네임 <span class="required">*</span></label>
            <input type="text" value="카카오사용자" readonly style="background: rgba(0, 0, 0, 0.3);">
          </div>
          <div class="input-group" style="margin-top: 16px;">
            <label>이메일</label>
            <input type="email" value="user@kakao.com" readonly style="background: rgba(0, 0, 0, 0.3);">
          </div>
        </div>
      </fieldset>

      <!-- 버튼 그룹 -->
      <div class="btn-group">
        <button type="button" class="btn-secondary" onclick="history.back()">취소</button>
        <button type="submit" class="btn-primary" id="submitBtn" disabled>
          <span>가입 완료하기</span>
          <span>→</span>
        </button>
      </div>

      <!-- 푸터 -->
      <div class="register-footer">
        <p>이미 계정이 있으신가요? <a href="auth.html">로그인하기</a></p>
      </div>
    </form>
  </div>

  <script>
    // 동의 토글 기능
    document.querySelectorAll('.agree-toggle').forEach(toggle => {
      toggle.addEventListener('click', function() {
        const checkbox = this.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
        this.classList.toggle('checked', checkbox.checked);
        updateSubmitButton();
      });
    });

    // 제출 버튼 상태 업데이트
    function updateSubmitButton() {
      const termsChecked = document.querySelector('input[name="agree_terms"]').checked;
      const privacyChecked = document.querySelector('input[name="agree_privacy"]').checked;
      const submitBtn = document.getElementById('submitBtn');
      
      submitBtn.disabled = !(termsChecked && privacyChecked);
    }

    // 폼 제출
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      e.preventDefault();
      alert('회원가입이 완료되었습니다!');
    });
  </script>
</body>
</html>

