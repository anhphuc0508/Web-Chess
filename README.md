Dự án này là một ứng dụng Web chơi cờ vua trực tuyến cơ bản, được xây dựng trên nền tảng PHP, MySQ và Node.js.
Các tính năng chính:
+	Giao diện và tương tác: Xây dựng giao diện bàn cờ thân thiện, hỗ trợ thao tác điều khiển mượt mà, kiểm soát chặt chẽ các nước đi hầu như tuân theo đúng luật cờ vua quốc tế.
+	Chế độ ngoại tuyến: Tích hợp stockfish với nhiều cấp độ khó khác nhau để người chơi có thể tự do tập luyện.
+	Chế độ trực tuyến: Triển khai luồng ghép trận ngẫu nhiên và hệ thống gửi lời mời thách đấu giữa những người chơi đang online.
+	Hệ thống quản lý và xếp hạng: Quản lý thông tin tài khoản người dùng an toàn. Tính toán và cập nhật điểm số minh bạch sau mỗi ván đấu, từ đó hiển thị các kỳ thủ trên bảng xếp hạng của hệ thống.
  
Hướng dẫn cài đặt và chạy chương trình chi tiết

Ứng dụng Cờ Vua này hoạt động dựa trên mô hình:
- Backend / Web: PHP (dùng để xử lý logic web cơ bản, giao diện, đăng nhập, đăng ký).
- Cơ sở dữ liệu: MySQL (lưu trữ thông tin người dùng, lịch sử đấu).
- Server Real-time: Node.js kết hợp Socket.io (xử lý logic trò chơi thời gian thực và ghép trận).


PHẦN 1: CHUẨN BỊ MÔI TRƯỜNG

1. Cài đặt Web Server (PHP & MySQL):
   - Tải và cài đặt Laragon (khuyên dùng, tại https://laragon.org/download).
   - Mở Laragon lên và nhấn "Start All" để khởi động Web Server và Database.

2. Cài đặt Node.js:
   - Truy cập trang chủ https://nodejs.org.
   - Tải và cài đặt phiên bản LTS (Long Term Support).
   - Để kiểm tra cài đặt thành công, mở Terminal (hoặc Command Prompt) và gõ lệnh: `node -v`. Nếu hiển thị số phiên bản (ví dụ: v18.x.x) là thành công.

3. Đưa source code vào đúng thư mục:
   - Nếu dùng Laragon: Đặt toàn bộ thư mục dự án (`chess`) vào thư mục gốc (ví dụ `D:\laragon\www\`).

PHẦN 2: THIẾT LẬP CƠ SỞ DỮ LIỆU (DATABASE)

1. Mở công cụ quản lý Database:
   - Dùng công cụ SQL (bằng cách nhấn nút "Database" trên giao diện Laragon).
     <img width="1026" height="655" alt="image" src="https://github.com/user-attachments/assets/1f41ae7d-b131-45ae-ab6e-5b59bbd5ccae" />

2. Tạo Database mới:
   - Nhấn "New" để tạo một cơ sở dữ liệu mới.
     <img width="853" height="598" alt="Screenshot 2026-06-24 203157" src="https://github.com/user-attachments/assets/d4e20e75-e277-4ec9-8366-2a3c2b5a1478" />
   - Đặt tên cơ sở dữ liệu là: `chess_db`
   - Chọn Network yype là : Maria or MySQL
   - Đăng nhập (thường User mặc định là `root`, Password để trống).
   - Port mặc định là 3306
   - Nhấn Open.
     
3. Import dữ liệu cấu trúc bảng:
   - Bấm vào database `chess_db` vừa tạo bên cột trái.
   - Chọn tab Query và chọn New query tab
     <img width="1806" height="776" alt="image" src="https://github.com/user-attachments/assets/a80feffb-d503-4aa0-8ed5-d108fe6f74af" />
   - Copy toàn bộ code của file CSDL dán vào tab Query
     <img width="1918" height="1010" alt="image" src="https://github.com/user-attachments/assets/5e189cf1-21ad-43d1-a57b-440bf00fe05b" />
   - Bôi đen toàn bộ và bấm vào nút Execute hình tam giác xanh
     <img width="1918" height="1072" alt="image" src="https://github.com/user-attachments/assets/521270f9-0e17-4f6d-86e5-7f6e4f9390db" />
   - Sau đó bấm vào tab Table phía trên trái, giao diện sẽ hiển thị các bảng đã được tạo
     <img width="1918" height="930" alt="image" src="https://github.com/user-attachments/assets/5cfcf01e-13a5-493f-be9d-cc36f761ffac" />

PHẦN 3: CẤU HÌNH KẾT NỐI

1. Cấu hình cho PHP:
   - Mở file `config.php` nằm ở thư mục gốc của dự án (ví dụ `d:\laragon\www\chess\config.php`).
   - Tìm đoạn mã kết nối và đảm bảo các thông số khớp với MySQL:
     $host = 'localhost';
     $dbname = 'chess_db'; 
     $db_user = 'root';    
     $db_pass = ''; 

2. Cấu hình cho Node.js:
   - Mở file `server.js` ở thư mục gốc.
   - Tìm đến dòng khai báo kết nối cơ sở dữ liệu `mysql.createConnection(...)` hoặc `mysql.createPool(...)`.
   - Kiểm tra thông tin `user: 'root'` và `password: ''` xem đã chính xác với máy của bạn chưa.


PHẦN 4: CÀI ĐẶT THƯ VIỆN & CHẠY SERVER REAL-TIME

1. Mở Terminal / Command Prompt:
   - Mở thư mục dự án bằng Visual Studio Code (hoặc trình soạn thảo bạn quen dùng).
   - Mở Terminal tích hợp trong VS Code (chọn Menu Terminal > New Terminal).

2. Cài đặt các gói NPM:
   - Đảm bảo Terminal đang ở đúng thư mục dự án (ví dụ `d:\laragon\www\chess>`).
   - Gõ lệnh sau và nhấn Enter:
     npm install
   - Quá trình này sẽ tải về thư mục `node_modules` chứa các thư viện bắt buộc để chạy game như `socket.io` và `mysql2`. Đợi vài giây đến khi chạy xong.

3. Khởi động WebSocket Server (Server Game):
   - Vẫn trong Terminal đó, gõ lệnh:
     node server.js
   - LƯU Ý QUAN TRỌNG: Bạn PHẢI giữ nguyên cửa sổ Terminal này (không được tắt) trong suốt quá trình chơi game. Nếu tắt, tính năng chơi 2 người  sẽ mất kết nối.

PHẦN 5: CHẠY TRANG WEB VÀ KIỂM TRA

Truy cập vào Game:
   - Mở trình duyệt web (Chrome, Edge, Safari...).
   - Nhập đường dẫn sau để vào trang chủ:
     http://localhost/chess
   - Bạn sẽ thấy giao diện trang chủ của ứng dụng. Hãy thử đăng ký một tài khoản mới và đăng nhập.


