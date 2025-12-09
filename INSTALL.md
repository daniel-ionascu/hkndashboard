# Installation

## Requirements

- PrestaShop 8.0+
- PHP 7.2.5+

## Install

1. Upload `hkdashboard` to `/modules/`
2. Install from Module Manager
3. Configure settings

## Configuration

Navigate to: Modules > Administration > Dashboard Stats Widget > Configure

### Setup

1. Choose widget type (Views or Stock)
2. Set view days (for top viewed products)
3. Set stock threshold (for low stock alerts)
4. Save settings

## Default Settings

- Widget Type: Top Viewed Products
- View Days: 30
- Stock Threshold: 5

## Viewing Widget

1. Go to Back Office Dashboard
2. Widget appears in Zone 2 (middle section)
3. Data refreshes on page load

## Widget Options

**Top Viewed Products:**
- Shows 5 most viewed products
- Configurable lookback period
- Falls back to recent products if no views

**Low Stock Alerts:**
- Shows up to 10 products
- Configurable threshold
- Only shows active products

## Troubleshooting

**No data showing:**
- Check if products exist
- For views: Ensure customers are browsing products
- For stock: Check stock levels in catalog

**Widget not appearing:**
- Clear cache
- Verify module is installed
- Check dashboard hooks

**Wrong data:**
- Verify configuration settings
- Check database tables exist
- Review product activity
