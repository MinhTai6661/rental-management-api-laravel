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
        <h1 class="header-title">Xác thực tài khoản MyRent 🔑</h1>

        <p>Chào <span class="user-highlight">{{ $user->name }}</span>,</p>

        <p>Cảm ơn bạn đã đăng ký thành viên trên hệ thống **MyRent**. Để hoàn tất quá trình đăng ký và kích hoạt tài khoản, vui lòng nhấn vào nút xác nhận bên dưới:</p>

        {{-- Sử dụng component button của Laravel --}}
        <x-mail::button :url="$url" color="primary">
            Xác nhận đăng ký
        </x-mail::button>

        <div class="info-card">
            <p style="margin: 0;">Vai trò tài khoản: <strong>{{ $user->role }}</strong></p>
        </div>

        <div class="alert-text">
            <strong>Lưu ý:</strong> Liên kết xác nhận này sẽ hết hạn sau {{ $expireHours }} giờ. Nếu người dùng không thực hiện đăng ký này, vui lòng bỏ qua email này.
        </div>

        <p class="footer-text">
            © {{ date('Y') }} MyRent. Tất cả quyền được bảo lưu.<br>
            Hệ thống quản lý thuê phòng thông minh.
        </p>
    </div>
</x-mail::message>