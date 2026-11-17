<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f241f; line-height: 1.5; max-width: 600px; margin: 0 auto; padding: 24px;">
    <h1 style="font-size: 22px; margin-bottom: 8px;">Thank you for your order</h1>
    <p style="margin-top: 0; color: #555;">Order Number: <strong>{{ $order->order_number }}</strong></p>
    <p>Status: {{ $order->status }} &middot; Payment: {{ $order->payment_status }}</p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <thead>
            <tr style="border-bottom: 1px solid #ddd; text-align: left;">
                <th style="padding: 8px 0;">Item</th>
                <th style="padding: 8px 0;">Qty</th>
                <th style="padding: 8px 0; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 8px 0;">
                    {{ $item->name }}
                    @if($item->variant)
                        <div style="font-size: 12px; color: #666;">{{ $item->variant }}</div>
                    @endif
                </td>
                <td style="padding: 8px 0;">{{ $item->qty }}</td>
                <td style="padding: 8px 0; text-align: right;">{{ format_price($item->line_total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin: 4px 0;">Subtotal: {{ format_price($order->subtotal) }}</p>
    @if($order->discount_amount > 0)
        <p style="margin: 4px 0;">Discount ({{ $order->discount_code }}): -{{ format_price($order->discount_amount) }}</p>
    @endif
    <p style="margin: 4px 0;">Shipping: {{ $order->shipping_cost > 0 ? format_price($order->shipping_cost) : 'Free' }}</p>
    <p style="margin: 8px 0; font-size: 16px;"><strong>Total: {{ format_price($order->total) }}</strong></p>
</body>
</html>

