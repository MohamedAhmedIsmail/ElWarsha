# ElWarsha Mobile API Documentation

This document covers the implemented Laravel mobile APIs for ElWarsha. It is intended for Android/iOS developers and should be used together with the Postman collection in `docs/ElWarsha-Mobile-API.postman_collection.json`.

## Base Setup

Base URL variable:

```text
{{base_url}} = http://127.0.0.1:8000
```

Common headers:

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer {{token}}
```

For file upload endpoints, use `multipart/form-data` instead of JSON.

## Response Shape

All API responses use the same wrapper:

```json
{
  "success": true,
  "message": "Action message.",
  "data": {},
  "errors": null
}
```

Validation errors return `422`:

```json
{
  "success": false,
  "message": "The given data was invalid.",
  "data": null,
  "errors": {
    "field": ["Validation message"]
  }
}
```

Common status codes:

| Code | Meaning |
|---|---|
| 200 | Success |
| 201 | Created |
| 401 | Missing/invalid Sanctum token |
| 404 | Resource not found or not owned by the authenticated user |
| 422 | Validation error or invalid status transition |

## Authentication and OTP

Public endpoints:

| Method | Endpoint | Purpose |
|---|---|---|
| POST | `/api/auth/request-otp` | Create local OTP |
| POST | `/api/auth/verify-otp` | Verify OTP once |
| POST | `/api/auth/resend-otp` | Reissue OTP |
| POST | `/api/auth/register` | Register customer/workshop owner/provider |
| POST | `/api/auth/login` | Login by phone/password |

Protected endpoints:

| Method | Endpoint | Purpose |
|---|---|---|
| POST | `/api/auth/logout` | Revoke current token |
| GET | `/api/me` | Current profile |
| PUT | `/api/me` | Update profile |

OTP request:

```json
{
  "phone": "01000000000",
  "purpose": "register"
}
```

Allowed OTP purposes: `register`, `login`, `reset_password`.

Local development returns the OTP in the response. OTP expires after 5 minutes and can be used once.

Register request:

```json
{
  "name": "Mohamed",
  "phone": "01000000000",
  "email": "test@example.com",
  "password": "password",
  "role": "customer"
}
```

Public roles: `customer`, `workshop_owner`, `provider`. Public registration cannot create `admin` or `super_admin`.

Login request:

```json
{
  "phone": "01000000000",
  "password": "password"
}
```

Save `data.token` from register/login and send it as `Bearer {{token}}`.

## Lookup APIs

Public endpoints:

| Method | Endpoint | Query |
|---|---|---|
| GET | `/api/car-brands` | `search`, `per_page` |
| GET | `/api/car-brands/{brand}/models` | `search` |
| GET | `/api/car-models` | `search`, `per_page` |
| GET | `/api/service-categories` | `search` |
| GET | `/api/services` | `search` |
| GET | `/api/service-categories/{category}/services` | none |
| GET | `/api/sos-service-types` | none |
| GET | `/api/maintenance-items` | none |
| GET | `/api/plans` | none |

All lookup APIs return active records only. Service categories are sorted by `sort_order`.

## Customer Garage

Protected endpoints:

| Method | Endpoint |
|---|---|
| GET | `/api/vehicles` |
| POST | `/api/vehicles` |
| GET | `/api/vehicles/{vehicle}` |
| PUT | `/api/vehicles/{vehicle}` |
| DELETE | `/api/vehicles/{vehicle}` |

Create/update vehicle:

```json
{
  "car_brand_id": 1,
  "car_model_id": 1,
  "year": 2020,
  "mileage_km": 65000,
  "plate_number": "ABC-1234",
  "vin": "1HGCM82633A004352",
  "color": "Black",
  "image": "https://example.com/car.jpg",
  "notes": "Family car"
}
```

Users can only access their own vehicles. Deleted vehicles are soft deleted.

## Workshop Directory

Public endpoints:

| Method | Endpoint |
|---|---|
| GET | `/api/workshops` |
| GET | `/api/workshops/nearby` |
| GET | `/api/workshops/{workshop}` |
| GET | `/api/workshops/{workshop}/services` |
| GET | `/api/workshops/{workshop}/reviews` |

Filters for `/api/workshops`:

```text
service_id, category_id, brand_id, city, area, rating, is_verified,
open_now, accepts_booking, accepts_sos, search, per_page
```

Nearby search:

```text
/api/workshops/nearby?lat=30.0444&lng=31.2357&radius=10&per_page=15
```

Only approved workshops are returned. Nearby uses Haversine distance and sorts by distance then rating. Authenticated detail views create profile view analytics.

Tracking endpoints:

| Method | Endpoint | Lead source |
|---|---|---|
| POST | `/api/workshops/{workshop}/track-view` | `profile_view` |
| POST | `/api/workshops/{workshop}/track-call` | `call_click` |
| POST | `/api/workshops/{workshop}/track-whatsapp` | `whatsapp_click` |
| POST | `/api/workshops/{workshop}/track-directions` | `directions_click` |

Tracking body:

```json
{
  "vehicle_id": 1,
  "latitude": 30.0444,
  "longitude": 31.2357,
  "metadata": {
    "screen": "workshop_profile"
  }
}
```

`vehicle_id`, GPS, and metadata are optional. Vehicle must belong to the authenticated user if provided. `profile_view` leads are deduplicated for the same user/workshop/day.

## Workshop Owner Profile

Protected, role `workshop_owner`:

| Method | Endpoint |
|---|---|
| POST | `/api/workshop/register` |
| GET | `/api/workshop/profile` |
| PUT | `/api/workshop/profile` |
| POST | `/api/workshop/images` |
| DELETE | `/api/workshop/images/{image}` |
| PUT | `/api/workshop/services` |
| PUT | `/api/workshop/brands` |
| PUT | `/api/workshop/working-hours` |

Workshop registration starts as `pending`; admin approval comes later.

Workshop body:

```json
{
  "name": "ElWarsha Nasr City",
  "description": "Full service workshop",
  "phone": "01011111111",
  "whatsapp": "01011111111",
  "email": "workshop@example.com",
  "address": "Abbas El Akkad",
  "city": "Cairo",
  "area": "Nasr City",
  "latitude": 30.0444,
  "longitude": 31.2357,
  "google_maps_url": "https://maps.google.com",
  "accepts_booking": true,
  "accepts_sos": true
}
```

Image upload is `multipart/form-data`:

```text
type=gallery
sort_order=1
images[]=file
```

Sync services/brands:

```json
{ "ids": [1, 2, 3] }
```

Working hours:

```json
{
  "hours": [
    { "day_of_week": "saturday", "opens_at": "09:00", "closes_at": "18:00", "is_closed": false },
    { "day_of_week": "friday", "opens_at": null, "closes_at": null, "is_closed": true }
  ]
}
```

## Diagnosis and Emergency Guidance

Protected endpoints:

| Method | Endpoint |
|---|---|
| POST | `/api/diagnoses` |
| GET | `/api/diagnoses` |
| GET | `/api/diagnoses/{diagnosis}` |
| POST | `/api/diagnoses/{diagnosis}/media` |
| GET | `/api/diagnoses/{diagnosis}/recommended-workshops` |
| POST | `/api/emergency-guidance` |

Create diagnosis:

```json
{
  "vehicle_id": 1,
  "description": "Car is not starting and I hear clicking",
  "symptoms_json": ["not starting", "clicking"],
  "disclaimer_accepted": true
}
```

Dummy AI sets status to complete synchronously. Descriptions containing battery/not starting/clicking map to Electricity, brake maps to Brakes, overheating maps to Mechanics or AC, default maps to Mechanics.

Diagnosis media upload:

```text
media_type=image|audio|video
files[]=file
```

Recommended workshops query supports optional location:

```text
/api/diagnoses/{diagnosis}/recommended-workshops?lat=30.0444&lng=31.2357
```

Emergency guidance request:

```json
{
  "vehicle_id": 1,
  "description": "Smoke is coming from the engine",
  "symptoms": ["smoke", "overheating"],
  "latitude": 30.0444,
  "longitude": 31.2357
}
```

High-risk keywords: `smoke`, `fire`, `brakes`, `steering`, `overheating`, `accident`. High-risk guidance returns `urgency=high`, `needs_sos=true`, and a recommended SOS service type. Guidance is safety-only and does not include repair instructions.

## Bookings

Customer endpoints:

| Method | Endpoint |
|---|---|
| POST | `/api/bookings` |
| GET | `/api/bookings` |
| GET | `/api/bookings/{booking}` |
| PUT | `/api/bookings/{booking}/cancel` |

Create booking:

```json
{
  "vehicle_id": 1,
  "workshop_id": 1,
  "diagnosis_id": 1,
  "service_id": 1,
  "scheduled_at": "2026-08-01 10:00:00",
  "description": "Need inspection"
}
```

Booking starts as `pending`. The API creates a booking status log, workshop lead, notification, and WhatsApp message record.

Workshop owner endpoints:

| Method | Endpoint |
|---|---|
| GET | `/api/workshop/bookings` |
| GET | `/api/workshop/bookings/{booking}` |
| PUT | `/api/workshop/bookings/{booking}/accept` |
| PUT | `/api/workshop/bookings/{booking}/decline` |
| PUT | `/api/workshop/bookings/{booking}/start` |
| PUT | `/api/workshop/bookings/{booking}/complete` |

Allowed transitions:

```text
pending -> accepted
pending -> declined
accepted -> in_progress
in_progress -> completed
```

Completion creates a service ledger entry for the vehicle.

## SOS Dispatch

Customer endpoints:

| Method | Endpoint |
|---|---|
| POST | `/api/sos-requests` |
| GET | `/api/sos-requests` |
| GET | `/api/sos-requests/{sosRequest}` |
| PUT | `/api/sos-requests/{sosRequest}/cancel` |

Create SOS request:

```json
{
  "sos_service_type_id": 1,
  "vehicle_id": 1,
  "latitude": 30.0444,
  "longitude": 31.2357,
  "description": "Need towing",
  "urgency": "high"
}
```

SOS starts as `pending`. If a nearest approved available provider offers the selected SOS service, the request becomes `assigned`. The API logs status changes, creates notification/WhatsApp records, and creates a workshop lead if the provider is linked to a workshop.

Provider endpoints:

| Method | Endpoint |
|---|---|
| GET | `/api/provider/sos-requests` |
| GET | `/api/provider/sos-requests/{sosRequest}` |
| PUT | `/api/provider/sos-requests/{sosRequest}/accept` |
| PUT | `/api/provider/sos-requests/{sosRequest}/decline` |
| PUT | `/api/provider/sos-requests/{sosRequest}/on-the-way` |
| PUT | `/api/provider/sos-requests/{sosRequest}/arrived` |
| PUT | `/api/provider/sos-requests/{sosRequest}/complete` |

Allowed transitions:

```text
assigned -> accepted
accepted -> on_the_way
on_the_way -> arrived
arrived -> completed
```

Provider decline currently returns the request to `pending` and unassigns the provider because the database enum has no `declined` SOS status. Completion creates a service ledger only when `vehicle_id` exists.

## Reviews

Protected endpoints:

| Method | Endpoint |
|---|---|
| POST | `/api/reviews` |
| GET | `/api/my-reviews` |
| PUT | `/api/reviews/{review}` |
| DELETE | `/api/reviews/{review}` |

Create review:

```json
{
  "workshop_id": 1,
  "booking_id": 1,
  "sos_request_id": null,
  "rating": 5,
  "quality_rating": 5,
  "price_rating": 4,
  "punctuality_rating": 5,
  "behavior_rating": 5,
  "comment": "Excellent service"
}
```

Ratings must be 1 to 5. If `booking_id` or `sos_request_id` is provided, it must belong to the authenticated user. Duplicate reviews for the same booking are rejected. Workshop `rating_avg` and `reviews_count` update after create/update/delete.

## Maintenance Planner

Protected endpoints:

| Method | Endpoint |
|---|---|
| GET | `/api/vehicles/{vehicle}/maintenance-reminders` |
| POST | `/api/vehicles/{vehicle}/maintenance-reminders` |
| PUT | `/api/maintenance-reminders/{reminder}` |
| DELETE | `/api/maintenance-reminders/{reminder}` |
| GET | `/api/maintenance-reminders/upcoming` |

Create reminder:

```json
{
  "maintenance_item_id": 1,
  "last_done_at": "2026-07-01",
  "last_done_mileage": 60000,
  "next_due_at": "2026-08-01",
  "next_due_mileage": 65000,
  "reminder_before_days": 7,
  "status": "active",
  "notes": "Use synthetic oil"
}
```

Statuses: `active`, `done`, `skipped`, `cancelled`.

Upcoming reminders are active reminders due within 14 days or already due by mileage compared to the vehicle mileage.

## Service Ledger

Protected endpoints:

| Method | Endpoint |
|---|---|
| GET | `/api/vehicles/{vehicle}/service-ledger` |
| POST | `/api/vehicles/{vehicle}/service-ledger` |
| GET | `/api/service-ledger/{ledger}` |
| PUT | `/api/service-ledger/{ledger}` |
| DELETE | `/api/service-ledger/{ledger}` |
| POST | `/api/service-ledger/{ledger}/media` |

Create ledger entry as `multipart/form-data`:

```text
title=Oil change
description=Routine service
service_date=2026-07-21
cost=750
mileage_km=65000
workshop_id=1
booking_id=1
diagnosis_id=1
sos_request_id=1
maintenance_item_id=1
invoice_file=file
```

Only `title` and `service_date` are required. Links are optional but must belong to the authenticated user and selected vehicle where applicable.

Upload ledger media:

```text
media_type=image|invoice|document
files[]=file
```

Ledger responses include linked workshop, booking, diagnosis, SOS request, maintenance item, invoice file, and media.

## Workshop CRM

Protected, workshop owner only:

| Method | Endpoint |
|---|---|
| GET | `/api/workshop/leads` |
| GET | `/api/workshop/leads/{lead}` |
| PUT | `/api/workshop/leads/{lead}/status` |
| POST | `/api/workshop/leads/{lead}/notes` |
| GET | `/api/workshop/crm/analytics` |

Lead filters:

```text
/api/workshop/leads?source=call_click&status=new
```

Lead sources: `profile_view`, `call_click`, `whatsapp_click`, `directions_click`, `booking`, `sos`, `diagnosis_recommendation`.

Lead statuses: `new`, `contacted`, `booked`, `in_service`, `completed`, `lost`, `follow_up_needed`.

Update status:

```json
{ "status": "contacted" }
```

Add note:

```json
{ "note": "Customer asked for follow-up tomorrow." }
```

CRM analytics returns:

```json
{
  "total_leads": 10,
  "leads_by_source": {},
  "leads_by_status": {},
  "call_clicks_count": 3,
  "whatsapp_clicks_count": 2,
  "directions_clicks_count": 1,
  "bookings_count": 4,
  "sos_leads_count": 1
}
```

## Subscriptions and Manual Payments

Public:

| Method | Endpoint |
|---|---|
| GET | `/api/plans` |

Workshop owner:

| Method | Endpoint |
|---|---|
| GET | `/api/workshop/subscription` |
| POST | `/api/workshop/subscription/request` |

Subscription request is `multipart/form-data`:

```text
plan_id=2
payment_method=instapay
transaction_reference=ABC123
receipt_image=file
```

Payment methods: `vodafone_cash`, `instapay`, `bank_transfer`, `cash`, `paymob`, `fawry`.

The API creates a pending subscription and pending payment. Workshop `subscription_status` remains `free` until future admin approval.

## Notifications

Protected endpoints:

| Method | Endpoint |
|---|---|
| POST | `/api/device-tokens` |
| GET | `/api/notifications` |
| PUT | `/api/notifications/{notification}/read` |
| PUT | `/api/notifications/read-all` |

Store token:

```json
{
  "token": "fcm-token-or-apns-token",
  "platform": "android",
  "device_name": "Pixel 8"
}
```

Platforms: `android`, `ios`, `web`.

Users can store multiple tokens. Re-submitting the same token updates platform/device info. FCM/APNs sending is intentionally not integrated yet; `NotificationService` stores records and isolates future push integration.

## Seeders Needed for Mobile Development

Run these seeders or `php artisan db:seed` after migrations:

```bash
php artisan db:seed --class=SosServiceTypeSeeder
php artisan db:seed --class=MaintenanceItemSeeder
php artisan db:seed --class=PlanSeeder
```

Seeded SOS service types:

```text
Towing, Dead Battery, Flat Tire, Overheating, Car Not Starting,
Out of Fuel, Accident Support, Locked Key, Brake Emergency, Electrical Emergency
```

Seeded maintenance items:

```text
Oil Change, Oil Filter, Air Filter, Fuel Filter, AC Filter, Battery,
Tires, Brakes, AC Check, License Renewal, Insurance Renewal, Pre-travel Check
```

Seeded plans:

```text
Free, Basic, Pro, Premium
```

## Important Mobile Notes

- All protected endpoints require Sanctum Bearer token.
- IDs in the Postman collection use variables like `{{vehicle_id}}`, `{{workshop_id}}`, `{{booking_id}}`.
- If a request returns 404 for an existing row, it usually means the authenticated user does not own that resource.
- File endpoints must use `multipart/form-data`.
- Admin approval flows are not part of the mobile API yet.
