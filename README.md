# HKN Dashboard Stats Widget

PrestaShop 8/9 admin dashboard widget for monitoring store performance. Displays top viewed products or low stock alerts with configurable timeframes and thresholds.

## Features

- Top viewed products widget
- Low stock alerts widget
- Configurable timeframe
- Configurable stock threshold
- Direct product edit links
- Clean service-based architecture

## Installation

1. Copy `hkndashboard` folder to `/modules/`
2. Install from Module Manager
3. Configure widget settings
4. View on main dashboard

## Configuration

Go to Modules > Administration > Dashboard Stats Widget > Configure

### Settings

**Widget Type:**
- Top Viewed Products - Shows most visited products
- Low Stock Alerts - Shows products below threshold

**View Days:** Number of days to analyze product views (default: 30)

**Stock Threshold:** Show products with stock below this number (default: 5)

## Widget Types

### Top Viewed Products
Displays the 5 most viewed products in the specified timeframe.
Shows product name and total view count.

### Low Stock Alerts
Displays up to 10 products with stock below the threshold.
Shows product name and current quantity.
Color-coded: Red for out of stock, orange for low stock.

## Dashboard Zone

Widget appears in Dashboard Zone Two (middle section).

## Compatibility

- PrestaShop 8.0.0 - 9.9.9

## Version

1.0.0

## Author

Daniel Ionașcu

## License

MIT
