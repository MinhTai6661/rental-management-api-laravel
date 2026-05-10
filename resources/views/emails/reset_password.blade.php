<x-mail::message>
    <style>
        .mail-container {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #334155;
            line-height: 1.6;
        }

        .header-title {
            color: #1e293b;
            font-size: 24px;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .user-highlight {
            color: #4f46e5;
            font-weight: 600;
        }

        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: center;
        }

        /* Ghi đè màu của component button mặc định sang màu Indigo của MyRent */
        .button-primary {
            background-color: #4f46e5 !important;
            border-color: #4f46e5 !important;
        }

        .alert-text {
            font-size: 14px;
            color: #64748b;
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 10px;
            margin: 20px 0;
        }

        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
    </style>

    <div class="mail-container">
        <h1 class="header-title">Đặt lại mật khẩu 🔑</h1>

        <p>Chào <span class="user-highlight">{{ $user->name }}</span>,</p>

        <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình trên hệ thống **MyRent**. Để hoàn tất quá trình đặt lại mật khẩu, vui lòng nhấn vào nút bên dưới:</p>

        {{-- Sử dụng component button của Laravel --}}
        <x-mail::button :url="$url" color="primary">
            Đặt lại mật khẩu
        </x-mail::button>


        <div class="alert-text">
            <strong>Lưu ý:</strong> Liên kết đặt lại mật khẩu này sẽ hết hạn sau {{ $expireHours }} giờ. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.
        </div>

        <p class="footer-text">
            © {{ date('Y') }} MyRent. Tất cả quyền được bảo lưu.<br>
            Hệ thống quản lý thuê phòng thông minh.
        </p>
    </div>
</x-mail::message>