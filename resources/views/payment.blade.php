<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Batch</title>

    <!-- Midtrans Snap -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            margin: 0;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            text-align: center;
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #222;
        }

        .subtitle {
            color: #777;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .payment-info {
            text-align: left;
            background: #f9fafb;
            border-radius: 12px;
            padding: 15px 18px;
            margin-bottom: 25px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .label {
            color: #777;
        }

        .value {
            font-weight: 600;
            color: #222;
        }

        .total {
            font-size: 18px;
            color: #2c7be5;
            font-weight: bold;
        }

        .btn-pay {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #2c7be5;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .btn-pay:hover {
            background: #1a68d1;
        }

        .btn-back {
            display: block;
            margin-top: 10px;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #2c7be5;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            color: #2c7be5;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .btn-back:hover {
            background: #eef5ff;
        }

        .footer-note {
            margin-top: 15px;
            font-size: 12px;
            color: #999;
        }

        @media (max-width: 480px) {
            .card {
                margin: 15px;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="card">
    <div class="title">Pembayaran Batch</div>
    <div class="subtitle">Selesaikan pembayaran untuk melanjutkan</div>

    <div class="payment-info">

        <div class="row">
            <div class="label">Order ID</div>
            <div class="value">{{ $payment->order_id }}</div>
        </div>

        <div class="row">
            <div class="label">Jumlah Peserta</div>
            <div class="value">{{ $payment->participants ?? 0 }} orang</div>
        </div>

        <div class="row">
            <div class="label">Harga per Peserta</div>
            <div class="value">Rp {{ number_format(200000, 0, ',', '.') }}</div>
        </div>

        <hr style="border: none; border-top: 1px dashed #ddd; margin:10px 0">

        <div class="row">
            <div class="label">Subtotal</div>
            <div class="value">
                Rp {{ number_format(($payment->participants ?? 0) * 200000, 0, ',', '.') }}
            </div>
        </div>

        <div class="row">
            <div class="label">PPN (11%)</div>
            <div class="value">
                Rp {{ number_format((($payment->participants ?? 0) * 200000) * 0.11, 0, ',', '.') }}
            </div>
        </div>

        <div class="row">
            <div class="label">Kode Unik</div>
            <div class="value">+{{ $payment->unique_code ?? 0 }}</div>
        </div>

        <hr style="border: none; border-top: 1px solid #eee; margin:12px 0">

        <div class="row">
            <div class="label"><strong>Total Pembayaran</strong></div>
            <div class="value total">
                Rp {{ number_format($payment->amount, 0, ',', '.') }}
            </div>
        </div>

    </div>

    <button id="pay-button" class="btn-pay">
        Bayar Sekarang
    </button>

    <a href="/client/batches" class="btn-back">
        ← Kembali ke Batch
    </a>

    <div class="footer-note">
        Pembayaran aman melalui Midtrans
    </div>
</div>

<script>
    document.getElementById('pay-button').onclick = function () {
        this.innerText = "Memproses...";
        this.disabled = true;

        snap.pay('{{ $payment->snap_token }}', {
            onSuccess: function(result){
                window.location.href = "/client/batches";
            },
            onPending: function(result){
                alert("Menunggu pembayaran!");
            },
            onError: function(result){
                alert("Pembayaran gagal!");
            },
            onClose: function(){
                alert("Kamu menutup popup tanpa bayar");
            }
        });
    };
</script>

</body>
</html>