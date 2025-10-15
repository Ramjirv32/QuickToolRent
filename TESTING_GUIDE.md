# 🧪 COMPLETE TESTING GUIDE - MY RENTALS SYSTEM

## 🎯 What to Test

Your new rental system has been completely upgraded with:
1. ✅ Auto-redirect after successful booking (3-second countdown)
2. ✅ Advanced My Rentals dashboard with live timers
3. ✅ Automatic fine calculation for overdue items
4. ✅ Real-time countdown timers
5. ✅ Statistics overview

---

## 🚀 Quick Start Testing

### Step 1: Start Server (if not running)
```bash
cd /home/ramji/Documents/WEBTECH
php -S localhost:8000
```

### Step 2: Login
```
URL: http://localhost:8000/login.php
Email: user@example.com
Password: password
```

### Step 3: Rent a Product
1. Go to homepage: http://localhost:8000/
2. Click any category (e.g., "Power Tools", "Electronics")
3. Click "Rent Now" on any product
4. Fill the form:
   - **Start Time**: Select current date/time
   - **End Time**: Select 2-3 hours from now
   - **Payment**: Choose any method
   - **Address**: Enter any address
5. Click "Pay & Place Order"

### Step 4: Watch the Magic! ✨
You'll see:
1. **Success Page** appears with:
   - ✅ Green checkmark
   - 📋 Order ID
   - 💰 Amount and ETA
   - ⏳ 3-second countdown
   - 🔄 "Redirecting to your rentals in 3... 2... 1..."

2. **Auto-redirect** to My Rentals page

3. **My Rentals Dashboard** shows:
   - 📊 Statistics cards at top
   - 🎴 Your new rental card with:
     - 🖼️ Product image
     - ⏰ **LIVE COUNTDOWN TIMER** (updating every second!)
     - 📅 Start/End dates
     - 💳 Payment method
     - 🚚 Delivery ETA
     - 💵 Total amount

---

## ⏰ Testing Live Countdown Timer

### Watch It Live!
The timer updates every second:
```
00d 02h 30m 15s
00d 02h 30m 14s
00d 02h 30m 13s
...continues counting down
```

### Color Changes to Watch:
- **More than 1 day**: 🟢 Green text
- **Less than 1 day**: 🟡 Yellow text
- **Less than 1 hour**: 🔴 Red text

### What Happens at 00:00:00?
- Page automatically refreshes
- Rental marked as overdue
- Fine calculation begins

---

## 💰 Testing Fine Calculation

### Option A: Create Overdue Rental (Quick Test)

Run this SQL to create an overdue rental:

```bash
mysql -u root -p'Ramji@2311' myapp << 'EOF'
INSERT INTO rentals (product_id, borrower_id, start_time, end_time, total_amount, payment_method, status, delivery_eta_minutes, created_at) 
VALUES (
  10,
  2,
  DATE_SUB(NOW(), INTERVAL 5 HOUR),
  DATE_SUB(NOW(), INTERVAL 3 HOUR),
  300.00,
  'card',
  'delivered',
  45,
  DATE_SUB(NOW(), INTERVAL 5 HOUR)
);
EOF
```

Then visit: http://localhost:8000/my-rentals.php

You'll see:
- ⚠️ **Big red "OVERDUE!" warning**
- ⏱️ **Should have been returned on**: [Date/Time]
- 💸 **ACCUMULATED FINE**: ₹XX.XX
- 📊 **Total Due**: Original amount + Fine

### Fine Calculation Example:
```
Product: Power Drill (₹20/hour)
Overdue by: 3 hours
Fine Rate: 50% of hourly rate = ₹10/hour
Fine Amount: 3 × ₹10 = ₹30
Total Due: ₹300 (rental) + ₹30 (fine) = ₹330
```

---

## 📊 Statistics Dashboard

At the top of My Rentals page, you'll see 4 cards:

### 1. Total Rentals
- Shows count of all your rentals ever
- Includes active, completed, and cancelled

### 2. Active Now
- Count of currently rented items
- Status: 'paid' or 'delivered'

### 3. Total Spent
- Sum of all rental amounts
- Excludes fines (rental costs only)

### 4. Total Fines
- Sum of all accumulated fines
- 🔴 Red if > 0
- Calculated from overdue rentals

---

## 🎴 Rental Card Features

### Active Rental Card (Green Border)
```
┌─────────────────────────────┐
│   🖼️ Product Image          │
│   🟢 PAID                    │
│   📦 Product Name            │
│                              │
│   ⏰ TIME REMAINING          │
│   00d 02h 15m 30s           │
│                              │
│   📅 Start: Oct 14, 2:00 PM │
│   📅 End: Oct 14, 5:00 PM   │
│   💳 Payment: CARD          │
│   🚚 Delivery: 45 mins      │
│                              │
│   💵 Rental: ₹360.00        │
└─────────────────────────────┘
```

### Overdue Rental Card (Red Border)
```
┌─────────────────────────────┐
│   🖼️ Product Image          │
│   🟢 DELIVERED               │
│   📦 Product Name            │
│                              │
│   ⚠️ OVERDUE!               │
│   Should have been returned: │
│   Oct 14, 2:00 PM           │
│                              │
│   💸 ACCUMULATED FINE        │
│   ₹120.00                    │
│   Fine: ₹15/hr (4 hrs)      │
│                              │
│   💵 Rental: ₹200.00        │
│   💸 + Fine: ₹120.00        │
│   ══════════════════════     │
│   📊 Total Due: ₹320.00     │
└─────────────────────────────┘
```

---

## 🧪 Complete Test Scenarios

### Scenario 1: Normal Rental Flow ✅
```
1. Login → Browse → Rent product (2 hours)
2. See success page with 3-sec countdown
3. Auto-redirect to My Rentals
4. See live countdown timer
5. Watch timer count down in real-time
✅ Expected: Green timer, no fines
```

### Scenario 2: Multiple Active Rentals 📦
```
1. Rent 3 different products
2. Set different end times
3. Visit My Rentals
✅ Expected: 
   - Statistics show "3" Active Now
   - 3 rental cards with individual timers
   - Each timer counting down independently
```

### Scenario 3: Overdue Rental 🔴
```
1. Insert overdue rental via SQL (see above)
2. Visit My Rentals
✅ Expected:
   - Red "OVERDUE!" warning
   - Fine amount calculated
   - Total Due = Rental + Fine
   - No countdown timer
```

### Scenario 4: Mixed Status History 📚
```
1. Have some active rentals
2. Have some completed rentals
3. Visit My Rentals
✅ Expected:
   - "Active Rentals" section with timers
   - "Rental History" section below
   - History cards are faded/grayed
   - Statistics show correct counts
```

### Scenario 5: Timer Expiry ⏰
```
1. Rent product for just 1 minute
2. Watch countdown reach 00:00:00
✅ Expected:
   - Page auto-refreshes
   - Rental moves to overdue
   - Fine starts calculating
```

---

## 🔍 What to Look For

### Visual Elements
- ✅ Beautiful gradient header (Orange → Red → Pink)
- ✅ 4 statistics cards with icons
- ✅ Large product images
- ✅ Status badges (Green for active)
- ✅ Category badges
- ✅ Live countdown timers
- ✅ Overdue warnings in red
- ✅ Fine amounts prominently displayed

### Functionality
- ✅ Timers update every second
- ✅ Color changes based on urgency
- ✅ Page refreshes at 00:00:00
- ✅ Fines calculate automatically
- ✅ Statistics update correctly
- ✅ All dates/times format properly
- ✅ Amounts display with ₹ symbol

### User Experience
- ✅ Smooth 3-second redirect after booking
- ✅ Clear success message
- ✅ Order ID displayed
- ✅ Easy navigation (My Rentals → Browse)
- ✅ No confusion about rental status
- ✅ Obvious overdue warnings

---

## 📱 Mobile Responsiveness

Test on mobile or narrow browser:
- ✅ Statistics cards stack vertically
- ✅ Rental cards become single column
- ✅ Countdown timer stays readable
- ✅ All buttons remain clickable
- ✅ Images scale properly

---

## 🐛 Troubleshooting

### Timer Not Updating?
```javascript
// Check browser console for errors
// Timer should log every second
```

### No Fines Showing?
```sql
-- Check end_time is in the past
SELECT end_time, NOW(), status 
FROM rentals 
WHERE id = X;

-- Status must be 'paid' or 'delivered' for fines
```

### Redirect Not Working?
- Check if session is active
- Verify flash message is set
- Check browser console for JS errors

### Page Not Refreshing at 00:00?
- Check JavaScript is enabled
- Verify countdown script is loaded
- Look for console errors

---

## 📊 Database Checks

### View All Rentals
```bash
mysql -u root -p'Ramji@2311' myapp -e "
SELECT 
  r.id,
  p.name,
  r.start_time,
  r.end_time,
  r.status,
  CASE 
    WHEN r.end_time < NOW() THEN 'OVERDUE'
    ELSE 'ON TIME'
  END as overdue_status,
  r.total_amount
FROM rentals r 
JOIN products p ON p.id = r.product_id 
ORDER BY r.created_at DESC;
"
```

### Check Fine Calculations
```bash
mysql -u root -p'Ramji@2311' myapp -e "
SELECT 
  r.id,
  p.name,
  p.price_per_hour,
  TIMESTAMPDIFF(HOUR, r.end_time, NOW()) as hours_overdue,
  CEIL(TIMESTAMPDIFF(MINUTE, r.end_time, NOW()) / 60.0) * (p.price_per_hour * 0.5) as calculated_fine
FROM rentals r 
JOIN products p ON p.id = r.product_id 
WHERE r.end_time < NOW() 
  AND r.status IN ('paid', 'delivered');
"
```

---

## ✅ Success Criteria

You know it's working when:

1. **After Booking:**
   - [x] See animated success page
   - [x] 3-second countdown runs
   - [x] Auto-redirects to My Rentals
   - [x] New rental appears immediately

2. **On My Rentals Page:**
   - [x] Statistics show correct numbers
   - [x] Active rentals have green borders
   - [x] Countdown timers update every second
   - [x] Timer format: XXd XXh XXm XXs
   - [x] Colors change: Green → Yellow → Red

3. **For Overdue Rentals:**
   - [x] Red border around card
   - [x] Big "OVERDUE!" warning
   - [x] Fine amount displayed
   - [x] Total Due = Rental + Fine
   - [x] "Should have been returned" message

4. **General:**
   - [x] Professional design
   - [x] All icons display
   - [x] Images load
   - [x] Buttons work
   - [x] Navigation functions
   - [x] Mobile responsive

---

## 🎉 Enjoy Your New System!

You now have a **professional rental management system** with:
- ⏰ Real-time countdown timers
- 💰 Automatic fine calculation
- 📊 Statistics dashboard
- 🎨 Beautiful UI
- 🔄 Smooth workflows
- 📱 Mobile responsive

**Test it thoroughly and enjoy!** 🚀

---

## 📞 Quick Reference

**Server:** `php -S localhost:8000`  
**Homepage:** http://localhost:8000/  
**My Rentals:** http://localhost:8000/my-rentals.php  
**Login:** user@example.com / password  
**Database:** myapp (root/Ramji@2311)

**Fine Formula:** `Hours Overdue × (Hourly Rate × 50%)`  
**Timer Update:** Every 1 second  
**Auto-refresh:** On timer expiry  
**Redirect Delay:** 3 seconds after booking
