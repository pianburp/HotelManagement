# Currency Migration: USD to MYR

## Overview
This document outlines the complete migration of the hotel management system from USD (US Dollar) to MYR (Malaysian Ringgit) currency.

## Changes Implemented

### 1. Database Changes

#### Migration Files Updated:
- `database/migrations/2025_08_27_105705_create_payments_table.php`
  - Changed default currency from `'USD'` to `'MYR'`

#### New Migration Created:
- `database/migrations/2025_08_29_024322_update_currency_to_myr.php`
  - Updates existing payment records from USD to MYR
  - Changes default currency value in payments table
  - Includes rollback functionality

#### Schema Updates:
- `schema.sql` - Updated default currency to MYR

### 2. Seeder Updates

#### PaymentSeeder:
- `database/seeders/PaymentSeeder.php`
  - Changed currency assignment from `'USD'` to `'MYR'`
  - All new payment records now use MYR currency

### 3. Configuration

The system was already properly configured for MYR:

#### Config File:
- `config/hotel.php`
  ```php
  'currency' => 'MYR',
  'currency_symbol' => 'RM',
  ```

#### Helper Function:
- `app/helpers.php`
  ```php
  function money($amount, $currency = 'RM', $decimals = 2) {
      return $currency . number_format((float)$amount, $decimals);
  }
  ```

### 4. View Updates

#### Admin Payment Views:
- `resources/views/admin/payments/index.blade.php`
  - Updated currency comparison from `'USD'` to `'MYR'`
  - Now only shows currency label if different from MYR

#### Reports Views:
- `resources/views/admin/reports/index.blade.php`
  - Updated Chart.js revenue chart tooltip to show `'RM'` instead of `'$'`

#### User Booking Views:
- `resources/views/user/bookings/create.blade.php`
  - JavaScript `formatMoney()` function already uses `'RM'` prefix
  - All amount displays properly formatted as MYR

### 5. Controller Updates

#### User Payment Controller:
- `app/Http/Controllers/User/PaymentController.php`
  - Already configured to use `'currency' => 'MYR'`

#### Admin Payment Controller:
- `app/Http/Controllers/Admin/PaymentController.php`
  - Uses dynamic currency from payment records

## Migration Results

### Before Migration:
```bash
Payment currencies: ['USD']
Monthly Revenue: $650.00
```

### After Migration:
```bash
Payment currencies: ['MYR']
Monthly Revenue: RM650.00
```

## Currency Display Format

### In Views:
- **PHP Helper**: `money($amount)` → `RM650.00`
- **JavaScript**: `formatMoney(amount)` → `RM650.00`
- **Chart Tooltips**: `'RM' + value.toLocaleString()` → `RM650`

### Database Storage:
- **Amount**: Stored as decimal(10,2)
- **Currency**: Stored as string(3) with default 'MYR'
- **Example**: `amount: 650.00, currency: 'MYR'`

## Testing Verification

### Database Verification:
```sql
SELECT currency, COUNT(*) as count, SUM(amount) as total 
FROM payments 
GROUP BY currency;
```
Result: `MYR | 2 | 650.00`

### Frontend Verification:
1. Visit admin reports dashboard → Monthly revenue shows `RM650.00`
2. View payment listings → Amounts display as `RM300.00`, `RM350.00`
3. Create booking → Price calculations show `RM` prefix
4. Revenue charts → Y-axis shows `RM` prefix

## Files Modified

### Core Database Files:
1. `database/migrations/2025_08_27_105705_create_payments_table.php`
2. `database/migrations/2025_08_29_024322_update_currency_to_myr.php` (new)
3. `database/seeders/PaymentSeeder.php`
4. `schema.sql`

### View Files:
1. `resources/views/admin/payments/index.blade.php`
2. `resources/views/admin/reports/index.blade.php`

### Documentation:
1. `REVENUE_SECURITY_FIX.md` - Updated currency examples

## Rollback Procedure

If rollback is needed, run the down migration:
```bash
php artisan migrate:rollback --step=1
```

This will:
1. Change all MYR payment records back to USD
2. Update default currency back to USD
3. Preserve all other data integrity

## Production Deployment

### Steps for Production:
1. **Backup Database**: Ensure full backup before migration
2. **Run Migration**: `php artisan migrate`
3. **Verify Data**: Check payment records are updated correctly
4. **Test Frontend**: Verify all currency displays show MYR
5. **Monitor**: Ensure new payments use MYR currency

### Rollout Verification:
- [ ] All existing payments show MYR currency
- [ ] New payments default to MYR
- [ ] Revenue reports display RM prefix
- [ ] Booking forms show RM amounts
- [ ] Charts use RM in tooltips
- [ ] Payment listings hide currency label for MYR

## Malaysian Ringgit (MYR) Details

- **Currency Code**: MYR
- **Currency Symbol**: RM
- **Subunit**: Sen (1/100)
- **Display Format**: RM123.45
- **Decimal Places**: 2

## Future Considerations

1. **Multi-Currency Support**: If international expansion is planned, consider implementing full multi-currency support
2. **Exchange Rates**: For USD bookings, implement exchange rate conversion
3. **Localization**: Consider Malaysian date/number formatting preferences
4. **Payment Gateways**: Ensure payment processors support MYR
5. **Reporting**: Update financial reports for Malaysian accounting standards

This migration ensures the hotel management system properly handles Malaysian Ringgit currency throughout all system components while maintaining data integrity and user experience.
