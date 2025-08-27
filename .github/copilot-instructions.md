# AI Agent Instructions for Hotel Management System

## Project Overview
A Laravel-based Hotel Management System implementing multi-language support, role-based access control, and real-time room status management. The system uses Spatie packages for media handling, translations, and permissions, with Redis for caching and performance optimization.

## Technical Stack
- Laravel Framework with Blade templates
- Spatie packages: Media, Translatable, Permission
- Redis for caching and session management
- MySQL/PostgreSQL database
- Vite for asset compilation

## Core Architecture

### 1. Multi-Language Room Types
- Room types use Spatie's translatable pattern with a separate `RoomTypeTranslation` model
- Translations are stored in a separate table, linked via `room_type_id`
- Example: `app/Models/RoomType.php` and its relationship with `RoomTypeTranslation`

### 2. Role-Based Controllers
Controllers are organized by user role in separate namespaces:
- `Admin/*`: Core management functionality (RoomTypes, Rooms, Bookings)
- `Staff/*`: Operational tasks (CheckInOut, RoomStatus)
- `User/*`: Customer-facing features (Booking, Waitlist)

### 3. Room Status Workflow
- Status changes are tracked in `RoomStatusHistory`
- Valid statuses: available, reserved, onboard, closed
- Staff updates trigger history records with reason and timestamp

## Development Workflows

### Setup and Database
```bash
# Initial setup
composer install
php artisan migrate:refresh
php artisan db:seed  # Creates roles and permissions

# Development
npm run dev  # For Vite asset compilation
php artisan serve  # Local development server
```

### Cache Management
- Room listings use Redis cache with tags
- Cache is automatically invalidated on room/type updates
- Pattern: `Cache::tags(['rooms'])->remember(...)`

## Project Conventions

### Controller Organization
1. Admin controllers handle full CRUD with validation
2. Staff controllers focus on status updates and operational tasks
3. User controllers implement booking and waitlist logic

### Model Relationships
- RoomType -> Room (one-to-many)
- Room -> Booking (one-to-many)
- Room -> RoomStatusHistory (one-to-many)
- RoomType -> Waitlist (one-to-many)

### Form Request Validation
- Custom form requests in `app/Http/Requests/`
- Validation rules centralized in request classes
- Example: `ProfileUpdateRequest` for profile updates

## Integration Points
1. Redis for caching (room listings, status)
2. Spatie Media Library for room type images
3. Spatie Translatable for multi-language content
4. Spatie Permissions for RBAC

## Performance Considerations

### Caching Strategy
- Room availability cached with 1-hour TTL
- Translations cached for 24 hours
- Session data stored in Redis
- Cache invalidation on model updates

### Query Optimization
- Eager load relationships (e.g., `with(['roomType', 'translations'])`)
- Use database indexes on frequently queried fields
- Implement pagination for large datasets

## Security Practices

### Authentication Flow
- Role-based middleware checks
- Session management via Redis
- CSRF protection on all forms
- Rate limiting on API endpoints

### Data Protection
- Sensitive data encryption
- Audit logging for critical changes
- GDPR compliance measures implemented

## Common Tasks
1. Room Status Management
   - Updates via `Staff\RoomStatusController`
   - Status history tracking
   - Automatic cache invalidation

2. Booking Workflow
   - Creation through `Admin\BookingController`
   - Payment processing integration
   - Notification system

3. Waitlist Handling
   - Queue management in `User\WaitlistController`
   - Automatic notifications
   - Priority processing

## Success Metrics
- System uptime: 99.9%
- Page load time: <2 seconds
- Booking completion rate: >95%
- Cache hit ratio: >80%
