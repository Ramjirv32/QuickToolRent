#!/bin/bash

# QuickToolRent - Quick Start Script

echo ""
echo "╔══════════════════════════════════════════════════════════╗"
echo "║         🛠️  QuickToolRent - Tool Rental Platform        ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""
echo "Starting PHP Development Server..."
echo ""
echo "📍 Server URL: http://localhost:8000"
echo ""
echo "👥 Test Accounts:"
echo "   ┌─────────────────────────────────────────────────────┐"
echo "   │ Admin:    admin@example.com / admin123             │"
echo "   │ Owner:    alice.owner@example.com / password       │"
echo "   │ Borrower: charlie.borrower@example.com / password  │"
echo "   └─────────────────────────────────────────────────────┘"
echo ""
echo "🔗 Quick Links:"
echo "   • Homepage: http://localhost:8000/index.php"
echo "   • Admin:    http://localhost:8000/admin/index.php"
echo "   • Login:    http://localhost:8000/login.php"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""
echo "════════════════════════════════════════════════════════════"
echo ""

php -S localhost:8000
