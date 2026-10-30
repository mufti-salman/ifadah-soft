@extends('layouts.admin')

@section('title', 'আয়-ব্যয় এন্ট্রি ফরম')

@section('page')

<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
:root {
    --blue: #1976d2;
    --blue-dark: #1565c0;
    --green: #2e7d32;
    --red: #c62828;
    --teal: #00897b;
    --bg: #f0f4f8;
    --card: #ffffff;
    --border: #e0e0e0;
    --text: #1a1a2e;
    --muted: #777;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Hind Siliguri', sans-serif;
    background: var(--bg);
}

.outer {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.card {
    background: var(--card);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
}

.top-row {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
}

.bottom-row {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
}

.form-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 20px;
}

.form-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 10px;
}

.type-tabs {
    display: flex;
    gap: 8px;
}

.tab-btn {
    padding: 8px 22px;
    border-radius: 20px;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-family: inherit;
    font-weight: 600;
    transition: all .2s;
}

.tab-btn.income {
    background: #e8f5e9;
    color: var(--green);
}

.tab-btn.expense {
    background: #fce4ec;
    color: var(--red);
}

.tab-btn.income.active {
    background: var(--green);
    color: #fff;
}

.tab-btn.expense.active {
    background: var(--red);
    color: #fff;
}

.voucher-no {
    text-align: right;
}

.voucher-no small {
    font-size: 12px;
    color: var(--muted);
}

.voucher-no strong {
    display: block;
    font-size: 20px;
    font-weight: 800;
    color: var(--blue);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 14px;
    margin-bottom: 14px;
}

.form-grid-2 {
    grid-template-columns: repeat(2,1fr);
}

.field-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.field-label {
    font-size: 11px;
    color: var(--muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.field-row {
    display: flex;
    gap: 6px;
    align-items: center;
}

.field-row select,
.field-row input {
    flex: 1;
    padding: 9px 12px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    color: #333;
    background: #fafafa;
    transition: border .2s;
}

.field-row select:focus,
.field-row input:focus {
    outline: none;
    border-color: var(--blue);
    background: #fff;
}

.plus-btn {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    background: var(--blue);
    color: #fff;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background .2s;
}

.plus-btn:hover {
    background: var(--blue-dark);
}

.amount-wrap {
    position: relative;
}

.amount-wrap .taka-sym {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #555;
    font-weight: 700;
    pointer-events: none;
}

.amount-wrap input {
    padding-left: 24px !important;
}

.add-item-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--blue), var(--blue-dark));
    color: #fff;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 14px;
    font-family: inherit;
    font-weight: 600;
    margin: 10px 0 16px auto;
    transition: transform .15s, box-shadow .15s;
}

.add-item-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(25,118,210,.35);
}

.items-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.items-header h4 {
    font-size: 14px;
    font-weight: 700;
    color: #333;
}

.items-header a {
    font-size: 12px;
    color: var(--red);
    cursor: pointer;
    font-weight: 600;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table th {
    background: #f5f7fa;
    padding: 9px 12px;
    text-align: left;
    font-size: 11px;
    color: #888;
    font-weight: 700;
    border-bottom: 1px solid #eee;
}

.items-table td {
    padding: 10px 12px;
    font-size: 13px;
    border-bottom: 1px solid #f3f3f3;
    vertical-align: middle;
}

.items-table tr:last-child td {
    border-bottom: none;
}

.serial-badge {
    background: var(--blue);
    color: #fff;
    border-radius: 6px;
    padding: 3px 10px;
    font-size: 12px;
    font-weight: 700;
}

.total-row td {
    background: #f5f7fa;
    font-weight: 700;
}

.del-row-btn {
    background: none;
    border: 1px solid #fce4ec;
    border-radius: 6px;
    padding: 5px 8px;
    cursor: pointer;
    color: var(--red);
    transition: all .2s;
}

.del-row-btn:hover {
    background: #fce4ec;
}

.notes-area {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    min-height: 65px;
    background: #fafafa;
    margin-top: 10px;
}

.notes-area:focus {
    outline: none;
    border-color: var(--blue);
}

.right-panel {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.panel-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 12px;
}

.payment-grid {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 8px;
}

.pay-card {
    border: 2px solid var(--border);
    border-radius: 12px;
    padding: 12px 6px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
}

.pay-card.active {
    border-color: var(--teal);
    background: #e0f2f1;
}

.pay-card i {
    font-size: 20px;
    margin-bottom: 5px;
    display: block;
    color: #777;
}

.pay-card.active i {
    color: var(--teal);
}

.pay-card span {
    font-size: 11px;
    font-weight: 700;
    color: #666;
    display: block;
}

.pay-card.active span {
    color: #00695c;
}

.cashier-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.cashier-btn {
    padding: 9px;
    border-radius: 8px;
    border: 2px solid var(--border);
    background: #fff;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    font-family: inherit;
    color: #333;
    transition: all .2s;
    text-align: center;
}

.cashier-btn.active {
    background: var(--blue);
    color: #fff;
    border-color: var(--blue);
}

.grand-total-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border: 1px solid var(--border);
    border-radius: 12px;
}

.grand-total-box .lbl {
    font-size: 14px;
    color: var(--muted);
}

.grand-total-box .amt {
    font-size: 24px;
    font-weight: 800;
    color: var(--text);
}

.confirm-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #00bfa5, #00897b);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-family: inherit;
    font-weight: 700;
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
}

.confirm-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,191,165,.4);
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.section-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title i {
    color: var(--blue);
}

.search-wrap {
    position: relative;
}

.search-wrap input {
    padding: 9px 14px 9px 36px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    width: 210px;
}

.search-wrap input:focus {
    outline: none;
    border-color: var(--blue);
}

.search-wrap i {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
}

.trans-table {
    width: 100%;
    border-collapse: collapse;
}

.trans-table th {
    font-size: 11px;
    color: #999;
    font-weight: 700;
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f0;
    text-align: left;
}

.trans-table td {
    padding: 12px;
    font-size: 13px;
    border-bottom: 1px solid #f5f5f5;
    vertical-align: top;
}

.trans-table tr:hover td {
    background: #fafafa;
}

.action-btns {
    display: flex;
    gap: 5px;
}

.action-btns button {
    background: none;
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 5px 9px;
    cursor: pointer;
    color: #666;
    font-size: 12px;
    transition: all .2s;
}

.action-btns button:hover {
    border-color: var(--blue);
    color: var(--blue);
}

.action-btns button.del:hover {
    border-color: var(--red);
    color: var(--red);
}

.voucher-badge {
    background: #e3f2fd;
    color: #1565c0;
    border-radius: 6px;
    padding: 4px 10px;
    font-weight: 700;
    font-size: 12px;
    display: inline-block;
}

.desc-list {
    list-style: none;
}

.desc-list li {
    font-size: 12px;
    color: #555;
    padding: 2px 0;
}

.desc-list li::before {
    content: '• ';
    color: var(--blue);
}

.income-amt {
    color: var(--green);
    font-weight: 700;
    font-size: 15px;
}

.expense-amt {
    color: var(--red);
    font-weight: 700;
    font-size: 15px;
}

.medium-badge {
    border-radius: 6px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 700;
    display: inline-block;
    background: #f3e5f5;
    color: #6a1b9a;
}

.balance-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 0;
    border-bottom: 1px solid #f5f5f5;
}

.balance-item:last-child {
    border-bottom: none;
}

.balance-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.balance-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
}

.balance-total {
    font-size: 16px;
    font-weight: 800;
    margin-left: auto;
}

.balance-sub {
    margin-top: 4px;
    padding-left: 54px;
}

.balance-sub-row {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #888;
    padding: 2px 0;
}

.balance-sub-row span:last-child {
    font-weight: 600;
    color: #444;
}

/* Modal */

.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.modal-overlay.open {
    display: flex;
}

.modal-box {
    background: #fff;
    border-radius: 18px;
    padding: 28px;
    width: 420px;
    max-width: 95vw;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
}

.modal-box h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 18px;
}

.modal-inp {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    margin-bottom: 12px;
    background: #fafafa;
}

.modal-inp:focus {
    outline: none;
    border-color: var(--blue);
    background: #fff;
}

.modal-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 6px;
}

.btn-cancel {
    padding: 9px 22px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-family: inherit;
    font-weight: 600;
    background: #f5f5f5;
    color: #555;
}

.btn-save {
    padding: 9px 22px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-family: inherit;
    font-weight: 600;
    background: var(--blue);
    color: #fff;
}

.alert {
    padding: 12px 18px;
    border-radius: 10px;
    margin-bottom: 16px;
    font-size: 14px;
    font-weight: 600;
}

.alert-success {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}

.alert-error {
    background: #fce4ec;
    color: #c62828;
    border: 1px solid #ef9a9a;
}

.empty-row td {
    text-align: center;
    color: #bbb;
    padding: 30px;
    font-size: 14px;
}

/* Payment Method Modal */

.pm-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.pm-overlay.open {
    display: flex;
}

.pm-box {
    background: #fff;
    border-radius: 10px;
    width: 700px;
    max-width: 97vw;
    box-shadow: 0 20px 60px rgba(0,0,0,.3);
    overflow: hidden;
}

.pm-titlebar {
    background: #00695c;
    padding: 10px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.pm-titlebar span {
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    flex: 1;
}

.pm-titlebar-close {
    background: #c62828;
    border: none;
    color: #fff;
    width: 26px;
    height: 26px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pm-body {
    padding: 16px 20px;
}

.pm-row {
    display: grid;
    gap: 12px;
    margin-bottom: 12px;
}

.pm-row.cols2 {
    grid-template-columns: 1fr 1fr;
}

.pm-row.cols1 {
    grid-template-columns: 1fr;
}

.pm-field label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #555;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: .4px;
}

.pm-field input,
.pm-field select {
    width: 100%;
    padding: 8px 10px;
    border: 1.5px solid var(--border);
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    background: #fafafa;
}

.pm-field input:focus,
.pm-field select:focus {
    outline: none;
    border-color: var(--blue);
    background: #fff;
}

.pm-actions {
    display: flex;
    gap: 8px;
    margin-top: 6px;
    margin-bottom: 16px;
}

.pm-btn {
    padding: 8px 18px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-family: inherit;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: opacity .15s;
}

.pm-btn:hover {
    opacity: .85;
}

.pm-btn-save {
    background: #2e7d32;
    color: #fff;
}

.pm-btn-update {
    background: #e65100;
    color: #fff;
}

.pm-btn-new {
    background: #1976d2;
    color: #fff;
}

.pm-btn-close {
    background: #757575;
    color: #fff;
}

.pm-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.pm-table th {
    background: #eeeeee;
    padding: 7px 10px;
    text-align: left;
    font-size: 11px;
    color: #666;
    font-weight: 700;
    border: 1px solid #ddd;
}

.pm-table td {
    padding: 8px 10px;
    border: 1px solid #eee;
    vertical-align: middle;
}

.pm-table tr:hover td {
    background: #f9f9f9;
}

.pm-table tr.pm-selected td {
    background: #e8f5e9 !important;
}

.pm-icon-btn {
    width: 26px;
    height: 26px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.pm-icon-btn.edit {
    background: #e3f2fd;
    color: #1565c0;
}

.pm-icon-btn.delete {
    background: #fce4ec;
    color: #c62828;
}

.pm-badge {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-block;
}

.pm-badge.cash {
    background: #e8f5e9;
    color: #2e7d32;
}

.pm-badge.mobile {
    background: #e3f2fd;
    color: #1565c0;
}

.pm-badge.bank {
    background: #fff3e0;
    color: #e65100;
}

@media(max-width: 1100px) {
    .top-row,
    .bottom-row {
        grid-template-columns: 1fr;
    }
}

@media(max-width: 700px) {
    .form-grid,
    .form-grid-2 {
        grid-template-columns: 1fr;
    }

    .form-header {
        flex-direction: column;
        gap: 15px;
    }

    .voucher-no {
        text-align: left;
    }

    .pm-row.cols2 {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="outer">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="top-row">

        {{-- LEFT --}}
        <div class="card">

            <div class="form-header">

                <div>
                    <div class="form-title">
                        আয়-ব্যয় এন্ট্রি ফরম
                    </div>

                    <div class="type-tabs">

                        <button type="button"
                                class="tab-btn income {{ $type == 'income' ? 'active' : '' }}"
                                onclick="setType('income')">
                            <i class="fas fa-arrow-down"></i>
                            আয় (Income)
                        </button>

                        <button type="button"
                                class="tab-btn expense {{ $type == 'expense' ? 'active' : '' }}"
                                onclick="setType('expense')">
                            <i class="fas fa-arrow-up"></i>
                            ব্যয় (Expense)
                        </button>

                    </div>
                </div>

                <div class="voucher-no">
                    <small>ভাউচার নং</small>
                    <strong>{{ $voucherNo }}</strong>
                </div>

            </div>

            <form method="POST"
                  action="{{ route('dashboard.transactions.store') }}"
                  id="mainForm">

                @csrf

                <input type="hidden"
                       name="type"
                       id="hiddenType"
                       value="{{ $type }}">

                <input type="hidden"
                       name="items"
                       id="itemsJson"
                       value="[]">

                <input type="hidden"
                       name="payment_method_id"
                       id="hiddenPayment"
                       value="">

                <div class="form-grid">

                    {{-- Date --}}
                    <div class="field-group">
                        <span class="field-label">তারিখ</span>

                        <div class="field-row">
                            <input type="date"
                                   name="date"
                                   value="{{ date('Y-m-d') }}"
                                   required>
                        </div>
                    </div>

                    {{-- Fund --}}
                    <div class="field-group">

                        <span class="field-label">ফান্ড</span>

                        <div class="field-row">

                            <select name="fund_id"
                                    id="fundSelect"
                                    required>

                                <option value="">
                                    ফান্ড নির্বাচন করুন
                                </option>

                                @foreach($funds as $f)

                                    <option value="{{ $f->id }}"
                                        {{ $loop->first ? 'selected' : '' }}>
                                        {{ $f->name }}
                                    </option>

                                @endforeach

                            </select>

                            <button type="button"
                                    class="plus-btn"
                                    onclick="openModal('fundModal')">
                                +
                            </button>

                        </div>
                    </div>

                    {{-- Ledger --}}
                    <div class="field-group">

                        <span class="field-label">
                            খাত (Ledger)
                        </span>

                        <div class="field-row">

                            <select id="ledgerSelect">
                                <option value="">
                                    সিলেক্ট করুন
                                </option>

                                @foreach($ledgers as $l)

                                    <option value="{{ $l->id }}">
                                        {{ $l->name }}
                                    </option>

                                @endforeach

                            </select>

                            <button type="button"
                                    class="plus-btn"
                                    onclick="openModal('ledgerModal')">
                                +
                            </button>

                        </div>
                    </div>

                    {{-- Sub Ledger --}}
                    <div class="field-group">

                        <span class="field-label">
                            সাব-লেজার
                        </span>

                        <div class="field-row">

                            <select id="subLedgerSelect">
                                <option value="">
                                    সিলেক্ট করুন
                                </option>
                            </select>

                            <button type="button"
                                    class="plus-btn"
                                    onclick="openModal('subLedgerModal')">
                                +
                            </button>

                        </div>
                    </div>

                </div>

                <div class="form-grid form-grid-2">

                    <div class="field-group">

                        <span class="field-label">
                            পরিমাণ (টাকা)
                        </span>

                        <div class="field-row amount-wrap">

                            <span class="taka-sym">
                                ৳
                            </span>

                            <input type="number"
                                   id="itemAmount"
                                   placeholder="0.00"
                                   min="0"
                                   step="0.01">

                        </div>

                    </div>

                    <div class="field-group">

                        <span class="field-label">
                            বিস্তারিত বিবরণ
                        </span>

                        <div class="field-row">

                            <input type="text"
                                   id="itemDesc"
                                   placeholder="বিবরণ লিখুন...">

                        </div>

                    </div>

                </div>

                <div style="display:flex;justify-content:flex-end;">

                    <button type="button"
                            class="add-item-btn"
                            onclick="addItem()">

                        <i class="fas fa-plus"></i>
                        আইটেম যুক্ত করুন

                    </button>

                </div>

                <div>

                    <div class="items-header">

                        <h4>
                            <i class="fas fa-list"
                               style="color:var(--blue);margin-right:6px;"></i>
                            যুক্ত আইটেমসমূহ
                        </h4>

                        <a onclick="clearItems()">
                            <i class="fas fa-trash-alt"></i>
                            সব মুছুন
                        </a>

                    </div>

                    <table class="items-table">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th>লেজার</th>
                                <th>সাব-লেজার</th>
                                <th>বিবরণ</th>
                                <th>পরিমাণ</th>
                                <th></th>
                            </tr>

                        </thead>

                        <tbody id="itemsBody">

                            <tr class="empty-row">

                                <td colspan="6">

                                    <i class="fas fa-inbox"></i>
                                    কোনো আইটেম যোগ হয়নি

                                </td>

                            </tr>

                        </tbody>

                        <tfoot>

                            <tr class="total-row">

                                <td colspan="4"
                                    style="text-align:right;font-size:12px;color:#777;padding-right:16px;">

                                    সর্বমোট :

                                </td>

                                <td style="font-size:15px;"
                                    id="itemsTotal">
                                    ৳ ০
                                </td>

                                <td></td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

                <textarea name="note"
                          class="notes-area"
                          placeholder="ফাইনাল রিমার্কস / নোট..."></textarea>

            </form>

        </div>

        {{-- RIGHT PANEL --}}
        <div class="right-panel">

            {{-- Payment --}}
            <div class="card">

                <div class="panel-label">
                    পেমেন্ট মেথড
                </div>

                <div class="payment-grid"
                     id="paymentTypeGrid">

                    <div class="pay-card active"
                         onclick="selectPaymentType(this, 'cash')">

                        <i class="fas fa-money-bill-wave"></i>
                        <span>ক্যাশ</span>

                    </div>

                    <div class="pay-card"
                         onclick="selectPaymentType(this, 'mobile')">

                        <i class="fas fa-mobile-alt"></i>
                        <span>মোবাইল ব্যাংকিং</span>

                    </div>

                    <div class="pay-card"
                         onclick="selectPaymentType(this, 'bank')">

                        <i class="fas fa-university"></i>
                        <span>ব্যাংক</span>

                    </div>

                </div>

                <div style="margin-top:14px;">

                    <label style="font-size:12px;font-weight:700;color:#777;">
                        একাউন্ট সিলেক্ট করুন
                    </label>

                    <select id="paymentMethodSelect"
                            style="width:100%;margin-top:6px;padding:6px;border:1.5px solid #ddd;border-radius:10px;font-family:inherit;">

                        <option value="">
                            একাউন্ট নির্বাচন করুন
                        </option>

                        @foreach($paymentMethods as $pm)

                            <option value="{{ $pm->id }}"
                                    data-type="{{ $pm->account_type }}">

                                {{ $pm->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                @if($paymentMethods->isEmpty())

                    <div style="text-align:center;color:#bbb;font-size:13px;padding:12px;">

                        + বাটন দিয়ে পেমেন্ট মেথড যোগ করুন

                    </div>

                @endif

                <div style="margin-top:10px;display:flex;justify-content:flex-end;">

                    <button type="button"
                            style="display:flex;align-items:center;gap:6px;padding:7px 16px;background:var(--blue);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-family:inherit;font-weight:600;"
                            onclick="openPmModal()">

                        <i class="fas fa-plus"></i>
                        মেথড যোগ

                    </button>

                </div>

            </div>



            {{-- Grand Total --}}
            <div class="card">

                <div class="grand-total-box">

                    <span class="lbl">
                        সর্বমোট
                    </span>

                    <span class="amt"
                          id="grandTotal">
                        ৳ ০
                    </span>

                </div>

            </div>

            <button type="button"
                    class="confirm-btn"
                    onclick="submitForm()">

                <i class="fas fa-check-circle"></i>
                কনফার্ম ও সেভ করুন

            </button>

        </div>

    </div>

    {{-- BOTTOM ROW --}}
    <div class="bottom-row">

        <div class="card">

            <div class="section-header">

                <div class="section-title">

                    <i class="fas fa-clock"></i>

                    সর্বশেষ

                    <span style="color:{{ $type == 'income' ? 'var(--green)' : 'var(--red)' }}">

                        {{ $type == 'income' ? 'আয়' : 'ব্যয়' }}

                    </span>

                    লেনদেন

                </div>

                <div class="search-wrap">

                    <i class="fas fa-search"></i>

                    <input type="text"
                           placeholder="খুঁজুন..."
                           onkeyup="filterTable(this)">

                </div>

            </div>

            <table class="trans-table"
                   id="transTable">

                <thead>

                    <tr>

                        <th>অ্যাকশন</th>
                        <th>অর্ডার</th>
                        <th>তারিখ</th>
                        <th>ভাউচার</th>
                        <th>বিবরণ</th>
                        <th>পরিমাণ</th>
                        <th>মাধ্যম</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($transactions as $t)

                        <tr>

                            <td>

                                <div class="action-btns">

                                    <button title="প্রিন্ট"
                                            onclick="window.print()">

                                        <i class="fas fa-print"></i>

                                    </button>

                                    <button class="del"
                                            title="মুছুন"
                                            onclick="deleteTransaction({{ $t->id }}, this)">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </div>

                            </td>

                            <td>
                                {{ $t->id }}
                            </td>

                            <td>

                                <div style="font-weight:600;color:#333;">

                                    {{ \Carbon\Carbon::parse($t->date)->format('d/m/Y') }}

                                </div>

                                <div style="font-size:11px;color:#aaa;">

                                    {{ $t->created_at->format('h:i A') }}

                                </div>

                            </td>

                            <td>

                                <span class="voucher-badge">
                                    {{ $t->voucher_no }}
                                </span>

                            </td>

                            <td>

                                <ul class="desc-list">

                                    @foreach($t->items as $item)

                                        <li>

                                            {{ $item->ledger->name ?? '—' }}

                                            @if($item->subLedger)
                                                › {{ $item->subLedger->name }}
                                            @endif

                                            @if($item->description)
                                                · {{ $item->description }}
                                            @endif

                                        </li>

                                    @endforeach

                                </ul>

                            </td>

                            <td>

                                <span class="{{ $t->type == 'income' ? 'income-amt' : 'expense-amt' }}">

                                    ৳ {{ number_format($t->total_amount, 2) }}

                                </span>

                            </td>

                            <td>

                                <span class="medium-badge">

                                    {{ $t->paymentMethod->name ?? '—' }}

                                @if($t->creator)
                                    <small class="text-muted">
                                        এন্ট্রি করেছেন: {{ $t->creator->name }}
                                    </small>
                                @endif

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr class="empty-row">

                            <td colspan="7">

                                <i class="fas fa-inbox"></i>
                                কোনো লেনদেন নেই

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Balance --}}
        <div class="card">

            <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:14px;display:flex;align-items:center;gap:8px;">

                <i class="fas fa-wallet"
                   style="color:var(--blue);"></i>

                বর্তমান ব্যালেন্স

            </div>

            @forelse($balance as $pmId => $pm)

                @if($pm['total'] != 0)

                    <div class="balance-item">

                        <div class="balance-icon"
                             style="background:#e3f2fd;color:var(--blue);">

                            <i class="fas {{ $pm['icon'] }}"></i>

                        </div>

                        <div class="balance-name">
                            {{ $pm['name'] }}
                        </div>

                        <div class="balance-total"
                             style="color:{{ $pm['is_negative'] ? 'var(--red)' : 'var(--green)' }}">

                            {{ $pm['is_negative'] ? '-' : '' }}
                            ৳ {{ number_format($pm['total_display'], 0) }}

                        </div>

                    </div>

                @endif

                @if(!empty($pm['members']))

                    <div class="balance-sub">

                        @foreach($pm['members'] as $m)

                            <div class="balance-sub-row">

                                <span>
                                    {{ $m['name'] }}
                                </span>

                                <span style="color:{{ $m['is_negative'] ? 'var(--red)' : 'var(--green)' }}">

                                    {{ $m['is_negative'] ? '-' : '' }}
                                    ৳ {{ number_format($m['amount'], 0) }}

                                </span>

                            </div>

                        @endforeach

                    </div>

                @endif

            @empty

                <div style="text-align:center;color:#bbb;font-size:13px;padding:20px;">
                    কোনো লেনদেন নেই
                </div>

            @endforelse

        </div>

    </div>

</div>

{{-- ==================== LEDGER MODAL ==================== --}}

<div class="modal-overlay"
     id="ledgerModal">

    <div class="modal-box">

        <h3>

            <i class="fas fa-book"
               style="color:var(--blue);margin-right:8px;"></i>

            নতুন লেজার যোগ করুন

        </h3>

        <input type="text"
               class="modal-inp"
               id="ledgerName"
               placeholder="লেজার নাম">

        <div class="modal-actions">

            <button class="btn-cancel"
                    type="button"
                    onclick="closeModal('ledgerModal')">

                বাতিল

            </button>

            <button class="btn-save"
                    type="button"
                    onclick="addLedger()">

                সেভ করুন

            </button>

        </div>

    </div>

</div>

{{-- ==================== SUB LEDGER MODAL ==================== --}}

<div class="modal-overlay"
     id="subLedgerModal">

    <div class="modal-box">

        <h3>

            <i class="fas fa-sitemap"
               style="color:var(--blue);margin-right:8px;"></i>

            নতুন সাব-লেজার যোগ করুন

        </h3>

        <input type="text"
               class="modal-inp"
               id="subLedgerName"
               placeholder="সাব-লেজার নাম">

        <div class="modal-actions">

            <button class="btn-cancel"
                    type="button"
                    onclick="closeModal('subLedgerModal')">

                বাতিল

            </button>

            <button class="btn-save"
                    type="button"
                    onclick="addSubLedger()">

                সেভ করুন

            </button>

        </div>

    </div>

</div>

{{-- ==================== FUND MODAL ==================== --}}

<div class="modal-overlay"
     id="fundModal">

    <div class="modal-box">

        <h3>

            <i class="fas fa-piggy-bank"
               style="color:var(--blue);margin-right:8px;"></i>

            নতুন ফান্ড যোগ করুন

        </h3>

        <input type="text"
               class="modal-inp"
               id="fundName"
               placeholder="ফান্ড নাম">

        <div class="modal-actions">

            <button class="btn-cancel"
                    type="button"
                    onclick="closeModal('fundModal')">

                বাতিল

            </button>

            <button class="btn-save"
                    type="button"
                    id="addFundBtn"
                    onclick="addFund()">

                সেভ করুন

            </button>

        </div>

    </div>

</div>

{{-- ==================== CASHIER MODAL ==================== --}}

<div class="modal-overlay"
     id="cashierModal">

    <div class="modal-box">

        <h3>

            <i class="fas fa-user"
               style="color:var(--blue);margin-right:8px;"></i>

            নতুন ক্যাশিয়ার যোগ করুন

        </h3>

        <input type="text"
               class="modal-inp"
               id="cashierName"
               placeholder="ক্যাশিয়ারের নাম">

        <div class="modal-actions">

            <button class="btn-cancel"
                    type="button"
                    onclick="closeModal('cashierModal')">

                বাতিল

            </button>

            <button class="btn-save"
                    type="button"
                    id="addCashierBtn"
                    onclick="addCashier()">

                সেভ করুন

            </button>

        </div>

    </div>

</div>

{{-- ==================== PAYMENT METHOD MODAL ==================== --}}

<div class="pm-overlay"
     id="pmOverlay">

    <div class="pm-box">

        <div class="pm-titlebar">

            <i class="fas fa-university"
               style="color:#fff;font-size:16px;"></i>

            <span>
                ব্যাংক তথ্য / পেমেন্ট মেথড
            </span>

            <button class="pm-titlebar-close"
                    type="button"
                    onclick="closePmModal()">

                <i class="fas fa-times"></i>

            </button>

        </div>

        <div class="pm-body">

            <div class="pm-row cols2">

                <div class="pm-field">

                    <label>
                        একাউন্ট ধরন
                    </label>

                    <select id="pmType">

                        <option value="cash">
                            ক্যাশ
                        </option>

                        <option value="mobile">
                            মোবাইল ব্যাংকিং
                        </option>

                        <option value="bank">
                            ব্যাংক
                        </option>

                    </select>

                </div>

                <div class="pm-field">

                    <label>
                        ঠিকানা
                    </label>

                    <input type="text"
                           id="pmAddress"
                           placeholder="ঠিকানা (ঐচ্ছিক)">

                </div>

            </div>

            <div class="pm-row cols2">

                <div class="pm-field">

                    <label>
                        একাউন্টের নাম
                    </label>

                    <input type="text"
                           id="pmName"
                           placeholder="নাম লিখুন">

                </div>

                <div class="pm-field">

                    <label>
                        একাউন্ট নাম্বার
                    </label>

                    <input type="text"
                           id="pmNumber"
                           placeholder="একাউন্ট নাম্বার">

                </div>

            </div>

            <div class="pm-row cols2">

                <div class="pm-field">

                    <label>
                        মোবাইল ১
                    </label>

                    <input type="text"
                           id="pmMobile1"
                           placeholder="মোবাইল নং ১">

                </div>

                <div class="pm-field">

                    <label>
                        মোবাইল ২
                    </label>

                    <input type="text"
                           id="pmMobile2"
                           placeholder="মোবাইল নং ২">

                </div>

            </div>

            <div class="pm-actions">

                <button class="pm-btn pm-btn-save"
                        type="button"
                        onclick="pmSave()">

                    <i class="fas fa-save"></i>
                    Save

                </button>

                <button class="pm-btn pm-btn-update"
                        type="button"
                        onclick="pmUpdate()">

                    <i class="fas fa-sync-alt"></i>
                    Update

                </button>

                <button class="pm-btn pm-btn-new"
                        type="button"
                        onclick="pmNew()">

                    <i class="fas fa-plus"></i>
                    New

                </button>

                <button class="pm-btn pm-btn-close"
                        type="button"
                        onclick="closePmModal()">

                    <i class="fas fa-times"></i>
                    Close

                </button>

            </div>

            <table class="pm-table">

                <thead>

                    <tr>

                        <th style="width:30px;"></th>
                        <th style="width:30px;"></th>
                        <th>কোড</th>
                        <th>একাউন্ট ধরন</th>
                        <th>একাউন্টের নাম</th>
                        <th>একাউন্ট নাম্বার</th>
                        <th>মোবাইল</th>

                    </tr>

                </thead>

                <tbody id="pmTbody">

                    <tr>

                        <td colspan="7"
                            style="text-align:center;color:#bbb;padding:18px;">

                            <i class="fas fa-inbox"></i>
                            কোনো একাউন্ট নেই

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>
'use strict';

/* ============================================================
   CONFIGURATION
============================================================ */

const TOKEN =
    document.querySelector('meta[name="csrf-token"]')?.content
    || @json(csrf_token());

const currentType = @json($type);

const ROUTES = {
    paymentList: @json(route('dashboard.payment-method.index')),
    paymentStore: @json(route('dashboard.payment-method.store')),
    paymentUpdate: @json(route('dashboard.payment-method.update', ['id' => '__ID__'])),
    paymentDestroy: @json(route('dashboard.payment-method.destroy', ['id' => '__ID__'])),

    fundStore: @json(route('dashboard.fund.store')),
    ledgerStore: @json(route('dashboard.ledger.store')),
    fundLedgers: @json(route('dashboard.ledger.fundLedgers', ['id' => '__ID__'])),

    subLedgerList: @json(route('dashboard.sub-ledger.getByLedger', ['id' => '__ID__'])),
    subLedgerStore: @json(route('dashboard.sub-ledger.store')),

    transactionDestroy: @json(route('dashboard.transactions.destroy', ['id' => '__ID__']))
};

function routeWithId(url, id) {
    return url.replace('__ID__', encodeURIComponent(id));
}

/* ============================================================
   HELPERS
============================================================ */

function esc(value) {
    if (value === null || value === undefined) return '';

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function showToast(message, type = 'error') {
    const toast = document.createElement('div');

    toast.style.cssText = `
        position:fixed;
        bottom:24px;
        right:24px;
        background:${type === 'success' ? '#2e7d32' : '#c62828'};
        color:#fff;
        padding:12px 20px;
        border-radius:10px;
        z-index:999999;
        font-size:14px;
        font-family:'Hind Siliguri',sans-serif;
        box-shadow:0 4px 16px rgba(0,0,0,.2);
        display:flex;
        align-items:center;
        gap:8px;
    `;

    const icon = document.createElement('i');
    icon.className = type === 'success'
        ? 'fas fa-check-circle'
        : 'fas fa-exclamation-circle';

    const text = document.createElement('span');
    text.textContent = message;

    toast.append(icon, text);
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity .3s';
        setTimeout(() => toast.remove(), 300);
    }, 2700);
}

async function readJsonResponse(response, defaultMessage) {
    let data;

    try {
        data = await response.json();
    } catch {
        throw new Error(defaultMessage || 'সার্ভার থেকে সঠিক response পাওয়া যায়নি।');
    }

    if (!response.ok) {
        let message = data.message || defaultMessage || 'অনুরোধটি সম্পন্ন হয়নি।';

        if (data.errors) {
            const firstError = Object.values(data.errors).flat()[0];
            if (firstError) message = firstError;
        }

        throw new Error(message);
    }

    return data;
}

/* ============================================================
   TRANSACTION TYPE
============================================================ */

function setType(type) {
    saveItemsToStorage();

    window.location.href =
        @json(route('dashboard.transactions.index'))
        + '?type='
        + encodeURIComponent(type);
}

/* ============================================================
   GENERIC MODALS
============================================================ */

function openModal(id) {
    document.getElementById(id)?.classList.add('open');
}

function closeModal(id) {
    document.getElementById(id)?.classList.remove('open');
}

document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function (event) {
        if (event.target === this) {
            this.classList.remove('open');
        }
    });
});

/* ============================================================
   PAYMENT METHODS
============================================================ */

let selectedPaymentId = '';
let selectedPaymentType = 'cash';

const pmIconMap = {
    cash: 'fa-money-bill-wave',
    mobile: 'fa-mobile-alt',
    bank: 'fa-university'
};

const pmLabels = {
    cash: 'ক্যাশ',
    mobile: 'মোবাইল ব্যাংকিং',
    bank: 'ব্যাংক'
};

let pmList = [];
let pmEditingId = null;

function selectPaymentType(element, type) {
    document.querySelectorAll('#paymentTypeGrid .pay-card')
        .forEach(card => card.classList.remove('active'));

    element?.classList.add('active');
    selectedPaymentType = type;

    loadPaymentMethods(type);
}

async function loadPaymentMethods(type = 'cash') {
    try {
        const response = await fetch(ROUTES.paymentList, {
            headers: { 'Accept': 'application/json' }
        });

        const result = await readJsonResponse(
            response,
            'Payment Method লোড করা যায়নি।'
        );

        const list = Array.isArray(result)
            ? result
            : (Array.isArray(result.data) ? result.data : []);

        const select = document.getElementById('paymentMethodSelect');

        if (!select) {
            selectedPaymentId = '';
            return;
        }

        const filtered = list.filter(item => item.account_type === type);

        select.innerHTML = '<option value="">একাউন্ট নির্বাচন করুন</option>';

        filtered.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            select.appendChild(option);
        });

        if (filtered.length) {
            const existing = filtered.find(
                item => String(item.id) === String(selectedPaymentId)
            );

            selectedPaymentId = String(
                existing ? existing.id : filtered[0].id
            );

            select.value = selectedPaymentId;
        } else {
            selectedPaymentId = '';
            select.value = '';
        }

        const hiddenPayment = document.getElementById('hiddenPayment');

        if (hiddenPayment) {
            hiddenPayment.value = selectedPaymentId;
        }
    } catch (error) {
        console.error('Payment Method Load Error:', error);

        const select = document.getElementById('paymentMethodSelect');

        if (select) {
            select.innerHTML = '<option value="">লোড করা যায়নি</option>';
        }

        selectedPaymentId = '';

        const hiddenPayment = document.getElementById('hiddenPayment');

        if (hiddenPayment) {
            hiddenPayment.value = '';
        }

        showToast(error.message || 'Payment Method লোড করা যায়নি।', 'error');
    }
}

const paymentMethodSelect = document.getElementById('paymentMethodSelect');

if (paymentMethodSelect) {
    paymentMethodSelect.addEventListener('change', function () {
        selectedPaymentId = this.value;

        const hiddenPayment = document.getElementById('hiddenPayment');

        if (hiddenPayment) {
            hiddenPayment.value = this.value;
        }
    });
}

function selectPayment(element, id) {
    document.querySelectorAll('#paymentTypeGrid .pay-card')
        .forEach(card => card.classList.remove('active'));

    element?.classList.add('active');

    selectedPaymentId = String(id);

    const hiddenPayment = document.getElementById('hiddenPayment');

    if (hiddenPayment) {
        hiddenPayment.value = selectedPaymentId;
    }
}

/* ============================================================
   PAYMENT METHOD MODAL
============================================================ */

function openPmModal() {
    document.getElementById('pmOverlay')?.classList.add('open');

    pmLoadList();
}

function closePmModal() {
    document.getElementById('pmOverlay')?.classList.remove('open');
}

function pmNew() {
    pmEditingId = null;

    const fields = {
        pmType: 'cash',
        pmName: '',
        pmNumber: '',
        pmMobile1: '',
        pmMobile2: '',
        pmAddress: ''
    };

    Object.entries(fields).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) element.value = value;
    });

    document.querySelectorAll('#pmTbody tr')
        .forEach(row => row.classList.remove('pm-selected'));

    document.getElementById('pmName')?.focus();
}

async function pmLoadList() {
    try {
        const response = await fetch(ROUTES.paymentList, {
            headers: { 'Accept': 'application/json' }
        });

        const result = await readJsonResponse(
            response,
            'Payment Method তালিকা লোড করা যায়নি।'
        );

        pmList = Array.isArray(result)
            ? result
            : (Array.isArray(result.data) ? result.data : []);

        pmRenderTable();
        return pmList;
    } catch (error) {
        console.error('Payment List Error:', error);
        pmList = [];
        pmRenderTable();
        showToast(error.message, 'error');
        return [];
    }
}

function pmRenderTable() {
    const tbody = document.getElementById('pmTbody');

    if (!tbody) return;

    if (!pmList.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7"
                    style="text-align:center;color:#bbb;padding:18px;">
                    <i class="fas fa-inbox"></i>
                    কোনো একাউন্ট নেই
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = pmList.map((account, index) => `
        <tr id="pmR_${account.id}">
            <td>
                <button type="button"
                    class="pm-icon-btn edit"
                    onclick="pmEdit(${Number(account.id)})"
                    title="এডিট">
                    <i class="fas fa-pen"></i>
                </button>
            </td>

            <td>
                <button type="button"
                    class="pm-icon-btn delete"
                    onclick="pmDelete(${Number(account.id)})"
                    title="মুছুন">
                    <i class="fas fa-trash"></i>
                </button>
            </td>

            <td style="font-weight:700;color:#555;">
                ${index + 1}
            </td>

            <td>
                <span class="pm-badge ${esc(account.account_type || 'cash')}">
                    ${esc(pmLabels[account.account_type] || account.account_type || 'ক্যাশ')}
                </span>
            </td>

            <td style="font-weight:600;">${esc(account.name)}</td>
            <td>${esc(account.account_number) || '—'}</td>
            <td>${esc(account.mobile1) || '—'}</td>
        </tr>
    `).join('');
}

function pmEdit(id) {
    const account = pmList.find(item => String(item.id) === String(id));

    if (!account) {
        showToast('একাউন্টটি পাওয়া যায়নি।', 'error');
        return;
    }

    pmEditingId = account.id;

    const values = {
        pmType: account.account_type || 'cash',
        pmName: account.name || '',
        pmNumber: account.account_number || '',
        pmMobile1: account.mobile1 || '',
        pmMobile2: account.mobile2 || '',
        pmAddress: account.address || ''
    };

    Object.entries(values).forEach(([fieldId, value]) => {
        const element = document.getElementById(fieldId);
        if (element) element.value = value;
    });

    document.querySelectorAll('#pmTbody tr')
        .forEach(row => row.classList.remove('pm-selected'));

    document.getElementById('pmR_' + id)?.classList.add('pm-selected');
}

function getPaymentFormData() {
    const getValue = id =>
        document.getElementById(id)?.value?.trim() || '';

    const type = getValue('pmType') || 'cash';
    const name = getValue('pmName');

    if (!name) {
        throw new Error('একাউন্টের নাম দিন।');
    }

    if (!['cash', 'mobile', 'bank'].includes(type)) {
        throw new Error('সঠিক Payment Method type নির্বাচন করুন।');
    }

    return {
        name,
        icon: pmIconMap[type],
        account_type: type,
        account_number: getValue('pmNumber'),
        mobile1: getValue('pmMobile1'),
        mobile2: getValue('pmMobile2'),
        address: getValue('pmAddress')
    };
}

async function pmSave() {
    let payload;

    try {
        payload = getPaymentFormData();
    } catch (error) {
        showToast(error.message, 'error');
        return;
    }

    try {
        const response = await fetch(ROUTES.paymentStore, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await readJsonResponse(
            response,
            'Payment Method সেভ করা যায়নি।'
        );

        if (data.success === false) {
            throw new Error(data.message || 'Payment Method সেভ হয়নি।');
        }

        showToast(data.message || 'Payment Method সেভ হয়েছে।', 'success');

        pmNew();
        await pmLoadList();
        await loadPaymentMethods(selectedPaymentType);
    } catch (error) {
        console.error('Payment Save Error:', error);
        showToast(error.message || 'Payment Method সেভ হয়নি।', 'error');
    }
}

async function pmUpdate() {
    if (!pmEditingId) {
        showToast('আপডেট করার আগে তালিকা থেকে একটি একাউন্ট নির্বাচন করুন।', 'error');
        return;
    }

    let payload;

    try {
        payload = getPaymentFormData();
    } catch (error) {
        showToast(error.message, 'error');
        return;
    }

    try {
        const response = await fetch(
            routeWithId(ROUTES.paymentUpdate, pmEditingId),
            {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            }
        );

        const data = await readJsonResponse(
            response,
            'Payment Method আপডেট করা যায়নি।'
        );

        if (data.success === false) {
            throw new Error(data.message || 'আপডেট হয়নি।');
        }

        showToast(data.message || 'Payment Method আপডেট হয়েছে।', 'success');

        pmNew();
        await pmLoadList();
        await loadPaymentMethods(selectedPaymentType);
    } catch (error) {
        console.error('Payment Update Error:', error);
        showToast(error.message || 'আপডেট করা যায়নি।', 'error');
    }
}

async function pmDelete(id) {
    if (!confirm('এই একাউন্ট মুছবেন?')) return;

    try {
        const response = await fetch(
            routeWithId(ROUTES.paymentDestroy, id),
            {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': TOKEN,
                    'Accept': 'application/json'
                }
            }
        );

        const data = await readJsonResponse(
            response,
            'Payment Method মুছতে সমস্যা হয়েছে।'
        );

        if (data.success === false) {
            throw new Error(data.message || 'মুছতে পারেনি।');
        }

        pmList = pmList.filter(
            account => String(account.id) !== String(id)
        );

        if (String(pmEditingId) === String(id)) {
            pmNew();
        }

        pmRenderTable();
        await loadPaymentMethods(selectedPaymentType);

        showToast(data.message || 'একাউন্ট মুছে ফেলা হয়েছে।', 'success');
    } catch (error) {
        console.error('Payment Delete Error:', error);
        showToast(error.message || 'একাউন্ট মুছতে পারেনি।', 'error');
    }
}

/* ============================================================
   SUB LEDGER LOAD
============================================================ */

async function loadSubLedgers(ledgerId, selectedId = null) {
    const select = document.getElementById('subLedgerSelect');

    if (!select) return;

    if (!ledgerId) {
        select.innerHTML = '<option value="">সিলেক্ট করুন</option>';
        return;
    }

    select.innerHTML = '<option value="">লোড হচ্ছে...</option>';

    try {
        const response = await fetch(
            routeWithId(ROUTES.subLedgerList, ledgerId),
            { headers: { 'Accept': 'application/json' } }
        );

        const result = await readJsonResponse(
            response,
            'Sub Ledger লোড করা যায়নি।'
        );

        const list = Array.isArray(result)
            ? result
            : (Array.isArray(result.data) ? result.data : []);

        select.innerHTML = '<option value="">সিলেক্ট করুন</option>';

        list.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            select.appendChild(option);
        });

        if (selectedId) {
            select.value = String(selectedId);
        }
    } catch (error) {
        console.error('Sub Ledger Load Error:', error);
        select.innerHTML = '<option value="">লোড করা যায়নি</option>';
        showToast(error.message, 'error');
    }
}

document.getElementById('ledgerSelect')?.addEventListener('change', function () {
    loadSubLedgers(this.value);
});

/* ============================================================
   ITEMS + LOCAL STORAGE
============================================================ */

const ITEMS_STORAGE_KEY = 'madrasa_erp_transaction_items_{{ $type }}';
let items = [];

function loadItemsFromStorage() {
    try {
        const stored = localStorage.getItem(ITEMS_STORAGE_KEY);
        const parsed = stored ? JSON.parse(stored) : [];
        items = Array.isArray(parsed) ? parsed : [];
    } catch (error) {
        console.error('Items Storage Load Error:', error);
        items = [];
    }
}

function saveItemsToStorage() {
    try {
        localStorage.setItem(ITEMS_STORAGE_KEY, JSON.stringify(items));
    } catch (error) {
        console.error('Items Storage Save Error:', error);
        showToast('ব্রাউজারে আইটেম সংরক্ষণ করা যায়নি।', 'error');
    }
}

function addItem() {
    const ledger = document.getElementById('ledgerSelect');
    const subLedger = document.getElementById('subLedgerSelect');
    const amountInput = document.getElementById('itemAmount');
    const descriptionInput = document.getElementById('itemDesc');

    if (!ledger || !subLedger || !amountInput || !descriptionInput) {
        showToast('আইটেম ফর্মের প্রয়োজনীয় field পাওয়া যায়নি।', 'error');
        return;
    }

    if (!ledger.value) {
        showToast('আগে একটি লেজার সিলেক্ট করুন।', 'error');
        return;
    }

    const amount = Number.parseFloat(amountInput.value);

    if (!Number.isFinite(amount) || amount <= 0) {
        showToast('সঠিক পরিমাণ দিন।', 'error');
        return;
    }

    items.push({
        ledger_id: ledger.value,
        ledger_name: ledger.options[ledger.selectedIndex]?.text || '',
        sub_ledger_id: subLedger.value || null,
        sub_ledger_name: subLedger.value
            ? (subLedger.options[subLedger.selectedIndex]?.text || '')
            : '',
        amount,
        description: descriptionInput.value.trim()
    });

    saveItemsToStorage();

    amountInput.value = '';
    descriptionInput.value = '';

    renderItems();
    showToast('আইটেম যুক্ত হয়েছে।', 'success');
}

function removeItem(index) {
    if (!confirm('এই আইটেমটি মুছবেন?')) return;

    items.splice(index, 1);
    saveItemsToStorage();
    renderItems();
}

function clearItems() {
    if (!items.length) return;
    if (!confirm('সব আইটেম মুছবেন?')) return;

    items = [];
    saveItemsToStorage();
    renderItems();
}

function renderItems() {
    const tbody = document.getElementById('itemsBody');
    const totalElement = document.getElementById('itemsTotal');
    const grandTotalElement = document.getElementById('grandTotal');
    const itemsJson = document.getElementById('itemsJson');

    if (!tbody || !totalElement || !grandTotalElement || !itemsJson) return;

    if (!items.length) {
        tbody.innerHTML = `
            <tr class="empty-row">
                <td colspan="6">
                    <i class="fas fa-inbox"></i>
                    কোনো আইটেম যোগ হয়নি
                </td>
            </tr>
        `;

        totalElement.textContent = '৳ ০';
        grandTotalElement.textContent = '৳ ০';
        itemsJson.value = '[]';
        return;
    }

    let total = 0;

    tbody.innerHTML = items.map((item, index) => {
        total += Number(item.amount) || 0;

        return `
            <tr>
                <td>
                    <span class="serial-badge">${index + 1}</span>
                </td>
                <td>${esc(item.ledger_name)}</td>
                <td>${esc(item.sub_ledger_name) || '—'}</td>
                <td>${esc(item.description) || '—'}</td>
                <td style="font-weight:700;">
                    ৳ ${Number(item.amount).toLocaleString()}
                </td>
                <td>
                    <button type="button"
                        class="del-row-btn"
                        onclick="removeItem(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    const formattedTotal = '৳ ' + total.toLocaleString();

    totalElement.textContent = formattedTotal;
    grandTotalElement.textContent = formattedTotal;
    itemsJson.value = JSON.stringify(items);
}

/* ============================================================
   FUND
============================================================ */

async function addFund() {
    const input = document.getElementById('fundName');
    const button = document.getElementById('addFundBtn');

    if (!input) return;

    const name = input.value.trim();

    if (!name) {
        showToast('Fund-এর নাম দিন।', 'error');
        input.focus();
        return;
    }

    if (button) {
        button.disabled = true;
        button.textContent = 'সংরক্ষণ হচ্ছে...';
    }

    try {
        const response = await fetch(ROUTES.fundStore, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': TOKEN
            },
            body: JSON.stringify({
                name,
                type: currentType,
                balance: 0
            })
        });

        const data = await readJsonResponse(response, 'Fund সংরক্ষণ করা যায়নি।');

        if (data.success === false) {
            throw new Error(data.message || 'Fund সংরক্ষণ হয়নি।');
        }

        const fund = data.fund || data.data || data;

        if (fund?.id) {
            const select = document.getElementById('fundSelect');

            if (select && (!fund.type || fund.type === currentType)) {
                select.add(new Option(fund.name || name, fund.id, true, true));
                select.dispatchEvent(new Event('change'));
            }
        }

        input.value = '';
        closeModal('fundModal');

        showToast(data.message || 'Fund সংরক্ষণ হয়েছে।', 'success');
    } catch (error) {
        console.error('Fund Store Error:', error);
        showToast(error.message || 'Fund সংরক্ষণ করা যায়নি।', 'error');
    } finally {
        if (button) {
            button.disabled = false;
            button.textContent = 'সেভ করুন';
        }
    }
}

/* ============================================================
   LEDGER
============================================================ */

async function addLedger() {
    const name = document.getElementById('ledgerName')?.value.trim() || '';
    const hiddenType = document.getElementById('hiddenType');
    const type = hiddenType?.value || currentType;
    const fundId = document.getElementById('fundSelect')?.value || '';

    if (!name) {
        showToast('লেজারের নাম দিন।', 'error');
        return;
    }

    if (!fundId) {
        showToast('আগে একটি ফান্ড সিলেক্ট করুন।', 'error');
        return;
    }

    const button = document.querySelector('#ledgerModal .btn-save');
    const originalText = button?.textContent || 'সেভ করুন';

    if (button) {
        button.disabled = true;
        button.textContent = 'সেভ হচ্ছে...';
    }

    try {
        const response = await fetch(ROUTES.ledgerStore, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name,
                type,
                fund_id: fundId
            })
        });

        const data = await readJsonResponse(response, 'লেজার সেভ করা যায়নি।');

        if (data.success === false) {
            throw new Error(data.message || 'লেজার সেভ হয়নি।');
        }

        const ledger = data.ledger || data.data || data;
        const ledgerId = ledger.id;
        const ledgerName = ledger.name || name;

        const select = document.getElementById('ledgerSelect');

        if (select && ledgerId) {
            select.add(new Option(ledgerName, ledgerId, true, true));
            select.dispatchEvent(new Event('change'));
        }

        const input = document.getElementById('ledgerName');
        if (input) input.value = '';

        closeModal('ledgerModal');
        showToast(data.message || `${ledgerName} যোগ হয়েছে।`, 'success');

        if (ledgerId) {
            await loadSubLedgers(ledgerId);
        }
    } catch (error) {
        console.error('Ledger Store Error:', error);
        showToast(error.message || 'লেজার সেভ হয়নি।', 'error');
    } finally {
        if (button) {
            button.disabled = false;
            button.textContent = originalText;
        }
    }
}

/* ============================================================
   FUND CHANGE -> LOAD LEDGERS
============================================================ */

document.getElementById('fundSelect')?.addEventListener('change', async function () {
    const fundId = this.value;
    const hiddenType = document.getElementById('hiddenType');
    const type = hiddenType?.value || currentType;

    const ledgerSelect = document.getElementById('ledgerSelect');
    const subLedgerSelect = document.getElementById('subLedgerSelect');

    if (!ledgerSelect || !subLedgerSelect) return;

    if (!fundId) {
        ledgerSelect.innerHTML = '<option value="">সিলেক্ট করুন</option>';
        subLedgerSelect.innerHTML = '<option value="">সিলেক্ট করুন</option>';
        return;
    }

    ledgerSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
    subLedgerSelect.innerHTML = '<option value="">সিলেক্ট করুন</option>';

    try {
        const url = routeWithId(ROUTES.fundLedgers, fundId)
            + '?type=' + encodeURIComponent(type);

        const response = await fetch(url, {
            headers: { 'Accept': 'application/json' }
        });

        const result = await readJsonResponse(response, 'Ledger লোড করা যায়নি।');

        const list = Array.isArray(result)
            ? result
            : (Array.isArray(result.data) ? result.data : []);

        ledgerSelect.innerHTML = '<option value="">সিলেক্ট করুন</option>';

        list.forEach(item => {
            ledgerSelect.add(new Option(item.name, item.id));
        });

        subLedgerSelect.innerHTML = '<option value="">সিলেক্ট করুন</option>';
    } catch (error) {
        console.error('Fund Ledger Load Error:', error);
        ledgerSelect.innerHTML = '<option value="">লোড করা যায়নি</option>';
        subLedgerSelect.innerHTML = '<option value="">সিলেক্ট করুন</option>';
        showToast(error.message, 'error');
    }
});

/* ============================================================
   SUB LEDGER SAVE
============================================================ */

async function addSubLedger() {
    const name = document.getElementById('subLedgerName')?.value.trim() || '';
    const ledgerId = document.getElementById('ledgerSelect')?.value || '';

    if (!name) {
        showToast('সাব-লেজারের নাম দিন।', 'error');
        return;
    }

    if (!ledgerId) {
        showToast('আগে একটি লেজার সিলেক্ট করুন।', 'error');
        return;
    }

    const button = document.querySelector('#subLedgerModal .btn-save');
    const originalText = button?.textContent || 'সেভ করুন';

    if (button) {
        button.disabled = true;
        button.textContent = 'সেভ হচ্ছে...';
    }

    try {
        const response = await fetch(ROUTES.subLedgerStore, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name,
                ledger_id: ledgerId
            })
        });

        const data = await readJsonResponse(response, 'সাব-লেজার সেভ করা যায়নি।');

        if (data.success === false) {
            throw new Error(data.message || 'সাব-লেজার সেভ হয়নি।');
        }

        const subLedger = data.subLedger || data.sub_ledger || data.data || data;
        const subLedgerId = subLedger.id;
        const subLedgerName = subLedger.name || name;

        await loadSubLedgers(ledgerId, subLedgerId);

        const input = document.getElementById('subLedgerName');
        if (input) input.value = '';

        closeModal('subLedgerModal');
        showToast(data.message || `${subLedgerName} যোগ হয়েছে।`, 'success');
    } catch (error) {
        console.error('Sub Ledger Store Error:', error);
        showToast(error.message || 'সাব-লেজার সেভ হয়নি।', 'error');
    } finally {
        if (button) {
            button.disabled = false;
            button.textContent = originalText;
        }
    }
}

/* ============================================================
   SUBMIT TRANSACTION
============================================================ */

async function submitForm() {
    const fundSelect = document.getElementById('fundSelect');

    if (!fundSelect?.value) {
        showToast('দয়া করে একটি ফান্ড সিলেক্ট করুন।', 'error');
        return;
    }

    if (!items.length) {
        showToast('কমপক্ষে একটি আইটেম যোগ করুন।', 'error');
        return;
    }

    if (!selectedPaymentId) {
        showToast('এই ধরনের কোনো Payment Method নেই। আগে Payment Method যোগ করুন।', 'error');
        return;
    }

    const form = document.getElementById('mainForm');

    if (!form) {
        showToast('Transaction form পাওয়া যায়নি।', 'error');
        return;
    }

    const hiddenPayment = document.getElementById('hiddenPayment');
    if (hiddenPayment) hiddenPayment.value = selectedPaymentId;

    const itemsJson = document.getElementById('itemsJson');
    if (itemsJson) itemsJson.value = JSON.stringify(items);

    const hiddenType = document.getElementById('hiddenType');
    if (hiddenType) hiddenType.value = currentType;

    const button = document.querySelector('.confirm-btn');

    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> সেভ হচ্ছে...';
    }

    sessionStorage.setItem(
        'transaction_submitting_{{ $type }}',
        '1'
    );

    form.submit();
}

/* ============================================================
   DELETE TRANSACTION
============================================================ */

async function deleteTransaction(id, button) {
    if (!confirm('এই লেনদেন মুছবেন?')) return;

    try {
        const response = await fetch(
            routeWithId(ROUTES.transactionDestroy, id),
            {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': TOKEN,
                    'Accept': 'application/json'
                }
            }
        );

        const data = await readJsonResponse(response, 'লেনদেন মুছতে সমস্যা হয়েছে।');

        if (data.success === false) {
            throw new Error(data.message || 'লেনদেন মুছতে পারেনি।');
        }

        button?.closest('tr')?.remove();

        showToast(data.message || 'লেনদেন মুছে ফেলা হয়েছে।', 'success');

        setTimeout(() => location.reload(), 700);
    } catch (error) {
        console.error('Transaction Delete Error:', error);
        showToast(error.message || 'লেনদেন মুছতে সমস্যা হয়েছে।', 'error');
    }
}

/* ============================================================
   TABLE SEARCH
============================================================ */

function filterTable(input) {
    const query = input.value.toLowerCase();

    document.querySelectorAll('#transTable tbody tr').forEach(row => {
        if (row.classList.contains('empty-row')) return;

        row.style.display = row.textContent.toLowerCase().includes(query)
            ? ''
            : 'none';
    });
}

/* ============================================================
   INITIAL LOAD
============================================================ */

document.addEventListener('DOMContentLoaded', async function () {
    loadItemsFromStorage();
    renderItems();

    // Cash is the default payment type.
    const cashCard = document.querySelector(
        '#paymentTypeGrid .pay-card[data-type="cash"]'
    );

    if (cashCard) {
        cashCard.classList.add('active');
    }

    selectedPaymentType = 'cash';
    await loadPaymentMethods('cash');

    const fundSelect = document.getElementById('fundSelect');

    if (fundSelect?.value) {
        fundSelect.dispatchEvent(new Event('change'));
    }

    const ledgerSelect = document.getElementById('ledgerSelect');

    if (ledgerSelect?.value) {
        await loadSubLedgers(ledgerSelect.value);
    }

    const hiddenType = document.getElementById('hiddenType');

    if (hiddenType) {
        hiddenType.value = currentType;
    }

    const submitting = sessionStorage.getItem(
        'transaction_submitting_{{ $type }}'
    );

    @if(session('success'))
        if (submitting) {
            localStorage.removeItem(ITEMS_STORAGE_KEY);
            items = [];
            renderItems();

            sessionStorage.removeItem(
                'transaction_submitting_{{ $type }}'
            );
        }
    @endif
});

/* ============================================================
   EXPOSE FUNCTIONS FOR INLINE HTML onclick HANDLERS
============================================================ */

Object.assign(window, {
    setType,
    openModal,
    closeModal,

    selectPayment,
    selectPaymentType,

    addItem,
    removeItem,
    clearItems,
    submitForm,

    deleteTransaction,
    filterTable,

    addFund,
    addLedger,
    addSubLedger,

    openPmModal,
    closePmModal,
    pmSave,
    pmUpdate,
    pmNew,
    pmEdit,
    pmDelete
});
</script>


@endsection