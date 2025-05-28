<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Hóa đơn</title>

    <style type="text/css">
        * {
            font-family: "DejaVu Sans", sans-serif;
        }
        table {
            font-size: x-small;
        }
        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }
        .gray {
            background-color: lightgray;
        }
        .font {
            font-size: 15px;
        }
        .authority {
            float: right;
        }
        .authority h5 {
            margin-top: -10px;
            color: green;
            margin-left: 35px;
        }
        .thanks p {
            color: green;
            font-size: 16px;
            font-weight: normal;
            font-family: serif;
            margin-top: 20px;
        }
    </style>

</head>
<body>

    <table width="100%" style="background: #F7F7F7; padding: 0 20px;">
        <tr>
            <td valign="top">
                <h2 style="color: green; font-size: 26px;"><strong>GREEN FOOD</strong></h2>
            </td>
            <td align="right">
                <pre class="font">
Trụ sở chính Green Food
Email: hoaibao@gmail.com
SĐT: 0772435566
Hòa Quý - Ngũ Hành Sơn - Đà Nẵng
                </pre>
            </td>
        </tr>
    </table>

    <table width="100%" style="background: white; padding: 2px;"></table>

    <table width="100%" style="background: #F7F7F7; padding: 0 5px;" class="font">
        <tr>
            <td>
                <p class="font" style="margin-left: 20px;">
                    <strong>Họ tên:</strong> {{ $order->name }} <br>
                    <strong>Email:</strong> {{ $order->email }} <br>
                    <strong>Số điện thoại:</strong> {{ $order->phone }} <br>
                    <strong>Địa chỉ:</strong> {{ $order->address }} <br>
                    <strong>Mã bưu điện:</strong> Post Code
                </p>
            </td>
            <td>
                <p class="font">
                    <h3><span style="color: green;">Mã hóa đơn:</span> #{{ $order->invoice_no }}</h3>
                    Ngày đặt hàng: {{ $order->order_date }} <br>
                    Phương thức thanh toán: {{ $order->payment_method }}
                </p>
            </td>
        </tr>
    </table>

    <br/>
    <h3>Sản phẩm</h3>

    <table width="100%">
        <thead style="background-color: green; color:#FFFFFF;">
            <tr class="font">
                <th>Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Mã</th>
                <th>Số lượng</th>
                <th>Tên cửa hàng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orderItem as $item)
                <tr class="font">
                    <td align="center">
                        <img src="{{ public_path($item->product->productTemplate->image) }}" height="60px;" width="60px;" alt="">
                    </td>
                    <td align="center">{{ $item->product->productTemplate->name }}</td>
                    <td align="center">{{ $item->product->productTemplate->code }}</td>
                    <td align="center">{{ $item->qty }}</td>
                    <td align="center">{{ $item->product->client->name }}</td>
                    <td align="center">{{ number_format($item->price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <table width="100%" style="padding: 0 10px;">
        <tr>
            <td align="right">
              <h2><span style="color: green;">Tổng giá gốc:</span> {{ number_format($totalPrice, 0, ',', '.') }} VNĐ</h2>
              <h2><span style="color: green;">Mã khuyến mãi:</span> {{ round($discountPercent, 2) }}%</h2>
              <h2><span style="color: green;">Tiết kiệm:</span> {{ number_format($discountAmount, 0, ',', '.') }} VNĐ</h2>
              <h2 style="color: rgb(0, 196, 0);"><span >Tổng thanh toán:</span> {{ number_format($totalAmount, 0, ',', '.') }} VNĐ</h2>
            </td>
        </tr>
    </table>

    <div class="mt-3" style="color: green;">
        <p>Cảm ơn bạn đã mua sản phẩm!</p>
    </div>

    <div class="authority float-right mt-5">
        <p>-----------------------------------</p>
        <h5>Chữ ký người có thẩm quyền:</h5>
    </div>

</body>
</html>
