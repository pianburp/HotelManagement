# Room Availability Validation System

This document describes the comprehensive room availability validation system implemented for the hotel management system.

## Overview

The validation system ensures that users can only book rooms for dates when they are actually available, preventing double bookings and maintaining data integrity.

## Components

### 1. BookingRequest Form Request (`app/Http/Requests/BookingRequest.php`)

This custom form request class handles all booking validation including:

- **Basic Field Validation**: Required fields, data types, and formats
- **Date Validation**: Check-in must be after today, check-out must be after check-in
- **Room Availability**: Checks if room is available for selected dates
- **Guest Capacity**: Ensures number of guests doesn't exceed room capacity
- **Payment Method**: Validates selected payment method
- **Terms Acceptance**: Ensures user accepts terms and conditions

#### Key Validation Methods:

```php
validateRoomAvailability(Validator $validator): void
```
- Checks if room status is 'available'
- Queries database for conflicting bookings
- Returns specific conflicting date ranges

```php
validateGuestCapacity(Validator $validator): void
```
- Compares requested guest count with room's max occupancy

### 2. Room Model Enhancements (`app/Models/Room.php`)

Updated the `isAvailable()` method to:
- Check room status
- Query for overlapping bookings
- Use correct field names (`check_in_date`, `check_out_date`)

### 3. API Endpoints (`app/Http/Controllers/Api/RoomAvailabilityController.php`)

Provides real-time availability checking:

#### Endpoints:
- `GET /api/rooms/{room}/availability` - Get available dates for a period
- `POST /api/rooms/{room}/check-availability` - Check specific date range

#### Features:
- Returns detailed availability information
- Lists conflicting bookings with dates
- Provides booking periods and room status

### 4. Frontend JavaScript Enhancement

Updated the booking form with real-time validation:
- **Live Date Validation**: Checks availability as user selects dates
- **Visual Feedback**: Shows specific error messages for conflicts
- **Submit Prevention**: Disables submit button for invalid dates
- **Conflict Display**: Shows exact conflicting date ranges

## Validation Rules

### Room Availability Criteria

A room is considered **available** if:
1. Room status is 'available'
2. No confirmed or checked-in bookings overlap with requested dates
3. Requested dates are in the future

### Date Conflict Detection

The system checks for overlapping bookings using these conditions:
- Booking starts during requested stay
- Booking ends during requested stay  
- Booking encompasses entire requested stay

```sql
-- Conflict detection query
WHERE (
  (check_in_date BETWEEN ? AND ?) OR
  (check_out_date BETWEEN ? AND ?) OR
  (check_in_date <= ? AND check_out_date >= ?)
) AND status IN ('confirmed', 'checked_in')
```

## Error Messages

### Backend Validation Messages:
- "Selected room is not available for booking." (Room status not available)
- "The selected dates conflict with existing bookings. Unavailable periods: [dates]"
- "Number of guests ([count]) exceeds room capacity ([max])."

### Frontend Real-time Messages:
- "Selected dates are not available. Conflicting bookings: [date ranges]"
- "Check-out date must be after check-in date"
- "Unable to verify availability. Please check dates."

## Usage Examples

### Testing Validation via Console Command

```bash
php artisan booking:test-validation {room_id} {check_in} {check_out}
```

Example:
```bash
php artisan booking:test-validation 5 2025-08-30 2025-09-02
```

### API Usage

```javascript
// Check availability via AJAX
const response = await fetch(`/api/rooms/${roomId}/check-availability`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        check_in: '2025-08-30',
        check_out: '2025-09-02'
    })
});

const data = await response.json();
if (!data.available) {
    // Handle unavailable dates
    console.log('Conflicts:', data.conflicts);
}
```

## Database Schema Considerations

The validation system expects:
- `bookings.check_in_date` (DATE)
- `bookings.check_out_date` (DATE)
- `bookings.status` (ENUM: confirmed, checked_in, cancelled, etc.)
- `rooms.status` (ENUM: available, reserved, onboard, closed)

## Security & Performance

### Security:
- Server-side validation prevents tampering
- CSRF protection on AJAX requests
- User authorization checks

### Performance:
- Indexed database queries on room_id and dates
- Efficient conflict detection queries
- Minimal API calls with debouncing

## Future Enhancements

1. **Calendar Integration**: Visual calendar showing available/blocked dates
2. **Partial Availability**: Handle partial day bookings
3. **Rate Limiting**: Prevent excessive availability checks
4. **Caching**: Cache availability for frequently checked rooms
5. **Waitlist Integration**: Automatically notify when dates become available

## Testing

The system includes comprehensive tests covering:
- Available date bookings (should succeed)
- Conflicting date bookings (should fail)
- Invalid room status bookings (should fail)
- Guest capacity violations (should fail)
- API endpoint responses

Run tests with:
```bash
php artisan test tests/Feature/RoomAvailabilityValidationTest.php
```

## Troubleshooting

### Common Issues:

1. **"could not find driver" error**: Ensure SQLite extension is installed for testing
2. **AJAX 419 errors**: Verify CSRF token is included in requests
3. **Validation not triggering**: Check that BookingRequest is used in controller
4. **Wrong field names**: Ensure using `check_in_date`/`check_out_date` not `check_in`/`check_out`

### Debug Commands:

```bash
# Check room availability
php artisan tinker --execute="App\Models\Room::find(5)->isAvailable('2025-08-30', '2025-09-02')"

# List room statuses
php artisan tinker --execute="App\Models\Room::distinct('status')->pluck('status')"

# Check existing bookings
php artisan tinker --execute="App\Models\Booking::whereIn('status', ['confirmed', 'checked_in'])->count()"
```
