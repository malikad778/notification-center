# 🧪 Testing Guide

This guide details how to verify the **Notification Center** using automated tests, manual scripts, and API calls.

## 1. Automated Tests (Pest)

The project includes a full suite of feature tests covering all phases.

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/Phase2ChannelsTest.php

# Run with coverage (requires Xdebug/PCOV)
php artisan test --coverage
```

### What is tested?
- **Channels**: Email, SMS (Mock), Database, Broadcast, Slack, etc.
- **Logic**: Rate limiting, quiet hours, concurrency, failover.
- **API**: Authentication, endpoints.

---

## 2. Manual Testing (Tinker)

You can manually trigger notifications using the Laravel Tinker shell.

1. **Enter Tinker**:
   ```bash
   php artisan tinker
   ```

2. **Create a User & Send Notification**:
   ```php
   // Create a test user
   $user = App\Models\User::factory()->create([
       'email' => 'test@example.com',
       'phone_number' => '+15550001234'
   ]);

   // Create a payload
   $payload = new App\DTOs\NotificationPayload(
       title: 'Hello World',
       body: 'This is a manual test notification.',
       actionUrl: 'https://example.com'
   );

   // Dispatch (Sends to all configured channels)
   app(App\Services\NotificationDispatcher::class)->dispatch($user, $payload);
   ```

3. **Check Results**:
   ```php
   // Check Database Channel
   $user->notifications()->get();

   // Check Logs
   App\Models\NotificationLog::latest()->get();
   ```

---

## 3. Testing the Queue & Batches

Notifications are queued by default. To process them:

1. **Start the Queue Worker**:
   ```bash
   php artisan queue:work
   ```
   *Keep this running in a separate terminal.*

2. **Dispatch a Bulk Batch** (in Tinker):
   ```php
   $users = App\Models\User::factory()->count(10)->create();
   $payload = new App\DTOs\NotificationPayload('Bulk Update', 'System maintenance tonight.');
   
   app(App\Services\NotificationBatchService::class)->dispatchBatch($users, $payload);
   ```

3. **Monitor Progress**:
   Check the output in your `queue:work` terminal.

---

## 4. API Testing

You can use Postman, Curl, or any HTTP client.

### Prerequisites
- **Sanctum Token**: You need an API token.
  ```php
  // In Tinker
  echo App\Models\User::first()->createToken('manual-test')->plainTextToken;
  ```

### Endpoints

**GET /api/notifications**
```bash
curl -X GET http://localhost:8000/api/notifications \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Accept: application/json"
```

**PATCH /api/notifications/{id}/read**
```bash
curl -X PATCH http://localhost:8000/api/notifications/<UUID>/read \
  -H "Authorization: Bearer <YOUR_TOKEN>" \
  -H "Accept: application/json"
```

---

## 5. Troubleshooting

- **"Table not found"**: Run `php artisan migrate`.
- **"Route not found"**: Ensure `api.php` is loaded and you are using the `/api` prefix.
- **"Unauthenticated"**: Verify your Bearer token.
- **Pulse Dashboard**: Visit `/pulse` (requires simple auth/middleware setup or local environment).
