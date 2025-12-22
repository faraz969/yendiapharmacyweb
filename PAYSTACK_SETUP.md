# Paystack Payment Integration Setup

## Environment Variables

Add the following to your `.env` file:

```env
PAYSTACK_PUBLIC_KEY=pk_test_34cbcaf072d4a0b34f01d4d07e8b5f0354e05b6f
PAYSTACK_SECRET_KEY=sk_test_97b96ec84a47292182732394397dc428d9451c92
```

## Database Migration

The payment fields have been added to the `orders` table:
- `payment_status` (enum: pending, paid, failed, refunded)
- `payment_method` (string, nullable)
- `payment_reference` (string, nullable)
- `paid_at` (timestamp, nullable)

Run the migration:
```bash
php artisan migrate
```

## Payment Flow

1. **Checkout**: User fills checkout form and submits
2. **Order Creation**: Order is created with `payment_status='pending'`
3. **Payment Page**: User is redirected to payment page
4. **Paystack Popup**: User clicks "Pay with Paystack" button
5. **Payment Initialization**: Backend initializes Paystack transaction
6. **Payment Processing**: User completes payment via Paystack popup
7. **Payment Verification**: Backend verifies payment status
8. **Order Update**: Order `payment_status` is updated to 'paid'
9. **Cart Clear**: Cart is cleared after successful payment
10. **Success Page**: User is redirected to order success page

## Routes

- `POST /paystack/initialize` - Initialize Paystack transaction
- `POST /paystack/verify` - Verify payment status
- `GET /paystack/callback` - Paystack callback URL
- `GET /checkout/payment/{order}` - Payment page
- `GET /checkout/success/{order}` - Order success page

## Testing

Use Paystack test cards:
- **Success**: 4084084084084081
- **Decline**: 5060666666666666666
- **Insufficient Funds**: 5060666666666666667

Use any future expiry date and any CVV.

## Production

When going live:
1. Replace test keys with live keys from Paystack dashboard
2. Update `PAYSTACK_PUBLIC_KEY` and `PAYSTACK_SECRET_KEY` in `.env`
3. Test thoroughly before going live

