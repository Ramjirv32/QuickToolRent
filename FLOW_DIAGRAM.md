# 🎯 COMPLETE RENTAL FLOW DIAGRAM

```
┌─────────────────────────────────────────────────────────────────────┐
│                    QUICKTOOLRENT - RENTAL FLOW                      │
└─────────────────────────────────────────────────────────────────────┘

┌──────────┐
│  START   │
└────┬─────┘
     │
     ▼
┌─────────────────┐
│  User Login     │
│  (Required)     │
└────┬────────────┘
     │
     ▼
┌─────────────────────────┐
│  Browse Homepage        │
│  - 80+ Products         │
│  - 9 Categories         │
│  - Search Function      │
└────┬────────────────────┘
     │
     ▼
┌──────────────────────────┐
│  Click Category          │
│  (e.g., Power Tools)     │
└────┬─────────────────────┘
     │
     ▼
┌──────────────────────────────┐
│  View Category Page          │
│  - 20+ products              │
│  - Filtered by category      │
│  - Product cards with images │
└────┬─────────────────────────┘
     │
     ▼
┌────────────────────────┐
│  Click "Rent Now"      │
│  on Product Card       │
└────┬───────────────────┘
     │
     ▼
┌──────────────────────────────────────┐
│  Rental Form (rent-product.php)      │
│  ┌────────────────────────────────┐  │
│  │ 📅 Start Time: [datetime]     │  │
│  │ 📅 End Time: [datetime]       │  │
│  │ 💳 Payment: [card/upi/etc]    │  │
│  │ 📍 Address: [delivery addr]   │  │
│  │                                │  │
│  │ [Pay & Place Order] Button    │  │
│  └────────────────────────────────┘  │
└────┬─────────────────────────────────┘
     │
     ▼
┌──────────────────────────────────────┐
│  Backend Processing                  │
│  (rent-confirm.php)                  │
│  ┌────────────────────────────────┐  │
│  │ ✅ Validate dates              │  │
│  │ ✅ Calculate total amount      │  │
│  │ ✅ Insert rental record        │  │
│  │ ✅ Mark product unavailable    │  │
│  │ ✅ Generate order ID           │  │
│  └────────────────────────────────┘  │
└────┬─────────────────────────────────┘
     │
     ▼
┌───────────────────────────────────────────────────────┐
│  🎉 SUCCESS PAGE (Embedded in rent-confirm.php)      │
│  ┌─────────────────────────────────────────────────┐  │
│  │   ✅ Green Checkmark (Large)                    │  │
│  │                                                  │  │
│  │   "Booking Successful!"                         │  │
│  │                                                  │  │
│  │   Order ID: #000007                            │  │
│  │   Amount: ₹360.00                              │  │
│  │   ETA: 45 mins                                  │  │
│  │                                                  │  │
│  │   🔄 Loading Spinner                            │  │
│  │   "Redirecting in 3... 2... 1..."              │  │
│  │                                                  │  │
│  │   [View My Rentals Now] Button                 │  │
│  └─────────────────────────────────────────────────┘  │
│                                                        │
│  ⏳ JavaScript: 3-second countdown                    │
│  ↓ Auto-redirect                                      │
└────┬───────────────────────────────────────────────────┘
     │
     │ (3 seconds later...)
     │
     ▼
┌──────────────────────────────────────────────────────────────────┐
│  📊 MY RENTALS DASHBOARD (my-rentals.php)                        │
│                                                                   │
│  ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓  │
│  ┃  🎨 HEADER (Orange Gradient)                              ┃  │
│  ┃  "My Rentals Dashboard"                                   ┃  │
│  ┃                                                            ┃  │
│  ┃  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐       ┃  │
│  ┃  │ Total   │ │ Active  │ │  Total  │ │  Total  │       ┃  │
│  ┃  │ Rentals │ │   Now   │ │  Spent  │ │  Fines  │       ┃  │
│  ┃  │    5    │ │    2    │ │ ₹1,200  │ │  ₹120   │       ┃  │
│  ┃  └─────────┘ └─────────┘ └─────────┘ └─────────┘       ┃  │
│  ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛  │
│                                                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  ⏰ ACTIVE RENTALS SECTION                                │  │
│  │  ┌────────────────────────┐  ┌────────────────────────┐  │  │
│  │  │ 🖼️ Product Image       │  │ 🖼️ Product Image      │  │  │
│  │  │ 🟢 PAID                │  │ 🟢 DELIVERED           │  │  │
│  │  │                         │  │                        │  │  │
│  │  │ Power Drill            │  │ Extension Ladder       │  │  │
│  │  │ 🏷️ Power Tools         │  │ 🏷️ Ladders            │  │  │
│  │  │                         │  │                        │  │  │
│  │  │ ⏰ TIME REMAINING       │  │ ⏰ TIME REMAINING      │  │  │
│  │  │ ┏━━━━━━━━━━━━━━━━━━┓ │  │ ┏━━━━━━━━━━━━━━━━━━┓ │  │  │
│  │  │ ┃ 00d 02h 15m 30s  ┃ │  │ ┃ 01d 05h 30m 45s  ┃ │  │  │
│  │  │ ┗━━━━━━━━━━━━━━━━━━┛ │  │ ┗━━━━━━━━━━━━━━━━━━┛ │  │  │
│  │  │ (Updates every 1s)  │  │ (Updates every 1s)     │  │  │
│  │  │                         │  │                        │  │  │
│  │  │ 📅 Start: Oct 14, 2PM  │  │ 📅 Start: Oct 13, 9AM │  │  │
│  │  │ 📅 End: Oct 14, 5PM    │  │ 📅 End: Oct 15, 3PM   │  │  │
│  │  │ 💳 Payment: CARD       │  │ 💳 Payment: UPI       │  │  │
│  │  │ 🚚 Delivery: 42 mins   │  │ 🚚 Delivery: 38 mins  │  │  │
│  │  │                         │  │                        │  │  │
│  │  │ 💰 Rental: ₹360.00     │  │ 💰 Rental: ₹864.00    │  │  │
│  │  └────────────────────────┘  └────────────────────────┘  │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  📚 RENTAL HISTORY SECTION                                │  │
│  │  ┌────────────┐  ┌────────────┐  ┌────────────┐         │  │
│  │  │ 🖼️ Image   │  │ 🖼️ Image  │  │ 🖼️ Image  │         │  │
│  │  │ 🟣 COMPLETED│  │🟣 COMPLETED│  │⚫ CANCELLED│         │  │
│  │  │            │  │            │  │            │         │  │
│  │  │ Saw        │  │ Generator  │  │ Drill      │         │  │
│  │  │ Oct 10-11  │  │ Oct 5-6    │  │ Oct 1      │         │  │
│  │  │ ₹200       │  │ ₹450       │  │ ₹150       │         │  │
│  │  └────────────┘  └────────────┘  └────────────┘         │  │
│  └───────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────┘

═══════════════════════════════════════════════════════════════════
                    SPECIAL CASE: OVERDUE RENTAL
═══════════════════════════════════════════════════════════════════

When timer reaches 00:00:00 OR rental past end_time:

┌─────────────────────────────────────────────────────────────┐
│  ⚠️ OVERDUE RENTAL CARD (Red Border)                        │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ 🖼️ Product Image                                    │    │
│  │ 🟢 DELIVERED                                        │    │
│  │                                                      │    │
│  │ Circular Saw                                        │    │
│  │ 🏷️ Power Tools                                      │    │
│  │                                                      │    │
│  │ ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓  │    │
│  │ ┃  ⚠️  OVERDUE!                                 ┃  │    │
│  │ ┃                                                ┃  │    │
│  │ ┃  Should have been returned on:               ┃  │    │
│  │ ┃  Oct 14, 2025 2:00 PM                        ┃  │    │
│  │ ┃                                                ┃  │    │
│  │ ┃  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━      ┃  │    │
│  │ ┃                                                ┃  │    │
│  │ ┃  💸 ACCUMULATED FINE                          ┃  │    │
│  │ ┃  ₹120.00                                      ┃  │    │
│  │ ┃                                                ┃  │    │
│  │ ┃  Fine: 50% of ₹15/hr (4 hours overdue)       ┃  │    │
│  │ ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛  │    │
│  │                                                      │    │
│  │ 📅 Start: Oct 14, 10:00 AM                          │    │
│  │ 📅 End: Oct 14, 2:00 PM (MISSED)                    │    │
│  │ 💳 Payment: CARD                                    │    │
│  │                                                      │    │
│  │ ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓  │    │
│  │ ┃  💰 Rental Amount:    ₹200.00                ┃  │    │
│  │ ┃  💸 + Late Fine:      ₹120.00                ┃  │    │
│  │ ┃  ═══════════════════════════════              ┃  │    │
│  │ ┃  📊 TOTAL DUE:        ₹320.00                ┃  │    │
│  │ ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛  │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘

═══════════════════════════════════════════════════════════════════
                    FINE CALCULATION LOGIC
═══════════════════════════════════════════════════════════════════

SQL Query (runs on page load):

  CASE 
    WHEN end_time < NOW() AND status NOT IN ('completed', 'cancelled')
    THEN CEIL(TIMESTAMPDIFF(MINUTE, end_time, NOW()) / 60.0) 
         × (price_per_hour × 0.5)
    ELSE 0 
  END as fine_amount

Example Calculation:
  • End Time: Oct 14, 2:00 PM
  • Current Time: Oct 14, 6:00 PM
  • Overdue: 4 hours
  • Hourly Rate: ₹15/hour
  • Fine Rate: 50% = ₹7.50/hour
  • Fine Amount: 4 × ₹7.50 = ₹30.00

═══════════════════════════════════════════════════════════════════
                    COUNTDOWN TIMER LOGIC
═══════════════════════════════════════════════════════════════════

JavaScript (runs every second):

  setInterval(() => {
    if (totalSeconds <= 0) {
      location.reload(); // Refresh to show overdue
      return;
    }
    
    days = floor(totalSeconds / 86400)
    hours = floor((totalSeconds % 86400) / 3600)
    minutes = floor((totalSeconds % 3600) / 60)
    seconds = totalSeconds % 60
    
    // Update display
    // Change color based on time remaining:
    if (totalSeconds < 3600)  → RED 🔴
    else if (totalSeconds < 86400) → YELLOW 🟡
    else → GREEN 🟢
    
    totalSeconds--
  }, 1000)

═══════════════════════════════════════════════════════════════════
                    USER ACTION OPTIONS
═══════════════════════════════════════════════════════════════════

From My Rentals Dashboard:

  ┌─────────────────────────────────┐
  │  [View My Rentals] (Current)    │
  │  [Browse More Tools] (Homepage) │
  │  [My Account] (Profile)         │
  │  [Logout]                       │
  └─────────────────────────────────┘

═══════════════════════════════════════════════════════════════════
                    STATUS FLOW DIAGRAM
═══════════════════════════════════════════════════════════════════

  ┌─────────┐     ┌──────────┐     ┌───────────┐     ┌──────────┐
  │ pending │ --> │   paid   │ --> │ delivered │ --> │completed │
  └─────────┘     └──────────┘     └───────────┘     └──────────┘
       │                                   │
       │                                   │
       ▼                                   ▼
  ┌──────────┐                      ┌──────────┐
  │cancelled │                      │ OVERDUE  │
  └──────────┘                      └──────────┘
                                    (Fine starts)

═══════════════════════════════════════════════════════════════════
                    END OF FLOW DIAGRAM
═══════════════════════════════════════════════════════════════════
```

## 🎯 Key Takeaways

1. **Booking Flow**: Browse → Rent → Success Page → Auto-redirect → My Rentals
2. **Success Page**: 3-second countdown with order details
3. **My Rentals**: Live timers, statistics, fine calculations
4. **Overdue**: Automatic fine calculation, red warnings
5. **Timer**: Updates every second, color-coded urgency
6. **Fine Formula**: Hours Overdue × (Hourly Rate × 50%)

## 📊 Page Hierarchy

```
Homepage
  ├── Category Pages
  │   └── Product Rent Form
  │       └── Success Page (3s)
  │           └── My Rentals Dashboard ⭐
  │               ├── Active Rentals (Live Timers)
  │               └── Rental History
  │
  ├── Login
  └── My Account
```

## 🚀 Ready to Test!

Server: `php -S localhost:8000`  
Test User: `user@example.com` / `password`  
Start Here: http://localhost:8000/

**Enjoy your advanced rental system!** 🎉
