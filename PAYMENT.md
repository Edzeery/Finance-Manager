# Payment System — Finance Manager

> آخر تحديث: 2026-07-20 (v6)

---

## Architecture

The payment system uses the **Strategy Pattern** with a **Gateway Registry**:

```
PaymentService (facade)
    ↓
GatewayManager (singleton registry)
    ↓ (driver)
PaymentGateway (interface)
    ↓
13 implementations (Online + Manual + Courier + Cash)
    ↓
PaymentResult (DTO) ──→ success() / failed()
```

### Gateway Interface

```php
interface PaymentGateway {
    public function name(): string;
    public function charge(array $data): PaymentResult;
    public function refund(Payment $payment, ?float $amount = null): PaymentResult;
    public function verify(Payment $payment): PaymentResult;
    public function isOnline(): bool;
    public function isOffline(): bool;
    public function supportedCurrencies(): array;
}
```

### PaymentResult DTO

```php
class PaymentResult {
    public readonly bool $success;
    public readonly bool $pending = false;
    public readonly string $message;
    public readonly ?string $transactionId;
    public readonly ?string $reference;
    public readonly ?array $metadata;
    public readonly ?string $redirectUrl;
    public static function success(...): self;
    public static function failed(string $message, ?array $metadata = null): self;
    public static function pending(...): self;
    public function isPending(): bool;
}
```

## Gateway Inventory

| # | Gateway | Type | Flow | Currencies | Webhook | Strategy |
|---|---------|------|------|-----------|---------|----------|
| 1 | **Chargily** | Online | Redirect → Webhook | DZD | ✅ HMAC | ChargilyPay SDK → Checkout |
| 2 | **BaridiMob** | Manual | Instructions → Proof → Admin verify | DZD | ❌ | Algerian postal (RIP) |
| 3 | **PayPal** | Online | Redirect → Webhook | USD, EUR, GBP, DZD | ✅ Token | REST API v1 |
| 4 | **RedotPay** | Manual | Instructions → Proof → Admin verify | USDT, BTC, ETH, USD | ❌ | Crypto manual |
| 5 | **Stripe** | Online | Redirect → Webhook | USD, EUR, GBP, DZD, AED, SAR | ✅ Token | Payment Intents API |
| 6 | **Wise** | Online | API Transfer → Webhook | USD, EUR, GBP, DZD, AED, SAR | ✅ Token | Transfers API |
| 7 | **WiseManual** | Manual | Instructions → Proof → Admin verify | USD, EUR, GBP, DZD | ❌ | Wise + receipt upload |
| 8 | **Payoneer** | Online | API Payout → Webhook | USD, EUR, GBP | ✅ Token | Payouts API |
| 9 | **Cash** | Offline | Instructions → Admin verify | DZD, USD, EUR | ❌ | Physical cash |
| 10 | **Delivery** | Offline | Schedule → Collect | DZD | ❌ | Cash on delivery |
| 11 | **Noest** | Auto | Create order → Poll delivery | DZD | ❌ | Algerian courier API |
| 12 | **PayTR** | Online | Redirect → Webhook | USD, TRY | ✅ Token | Turkish payment gateway |
| 13 | **Rasmal** | Online | Redirect → Webhook | DZD | ✅ Token | Algerian payment gateway (rasmal.dz) |

## Key Services

| Service | File | Responsibility |
|---------|------|---------------|
| `PaymentService` | `app/Services/PaymentService.php` | Create payments, charge via gateway, verify manually, revenue stats |
| `OnboardingService` | `app/Services/OnboardingService.php` | Plan selection, free/paid processing, manual proof, Noest delivery info |
| `SubscriptionService` | `app/Services/SubscriptionService.php` | Plan changes, coupon validation, invoice generation, cancellations |
| `GatewayManager` | `app/Services/Payments/GatewayManager.php` | Registry: `driver()`, `online()`, `offline()` |
| `PaymentWebhookController` | `app/Http/Controllers/PaymentWebhookController.php` | Webhook handlers for all online gateways |
| `NoestService` | `app/Services/Payments/Noest/NoestService.php` | Noest API — create orders, get wilayas/desks/communes, tracking |

## Payment Lifecycle

```
                 ┌─────────────┐
                 │   Pending   │
                 └──────┬──────┘
                        │
              ┌─────────┴─────────┐
              ▼                   ▼
         ┌──────────┐     ┌──────────────┐
         │  Online  │     │ Manual/Offline│
         │  Gateway │     │              │
         └────┬─────┘     └──────┬───────┘
              │                  │
     Webhook  │          Upload Proof
     Callback │                  │
              │          ┌───────▼───────┐
              │          │  Verification │
              │          │  (Pending)    │
              │          └───────┬───────┘
              │                  │ Admin Approves
              ▼                  ▼
         ┌─────────┐     ┌───────────┐
         │Completed│     │  Completed │
         └─────────┘     └───────────┘
```

## Gateway-Specific Return Routes

Each online gateway has a dedicated return URL where the gateway redirects the user after payment:

| Gateway | Route Name | URL |
|---------|-----------|-----|
| Chargily | `chargily.back` | `/payment/chargily/result?checkout_id=...` |
| PayPal | `paypal.back` | `/payment/paypal/result?checkout_id=...` |
| PayTR | `paytr.back` | `/payment/paytr/result?checkout_id=...` |
| Rasmal | `rasmal.back` | `/payment/rasmal/result?checkout_id=...` |

All routes render the same Volt component (`pages.onboarding.payment-result`) which resolves the payment via the `checkout_id` query parameter or session `pending_payment_id`, then polls for webhook confirmation.

## Online Payment Flow (Automatic)

```
User → App Server: Select Plan + Payment
    ← Redirect URL
User → Gateway Checkout Page
Gateway → User's browser: Redirect to gateway-specific return URL
    e.g., GET /payment/chargily/result?checkout_id=01kx3k...
App Server (payment-result Volt): Resolve payment, poll status
Gateway → App Server: Webhook (POST) — status update
App Server: Poll detects completion → redirect to onboarding.setup
```

## Manual Payment Flow

```
User → App Server: Select Plan + Method
    ← Payment Instructions + Reference
User pays + Uploads Receipt/Proof + TX Ref
App Server → Admin: Notify
Admin: Approve/Reject
App Server → User: Activate Subscription
```

## Webhook System

Chargily uses full HMAC-SHA256 signature verification via the official SDK. Other gateways (PayPal, Stripe, Wise, Payoneer) use a shared `X-Webhook-Token` secret.

## Gateway Registration

```php
$this->app->singleton(GatewayManager::class, function () {
    $manager = new GatewayManager;
    $manager->register('chargily', new ChargilyGateway);
    $manager->register('baridimob', new BaridiMobGateway);
    $manager->register('paypal', new PayPalGateway); 
    $manager->register('redotpay', new RedotPayGateway);
    $manager->register('stripe', new StripeGateway);
    $manager->register('wise', new WiseGateway);
    $manager->register('wise_manual', new WiseManualGateway);
    $manager->register('payoneer', new PayoneerGateway);
    $manager->register('noest', new NoestGateway);
    $manager->register('cash', new CashGateway);
    $manager->register('delivery', new DeliveryGateway);
    $manager->register('rasmal', new \App\Services\Payments\Rasmal\RasmalGateway);
    return $manager;
});
```

## Chargily Integration

### Files (7)

| File | Responsibility |
|------|---------------|
| `ChargilyClient.php` | Singleton factory: `make()` returns `ChargilyPay` instance |
| `ChargilyGateway.php` | Implements `PaymentGateway` |
| `ChargilyCheckoutService.php` | Creates/retrieves checkouts via SDK |
| `ChargilySignatureValidator.php` | Validates webhook signature via library |
| `ChargilyWebhookService.php` | Orchestrates validation + dispatch |
| `DTOs/CheckoutData.php` | Checkout data transfer object |
| `Exceptions/ChargilyException.php` | Named constructors for errors |

### Checkout Creation (Route Usage)

Both `ChargilyGateway::charge()` and `ChargilyCheckoutService::create()` use:
```php
'success_url' => $data['success_url'] ?? route('chargily.back'),
'failure_url' => $data['failure_url'] ?? route('chargily.back'),
'webhook_endpoint' => route('payment.webhook.chargily'),
```

The `success_url` and `failure_url` are sent to Chargily's API. After payment, Chargily redirects the user's browser to `/payment/chargily/result?checkout_id=...` (with the `checkout_id` from Chargily's response). The Volt component resolves the payment via `checkout_id` or `transaction_id` and polls for webhook confirmation.

### Webhook Flow

```
Chargily → POST /payment/webhook/chargily
→ PaymentWebhookController@chargily
  → ChargilyWebhookService::process()
    → ChargilySignatureValidator::validate()
      → ChargilyClient::make()->webhook()->get()
    → Find payment by payment_id from metadata
    → Log PaymentWebhookLog
    → match ($webhookElement->getType()):
        'checkout.paid'   → payment=completed (PaymentStatus::Completed), subscription=active via activateFromPayment()
        'checkout.failed' → payment=failed
        'checkout.canceled'→ payment=canceled
        'checkout.expired' → payment=failed
  → 200 {"received":true}
```

### Route Definitions

From `routes/tenant.php`:

```php
// Payment resume (pending payment — shows details immediately, no spinner)
Volt::route('/payment/resume/{payment}', 'pages.onboarding.payment-resume')->name('payment.resume');

// Payment retry (failed/canceled payments)
Volt::route('/payment/retry/{payment}', 'pages.onboarding.payment-retry')->name('payment.retry');

// Gateway return routes
Volt::route('/payment/chargily/result/{payment?}', 'pages.onboarding.payment-result')->name('chargily.back');
Volt::route('/payment/paypal/result/{payment?}', 'pages.onboarding.payment-result')->name('paypal.back');
Volt::route('/payment/paytr/result/{payment?}', 'pages.onboarding.payment-result')->name('paytr.back');
Volt::route('/payment/rasmal/result/{payment?}', 'pages.onboarding.payment-result')->name('rasmal.back');

// Payment gateway redirect (Charge via gateway-manager route)
Route::get('/payment/checkout/{payment}', [CheckoutController::class, 'redirect'])->name('payment.checkout');
// Payment status polling
Route::get('/payment/status/{payment}', [PaymentReturnController::class, 'checkStatus'])->name('payment.check-status');
```

## Fee Breakdown

The payment system supports three fee types linked to payment methods via `payment_method_tax_rate` pivot table:

| Fee Type | Charge Type | Description |
|----------|-------------|-------------|
| Gateway Fee | `gateway_fee` | Added to total (e.g., 2.5% processing fee) |
| Tax Added | `tax_added` | Added to total (e.g., 19% VAT) |
| Tax Disclosed | `tax_disclosed` | Informational (included in base price) |

Fees are calculated in `PaymentService::chargeForPlan()` using `TaxRate::calculateForAmount()`.

### Display

Fee breakdown is shown as a coupon-style price breakdown on three pages:

1. **`/onboarding/plan`** — After selecting a payment method, shows real-time fee estimate (same logic as `PaymentService`)
2. **`/onboarding/payment`** — After selecting a payment method, shows real-time fee estimate via `getFeeBreakdownProperty()` computed property
3. **`/payment/retry/{payment}`** — Reads stored `gateway_fee`, `tax_added`, `tax_disclosed` columns from the Payment record
4. **`/payment/resume/{payment}`** — Same as retry, reads stored fee columns

## Currency-Based Gateway Filtering

Payment methods can be restricted to specific currencies via the `supported_currencies` JSON column on the `payment_methods` table. If the column is `null` or empty, the method is shown for all currencies.

The filtering runs in both onboarding steps:

1. **`/onboarding/plan`** — `mount()` filters `PaymentMethod::active()->public()->ordered()->get()` against the user's session currency (`session('currency')`, falling back to user profile currency, then `config('finance.currency')`).
2. **`/onboarding/payment`** — `getPaymentMethodsProperty()` applies the same filter.

Relevant files:
- `resources/views/livewire/pages/onboarding/plan.blade.php` (L80-93)
- `resources/views/livewire/pages/onboarding/payment.blade.php` (L326-342)

## Coupon Gateway Validation

Coupons can be restricted to specific payment gateways via `coupon_gateway_restrictions` pivot. Validation runs live (on each keystroke) in both the **plan** and **payment** onboarding steps:

| Scenario | Result |
|----------|--------|
| Coupon restricted to gateways, no gateway selected | Inline error: `coupon_requires_gateway` |
| Coupon restricted to gateways, selected gateway incompatible | Inline error: `coupon_not_for_gateway` |
| Coupon restricted, compatible gateway selected | Applies normally |
| Coupon has no gateway restrictions | Applies regardless of gateway |

The gateway check runs **unconditionally** (not gated by `$this->paymentMethod`), ensuring the coupon validity is always evaluated against the current gateway selection.

Relevant files:
- `resources/views/livewire/pages/onboarding/plan.blade.php` (L273-289)
- `resources/views/livewire/pages/onboarding/payment.blade.php` (L205-219)

## API Error Responses

Invalid/expired API tokens → JSON 401 (`AuthenticationException`). Authenticated but unauthorized → JSON 403 (`AuthorizationException`). All other HTTP errors → dynamic status from the exception. Configured in `bootstrap/app.php` via `withExceptions()`.

## Session Expiry

Session expiry during onboarding returns a JSON 401 with `{"message": "...", "status": 401}` instead of redirecting to the HTML login page. This is handled by the `AuthenticationException` render callback in `bootstrap/app.php`.

### Important: PaymentStatus Enum Values
All gateways now write PaymentStatus enum values (`CheckoutPending`, `CheckoutPaid`, `CheckoutFailed`, `CheckoutCanceled`, `CheckoutExpired`). The `isCompleted()` method checks `=== PaymentStatus::CheckoutPaid`. This is consistent across all services. No raw string statuses remain in application code for the Payment model.

**PaymentTransitionValidator** is now actively used across all payment flows:
- `PaymentService::applyPaymentSideEffects()` — transitions on approval/rejection
- `PaymentStatusService` — Blade-level transitions via `cancel()`, `retry()`
- `SubscriptionActivationService` — payment status updates
- `ChargilyWebhookService` — webhook-driven transitions
- `canTransitionTo()` uses `$target->value` for correct PHP 8.1+ enum comparison (fixed 2026-07-20)

### Configuration

`.env`:
```
CHARGILY_MODE=test
CHARGILY_PUBLIC_KEY=test_pk_...
CHARGILY_SECRET_KEY=test_sk_...
```

`config/payment.php`: `gateways.chargily` array. Values read via `Setting::getSecret()` with `config()` fallback.

### Manual Payment Verification (Admin)

1. User submits receipt via `submitManualPaymentProof()` → `PaymentVerification` (status: pending)
2. Admin reviews in SuperAdmin `PaymentController` → approves/rejects
3. `PaymentService::verifyPayment()` updates payment + activates subscription

### Gateway Config

Sensitive keys encrypted in `payment_methods.credentials` JSON column. `HasGatewaySettings` trait reads from DB with config/env fallback.
