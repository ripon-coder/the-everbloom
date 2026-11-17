<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order Status Update</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f241f; line-height: 1.5; max-width: 600px; margin: 0 auto; padding: 24px;">
    <h1 style="font-size: 20px; margin-bottom: 8px;">Order Status Update</h1>
    <p>Your order <strong>{{ $order->order_number }}</strong> is now <strong>{{ $order->status }}</strong>.</p>
    @if(!empty($order->tracking_number) && $order->tracking_number !== 'Pending assignment')
        <p>Tracking Number: <strong>{{ $order->tracking_number }}</strong></p>
    @endif
    <p>Total: <strong>{{ format_price($order->total) }}</strong></p>
</body>
</html>

